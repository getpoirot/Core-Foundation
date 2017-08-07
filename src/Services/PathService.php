<?php
namespace Module\Foundation\Services;

use Module\Foundation\Services\PathService\PathAction;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;
use Poirot\Std\Type\StdArray;

/*
 Merged Config:

\Module\Foundation\Services\PathService::CONF => [
    'paths' => [
        // According to route name 'www-assets' to serve statics files
        // @see cor-http_foundation.routes
        'mod-content-media_cdn' => function($args) {
            $uri = $this->assemble('$serverUrlTenderBin', $args);
            return $uri;
        },
    ],
    'variables' => [
        'serverUrlTenderBin' => function() {
            return \Module\HttpFoundation\Actions::url(
                'main/tenderbin/resource/get'
                , [ 'resource_hash' => '$hash' ]
                , \Module\HttpFoundation\Actions\Url::INSTRUCT_NOTHING
            );
        },
    ],
],


 Access:

 \Module\Foundation\Actions::Path()
    ->assemble('$baseUrl');

*/

class PathService
    extends aServiceContainer
{
    const CONF = 'module.foundation.path';
         
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
        $config = $config->get(self::CONF, array());
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
