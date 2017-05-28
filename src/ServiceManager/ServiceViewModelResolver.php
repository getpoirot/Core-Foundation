<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Ioc\Container\Service\aServiceContainer;

use Poirot\Loader\LoaderAggregate;
use Poirot\Loader\LoaderMapResource;
use Poirot\Loader\LoaderNamespaceStack;


class ServiceViewModelResolver
    extends aServiceContainer
{
    /**
     * @var string Service Name
     */
    protected $name = 'ViewModelResolver';
    protected $_layout_extension = 'phtml';


    /**
     * Create Service
     *
     * @return LoaderAggregate
     */
    function newService()
    {
        $loader = new LoaderAggregate;
        $loader->attach(new LoaderMapResource, 500);
        $loader->attach(new LoaderNamespaceStack(null, function($name, $resource, $match) {
                return \Poirot\Loader\funcWatchFileExists($name, $resource, $match, '.'.$this->_layout_extension);
            }
        ));


        return $loader;
    }

    /**
     * Set Layout File Extension
     *
     * @param string $extension
     *
     * @return $this
     */
    function setLayoutFileExt($extension)
    {
        $this->_layout_extension = ltrim('.', (string) $extension);
        return $this;
    }
}
