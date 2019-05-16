<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableInterface;

interface JmCheckoutStepInterface extends RenderableInterface
{
    public function handleRequest(array $requestParameters = array());

    public function hasErrors();

    public function getErrors();
}
