<?php
/**
 * Default Content IOC Services
 *
 * @see \Poirot\Ioc\Container\BuildContainer
 *
 * ! These Services Can Be Override By Name (also from other modules).
 *   Nested in IOC here at: /module/tenderbin/services
 *
 *
 * @see \Module\Content::getServices()
 */

return [
    'services' => [
        \Module\Foundation\Services\PathService::class,
    ],
];