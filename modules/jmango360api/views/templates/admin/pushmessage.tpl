{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        body {
            color: #727274;
        }
        form .form-control {
            color: #727274;
        }
        form .form-title {
            background: #3793e6;
            color: #fff;
        }
        form span.required {
            color: #dc3545;
        }
        form .form-control-plaintext:focus,
        form .form-control-plaintext:active {
            outline-style: none;
        }
        form.was-validated .form-control:invalid + .select2 .select2-selection,
        form.was-validated .form-control:invalid + .emojionearea.form-control {
            border-color: #dc3545;
        }
        form .alert {
            border-radius: 0;
        }
        .modal .modal-content {
            background-color: transparent;
        }
        .modal .modal-header {
            background-color: #3793e6;
            color: #fff;
        }
        .modal .modal-header .close {
            color: #fff;
            opacity: 1;
        }
        .modal.modal-confirm .modal-header {
            background-color: #434343;
        }
        .modal .modal-full {
            margin: 1% 2% 0 2%;
            max-width: 96%;
        }
        .modal .modal-body {
            background-color: #fff;
            font-size: .875rem;
            line-height: 1.5;
        }
        .modal .modal-body #groupTabs {
            margin-bottom: -1px;
            z-index: 1;
        }
        .modal .modal-body .btn-group-tab {
            white-space: normal;
            border-radius: 0;
        }
        .modal .modal-body .list-group-wrapper {
            height: 100%;
            margin-top: -31px;
            padding-top: 31px;
        }
        .modal .modal-body .list-group {
            border-radius: 0;
            border: 1px solid #6c757d;
        }
        .modal .modal-body .list-group-wrapper > ul.list-group{
            height:100%
        }
        .modal .modal-body .list-group .list-group {
            border: 0;
        }
        .modal .modal-body .list-group .list-group-item {
            border-radius: 0;
            border-left: 0;
            border-right: 0;
            border-color: #6c757d;
        }
        .modal .modal-body .list-group .list-group-item:first-child {
            border-top: 0;
        }
        .modal .modal-body .list-group .list-group-item:last-child {
            border-bottom: 0;
        }
        .modal .modal-body .list-group .list-group-item .custom-control {
            line-height: 1.5rem;
        }
        .modal .modal-footer {
            background-color: #fff;
        }
        .color-3793E6 {
            color: #3793E6;
        }
        .color-FF4800 {
            color: #FF4800;
        }
        .col-btns {
            margin: auto 0 !important;
        }
        .btn.disabled,
        .btn:disabled {
            opacity: 0.3;
        }
        .input-group .select2-container--default {
            flex: 1 1 auto;
            width: 1% !important;
            min-width: auto;
        }
        .input-group .select2-container--default + .input-group-append {
            padding-left: 0.2rem;
        }
        .select2-container--default {
            min-width: 100%;
        }
        .select2-container--default .select2-selection--single {
            padding: .375rem .75rem;
            height: 100%;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            float: right;
            padding: 0 0 0 0.5rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding: 0 1rem;
        }
        .select2-container--default .select2-selection {
            border-color: transparent;
        }
        .select2-container--default.select2-container--open .select2-selection {
            border: 1px solid #aaa;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: 1px solid #aaa;
        }
        .ladda-button {
            font-size: 1rem;
            line-height: 1.5;
        }
        .ladda-button[data-size=s] {
            padding: .375rem .75rem;
        }
        .ladda-button[data-color=orchard] {
            background-color: #34495e;
        }
        .ladda-button[data-color=orchard]:hover {
            background-color: #34495e;
        }
        .list-group-item-search .input-group-text {
            background: transparent;
        }
        .list-group-item-search .form-control {
            border-left: 0;
            border-right: 0;
        }
        .list-group-item-search .btn-clear {
            cursor: pointer;
        }
        .emojionearea.form-control {
            border: 1px solid #ddd;
            box-shadow: none;
        }
        .emojionearea .emojionearea-editor {
            font-size: 1rem;
        }
        .emojionearea .emojionearea-editor:empty:before {
            color: #727274;
        }
        .container-fluid .form-group {
            border: 1px solid #e4e5e7;
            margin: 0 0 0.5rem 0;
            border-radius: 3px;
            padding: 0.2rem 0.1rem 0.2rem 0.1rem;
        }
        .container-fluid .form-group select,
        .container-fluid .form-group input {
            border: 0;
        }
        .container-fluid .form-group textarea {
            min-height: 8em;
        }
        .container-fluid .form-group .input-group button {
            border-radius: 3px;
            border-color: #e4e5e7;
        }
        .container-fluid .form-group .input-group .input-group-append button {
            border-radius: 3px;
        }
        .container-fluid .form-group > div[class^="col-"] {
            padding-right: 0;
        }
        .container-fluid .form-group.form-message {
            border: 0;
            padding: 0;
        }
        #sendTo {
            visibility: hidden;
        }
        .select2-container--default .select2-selection--single{
            line-height:28px;
            height:30px;
            padding: 0;
        }
        .push-message-form .form-group label{
            line-height: 30px;
            padding: 0 15px;
        }
        span.select2-container--default .select2-selection--multiple{
            border: none;
        }
        span.select2-container--default.select2-container--open .select2-selection--multiple{
            border: 1px solid #aaa;
        }
        .list-group-item-checkall .custom-checkbox{
            margin: 0 5px 0 23px;
            padding: 0;
        }
        form .alert {
            border-radius: 0;
            display: none;
        }

        #ajax_confirmation{
            display: none;
        }

        span.select2-container--default .select2-selection--single {
            border: none;
        }
        .list-group.list-group-content{
            overflow: scroll;
        }
        ul#selectedRecipients{
            overflow-y: auto;
        }
        .deep-link-target .select2-container {
            max-width: 100%;
        }
        /* PS-637 : UI not nice in case select category to send push message that has long name */
        .jstree-node {
            overflow: hidden;
        }

    </style>

    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.min.css">
    <script defer src="//use.fontawesome.com/releases/v5.0.12/js/all.js"
            integrity="sha384-Voup2lBiiyZYkRto2XWqbzxHXwzcm4A5RfdfG6466bu5LqjwwrjXCMBQBLMWh7qR"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/ladda.min.css" media="all" />
    {*<link rel="stylesheet" type="text/css" href="https://s3-ap-southeast-1.amazonaws.com/bucket-pxson/pushmessage.css" media="all" />*}
</head>
<body>
<form class="push-message-form" method="post">
    <div class="alert alert-success alert-dismissible mb-0" role="alert" style="display: none">
        <p class="mb-0"></p>
        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="alert alert-danger alert-dismissible mb-0" role="alert" style="display: none">
        <p class="mb-0"></p>
        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <input name="form_key" type="hidden" value="{$form_key|escape:'htmlall':'UTF-8'}"/>
    <!--<h3 class="p-3 form-title">{l s='New Message' mod='jmango360api'}</h3>-->
    <div class="container-fluid pt-3">
        <div class="form-group row">
            <label for="sendTo" class="col-sm-2 col-form-label">To<span class="required">*</span></label>
            <div class="col-sm-10 input-group">
                <select multiple="multiple" id="sendTo" class="form-control" size="1" required
                        data-placeholder="{l s='Please specify recipient' mod='jmango360api'}"></select>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="addUserBtn"
                            title="{l s='Add recipient' mod='jmango360api'}">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    {l s='Please specify the recipient' mod='jmango360api'}
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="deepLinkTo" class="col-sm-2 col-form-label">Deeplink to</label>
            <div class="col-sm-10">
                <select id="deepLinkTo" class="form-control" name="deep_link">
                    {foreach $deep_links as $key=>$value}
                        <option value="{$key|escape:'htmlall':'UTF-8'}">{$value|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group row deep-link-target" style="display: none" id="field1">
            <label for="dl-category" class="col-sm-2 col-form-label">{l s='Category' mod='jmango360api'}<span class="required">*</span></label>
            <div class="col-sm-10">
                <div class="input-group">
                    <select id="dl-category" class="form-control" name="" data-type="category"
                            data-placeholder="{l s='Select a catalog' mod='jmango360api'}" required
                            data-ajax="false" disabled="disabled"></select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="browseCategoryBtn"
                                title="{l s='Select a catalog' mod='jmango360api'}">
                            <i class="fas fa-folder"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        {l s='Please select a catalog' mod='jmango360api'}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row deep-link-target" style="display: none" id="field3">
            <label for="dl-product" class="col-sm-2 col-form-label">{l s='Product' mod='jmango360api'}<span class="required">*</span></label>
            <div class="col-sm-10">
                <select id="dl-product" class="form-control" name="" data-type="product" required
                        data-placeholder="{l s='Select a product' mod='jmango360api'}"
                        disabled="disabled"></select>
                <div class="invalid-feedback">
                    {l s='Please specify a product url' mod='jmango360api'}
                </div>
            </div>
        </div>
        <div class="form-group row deep-link-target" style="display: none" id="field2">
            <label for="dl-module" class="col-sm-2 col-form-label">{l s='Module' mod='jmango360api'}<span class="required">*</span></label>
            <div class="col-sm-10">
                <select id="dl-module" class="form-control" name="" data-type="module" required
                        data-placeholder="{l s='Select a module' mod='jmango360api'}"
                        disabled="disabled">
                    <option value="">{l s='Please select a module' mod='jmango360api'}</option>
                </select>
                <div class="invalid-feedback">
                    {l s='Please select a module' mod='jmango360api'}
                </div>
            </div>
        </div>
        <div class="form-group form-message">
            <textarea class="form-control" id="detailMessage" rows="3" name="body" maxlength="110" required
                      placeholder="{l s='Enter your message here' mod='jmango360api'}"></textarea>
            <div class="invalid-feedback">
                {l s='Message content can not be empty' mod='jmango360api'}
            </div>
        </div>
        <div class="text-right">
            <div class="float-left">
                <label id="detailMessageCount" class=""></label>
            </div>
            <button type="button" class="ladda-button" id="sendBtn"
                    data-style="slide-up"
                    data-color="orchard"
                    data-size="s">
                <span class="ladda-label">
                    <i class="fas fa-paper-plane"></i> {l s='Send' mod='jmango360api'}
                </span>
            </button>
        </div>
    </div>
</form>
<div class="modal" tabindex="-1" role="dialog" id="addRecipientModal">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{l s='Add Recipient' mod='jmango360api'}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6" id="listRecipients">
                        <div class="btn-group" role="group" id="groupTabs">
                            {foreach $tabs as $index=>$tab}
                                <button type="button"
                                        class="btn-sm btn-group-tab btn btn-outline-secondary {if $index == 0}active{/if}"
                                        data-id="{$tab['id']|escape:'htmlall':'UTF-8'}">
                                    {$tab['label']|escape:'htmlall':'UTF-8'}
                                </button>
                            {/foreach}
                        </div>
                        <div class="list-group-wrapper">
                            {foreach $tabs as $index=>$tab}
                                <ul class="list-group list-group-content" id="groupList{$tab['id']|escape:'htmlall':'UTF-8'}"
                                    style="{if $index > 0}'display: none'{/if}"
                                    data-type="{$tab['id']|escape:'htmlall':'UTF-8'}"
                                    data-loaded="{if isset($tab['options'])}1{else}0{/if}">
                                <li class="list-group-item list-group-item-checkall">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input check-all"
                                           id="checkAll{$tab['id']|escape:'htmlall':'UTF-8'}"
                                           data-id="{$tab['id']|escape:'htmlall':'UTF-8'}"
                                           data-check="all">
                                    <label class="custom-control-label"
                                           for="checkAll{$tab['id']|escape:'htmlall':'UTF-8'}">{l s='Select All' mod='jmango360api'}</label>
                                </div>
                                </li>
                                <li class="list-group-item list-group-item-search" style="display: none">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" class="form-control form-search" placeholder="Search"
                                               aria-label="Search">
                                        <div class="input-group-append">
                                                <span class="input-group-text btn-clear">
                                                    <i class="fas fa-times"></i>
                                                </span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-loading" style="display: none">
                                    <i class="fas fa-spinner fa-spin"></i> {l s='Loading...' mod='jmango360api'}
                                </li>
                                <li class="list-group-item p-0">
                                    <ul class="list-group"></ul>
                                </li>
                                </ul>
                            {/foreach}
                        </div>
                    </div>
                    <!--<div class="col-2 col-btns">
                        <button class="btn btn-sm btn-primary btn-block" disabled>
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-block" disabled>
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                    </div>-->
                    <div class="col-6">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary btn-group-tab active">
                                {l s='Selected recipients' mod='jmango360api'}
                            </button>
                        </div>
                        <div class="list-group-wrapper">
                            <ul class="list-group" id="selectedRecipients"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    {l s='Cancel' mod='jmango360api'}
                </button>
                <button type="button" class="btn btn-primary" id="selectBtn">
                    <i class="fas fa-user-plus"></i> {l s='Add' mod='jmango360api'}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="addCategoryModal">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{l s='Select Category' mod='jmango360api'}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="">
                    <div class="category-tree" id="category-tree"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    {l s='Cancel' mod='jmango360api'}
                </button>
                <button type="button" class="btn btn-primary" id="addCategoryBtn">
                    <i class="fas fa-folder"></i> {l s='Add' mod='jmango360api'}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-confirm" tabindex="-1" role="dialog" id="confirmationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"><p></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-cancel" data-dismiss="modal">
                    {l s='No' mod='jmango360api'}
                </button>
                <button type="button" class="btn btn-primary btn-ok">
                    <i class="fas fa-check-circle"></i> {l s='Yes' mod='jmango360api'}
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function () {
        window.addEventListener('load', function () {
            jQuery('form').pushMessageForm({
                recipientDataUrl: window.location.href + '&token={$token|escape:'htmlall':'UTF-8'}&type=user',
                sendUrl: window.location.href + '&token={$token|escape:'htmlall':'UTF-8'}&type=send',
                deepLinkDataUrl: window.location.href + '&token={$token|escape:'htmlall':'UTF-8'}&type=product',
                categoryDataUrl: window.location.href + '&token={$token|escape:'htmlall':'UTF-8'}&type=category',
                moduleDataUrl: window.location.href + '&token={$token|escape:'htmlall':'UTF-8'}&type=module',
                bodyCountTpl: '{literal}{{count}}{/literal} characters left',
                sendConfirmTitle: 'SEND PUSH MESSAGE CONFIRMATION',
                sendConfimBody: 'Push messages may be delayed by up to 2 hours due to a queuing system with Apple and Google.'
            });
        });
    })();

</script>
<script src="//code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"
        integrity="sha384-uQwKPrmNkEOvI7rrNdCSs6oS1F3GvnZkmPtkntOSIiPQN4CCbFSxv+Bj6qe0mWDb"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"
        integrity="sha384-3xJpTAehKTp8sw2H5VBvFFFeUdcxlxQYYyML7JGy/N8CdC+Ij8AALUibuWZRjiJq"
        crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/emojione@3.1.4/lib/js/emojione.min.js"
        integrity="sha384-e8w7egdoYGmmGwOA21YwuTSm0fuAhUKr+YMcrvejRVcd54csXKmCLLNsVtRjWWvM"
        crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.min.js"
        integrity="sha384-5YU+7tTEkP6y91NNn+Dq6vT3dLlZQxNNtAq6zBKlG3HJcRfPmpO4E7/3DufyNnvg"
        crossorigin="anonymous"></script>
<script type="text/javascript" src="https://s3-ap-southeast-2.amazonaws.com/jmango-prod/prestashop/js/prototype.noconflict.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/spin.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/ladda.min.js"></script>
<script type="text/javascript" src="https://s3-ap-southeast-2.amazonaws.com/jmango-prod/prestashop/js/jquery.pushMessageForm.js"></script>

</body>
</html>