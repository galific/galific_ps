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

class KbmarketplaceEarningModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'earning';

    const REPORT_FORMAT_DAILY = 1;
    const REPORT_FORMAT_WEEKLY = 2;
    const REPORT_FORMAT_MONTHLY = 3;
    const REPORT_FORMAT_YEARLY = 4;

    private $report_format_types = array('last_7_days', 'week', 'month', 'year');

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
        $total_revenue = Tools::displayPrice(
            KbSellerEarning::getTotalEarningInSellerOrders($this->seller_info['id_seller'])
        );
        $this->context->smarty->assign('total_revenue', $total_revenue);

        $total_earning = KbSellerEarning::getTotalSellerEarning($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_earning', $total_earning);

        $total_orders = KbSellerEarning::getSellerTotalOrders($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_orders', $total_orders);

        $this->renderEarningHistory();
        $this->renderOrderWiseEarning();
        $this->setKbTemplate('seller/earning.tpl');
        parent::initContent();
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Earnings';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    public function getAllReportFormat()
    {
        return array(
            self::REPORT_FORMAT_DAILY => $this->module->l('Daily', 'earning'),
            self::REPORT_FORMAT_WEEKLY => $this->module->l('Weekly', 'earning'),
            self::REPORT_FORMAT_MONTHLY => $this->module->l('Monthly', 'earning'),
            self::REPORT_FORMAT_YEARLY => $this->module->l('Yearly', 'earning')
        );
    }

    private function renderEarningHistory()
    {
        $formats = array();
        foreach ($this->getAllReportFormat() as $key => $val) {
            $formats[] = array('value' => $key, 'label' => $val);
        }



        $this->table_header = array(
            array('label' => $this->module->l('Interval', 'earning')),
            array('label' => $this->module->l('Orders', 'earning'), 'align' => 'kb-tright'),
            array('label' => $this->module->l('Product Sold', 'earning'), 'align' => 'kb-tright'),
            array('label' => $this->module->l('Order Total', 'earning'), 'align' => 'kb-tright'),
            array('label' => $this->module->l('Your Earning', 'earning'), 'align' => 'kb-tright')
        );


        $this->total_records = $this->getEarningHistoryList(self::REPORT_FORMAT_DAILY, '', '', true);
        if ($this->total_records > 0) {
            $histories = $this->getEarningHistoryList(
                self::REPORT_FORMAT_DAILY,
                '',
                '',
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );

            foreach ($histories as $so) {
                $this->table_content[] = array(
                    array('value' => $so['interval']),
                    array('value' => $so['total_orders'], 'align' => 'kb-tright'),
                    array('value' => $so['ordered_qty'], 'align' => 'kb-tright'),
                    array('value' => $so['total_revenue'], 'align' => 'kb-tright'),
                    array('value' => $so['seller_revenue'], 'align' => 'kb-tright')
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kb_earning_history_list', $this->renderKbList());
    }

    private function renderOrderWiseEarning()
    {
        $statuses = array();
        $tmp = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($tmp as $val) {
            $statuses[$val['id_order_state']] = array('value' => $val['id_order_state'], 'label' => $val['name']);
        }

        $this->table_header = array(
            array('label' => $this->module->l('Reference', 'earning')),
            array('label' => $this->module->l('Order Date', 'earning'), 'width' => '100'),
            array('label' => $this->module->l('Quantity', 'earning'), 'width' => '100'),
            array('label' => $this->module->l('Order Total', 'earning'), 'width' => '100'),
            array('label' => $this->module->l('Status', 'earning')),
            array('label' => $this->module->l('Your Earning', 'earning'), 'width' => '100')
        );

        $this->total_records = KbSellerEarning::getOrdersBySellerId($this->seller_obj->id, true);
        $this->table_content = array();
        if ($this->total_records > 0) {
            $seller_orders = KbSellerEarning::getOrdersBySellerId(
                $this->seller_obj->id,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );

            foreach ($seller_orders as $so) {
                $order = new Order($so['id_order']);
                $currency = new Currency($order->id_currency);
                $view_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    'order',
                    array('render_type' => 'view', 'id_order' => $order->id),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $this->table_content[] = array(
                    array(
                        'link' => array(
                            'href' => $view_link,
                            'function' => '',
                            'title' => $this->module->l('Click to view order detail', 'earning'),
                            'target' => '_blank'
                        ),
                        'value' => Tools::strtolower($order->getUniqReference()),
                    ),
                    array('value' => Tools::displayDate($order->date_add, null, false)),
                    array('value' => $so['product_count'], 'align' => 'kb-tright'),
                    array('value' => Tools::displayPrice($so['total_earning'], $currency), 'align' => 'kb-tright'),
                    array('value' => $statuses[$order->current_state]['label']),
                    array('value' => Tools::displayPrice($so['seller_earning'], $currency), 'align' => 'kb-tright'),
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kb_owise_earning_list', $this->renderKbList());
    }

    protected function getEarningHistoryList(
        $report_format,
        $from_date = '',
        $to_date = '',
        $only_count = false,
        $start = 0,
        $limit = 0
    ) {
        $columns = 'IF(SUM(total_earning) IS NULL,0,SUM(total_earning)) as total_revenue, 
				IF(SUM(seller_earning) IS NULL,0,SUM(seller_earning)) as seller_revenue, 
				IF(SUM(product_count) IS NULL,0,SUM(product_count)) as ordered_qty, 
				IF(COUNT(id_order) IS NULL,0,COUNT(id_order)) as total_order, id_order';

        $condition = ' Where id_seller = ' . (int)$this->seller_obj->id . ' AND is_canceled = "0"';
        $order_by = ' date_add DESC';
        $group_by = 'DATE(date_add)';

        switch ($report_format) {
            case self::REPORT_FORMAT_DAILY:
                $columns .= ', date_add as from_date';
                break;
            }

        
        $sql = 'Select {COLUMNS} from ' . _DB_PREFIX_ . 'kb_mp_seller_earning ' . $condition;

        
        if ($only_count) {
            $sql = str_replace('{COLUMNS}', 'IF(COUNT(*) IS NULL,0,COUNT(*)) as total', $sql);
            $sql .= ' GROUP BY ' . pSQL($group_by);

            $sql .= ' ORDER BY ' . pSQL($order_by);
            
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql = str_replace('{COLUMNS}', $columns, $sql);

            $sql .= ' GROUP BY ' . pSQL($group_by);

            $sql .= ' ORDER BY ' . pSQL($order_by);

            if ((int)$start >= 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $data = array();
            if (count($results) > 0) {
                foreach ($results as $row) {
                    $order = new Order($row['id_order']);
                    $currency = new Currency($order->id_currency);
                    $data[] = array(
                        'interval' => $this->intervalRenderer($row, $report_format),
                        'total_orders' => (int)$row['total_order'],
                        'ordered_qty' => (int)$row['ordered_qty'],
                        'total_revenue' => Tools::displayPrice($row['total_revenue'], $currency),
                        'seller_revenue' => Tools::displayPrice($row['seller_revenue'], $currency)
                    );
                }
            }

            return $data;
        }
    }

    private function intervalRenderer($data, $type)
    {
        $html = '';
        switch ($type) {
            case self::REPORT_FORMAT_DAILY:
                $html .= Tools::displayDate($data['from_date'], $this->seller_obj->id_default_lang);
                break;
            //Weekly report will give data from Sunday to Saturday
            case self::REPORT_FORMAT_WEEKLY:
                $html .= Tools::displayDate($data['from_date'], $this->seller_obj->id_default_lang)
                    . ' to ' . Tools::displayDate($data['to_date'], $this->seller_obj->id_default_lang);
                break;
            case self::REPORT_FORMAT_MONTHLY:
                $html .= $data['from_date'];
                break;
            case self::REPORT_FORMAT_YEARLY:
                $html .= $data['from_date'];
                break;
        }

        return $html;
    }

   
}
