<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 */

require_once 'KbCore.php';

class KbmarketplaceOrderModuleFrontController extends KbmarketplaceCoreModuleFrontController
{

    public $controller_name = 'order';

    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function postProcess()
    {
        parent::postProcess();
                }

    public function initContent()
    {
            $this->renderList();

        parent::initContent();
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = $this->module->l('Orders', 'order');
            $page['meta']['title'] = $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }

    

    private function renderList()
    {
        $statuses = array();
        $tmp = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($tmp as $val) {
            $statuses[$val['id_order_state']] = array('value' => $val['id_order_state'], 'label' => $val['name']);
        }

        $this->table_id     = $this->filter_id;
        $this->table_header = array(
            array(
                'label' => $this->module->l('Reference', 'order'),
                'align' => 'right',
                'width' => '100'
            ),
            array(
                'label' => $this->module->l('Order Date', 'order'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Customer Name', 'order'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Customer Email', 'order'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Qty', 'order'),
                'align' => 'right',
                'width' => '50'
            ),
            array(
                'label' => $this->module->l('Status', 'order'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Order Total', 'order'),
                'align' => 'right',
                'width' => '100',
            )
        );

        $this->total_records = KbSellerEarning::getOrdersBySellerId(
            $this->seller_info['id_seller'],
            true
        );

        if ($this->total_records > 0) {
            $seller_orders = KbSellerEarning::getOrdersBySellerId(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );

            foreach ($seller_orders as $so) {
                $order = new Order($so['id_order']);
                $customer = $order->getCustomer();
                /*Start-MK made changes on 28-05-18 for GDPR changes*/
                $customer_email = $customer->email;
                
                /*End-MK made changes on 28-05-18 for GDPR changes*/
                $currency = new Currency($order->id_currency);
                $view_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'view', 'id_order' => $order->id),
                    (bool) Configuration::get('PS_SSL_ENABLED')
                );
                $this->table_content[] = array(
                    array(
                        'link' => array(
                            'href' => $view_link,
                            'function' => '',
                            'title' => $this->module->l('Click to view order detail', 'order'),
                            'target' => '_blank'
                        ),
                        'value' => Tools::strtoupper($order->getUniqReference()),
                    ),
                    array('value' => Tools::displayDate($order->date_add, null, false)),
                    array('value' => $customer->firstname . ' ' . $customer->lastname),
                    array('value' => $customer_email),
                    array('value' => $so['product_count'], 'align' => 'kb-tright'),
                    array('value' => $statuses[$order->current_state]['label']),
                    array('value' => Tools::displayPrice($so['total_earning'], $currency), 'align' => 'kb-tright')
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $total_revenue = Tools::displayPrice(
            KbSellerEarning::getTotalEarningInSellerOrders($this->seller_info['id_seller']),
            $this->seller_currency
        );
        $this->context->smarty->assign('total_revenue', $total_revenue);

        $total_sold_products = KbSellerEarning::getTotalSellerSoldProduct($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_sold_products', $total_sold_products);

        $total_pending_orders = KbSellerEarning::getSellerPendingOrders($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_pending_orders', $total_pending_orders);

        $this->setKbTemplate('order/list.tpl');
    }

        }
