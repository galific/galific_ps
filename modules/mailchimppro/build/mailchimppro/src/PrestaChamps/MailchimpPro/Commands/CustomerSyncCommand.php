<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    Mailchimp
 * @copyright Mailchimp
 * @license   commercial
 */

namespace PrestaChamps\MailchimpPro\Commands;

use DrewM\MailChimp\MailChimp;
use PrestaChamps\MailchimpPro\Formatters\CustomerFormatter;
use PrestaChamps\MailchimpPro\Formatters\ListMemberFormatter;

/**
 * Class CustomerSyncCommand
 *
 * @package PrestaChamps\MailchimpPro\Commands
 */
class CustomerSyncCommand extends BaseApiCommand
{
    protected $context;
    protected $customerIds;
    protected $mailchimp;
    protected $batch;
    protected $batchPrefix        = '';
    protected $triggerDoubleOptIn = false;

    /**
     * ProductSyncService constructor.
     *
     * @param \Context  $context
     * @param MailChimp $mailchimp
     * @param array     $customerIds
     */
    public function __construct(\Context $context, MailChimp $mailchimp, $customerIds = array())
    {
        $this->context = $context;
        $this->mailchimp = $mailchimp;
        $this->batchPrefix = uniqid('CUSTOMER_SYNC', true);
        $this->batch = $this->mailchimp->new_batch($this->batchPrefix);
        $this->customerIds = $customerIds;
    }

    /**
     * Trigger DoubleOptIn feature
     *
     * @param bool $trigger
     */
    public function triggerDoubleOptIn($trigger = true)
    {
        $this->triggerDoubleOptIn = (bool)$trigger;
    }

    /**
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public function execute()
    {
        $this->responses = array();
        if ((int)$this->syncMode === self::SYNC_MODE_REGULAR) {
            $listId = $this->getListIdFromStore();
            $listRequiresDoi = $this->getListRequiresDOI($listId);
            foreach ($this->customerIds as $customerId) {
                $customer = new \Customer($customerId);
                $formatted = new CustomerFormatter($customer, $this->context);
                if ($this->method === self::SYNC_METHOD_POST) {
                    $data = $formatted->format();
                    if ($this->triggerDoubleOptIn) {
                        $listMemberFormatter = new ListMemberFormatter(
                            $customer,
                            $this->context,
                            $listRequiresDoi
                                ?
                                ListMemberFormatter::STATUS_PENDING
                                :
                                ListMemberFormatter::STATUS_SUBSCRIBED,
                            ListMemberFormatter::EMAIL_TYPE_HTML
                        );
                        $this->mailchimp->post("/lists/{$listId}/members/", $listMemberFormatter->format());
                        $data['opt_in_status'] = false;
                    }
                    $this->mailchimp->post(
                        "/ecommerce/stores/{$this->context->shop->id}/customers",
                        $data
                    );
                }
                if ($this->method === self::SYNC_METHOD_PATCH) {
                    $data = $formatted->format();
                    $this->mailchimp->patch(
                        "/ecommerce/stores/{$this->context->shop->id}/customers/{$customerId}",
                        $data
                    );
                }
                if ($this->method === self::SYNC_METHOD_DELETE) {
                    $this->mailchimp->delete(
                        "/ecommerce/stores/{$this->context->shop->id}/customers/{$customerId}"
                    );
                }
                $this->responses[] = $this->mailchimp->getLastResponse();
            }
        }

        if ((int)$this->syncMode === self::SYNC_MODE_BATCH) {
            $batch = $this->mailchimp->new_batch();
            foreach ($this->customerIds as $customerId) {
                $formatted = new CustomerFormatter(new \Customer($customerId), $this->context);
                if ($this->method === 'POST') {
                    $batch->post(
                        "{$this->batchPrefix}_{$customerId}",
                        "/ecommerce/stores/{$this->context->shop->id}/customers",
                        $formatted->format()
                    );
                }
                if ($this->method === 'PATCH') {
                    $data = $formatted->format();
                    $batch->patch(
                        "{$this->batchPrefix}_{$customerId}",
                        "/ecommerce/stores/{$this->context->shop->id}/customers/{$customerId}",
                        $data
                    );
                }
                if ($this->method === 'DELETE') {
                    $batch->delete(
                        "{$this->batchPrefix}_{$customerId}",
                        "/ecommerce/stores/{$this->context->shop->id}/customers/{$customerId}"
                    );
                }
                $this->responses[] = $this->mailchimp->getLastResponse();
            }
            $this->responses[] = $batch->execute();
        }

        return $this->responses;
    }
}
