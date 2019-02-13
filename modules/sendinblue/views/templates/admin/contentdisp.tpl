{*
* 2007-2018 PrestaShop
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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright  2007-2018 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{if $resp != 1}
<div class="module_error alert error">' . $this->l('You need to set up PrestaShop Newsletter module on your Prestashop back office before using it' mod='sendinblue'}</div>
{/if}
{if $chk_port_status === 0}
<div class="bootstrap"><div class="module_error alert alert-danger">{l s='Your server configuration does not allow to send emails. Please contact you system administrator to allow outgoing connections on port 587 for following IP ranges: 94.143.17.4/32, 94.143.17.6/32 and 185.107.232.0/24.' mod='sendinblue'}</div></div>
{/if}

<div class="header">
    <a href="#" class="logo"><img src="{$img_source|escape:'htmlall':'UTF-8'}sendinblue.png" width="180"></a>
    <h3>{l s='SendinBlue is the all-in-one app for your marketing and transactional emails' mod='sendinblue'}</h3>
    <div class="clear"></div>
</div>

<ul class="main-tabs">
	<li><a data-id="#about-sendinblue" id="about-sendinblue" href="javascript:void(0)" class="active">{l s='About SendinBlue' mod='sendinblue'}</a></li>
	<li><a data-id="#subscribe-manager" id="subscribe-manager" href="javascript:void(0)">{l s='Contacts Manager' mod='sendinblue'}</a></li>
    <li><a data-id="#code-tracking" id="code-tracking" href="javascript:void(0)">{l s='Track & Sync Orders' mod='sendinblue'}</a></li>
    <li><a data-id="#automation-tracking" id="automation-tracking" href="javascript:void(0)">{l s='Automation' mod='sendinblue'}</a></li>
    <li><a data-id="#transactional-email-sms-management" id="transactional-email-sms-management" href="javascript:void(0)">{l s='Transactional Email & Text Message Manager' mod='sendinblue'}</a></li>
    <li><a data-id="#contact_list" id="contact_list" href="javascript:void(0)">{l s='Contact List' mod='sendinblue'}</a></li>
</ul>
        <div class="clear"></div>
        <div class="main-tabs-content">
			<div  class="tab-pane active"  id="about-sendinblue">
				<div class="form-box">
				<h2 class="heading"><img src="{$img_source|escape:'htmlall':'UTF-8'}logo_sib.png" alt="" />SendinBlue</h2>
				<div class="form-box-content">
				<div class="contact-box">
				<h2 style="color:#268CCD;">{l s='Contact SendinBlue Team' mod='sendinblue'}</h2>
				<div style="clear: both;"></div>
				<p>{l s=' Contact us :' mod='sendinblue'}<br /><br />
				{l s='Email : ' mod='sendinblue'}<a href="mailto:contact@sendinblue.com" style="color:#268CCD;">contact@sendinblue.com</a></p>
				<p style="padding-top:20px;"><b>{l s='For further informations, please visit our website:' mod='sendinblue'}</b><br /><a href="https://www.sendinblue.com?utm_source=prestashop_plugin&utm_medium=plugin&utm_campaign=module_link" target="_blank"
				style="color:#268CCD;">https://www.sendinblue.com</a></p>
			</div>
			<p class="sub-heading">{l s='With SendinBlue, you can build and grow relationships with your contacts and customers. ' mod='sendinblue'}</p>
			<ul class="listt">
			<li>{l s=' Automatically sync your Prestashop opt-in contacts with your SendinBlue Account' mod='sendinblue'}</li>
			<li>{l s=' Easily create engaging, mobile-friendly emails' mod='sendinblue'}</li>
			<li>{l s=' Schedule email and text message campaigns' mod='sendinblue'}</li>
			<li>{l s=' Manage transactional emails with better deliverability, custom templates, and real-time analytics' mod='sendinblue'}</li>
			</ul>
			<p class="sub-heading">{l s='Why use SendinBlue ?' mod='sendinblue'}</p>
			<ul class="listt">
			<li>{l s=' Reach the inbox with optimized deliverability' mod='sendinblue'}</li>
			<li>{l s=' Unbeatable pricing - the best value in email marketing' mod='sendinblue'}</li>
			<li>{l s=' Friendly customer support by phone and email' mod='sendinblue'}</li>
			</ul><div style="clear:both;">&nbsp;</div>

