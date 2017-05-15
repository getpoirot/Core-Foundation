<?php
use Module\Foundation\ServiceManager\ViewModelRenderer;
use Poirot\Ioc\Container\Service\ServiceInstance;


return [
    'implementations' => [
        'viewModel'         => \Poirot\View\Interfaces\iViewModelPermutation::class,
        'viewModelRenderer' => \Poirot\View\Interfaces\iViewRenderer::class,
        'viewModelResolver' => \Poirot\Loader\LoaderAggregate::class,
    ],
    'services' => [
        \Module\Foundation\ServiceManager\ServiceViewModel::class,
            new ServiceInstance('viewModelRenderer', ViewModelRenderer::class),
            \Module\Foundation\ServiceManager\ServiceViewModelResolver::class,
    ],
];
