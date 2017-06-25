<?php
namespace Module\Foundation
{

    use Module\Foundation\Actions\Helper\ConfigAction;
    use Module\Foundation\Actions\Helper\CycleAction;
    use Module\Foundation\Actions\Helper\ViewService;
    use Module\Foundation\ServiceManager\ServiceViewModel;
    use Module\Foundation\Services\PathService;
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\Sapi\Module\ContainerForFeatureActions;
    use Poirot\Ioc\Container;
    use Poirot\Ioc\Container\BuildContainer;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
    use Poirot\Loader\Interfaces\iLoaderAutoload;

    use Module\Foundation\Actions\BuildContainerActionOfFoundationModule;


    /**
     * - Provide Base Services in Service Manager:
     *   ViewModel (two step view) accompaniment with two others services
     *   include ViewModelRenderer and ViewModelResolver
     *
     *   @see ServiceViewModel
     *
     *
     * - Provide Action Helpers:
     *   @see ConfigAction
     *   @see CycleAction
     *   @see PathService
     *   @see ViewService
     *
     *   @see BuildContainerActionOfFoundationModule
     *
     *
     * - Path as a Service:
     *   set Variable into path and use it to retrieve path prefix,
     *   it can be configured from merged config by modules.
     *
     *   @see PathService
     */

    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleInitServices
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureModuleNestActions
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
         * Build Service Container
         *
         * priority: 1000 X
         *
         * - register services
         * - define aliases
         * - add initializer(s)
         * - ...
         *
         * @param Container $services
         *
         * @return array|\Traversable|void Container Builder Config
         */
        function initServiceManager(Container $services)
        {
            return \Poirot\Config\load(__DIR__ . '/../config/cor-foundation.servicemanager');
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
            return \Poirot\Config\load(__DIR__ . '/../config/cor-foundation.services');
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
    }
}


namespace Module\Foundation
{

    use Module\Foundation\Actions\Helper\ViewAction;
    use Module\Foundation\Services\PathService\PathAction;

    /**
     * @method static ConfigAction config($confKey = null, $default = null)
     * @method static CycleAction  cycle($action = null, $steps = 1, $reset = true)
     * @method static PathAction   path($pathString = null, $variables = array())
     * @method static ViewAction   view($template = null, $variables = null)
     */
    class Actions extends \IOC
    { }
}

namespace Module\Foundation
{
    /**
     * @method static PathAction Path()
     */
    class Services extends \IOC
    { }
}
