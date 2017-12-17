<?php
use Module\Foundation\ServiceManager\ViewModelRenderer;

return [
    'implementations' => [
        'ViewModel'         => \Poirot\View\Interfaces\iViewModel::class,
        'ViewModelRenderer' => \Poirot\View\Interfaces\iViewRenderer::class,
        'ViewModelResolver' => \Poirot\Loader\LoaderAggregate::class,
    ],
    'services' => [
          'ViewModel'         => \Module\Foundation\ServiceManager\ServiceViewModel::class,
          'ViewModelRenderer' => ViewModelRenderer::class,
          'ViewModelResolver' => \Module\Foundation\ServiceManager\ServiceViewModelResolver::class,
    ],
];
