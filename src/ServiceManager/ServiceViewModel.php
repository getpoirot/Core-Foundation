<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\View\DecorateViewModel;
use Poirot\View\ViewFactory;
use Poirot\View\ViewModelTemplate;


/**
 * Two Step View Model
 *
 */
class ServiceViewModel
    extends aServiceContainer
{
    /** @var string Service Name */
    protected $name = 'ViewModel';


    /**
     * @inheritdoc
     *
     * @return DecorateViewModel|ViewModelTemplate
     * @throws \Exception
     */
    function newService()
    {
        $services = $this->services();

        $view = new ViewModelTemplate;
        $view->setRenderer( $renderer = $services->fresh('viewModelRenderer') );
        $view->setResolver( $resolver = $services->get('viewModelResolver') );


        return ViewFactory::makeTwoStepView($view);
    }
}
