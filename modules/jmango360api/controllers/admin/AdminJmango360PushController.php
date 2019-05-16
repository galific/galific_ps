<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @version  Release: $Revision: 13573 $
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once 'JapiOrchardConnecter.php';
require_once 'JapiOrchardConnecterLogger.php';

class AdminJmango360PushController extends ModuleAdminController
{

    private $baseUrl;
    private $ticket;
    private $appkey;
    private $store_id;
    private $cookie;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();

        $this->cookie = new Cookie('japi_auth');

        $cookieChanged = false;

        if (Tools::getIsset('base_url')) {
            $this->cookie->__set('japi_base_url', Tools::getValue('base_url'));
            $cookieChanged = true;
        }
        if (Tools::getIsset('ticket')) {
            $this->cookie->__set('japi_ticket', Tools::getValue('ticket'));
            $cookieChanged = true;
        }
        if (Tools::getIsset('appKey')) {
            $this->cookie->__set('japi_appKey', Tools::getValue('appKey'));
            $cookieChanged = true;
        }
        if (Tools::getIsset('store_id')) {
            $this->cookie->__set('japi_store_id', Tools::getValue('store_id'));
            $cookieChanged = true;
        }
        if ($cookieChanged) {
            $this->cookie->write();
        }
    }

    /**
     * Get connection object with Orchard
     *
     * @return JapiOrchardConnecter
     * @throws
     */
    protected function _getOrchardConnector()
    {
        $this->baseUrl = $this->cookie->__isset('japi_base_url') ? $this->cookie->__get('japi_base_url'): null;
        $this->ticket = $this->cookie->__isset('japi_ticket') ? $this->cookie->__get('japi_ticket'): null;
        $this->appKey = $this->cookie->__isset('japi_appKey') ? $this->cookie->__get('japi_appKey'): null;
        $this->store_id = $this->cookie->__isset('japi_store_id') ? $this->cookie->__get('japi_store_id'): null;

        $connector = new JapiOrchardConnecter($this->baseUrl, $this->ticket, $this->appKey);
        $connector->setLogger(new JapiOrchardConnecterLogger());
        return $connector;
    }

    public function postProcess()
    {
        if (!Tools::getIsset('type')) {
            return parent::postProcess();
        }
        try {
            $result = '';
            $target = Tools::getValue('type');

            if ($target === 'category') {
                $result = $this->loadCategory();
            } elseif ($target === 'deeplink') {
                $result = $this->loadDeepLink();
            } elseif ($target === 'user') {
                $id = Tools::getValue('id');

                if ($id === 'USERS') {
                    $result = $this->loadUser();
                } elseif ($id === 'PRESTASHOP_USER_GROUP') {
                    $result = $this->loadUserGroup();
                } elseif ($id === 'DEVICE_GROUP') {
                    $result = $this->loadDeviceGroup();
                }
            } elseif ($target === 'product') {
                $result = $this->loadProduct();
            } elseif ($target === 'module') {
                $result = $this->loadModule();
            } elseif ($target === 'send') {
                $result = $this->sendPushMessage();
            }

            header('Content-Type: application/json');
            die(json_encode($result));
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    public function loadCategory()
    {
        try {
            $connecter = $this->_getOrchardConnector();
            $result = $connecter->getCategoryTreeAndModules();
            $output = empty($result['categories']) ? array() : $result['categories'];
        } catch (Exception $e) {
            $output = array();
        }
        return $output;
    }

    public function loadDeepLink()
    {
    }

    public function loadProduct()
    {
        $limit = Tools::getIsset('limit') ? Tools::getValue('limit') : 10;
        $page = Tools::getIsset('page') ? Tools::getValue('page') : 1;
        $query = Tools::getIsset('query') ? Tools::getValue('query') : '';
        $id_lang = Context::getContext()->language->id;
        $results = Search::find($id_lang, $query, $page, $limit, 'position', 'desc', true);
        $items = array();
        $item = array();
        foreach ($results as $result) {
            $product = new Product($result['id_product']);
            $item['id'] = $result['id_product'];
            $item['name'] = $result['pname'];
            $item['url'] =  Context::getContext()->link->getProductLink($product);
            $items[] = $item;
        }

        $data = array(
            "items" => is_null($items) ? array() : $items,
            "type" => "product",
            "icon" => null,
            "limit" => $limit,
            "page" => $page,
            "query" => $query,
            "total" => is_null($items) ? 0 : count($items)
        );

        return $data;
    }

    public function loadUser()
    {
        $limit = Tools::getIsset('limit') ? (int)Tools::getValue('limit') : 10;
        $page = Tools::getIsset('page') ? (int)Tools::getValue('page') : 1;
        $query = Tools::getIsset('query') ? Tools::getValue('query') : '';

        $sql = sprintf('SELECT id_customer AS id, email FROM `' . _DB_PREFIX_. 'jmango360_user` LEFT JOIN `'. _DB_PREFIX_ .'customer` ON id_user = id_customer WHERE email LIKE \'%%%s%%\'', pSQL($query));
        $results = DB::getInstance()->executeS($sql);

        $only_active = true;
        $sql = 'SELECT `id_customer`, `email`
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
            ($only_active ? ' AND `active` = 1' : '').' 
                AND email LIKE \'%'. pSQL($query) .'%\'
				ORDER BY `id_customer` ASC
				LIMIT '.(int)(($page - 1) * $limit).','.(int)$limit;

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $sql = 'SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
            ($only_active ? ' AND `active` = 1' : '').' 
                AND email LIKE \'%'. pSQL($query) .'%\'';

        $countResult = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $options = array();
        foreach ($results as $result) {
            $options[] = array(
                'id' => 'u' . $result['id_customer'],
                'oid' => $result['id_customer'],
                'label' => $result['email']
            );
        }

        $data = array(
            "options" => is_null($options) ? array() : $options,
            "type" => "USERS",
            "icon" => "fas fa-user color-3793E6",
            "limit" => $limit,
            "page" => $page,
            "total" => is_null($countResult) ? 0 : $countResult
        );

        return $data;
    }

    public function loadUserGroup()
    {
        $limit = Tools::getIsset('limit') ? Tools::getValue('limit') : 10;
        $page = Tools::getIsset('page') ? Tools::getValue('page') : 1;

        $id_lang = Context::getContext()->language->id;
        $groups = Group::getGroups($id_lang);

        $options = array();
        $option = array();
        foreach ($groups as $group) {
            $g = new Group($group['id_group']);
            $option['id'] = 'g' . $group['id_group'];
            $option['oid'] = $group['id_group'];
            $option['label'] = $group['name'];
            $option['count'] = $g->getCustomers(true);
            $options[] = $option;
        }

        $data = array(
            "options" => is_null($options) ? array() : $options,
            "type" => "PRESTASHOP_USER_GROUP",
            "icon" => "fas fa-user color-3793E6",
            "limit" => $limit,
            "page" => $page,
            "total" => is_null($options) ? 0 : count($options)
        );

        return $data;
    }

    public function loadDeviceGroup()
    {
        $limit = Tools::getIsset('limit') ? Tools::getValue('limit') : 10;
        $page = Tools::getIsset('page') ? Tools::getValue('page') : 1;

        $options = array(
            array(
                'id' => 1,
                'label' => $this->__('All Devices')
            ),
            array(
                'id' => 2,
                'label' => $this->__('Android Devices')
            ),
            array(
                'id' => 3,
                'label' => $this->__('iOS Devices')
            )
        );
        $data = array(
            "options" => is_null($options) ? array() : $options,
            "type" => "USERS",
            "icon" => "fas fa-user color-3793E6",
            "limit" => $limit,
            "page" => $page,
            "total" => is_null($options) ? 0 : count($options)
        );
        return $data;
    }

    public function loadModule()
    {
        try {
            $output = array();
            $connector = $this->_getOrchardConnector();
            $result = $connector->getCategoryTreeAndModules();
            $output = empty($result['modules']) ? array() : $result['modules'];
        } catch (Exception $e) {
            $output['error'] = 1;
            $output['message'] = $e->getMessage();
        }
        return $output;
    }

    public function sendPushMessage()
    {
        $output = array();
        $data = array();

        $deepLink = Tools::getValue('deep_link');
        if ($deepLink == 1 || $deepLink == 2) {//category or module
            $data['moduleType'] = Tools::getValue('moduleType');
            $data['moduleName'] = Tools::getValue('moduleName');
            $data['moduleId'] = Tools::getValue('moduleId');
        } elseif ($deepLink == 3) {//product
            $data['productUrl'] = Tools::getValue('productUrl');
            $data['productId'] = Tools::getValue('productId');
        }

        $data['message'] = Tools::getValue('body', '');

        $users = array();
        $recipient = Tools::getValue('recipient');
        if (is_array($recipient)) {
            foreach ($recipient as $type => $values) {
                switch ($type) {
                    case 'PRESTASHOP_USER_GROUP':
                        $users = array_unique(array_merge($users, $this->getJapiCustomerFromGroup($values)));
                        break;
                    case 'USERS':
                        $users = array_unique($values);
                        break;
                }
            }
        }
        $data['users'] = array();
        foreach ($users as $user) {
            $data['users'][] = array('id' => $user);
        }

        try {
            $connector = $this->_getOrchardConnector();
            $output = $connector->pushMessage($data);
        } catch (Exception $e) {
            $output['error'] = 1;
            $output['message'] = $e->getMessage();
        }
        return $output;
    }

    public function getJapiCustomerFromGroup($values)
    {
        $sql = sprintf('SELECT id_customer AS id FROM `'. _DB_PREFIX_ .'customer_group` WHERE id_group IN (%s)', implode(',', array_map('intval', $values)));

        $results = DB::getInstance()->executeS($sql);

        $output = array();
        foreach ($results as $result) {
            $output[] = $result['id'];
        }

        return $output;
    }

    public function display()
    {
        $this->context->smarty->assign(array(
            'display_header' => $this->display_header,
            'display_header_javascript'=> $this->display_header_javascript,
            'display_footer' => $this->display_footer,
            'js_def' => Media::getJsDef(),
        ));

        // Use page title from meta_title if it has been set else from the breadcrumbs array
        if (!$this->meta_title) {
            $this->meta_title = $this->toolbar_title;
        }
        if (is_array($this->meta_title)) {
            $this->meta_title = strip_tags(implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->meta_title));
        }
        $this->context->smarty->assign('meta_title', $this->meta_title);

        $template_dirs = $this->context->smarty->getTemplateDir();

        // Check if header/footer have been overriden
        $dir = $this->context->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.trim($this->override_folder, '\\/').DIRECTORY_SEPARATOR;
        $module_list_dir = $this->context->smarty->getTemplateDir(0).'helpers'.DIRECTORY_SEPARATOR.'modules_list'.DIRECTORY_SEPARATOR;

        $header_tpl = file_exists($dir.'header.tpl') ? $dir.'header.tpl' : 'header.tpl';
        $page_header_toolbar = file_exists($dir.'page_header_toolbar.tpl') ? $dir.'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
        $footer_tpl = file_exists($dir.'footer.tpl') ? $dir.'footer.tpl' : 'footer.tpl';
        $modal_module_list = file_exists($module_list_dir.'modal.tpl') ? $module_list_dir.'modal.tpl' : 'modal.tpl';
        $tpl_action = $this->tpl_folder.$this->display.'.tpl';

        // Check if action template has been overriden
        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir.DIRECTORY_SEPARATOR.$tpl_action) && $this->display != 'view' && $this->display != 'options') {
                if (method_exists($this, $this->display.Tools::toCamelCase($this->className))) {
                    $this->{$this->display.Tools::toCamelCase($this->className)}();
                }
                $this->context->smarty->assign('content', $this->context->smarty->fetch($tpl_action));
                break;
            }
        }

        if (!$this->ajax) {
            $template = $this->createTemplate($this->template);
            $page = $template->fetch();
        } else {
            $page = $this->content;
        }

        if ($conf = Tools::getValue('conf')) {
            $this->context->smarty->assign('conf', $this->json ? Tools::jsonEncode($this->_conf[(int)$conf]) : $this->_conf[(int)$conf]);
        }

        foreach (array('errors', 'warnings', 'informations', 'confirmations') as $type) {
            if (!is_array($this->$type)) {
                $this->$type = (array)$this->$type;
            }
            $this->context->smarty->assign($type, $this->json ? Tools::jsonEncode(array_unique($this->$type)) : array_unique($this->$type));
        }

        if ($this->show_page_header_toolbar && !$this->lite_display) {
            $this->context->smarty->assign(
                array(
                    'page_header_toolbar' => $this->context->smarty->fetch($page_header_toolbar),
                    'modal_module_list' => $this->context->smarty->fetch($modal_module_list),
                )
            );
        }

        $this->context->smarty->assign(
            array(
                'page' =>  $this->json ? Tools::jsonEncode($page) : $page,
                'header' => '',
                'footer' => '',
            )
        );

        $this->smartyOutputContent($this->layout);
    }
    public function renderView()
    {
        $token = Tools::getAdminToken($this->controller_name.(int)$this->id.(int)$this->context->employee->id);

        $tpl = $this->context->smarty->createTemplate(dirname(__FILE__).'/../../views/templates/admin/pushmessage.tpl');
        $tpl->assign(array(
            'new_message_label'       => $this->__('New Message'),
            'deep_links'              => $this->getDeepLinkTo(),
            'tabs'                    => $this->getTabs(),
            'form_key'                => $this->getFormKey(),
            'token'                   => $token
        ));

        return $tpl->fetch();
    }

    public function __($msg)
    {
        return $msg;
    }

    public function getFormKey()
    {
        return 123;
    }

    public function getDeepLinkTo()
    {
        return array(
            '0' => $this->__('None'),
            '1' => $this->__('Category'),
            '2' => $this->__('Module'),
            '3' => $this->__('Product')
        );
    }

    public function getTabs()
    {
        return array(
            array(
                'id' => 'USERS',
                'label' => $this->__('Users')
            ),
            array(
                'id' => 'PRESTASHOP_USER_GROUP',
                'label' => $this->__('Prestashop Group')
            ),
//            array(
//                'id' => 'DEVICE_GROUP',
//                'label' => $this->__('Device Group')
//            )
        );
    }
}
