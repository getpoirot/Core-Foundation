<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Application\aSapi;
use Poirot\Ioc\Container\Service\aServiceContainer;

use Poirot\Loader\LoaderAggregate;
use Poirot\Loader\LoaderMapResource;
use Poirot\Loader\LoaderNamespaceStack;
use Poirot\Std\Struct\DataEntity;


class ServiceViewModelResolver
    extends aServiceContainer
{
    const CONF = 'resolver';


    /**
     * @var string Service Name
     */
    protected $name = 'ViewModelResolver';
    protected $_layout_extension = 'phtml';


    /**
     * Create Service
     *
     * @return LoaderAggregate
     */
    function newService()
    {
        $loader = new LoaderAggregate;
        $loader->attach(new LoaderMapResource, 500);
        $loader->attach(new LoaderNamespaceStack(null, function($name, $resource, $match) {
                return \Poirot\Loader\funcWatchFileExists($name, $resource, $match, '.'.$this->_layout_extension);
            }
        ));


        # Setup By Configs:
        $services = $this->services();

        /** @var aSapi $config */
        $config   = $services->get('/sapi');
        $config   = $config->config();
        /** @var DataEntity $config */
        $config   = $config->get( self::CONF, [] );

        if (! empty($config) )
            $loader->with( $loader::parseWith($config), true );


        return $loader;
    }

    /**
     * Set Layout File Extension
     *
     * @param string $extension
     *
     * @return $this
     */
    function setLayoutFileExt($extension)
    {
        $this->_layout_extension = ltrim('.', (string) $extension);
        return $this;
    }
}
