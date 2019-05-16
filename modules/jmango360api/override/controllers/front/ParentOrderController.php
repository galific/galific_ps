<?php
/**
 * Created by Son Pham.
 * User: JMango
 * Date: 8/31/17
 * Time: 10:13
 * Class ParentOrderControllerCoreOverride
 * @author Son Pham
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ParentOrderController extends ParentOrderControllerCore
{
    public function init()
    {
        // Always set PS_ORDER_PROCESS_TYPE = Onepage if request from Jmango360
        if (Tools::strlen(strstr($_SERVER['HTTP_USER_AGENT'], "JM360-Mobile")) > 0) {
            Configuration::set('PS_ORDER_PROCESS_TYPE', 1);
        }

        parent::init();
    }
}
