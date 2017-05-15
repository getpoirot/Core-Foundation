<?php
use Module\Foundation\ServiceManager\ServiceViewModelResolver;

return [
    ServiceViewModelResolver::CONF_KEY => [
        /*
         * > Setup Aggregate Loader
         *   Options:
         *  [
         *    'attach' => [new Loader(), $priority => new OtherLoader(), ['loader' => iLoader, 'priority' => $pr] ],
         *    Loader::class => [
         *       // Options
         *       'Poirot\AaResponder'  => [APP_DIR_VENDOR.'/poirot/action-responder/Poirot/AaResponder'],
         *       'Poirot\Application'  => [APP_DIR_VENDOR.'/poirot/application/Poirot/Application'],
         *    ],
         *    OtherLoader::class => [options..]
         *  ]
         */

        /** @see \Poirot\Loader\LoaderAggregate */
    ],
];
