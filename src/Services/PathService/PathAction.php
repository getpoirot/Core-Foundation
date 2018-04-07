<?php
namespace Module\Foundation\Services\PathService;

use Poirot\Std\ConfigurableSetter;
use Poirot\Std\Struct\DataEntity;


class PathAction 
    extends ConfigurableSetter
{
    /** @var array key value of paths name and uri */
    protected $paths = array(
        ## name => '$var/uri/to/path'
    );

    /** @var array Path names Restricted from override */
    protected $__restricted = array(
        # 'path-name' => true
    );

    /** @var string Last invoked path name */
    protected $__lastInvokedPath;
    /** @var string Last invoked uri */
    protected $__lastInvokedUri = "";
    
    /** @var DataEntity */
    protected $params;

    /**
     * @inheritdoc
     *
     * - path('path-name')
     * - path('$user/path/uri')
     *
     * - path($uriOrName, ['var' => 'value'])
     * @see PathAction::assemble
     *
     * @param null $arg
     *
     * @return $this
     */
    function __invoke()
    {
        $funcArgs = func_get_args();

        if ( empty($funcArgs) )
            ## path()
            return $this;


        // attain uri:

        ## path($name, ..)
        $name = array_shift($funcArgs);

        if ( $this->hasPath($name) ) {
            $uri = $this->getPath($name);
            if ($uri instanceof \Closure) {
                // Bind given closure into this class
                $uri = \Closure::bind( $uri
                    , $this
                    , get_class($this)
                );
            }

            if ( is_callable($uri) )
                $uri = call_user_func_array($uri, $funcArgs);

        }
        else
            ## we don't have pathName, assume that entered text is uri
            $uri = $name;


        // assemble uri with given arguments as variables:

        array_unshift($funcArgs, $uri); ### we want uri as first argument
        ## assemble($uri, ..arguments)
        $assembledUri = call_user_func_array(
            array($this, 'assemble')
            , $funcArgs
        );

        $assembledUri = rtrim($assembledUri, '/');

        $this->__lastInvokedUri  = $assembledUri;
        $this->__lastInvokedPath = $name;

        return $this;
    }
    
    /**
     * Set path uri alias
     *
     * $uri
     * function($args) : string
     *
     * @param string          $name
     * @param string|callable $uri
     * @param bool            $isRestricted
     *
     * @throws \Exception
     * @return $this
     */
    function setPath($name, $uri, $isRestricted = false)
    {
        if ( $this->hasPath($name) && $this->_isRestricted($name) )
            throw new \Exception(
                sprintf('Path with name (%s) already exists and not allow override it.', $name)
            );

        $n = $this->_normalizeName($name);
        $this->paths[$n] = (is_callable($uri)) ? $uri : (string) $uri;
        (!$isRestricted) ?: $this->__restricted[$n] = true;

        return $this;
    }

    /**
     * Get pathName uri
     *
     * @param string $name
     *
     * @return string|callable
     * @throws \Exception
     */
    function getPath($name)
    {
        if (! $this->hasPath($name) )
            throw new \Exception(sprintf('Path with name (%s) not found.', $name));

        $n = $this->_normalizeName($name);
        return $this->paths[$n];
    }

    /**
     * Determine that pathname is exists?
     *
     * @param $name
     *
     * @return bool
     */
    function hasPath($name)
    {
        $n = $this->_normalizeName($name);
        return isset($this->paths[$n]);
    }

    /**
     * Get All Registered Paths
     *
     * @return array
     */
    function getPaths()
    {
        return $this->paths;
    }

    
    // Options:

    /**
     * Set key/value pair of paths and Uri
     *
     * @param array $paths
     *
     * @throws \Exception
     * @return $this
     */
    function setPaths(array $paths)
    {
        if (!empty($paths) && array_values($paths) == $paths)
            throw new \Exception('Paths Must Be Associated Array.');

        foreach ($paths as $name => $uri)
            $this->setPath($name, $uri);

        return $this;
    }

    /**
     * Set Params Options
     *
     * @param array|\Traversable $params
     * 
     * @return $this
     */
    function setVariables($params)
    {
        $this->variables()->import($params);
        return $this;
    }
    
    /**
     * Variables Bind With Paths
     * 
     * @return DataEntity
     */
    function variables()
    {
        if (!$this->params)
            $this->params = new DataEntity;
        
        return $this->params;
    }


    // ...

    /**
     * Get Last Invoked Uri
     *
     * !! echo path()
     *
     * @return string
     */
    function __toString()
    {
        return $this->__lastInvokedUri;
    }

    /**
     * Assemble Uri
     *
     * ! the uri is a string including variable names
     *
     * usage:
     * $uri = '/path/to/:variable'
     * - assembleUri($uri);                          # using default class variables
     * - assembleUri($uri, ['variable' => 'value']); # replace default class variables
     *
     * - variable can be a valid callable
     *   ['variable' => function() { return 'fetched-value'; }]
     *
     * @param string $uri  Uri or Registered Path
     * @param array  $availVariables
     *
     * @throws \Exception
     * @return mixed
     */
    function assemble($uri, array $availVariables = [])
    {
        if ( !empty($availVariables) && array_values($availVariables) == $availVariables )
            throw new \Exception('Variable Arrays Must Be Associated.');

        /*
         * [
             0 => [
                "$baseUrl"
             ]
             1 => [
                "baseUrl"
             ]
           ]
         *
         * $matches[0] retrun array of full variables matched, exp. $path
         * $matches[1] retrun array of variables name matched, exp. path
         */
        preg_match_all('/\$(\w[\w\d]*)/', $uri, $matches);

        if (count($matches[0]) === 0)
            // we don't have any variable in uri
            return $uri;


        $availVariables = array_merge(
            \Poirot\Std\cast($this->variables())->toArray()
            , $availVariables
        );


        // Build order of variables
        // 'path' => 'ValuablePath' TO 0 => 'ValuablePath'
        //
        foreach ($matches[1] as $i => $var)
        {
            if (! array_key_exists($var, $availVariables) )
                throw new \Exception(sprintf(
                    'Value of variable (%s) is not passed as properties.', $var ));

            $currValue = $availVariables[$var];
            if ($currValue instanceof \Closure)
                $currValue = $currValue();


            $availVariables[$i]   = $currValue;
            $availVariables['$'.$var] = $currValue;
            #unset($availVariables[$var]); with explode
        }


        // TODO "\$baseUrl/www/", none baseurl should be "/www" but is "www"
        // replace variables to uri
        //
        $expUri = explode('/', $uri);
        foreach ($expUri as $i => $segment) {
            if ( isset($availVariables[$segment]) ) {
                ## Only if empty variable passed not for http://
                #
                $expUri[$i] = $availVariables[$segment];
                if ($expUri[$i] === '' || $expUri[$i] === null)
                    unset($expUri[$i]);
            }
        }

        # with explode
        #foreach ($matches[0] as $i => $inUriVar) {
        #    $uri = preg_replace('/\\'.$inUriVar.'/', $availVariables[$i], $uri, 1);
        #}


        return rtrim(implode('/', $expUri), '/');
    }

    /**
     * Check that given path name is restricted from override
     * @param $name
     * @return bool
     */
    protected function _isRestricted($name)
    {
        $name = $this->_normalizeName($name);
        return isset($this->__restricted[$name]);
    }

    /**
     * Normalize names
     *
     * @param $name
     *
     * @return string
     */
    protected function _normalizeName($name)
    {
        return strtolower((string) $name);
    }
    
    
    // ..

    /**
     * Load Build Options From Given Resource
     *
     * - usually it used in cases that we have to support
     *   more than once configure situation
     *   [code:]
     *     Configurable->with(Configurable::withOf(path\to\file.conf))
     *   [code]
     *
     *
     * @param array|mixed $optionsResource
     * @param array       $_
     *        usually pass as argument into ::with if self instanced
     *
     * @throws \InvalidArgumentException if resource not supported
     * @return array
     */
    static function parseWith($optionsResource, array $_ = null)
    {
        if (!static::isConfigurableWith($optionsResource))
            throw new \InvalidArgumentException(sprintf(
                'Invalid Configuration Resource provided on (%s); given: (%s).'
                , static::class, \Poirot\Std\flatten($optionsResource)
            ));

        // ..

        if ($optionsResource instanceof \Traversable)
            $optionsResource = \Poirot\Std\cast($optionsResource)->toArray(null, true);
        
        return $optionsResource;
    }
}
