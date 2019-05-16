<?php
/**
 * Created by PhpStorm.
 * User: bangle
 * Date: 17/06/2018
 * Time: 22:11
 * @author : bangle
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmLink extends LinkCore
{
    public function getModuleLink($module, $controller = 'default', array $params = array(), $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);
        return $url.'?fc=module&module='.$module.'&controller='.$controller;
    }
}
