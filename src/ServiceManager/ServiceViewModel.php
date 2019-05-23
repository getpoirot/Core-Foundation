<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\View\DecorateViewModel;
use Poirot\View\Interfaces\iViewModel;
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
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        $services = $this->services();

        $view = new ViewModelTemplate;
        $view->setRenderer( $renderer = $services->fresh('viewModelRenderer') );
        $view->setResolver( $resolver = $services->get('viewModelResolver') );


        # PHP Step as first step of view Renderer:

        $bind = new (DecorateViewModel($view, $this->_funcDelegateRender(), $this->_funcAssertResult()))
            ->setRenderer(function($template, $vars) use ($view) {
                $renderer = $view->renderer();

                if (substr($template, -3) == 'php' && ! file_exists($template) )
                    ## the two step is optional and can be avoided.
                    return null;

                ## This result will merge on assert render result feature
                $result = $renderer->capture($template, $vars);
                return $result;
            });

        $view = new (DecorateViewModel($view))
            ->bind($bind, 1000); // render before all others children

        return $view;
    }


    // ..

    protected function _funcDelegateRender()
    {
        /**
         * @param iViewModel        $parentView ViewModel Itself
         * @param DecorateViewModel $self       ViewModel bind to root
         */
        return function($parentView, $_, $self) {
            // Lookin for template_name.php beside base template
            // by renderManipulatedVars Renderer
            /** @var DecorateViewModel $self */
            /** @var ViewModelTemplate $parentView */
            $template = $parentView->resolver()->resolve( $parentView->getTemplate() );
            if ( $template ) {
                // change template to .php extension
                $template = substr_replace($template , 'php', strrpos($template , '.') +1);
                $self->setTemplate($template);
            }

            $self->setVariables( $parentView->variables() );
        };
    }

    protected function _funcAssertResult()
    {
        return function($result, $parent, $root, $self)
        {
            if ($result === null)
                ## Nothing to do!!
                return;

            if (! is_callable($result) )
                throw new \Exception(sprintf(
                    'Result return from (%s) template as step layer is not valid callable; given (%s).'
                    , $self->getTemplate(), \Poirot\Std\flatten($result)
                ));

            /** @var ViewModelTemplate $parent */
            call_user_func($result, $parent, $root, $self);
        };
    }
}
