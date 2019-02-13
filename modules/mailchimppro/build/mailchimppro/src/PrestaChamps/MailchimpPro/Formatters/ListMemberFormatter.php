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
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

namespace PrestaChamps\MailchimpPro\Formatters;

/**
 * Class ListMemberFormatter
 *
 * @package PrestaChamps\MailchimpPro\Formatters
 */
class ListMemberFormatter
{
    const EMAIL_TYPE_HTML = 'html';
    const EMAIL_TYPE_TEXT = 'text';

    const STATUS_SUBSCRIBED   = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_CLEANED      = 'cleaned';
    const STATUS_PENDING      = 'pending';

    public $customer;
    public $address;
    public $context;
    public $status;
    public $emailType;

    /**
     * ListMemberFormatter constructor.
     *
     * @param \Customer $customer
     * @param \Context  $context
     * @param           $status
     * @param           $emailType
     * @param \Address  $address
     */
    public function __construct(\Customer $customer, \Context $context, $status, $emailType, \Address $address = null)
    {
        $this->customer = $customer;
        $this->address = $address;
        $this->context = $context;
        $this->status = $status;
        $this->emailType = $emailType;
    }

    /**
     * @return array
     */
    public function format()
    {
        $data = array(
            'email_address' => $this->customer->email,
            'status' => $this->status,
            'language' => $this->context->language->iso_code,
            'timestamp_signup' => gmdate('Y-m-d H:i:s', time()),
        );

        return $data;
    }
}
