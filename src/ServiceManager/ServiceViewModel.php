<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\View\DecorateViewModelFeatures;
use Poirot\View\Interfaces\iViewModel;
use Poirot\View\ViewModel\Feature\iViewModelBindAware;
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

        $view     = new ViewModelTemplate;
        $view->setResolver( $services->get('viewModelResolver') );
        $view->setRenderer( function($template, $vars) use ($services) {
            $renderer = $services->fresh('viewModelRenderer');
            if (substr($template, -3) == 'php' && ! file_exists($template) )
                ## the two step is optional and can be avoided.
                return null;

            ## This result will merge on assert render result feature
            $result = $renderer->capture($template, $vars);
            return $result;
        } );


        // ..

        $bind = new DecorateViewModelFeatures(
            clone $view
            , $this->_funcDelegateRender()
            , $this->_funcAssertResult()
        );

        $view->bind($bind, 1000);
        return $view;
    }


    // ..

    protected function _funcDelegateRender()
    {
        /**
         * @param iViewModel          $parentView ViewModel Itself
         * @param iViewModelBindAware $self       ViewModel bind to root
         */
        return function($parentView, $self) {
            // Lookin for template_name.php beside base template
            // by renderManipulatedVars Renderer
            /** @var DecorateViewModelFeatures $self */
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
        return function($result, $parent, $self)
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
            call_user_func($result, $parent, $self);
        };
    }
}
