<?php
use Module\Foundation\ServiceManager\ViewModelRenderer;
use Poirot\Ioc\Container\Service\ServiceInstance;


return [
    'implementations' => [
        'ViewModel'         => \Poirot\View\Interfaces\iViewModel::class,
        'ViewModelRenderer' => \Poirot\View\Interfaces\iViewRenderer::class,
        'ViewModelResolver' => \Poirot\Loader\LoaderAggregate::class,
    ],
    'services' => [
        \Module\Foundation\ServiceManager\ServiceViewModel::class,
            new ServiceInstance('viewModelRenderer', ViewModelRenderer::class),
            \Module\Foundation\ServiceManager\ServiceViewModelResolver::class,
    ],
];
