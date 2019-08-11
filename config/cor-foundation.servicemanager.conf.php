<?php
use \Module\Foundation\ServiceManager;

return [
    'implementations' => [
        'ViewModel'         => \Poirot\View\Interfaces\iViewModel::class,
        'ViewModelRenderer' => \Poirot\View\Interfaces\iViewRenderer::class,
        'ViewModelResolver' => \Module\Foundation\View\ViewModelResolver::class,
    ],
    'services' => [
          'ViewModel'         => ServiceManager\ServiceViewModel::class,
          'ViewModelRenderer' => ServiceManager\ViewModelRenderer::class,
          'ViewModelResolver' => ServiceManager\ServiceViewModelResolver::class,
    ],
];
