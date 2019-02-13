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

/**
 * Class AdminMailchimpProPromoRulesController
 *
 * @property Mailchimppro $module
 */
class AdminMailchimpProPromoRulesController extends \PrestaChamps\MailchimpPro\Controllers\BaseMCObjectController
{
    public $entityPlural   = 'promo_rules';
    public $entitySingular = 'promo_rule';

    protected function getListApiEndpointUrl()
    {
        return "/ecommerce/stores/{$this->context->shop->id}/promo-rules";
    }

    protected function getSingleApiEndpointUrl($entityId)
    {
        return "/ecommerce/stores/{$this->context->shop->id}/promo-rules/{$entityId}/promo-codes";
    }
}
