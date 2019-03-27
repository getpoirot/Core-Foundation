<?php
namespace Module\Foundation
{
    use Module\Foundation\Actions\Helper\ConfigAction;
    use Module\Foundation\Actions\Helper\CycleAction;
    use Module\Foundation\Actions\Helper\ViewAction;
    use Module\Foundation\Services\PathService\PathAction;


    /**
     * @method static ConfigAction config($key = null, $_ = null)
     * @method static CycleAction  cycle($action = null, $steps = 1, $reset = true)
     * @method static PathAction   path($pathString = null, $variables = array())
     * @method static ViewAction   view($template = null, $variables = null)
     */
    class Actions extends \IOC
    { }
}
