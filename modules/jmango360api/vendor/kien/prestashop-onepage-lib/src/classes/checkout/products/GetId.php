<?php
/**
 * Created by PhpStorm.
 * User: bangle
 * Date: 28/05/2018
 * Time: 17:28
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

//require_once _PS_MODULE_DIR_ . '/jmango360_api/classes/products/Check.php';

class GetId extends BaseService
{
    public function doExecute()
    {
        $link = Tools::getValue('link');

        if (!$this->isValidUrl($link)) {
            $error = new JmError(403, "Product URL is not valid");
            $this->response->errors = array($error);
            return;
        }

        $f = Tools::file_get_contents($link);

//        $tem = Check::getInstance();
        $dom = new DOMDocument();
            @$dom->loadHTML($f);

        $data = $dom->getElementById('product_page_product_id');
        $attrs = array();
        for ($i = 0; $i < $data->attributes->length; ++$i) {
            $node = $data->attributes->item($i);
            $attrs[$node->nodeName] = $node->nodeValue;
        }

//        var_dump($attrs);
//        $html = $dom->saveHTML($data);
//        die(json_encode($data));
//        $tem->request_uri = $link;

        $this->response=(int)$attrs['value'];
    }

    public function isValidUrl($url)
    {
        // Only accept http and https
        if ((strpos($url, 'http')) === false && (strpos($url, 'https')) === false) {
            return false;
        }

        // Only accept shop domain
        if ((strpos($url, Tools::getHttpHost(false))) === false) {
            return false;
        }

        // Not contain root directory
        if ((strpos($url, _PS_ROOT_DIR_)) > 0) {
            return false;
        }

        return true;
    }
}
