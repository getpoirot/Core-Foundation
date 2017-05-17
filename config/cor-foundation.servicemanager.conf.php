<?php
use Poirot\Ioc\Container\Service\ServiceInstance;
use Poirot\View\ViewModel\RendererPhp;


return [
    'implementations' => [
        'ViewModel'         => \Poirot\View\Interfaces\iViewModelPermutation::class,
        'ViewModelRenderer' => \Poirot\View\Interfaces\iViewRenderer::class,
        'ViewModelResolver' => \Poirot\Loader\LoaderAggregate::class,
    ],
    'services' => [
        \Module\Foundation\ServiceManager\ServiceViewModel::class,
            new ServiceInstance('viewModelRenderer', RendererPhp::class),
            \Module\Foundation\ServiceManager\ServiceViewModelResolver::class,
    ],
];
