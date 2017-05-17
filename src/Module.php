<?php
namespace Module\Foundation
{
    use Module\Foundation\Actions;
    use Module\Foundation\Services;
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
    use Poirot\Ioc\Container;
    use Poirot\Ioc\Container\BuildContainer;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
    use Poirot\Loader\Interfaces\iLoaderAutoload;
    use Poirot\Std\Interfaces\Struct\iDataEntity;

    use Module\Foundation\Actions\BuildContainerActionOfFoundationModule;

    use Module\Foundation\Actions\Helper\CycleAction;
    use Module\Foundation\Services\PathService\PathAction;
    use Module\Foundation\Actions\Helper\ViewAction;


    /**
     * @method static ViewAction         view($template = null, $variables = null)
     * @method static PathAction         path($pathString = null, $variables = array())
     * @method static CycleAction        cycle($action = null, $steps = 1, $reset = true)
     */
    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleInitServices
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureModuleNestActions
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
    {
        /**
         * Register class autoload on Autoload
         *
         * priority: 1000 B
         *
         * @param LoaderAutoloadAggregate $baseAutoloader
         *
         * @return iLoaderAutoload|array|\Traversable|void
         */
        function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
        {
            #$nameSpaceLoader = \Poirot\Loader\Autoloader\LoaderAutoloadNamespace::class;
            $nameSpaceLoader = 'Poirot\Loader\Autoloader\LoaderAutoloadNamespace';
            /** @var LoaderAutoloadNamespace $nameSpaceLoader */
            $nameSpaceLoader = $baseAutoloader->loader($nameSpaceLoader);
            $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);
        }

        /**
         * Register config key/value
         *
         * priority: 1000 D
         *
         * - you may return an array or Traversable
         *   that would be merge with config current data
         *
         * @param iDataEntity $config
         *
         * @return array|\Traversable
         */
        function initConfig(iDataEntity $config)
        {
            return \Poirot\Config\load(__DIR__ . '/../../config/cor-foundation');
        }

        /**
         * Build Service Container
         *
         * priority: 1000 X
         *
         * - register services
         * - define aliases
         * - add initializers
         * - ...
         *
         * @param Container $services
         *
         * @return array|\Traversable|void Container Builder Config
         */
        function initServiceManager(Container $services)
        {
            return \Poirot\Config\load(__DIR__ . '/../../config/cor-foundation.servicemanager');
        }

        /**
         * Get Nested Module Services
         *
         * it can be used to manipulate other registered services by modules
         * with passed Container instance as argument.
         *
         * priority not that serious
         *
         * @param Container $moduleContainer
         *
         * @return null|array|BuildContainer|\Traversable
         */
        function getServices(Container $moduleContainer = null)
        {
            return \Poirot\Config\load(__DIR__ . '/../../config/cor-foundation.services');
        }

        /**
         * Get Action Services
         *
         * priority: after GrabRegisteredServices
         *
         * - return Array used to Build ModuleActionsContainer
         *
         * @return array|ContainerForFeatureActions|BuildContainer|\Traversable
         */
        function getActions()
        {
            return new BuildContainerActionOfFoundationModule;
        }

        /**
         * Proxy Call To Actions
         *
         * @param $name
         * @param array $arguments
         *
         * @return mixed
         */
        static function __callStatic($name, array $arguments)
        {
            return call_user_func_array([Actions\IOC::class, $name], $arguments);
        }

        /**
         * Retrieve Module Service
         *
         * @param string $get
         *
         * @return mixed
         */
        static function services($get, $options = null)
        {
            return call_user_func([Services\IOC::class, $get], $options);
        }
    }
}


namespace Module\Foundation\Actions
{
    class IOC extends \IOC
    { }
}

namespace Module\Foundation\Services
{
    use Module\Foundation\Services\PathService\PathAction;

    /**
     * @method static PathAction Path()
     */
    class IOC extends \IOC
    { }
}
