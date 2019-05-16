<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 2/28/18
 * Time: 11:41 AM
 * @author kien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

//require_once _PS_MODULE_DIR_ . '/jmango360api/classes/BaseService.php';
//require_once _PS_MODULE_DIR_ . '/jmango360api/classes/smartAppBanner/CustomRequest.php';

class CustomizeOrderByValuesService extends BaseService
{
    public $order_by_configuration;
    const JM_ORDER_BY_VALUES = 'JM_ORDER_BY_VALUES';

    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            ConfigurationCore::updateValue(JM_ORDER_BY_VALUES, $jsonRequestBody);
            $this->response = json_decode('
            {
               "success": {
                   "message": "OK"
               }
            }');
        } else {
            $order_by_configuration=ConfigurationCore::get(JM_ORDER_BY_VALUES);
            if (!empty($order_by_configuration)) {
                $this->response = json_decode(ConfigurationCore::get(JM_ORDER_BY_VALUES));
            } else {
                //If order by values are not customized, all sort options are enabled as default
                $this->response = json_decode('
                {
                  "orderBy": {
                    "name": "enable",
                    "price": "enable",
                    "date_add": "enable",
                    "date_upd": "enable",
                    "position": "enable",
                    "manufacturer_name": "enable",
                    "quantity": "enable",
                    "reference": "enable"
                  }
                }');
            }
        }
    }
}
