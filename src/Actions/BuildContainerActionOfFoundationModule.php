<?php
namespace Module\Foundation\Actions;

use Module\Foundation\Actions\Helper\ConfigAction;
use Module\Foundation\Actions\Helper\CycleAction;
use Module\Foundation\Actions\Helper\ViewService;
use Poirot\Ioc\Container\BuildContainer;


class BuildContainerActionOfFoundationModule
    extends BuildContainer
{
    protected $extends = [
        'path' => '/module/Foundation/services/path'
    ];

    protected $services = [
        ## Helpers
        'view'          => ViewService::class,
        'config'        => ConfigAction::class,
        'cycle'         => CycleAction::class,
    ];
}
