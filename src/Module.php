<?php
namespace Module\Foundation
{
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Ioc\Container;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
    use Poirot\Std\Interfaces\Struct\iDataEntity;

    use Module\Foundation\Actions\BuildContainerActionOfFoundationModule;
    use Module\Foundation\Actions\Helper\ConfigAction;
    use Module\Foundation\Actions\Helper\CycleAction;
    use Module\Foundation\Actions\Helper\ViewService;
    use Module\Foundation\ServiceManager\ServiceViewModel;
    use Module\Foundation\Services\PathService;


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
     *
     *
     * - Configuration:
     *   module is configurable by override "cor-foundation"
     *
     */
    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleInitServices
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleNestActions
    {
        /**
         * @inheritdoc
         */
        function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
        {
            /** @var LoaderAutoloadNamespace $nameSpaceLoader */
            $nameSpaceLoader = $baseAutoloader->loader(LoaderAutoloadNamespace::class);
            $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);
        }

        /**
         * @inheritdoc
         */
        function initServiceManager(Container $services)
        {
            $cnf = include __DIR__ . '/../config/cor-foundation.servicemanager.conf.php';
            return $cnf;
        }

        /**
         * @inheritdoc
         */
        function getServices(Container $moduleContainer = null)
        {
            $cnf = include __DIR__ . '/../config/cor-foundation.services.conf.php';
            return $cnf;
        }

        /**
         * @inheritdoc
         */
        function initConfig(iDataEntity $config)
        {
            return \Poirot\Config\load(__DIR__ . '/../config/cor-foundation');
        }

        /**
         * @inheritdoc
         */
        function getActions()
        {
            return new BuildContainerActionOfFoundationModule;
        }
    }
}
