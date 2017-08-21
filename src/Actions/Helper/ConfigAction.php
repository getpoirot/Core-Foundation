<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;
use Poirot\Application\aSapi;
use Poirot\Std\Struct\DataEntity;


class ConfigAction 
    extends aAction
{
    /** @var  DataEntity */
    protected $config;


    /**
     * Get Config Values
     *
     * Argument can passed and map to config if exists [$key][$_][$__] ..
     *
     * @param $key
     * @param null $_
     *
     * @return mixed|null|DataEntity
     */
    function __invoke($key = null, $_ = null)
    {
        $config = $this->_attainSapiConfig();
        if ($key === null)
            // Just Return Config Instance
            return $config;


        /** @var DataEntity $config */
        $config   = $config->get($key, array());
        $keyconfs = func_get_args();
        array_shift($keyconfs);

        foreach ($keyconfs as $key) {
            if ($key === null)
                continue;

            if (! isset($config[$key]) )
                return null;

            $config = $config[$key];
        }

        return $config;
    }

    
    // ..
    
    protected function _attainSapiConfig()
    {
        if (! $this->config ) {
            /** @var aSapi $sapi */
            $services = $this->services; 
            $sapi     = $services->get('/sapi');
            $this->config = $sapi->config();
        }

        return $this->config;
    }
}
