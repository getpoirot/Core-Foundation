<?php
namespace Module\Foundation\Services;

use Module\Foundation\Services\PathService\PathAction;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;
use Poirot\Std\Type\StdArray;


/**
 * Detect Server Url, BasePath, BaseUrl
 * can retrieved as a service
 *
 * PathAction \Module\Foundation\Module::Path()->assemble('$baseUrl');
 */
class PathService
    extends aServiceContainer
{
    const CONF_KEY = 'module.foundation.path-service';
    
    const PARAM_SERVER_URL = 'serverUrl';
    const PARAM_BASE_PATH  = 'basePath';
    const PARAM_BASE_URL   = 'baseUrl';
    
         
    /**
     * @var string Service Name
     */
    protected $name = 'path';

    
    /**
     * Create Service
     *
     * @return PathAction|callable
     */
    function newService()
    {
        $pathAction = new PathAction;


        # build with merged config
        /** @var DataEntity $config */
        $services = $this->services();
        $config = $services->get('/sapi')->config();
        $config = $config->get(self::CONF_KEY, array());
        // strip null values from config
        $stdTrav = new StdArray($config);
        $config  = $stdTrav->withWalk(function($val) {
            return $val === null; // null values not saved
        }, true);

        // Rewrite Default Variables If Config Set
        $pathAction->with( $pathAction::parseWith($config) );
        return $pathAction;
    }
}
