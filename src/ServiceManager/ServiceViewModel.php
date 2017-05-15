<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\View\DecorateViewModelFeatures;
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
        $view->setRenderer( $renderer = $services->fresh('viewModelRenderer') );
        
        $bind = new DecorateViewModelFeatures(clone $view);
        $bind->setRenderer(function($template, $vars) use ($renderer) {
            if (!file_exists($template))
                ## the two step is optional and can be avoided.
                return null;

            ## This result will merge on assert render result feature
            $result = $renderer->capture($template, $vars);
            return $result;
        });
        
        $bind->delegateRenderBy = function($parentView, $self) {
            // Lookin for template_name.php beside base template
            // by renderManipulatedVars Renderer
            /** @var DecorateViewModelFeatures $self */
            /** @var ViewModelTemplate $parentView */
            $template = $parentView->resolver()->resolve($parentView->getTemplate());
            if ($template) {
                // change template to .php extension
                $template = substr_replace($template , 'php', strrpos($template , '.') +1);
                $self->setTemplate($template);
            }

            $self->setVariables($parentView->variables());
        };

        $bind->assertRenderResult = function($result, $parent, $self) {
            if ($result === null)
                ## Nothing to do!!
                return;

            if (is_array($result)) {
                /** @var ViewModelTemplate $parent */
                $parent->variables()->import($result);
            } else
                throw new \Exception(sprintf(
                    'Result return from (%s) template as step layer is not array; given (%s).'
                    , $self->getTemplate(), \Poirot\Std\flatten($result)
                ));
        };

        $view->bind($bind);
        return $view;
    }
}
