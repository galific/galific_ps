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
<div class="pres-plugin-config">

    <div class="pres-plugin-tabs">
        <div class="tab-buttons-group">
            <div id="btn-tab-get-started" class="tab-item">
                <span id="txt_app_config_get_started">Get started</span>
            </div>
            <div id="btn-tab-introduction" class="tab-item active">
                <span id="txt_app_config_introduction_tab">Introduction</span>
            </div>
            <div id="btn-tab-pricing" class="tab-item">
                <span id="txt_app_config_pricing_tab">Pricing</span>
            </div>
            {*<div id="btn-tab-roi-calculator" class="tab-item">*}
            {*<span>ROI calculator</span>*}
            {*</div>*}
            <div id="btn-tab-benefits" class="tab-item">
                <span id="txt_app_config_benefit_tab">Benefits</span>
            </div>
            <div id="btn-tab-faqs-contact" class="tab-item">
                <span id="txt_app_config_faq_tab">FAQ</span>
            </div>
        </div>
        <div class="tab-contents-group">
            <div id="get-started" class="tab-content active">
                <div class="banner-image">
                    <img src="../../../../modules/jmango360api/views/img/backend_pretashop_qs/image-intro-app-config.png" />

                    {*<div class="deal-box">*}
                        {*<a href="https://jmango360.com/shop/?add-to-cart=17599" target="_blank">*}
                            {*<span id="txt-app-config-intro-deal" class="line-1">Introduction DEAL</span>*}
                            {*<span id="txt-app-config-intro-deal-content" class="line-2">Until September 30th <br />Free App Design <br />(Worth 499 EUR)</span>*}
                        {*</a>*}
                    {*</div>*}
                    <div class="banner-content-block">
                        <div id="txt_app_config_loyal_customer" class="title">
                            Turn Mobile Visitors into Loyal Customers with your own Shopping App
                        </div>
                        <div class="banner-feature-item">
                            <div class="item">
                                <div class="item-left">
                                    <span>x</span>3
                                </div>
                                <div id="txt_app_config_high_conversion" class="item-right">
                                    Higher conversion compared to a mobile site
                                </div>
                            </div>
                            <div class="item">
                                <div class="item-left">
                                    <span>x</span>2
                                </div>
                                <div id="txt_app_config_more_returning_customer" class="item-right">
                                    More returning customers
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {*<div class="feature-items">*}
                    {*<div class="row">*}
                        {*<div class="col-md-3 col-sm-6 col-xs-12">*}
                            {*<div id="create_your_app_tooltip" class="item tTips" data-title="The whole process of creating your app is for free. There are no charges and you will be able to experience your app straight on your phone.">*}
                                {*<div class="image">*}
                                    {*<svg xmlns="http://www.w3.org/2000/svg" width="19" height="34" viewBox="0 0 19 34">*}
                                        {*<path fill="#FFF" d="M3.69444444,388 C1.65755208,388 0,389.657552 0,391.694444 L0,418.083333 C0,420.120226 1.65755208,421.777778 3.69444444,421.777778 L15.3055556,421.777778 C17.3424479,421.777778 19,420.120226 19,418.083333 L19,391.694444 C19,389.657552 17.3424479,388 15.3055556,388 L3.69444444,388 Z M3.69444444,389.055556 L15.3055556,389.055556 C16.761068,389.055556 17.9444444,390.238933 17.9444444,391.694444 L17.9444444,392.222222 L1.05555556,392.222222 L1.05555556,391.694444 C1.05555556,390.238933 2.23893256,389.055556 3.69444444,389.055556 Z M6.86111111,390.111111 C6.56835964,390.111111 6.33333333,390.346137 6.33333333,390.638889 C6.33333333,390.931641 6.56835964,391.166667 6.86111111,391.166667 L7.91666667,391.166667 C8.20941867,391.166667 8.44444444,390.931641 8.44444444,390.638889 C8.44444444,390.346137 8.20941867,390.111111 7.91666667,390.111111 L6.86111111,390.111111 Z M10.0277778,390.111111 C9.73502631,390.111111 9.5,390.346137 9.5,390.638889 C9.5,390.931641 9.73502631,391.166667 10.0277778,391.166667 L12.1388889,391.166667 C12.4316409,391.166667 12.6666667,390.931641 12.6666667,390.638889 C12.6666667,390.346137 12.4316409,390.111111 12.1388889,390.111111 L10.0277778,390.111111 Z M1.05555556,393.277778 L17.9444444,393.277778 L17.9444444,415.444444 L1.05555556,415.444444 L1.05555556,393.277778 Z M1.05555556,416.5 L17.9444444,416.5 L17.9444444,418.083333 C17.9444444,419.538846 16.761068,420.722222 15.3055556,420.722222 L3.69444444,420.722222 C2.23893256,420.722222 1.05555556,419.538846 1.05555556,418.083333 L1.05555556,416.5 Z M9.5,417.555556 C8.91655803,417.555556 8.44444444,418.027669 8.44444444,418.611111 C8.44444444,419.194553 8.91655803,419.666667 9.5,419.666667 C10.083442,419.666667 10.5555556,419.194553 10.5555556,418.611111 C10.5555556,418.027669 10.083442,417.555556 9.5,417.555556 Z" transform="translate(0 -388)"/>*}
                                    {*</svg>*}
                                {*</div>*}
                                {*<div id="txt-app-config-step1" class="desc">*}
                                    {*1. Create your<br/> App for free*}
                                {*</div>*}
                            {*</div>*}
                        {*</div>*}
                        {*<div class="col-md-3 col-sm-6 col-xs-12">*}
                            {*<div id="order_setup_package_tooltip" class="item tTips" data-title="Included in the set-up package is the end-to-end testing, potential bug fixing, design and publishing of your app in the app stores">*}
                                {*<div class="image">*}
                                    {*<svg xmlns="http://www.w3.org/2000/svg" width="25" height="30" viewBox="0 0 25 30">*}
                                        {*<path fill="#FFF" d="M191,390 L191,419.761905 L216,419.761905 L216,390 L191,390 Z M192.190476,391.190476 L214.809524,391.190476 L214.809524,418.571429 L192.190476,418.571429 L192.190476,391.190476 Z M196.357143,396.547619 L196.357143,397.738095 L210.642857,397.738095 L210.642857,396.547619 L196.357143,396.547619 Z M196.357143,404.880952 L196.357143,406.071429 L198.738095,406.071429 L198.738095,404.880952 L196.357143,404.880952 Z M200.52381,404.880952 L200.52381,406.071429 L210.642857,406.071429 L210.642857,404.880952 L200.52381,404.880952 Z M196.357143,408.452381 L196.357143,409.642857 L198.738095,409.642857 L198.738095,408.452381 L196.357143,408.452381 Z M200.52381,408.452381 L200.52381,409.642857 L210.642857,409.642857 L210.642857,408.452381 L200.52381,408.452381 Z M196.357143,412.02381 L196.357143,413.214286 L198.738095,413.214286 L198.738095,412.02381 L196.357143,412.02381 Z M200.52381,412.02381 L200.52381,413.214286 L210.642857,413.214286 L210.642857,412.02381 L200.52381,412.02381 Z" transform="translate(-191 -390)"/>*}
                                    {*</svg>*}
                                {*</div>*}
                                {*<div id="txt-app-config-step2" class="desc">*}
                                    {*2. Order set-up<br/> package*}
                                {*</div>*}
                            {*</div>*}
                        {*</div>*}
                        {*<div class="col-md-3 col-sm-6 col-xs-12">*}
                            {*<div id="approve_design_app_tooltip" class="item tTips" data-title="During the feedback round you can point out desired changes to the design of your app.">*}
                                {*<div class="image">*}
                                    {*<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">*}
                                        {*<path fill="#FFF" d="M393.004623,390 L392.545798,390.438876 L387.438876,395.545798 L387,396.004623 L387.438876,396.463448 L397.413333,406.437905 L389.413819,414.43742 C389.33153,414.512229 389.269189,414.609479 389.234278,414.716705 L387.957548,419.504444 C387.900195,419.723882 387.965029,419.955788 388.12462,420.11538 C388.284212,420.274971 388.516117,420.339805 388.735556,420.282452 L393.523295,419.005721 C393.630521,418.970811 393.727772,418.90847 393.80258,418.826181 L415.866079,396.762682 C415.863586,396.765175 416.384751,396.24401 416.384751,396.24401 C417.599141,395.02962 417.596648,393.067146 416.384751,391.855249 C415.17036,390.640859 413.21038,390.643353 411.99599,391.855249 L403.437905,400.413333 L393.463448,390.438876 L393.004623,390 Z M393.004623,391.815351 L394.041967,392.852695 L393.224061,393.6706 L394.141711,394.58825 L394.959617,393.770345 L395.957063,394.767791 L394.500792,396.224061 L395.418442,397.141711 L396.874713,395.685441 L397.872158,396.682886 L397.054253,397.500792 L397.971903,398.418442 L398.789808,397.600536 L399.787254,398.597982 L398.330983,400.054253 L399.248633,400.971903 L400.704904,399.515632 L401.70235,400.513078 L400.884444,401.330983 L401.70235,402.148889 L398.330983,405.520255 L388.815351,396.004623 L393.004623,391.815351 Z M412.375019,393.271622 L414.968378,395.864981 L393.024572,417.808787 L390.431213,415.215428 L412.375019,393.271622 Z M408.365287,405.360664 L407.906462,405.79954 L406.629732,407.076271 L407.547382,407.993921 L408.365287,407.176015 L409.362733,408.173461 L407.906462,409.629732 L408.824112,410.547382 L410.280383,409.091111 L411.277829,410.088557 L410.459923,410.906462 L411.377573,411.824112 L412.195479,411.006207 L413.192924,412.003652 L411.736654,413.459923 L412.654304,414.377573 L414.110575,412.921303 L415.10802,413.918748 L414.290115,414.736654 L415.207765,415.654304 L416.02567,414.836398 L416.424649,415.235377 L412.235377,419.424649 L403.258365,410.467586 L402.360664,411.365287 L411.776552,420.801124 L412.235377,421.24 L412.694202,420.801124 L417.801124,415.694202 L418.24,415.235377 L417.801124,414.776552 L408.365287,405.360664 Z" transform="translate(-387 -390)"/>*}
                                    {*</svg>*}
                                {*</div>*}
                                {*<div id="txt-app-config-step3" class="desc">*}
                                    {*3. Approve the<br/> design of your app*}
                                {*</div>*}
                            {*</div>*}
                        {*</div>*}
                        {*<div class="col-md-3 col-sm-6 col-xs-12">*}
                            {*<div id="subscription_going_live_tooltip" class="item tTips" data-title="When you are happy with the design of your app, it can be published to the app stores. At that moment you can choose how to pay for the annual subscription. Monthly or per year. See the tab Pricing for more details.">*}
                                {*<div class="image">*}
                                    {*<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">*}
                                        {*<path fill="#FFF" d="M1079.36,563 L1078.68,563.02 C1078.68,563.02 1073.975,563.0825 1069.44,565.34 C1067.9825,566.0675 1066.5175,567.065 1065.2,568.38 C1063.5775,570.0025 1061.2875,572.665 1059.2,575.18 L1053.74,575.18 C1053.72,575.18 1053.7,575.18 1053.68,575.18 C1053.5075,575.1975 1053.3475,575.2825 1053.24,575.42 L1050.04,579.26 C1049.905,579.43 1049.8625,579.6575 1049.9325,579.8625 C1050,580.0675 1050.17,580.225 1050.38,580.28 L1054.28,581.26 C1054.25,581.2975 1054.06,581.54 1054.06,581.54 L1053.98,581.62 L1053.94,581.74 C1053.94,581.74 1053.885,581.965 1053.9,582.18 C1053.905,582.26 1053.92,582.365 1053.94,582.46 L1052.56,584.78 C1052.56,584.78 1052.4975,584.905 1052.48,584.98 C1052.4625,585.055 1052.4575,585.1475 1052.46,585.24 C1052.465,585.425 1052.51,585.6325 1052.62,585.88 C1052.84,586.375 1053.31,587.03 1054.32,588.04 C1055.33,589.0475 1055.9825,589.52 1056.48,589.74 C1056.7275,589.85 1056.935,589.895 1057.12,589.9 C1057.2125,589.9025 1057.305,589.8975 1057.38,589.88 C1057.455,589.8625 1057.58,589.8 1057.58,589.8 L1059.92,588.42 C1060.0075,588.4375 1060.1075,588.455 1060.18,588.46 C1060.395,588.475 1060.62,588.42 1060.62,588.42 L1060.74,588.38 L1060.84,588.3 C1060.84,588.3 1061.0675,588.125 1061.1,588.1 L1062.08,591.98 C1062.135,592.19 1062.2925,592.36 1062.4975,592.4275 C1062.7025,592.4975 1062.93,592.455 1063.1,592.32 L1066.94,589.12 C1067.0925,588.9975 1067.18,588.815 1067.18,588.62 L1067.18,583.14 C1069.7125,581.035 1072.3925,578.7475 1073.98,577.16 C1075.29,575.8475 1076.2725,574.38 1077,572.92 C1079.2475,568.3925 1079.34,563.68 1079.34,563.68 L1079.36,563 Z M1077.98,564.38 C1077.9275,565.3425 1077.675,568.7 1075.86,572.36 C1075.185,573.7175 1074.28,575.06 1073.08,576.26 C1071.52,577.82 1068.775,580.16 1066.22,582.28 C1066.1675,582.3125 1066.12,582.3525 1066.08,582.4 C1063.0875,584.88 1060.3875,587.0275 1060.22,587.16 C1060.145,587.1525 1060.0275,587.145 1059.82,587.06 C1059.3425,586.865 1058.505,586.365 1057.26,585.12 C1056.015,583.875 1055.495,583.0175 1055.3,582.54 C1055.215,582.3325 1055.2075,582.215 1055.2,582.14 C1055.3325,581.9725 1057.45,579.3025 1059.92,576.32 C1059.9875,576.265 1060.04,576.1975 1060.08,576.12 C1062.1775,573.59 1064.5025,570.8775 1066.1,569.28 C1067.305,568.075 1068.645,567.1775 1070,566.5 C1073.665,564.6775 1077.02,564.43 1077.98,564.38 Z M1069.74,569.42 C1067.98,569.42 1066.54,570.86 1066.54,572.62 C1066.54,574.38 1067.98,575.82 1069.74,575.82 C1071.5,575.82 1072.94,574.38 1072.94,572.62 C1072.94,570.86 1071.5,569.42 1069.74,569.42 Z M1069.74,570.7 C1070.8075,570.7 1071.66,571.5525 1071.66,572.62 C1071.66,573.6875 1070.8075,574.54 1069.74,574.54 C1068.6725,574.54 1067.82,573.6875 1067.82,572.62 C1067.82,571.5525 1068.6725,570.7 1069.74,570.7 Z M1054.04,576.46 L1058.14,576.46 C1056.9125,577.9575 1055.9025,579.23 1055.16,580.16 L1051.68,579.28 L1054.04,576.46 Z M1054.58,583.88 C1054.945,584.465 1055.48,585.16 1056.34,586.02 C1057.1975,586.8775 1057.895,587.415 1058.48,587.78 L1057.1,588.6 C1057.08,588.595 1057.0825,588.5975 1057,588.56 C1056.7375,588.445 1056.165,588.0825 1055.22,587.14 C1054.2775,586.195 1053.915,585.6225 1053.8,585.36 C1053.7625,585.2775 1053.765,585.28 1053.76,585.26 L1054.58,583.88 Z M1065.9,584.22 L1065.9,588.32 L1063.08,590.68 L1062.2,587.22 C1063.1275,586.4775 1064.3875,585.465 1065.9,584.22 Z M1052.08,586.84 C1051.035,587.6425 1050.09,587.745 1049.24,588.58 C1048.815,588.9975 1048.4775,589.59 1048.28,590.4 C1048.0825,591.21 1048,592.255 1048,593.72 L1048,594.36 L1048.64,594.36 C1051.58,594.36 1053.0175,593.895 1053.88,593.02 C1054.7425,592.145 1054.885,591.17 1055.54,590.28 L1054.5,589.52 C1053.67,590.65 1053.485,591.5875 1052.96,592.12 C1052.4975,592.59 1051.4675,592.935 1049.34,593.02 C1049.3725,592.09 1049.4175,591.1975 1049.54,590.7 C1049.7,590.0525 1049.895,589.74 1050.14,589.5 C1050.6275,589.02 1051.5825,588.805 1052.84,587.84 L1052.08,586.84 Z M1057,588.66 L1056.94,588.7 L1056.94,588.68 C1056.94,588.68 1056.975,588.67 1057,588.66 Z" transform="translate(-1048 -563)"/>*}
                                    {*</svg>*}
                                {*</div>*}
                                {*<div id="txt-app-config-step4" class="desc">*}
                                    {*4. Choose subscription<br/> and go LIVE*}
                                {*</div>*}
                            {*</div>*}
                        {*</div>*}
                    {*</div>*}
                {*</div>*}



                <div class="app-configuration">
                    <div class="form-fields">
                        <div class="title-form"><span id="txt-app-config-title">App configuration</span><span id="app_configuration_tooltip" class="tTips" data-title="Below fields show information that was pulled from your Prestashop. We need this to create the app for you. Please make sure that the correct information is used."><i class="fa fa-question-circle"></i></span></div>
                        <div class="form-field">
                            <div class="form-label" id="txt-app-config-store-url">URL</div>
                            <div class="form-value">
                                <input id="store-url" type="text" class="txt_text"/>
                            </div>
                            <div class="form-extra"><span id="store_url_tooltip" class="tTips" data-title="JMango360 creates your app based on the shop's URL"><i class="fa fa-question-circle"></i></span></div>

                        </div>
                        <div class="form-field">
                            <div class="form-label" id="txt-app-config-store-id">Store ID</div>
                            <div class="form-value">
                                <select id="shop-ids" class="txt-select"></select>
                            </div>
                            <div class="form-extra"><span id="store_id_tooltip" class="tTips" data-title="Please verify the shop ID so that your app will be created for the correct Shop ID"><i class="fa fa-question-circle"></i></span></div>

                        </div>
                        <div class="form-field">
                            <div class="form-label" id="txt-app-config-store-language">Store language</div>
                            <div class="form-value">
                                <select id="languages" class="txt-select">
                                </select>
                            </div>
                            <div class="form-extra"><span id="store_language_tooltip" class="tTips" data-title="Please verify the correct Language is selected for the creation of your app."><i class="fa fa-question-circle"></i></span></div>

                        </div>
                        <div id="error_msg_save_app_config" class="error_intergrate_shop_app">We can't integrate with your webshop, please validate if the url and webservice key are correct</div>
                        <div class="form-field field-submit">
                            <div class="form-value">
                                <button type="button" id="ps-create-app" class="btn-submit">Create my app for me</button>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
            <div id="introduction" class="tab-content">
                <div class="block1">
                    <div id="txt_app_config_launch_your_app" class="title bottom-border">Launch your Own Shopping App in just 3 Weeks</div>
                    <div id="txt_app_config_big_boys" class="desc">
                        Big boys are gaining 53% of revenue with their shopping app. JMango lets you play along with them by enabling you to create and manage a powerful shopping app. Offer your customers the best mobile shopping experience, just without the long development time, coding or big budgets.
                    </div>
                    <div class="video-play">
                        {*<embed id="app_config_intro_video" src="https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-en.mp4" width="400" height="226">*}
                        <video id="app_config_intro_video" controls="controls"  width="400" height="226">
                            <source id="app_config_intro_video_source" src="https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-en.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>
                <div class="block2">
                    <div id="txt_app_config_get_started_desc" class="title bottom-border">Get started with JMango360</div>
                    <div class="intro-feature-items">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12 feature-item">
                                <div class="feature-item-inner">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 31" width="30" height="31">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M30,16c0,8.28 -6.72,15 -15,15c-8.28,0 -15,-6.72 -15,-15c0,-8.28 6.72,-15 15,-15c8.28,0 15,6.72 15,15zM1.3,16c0,7.57 6.12,13.7 13.7,13.7c7.57,0 13.7,-6.12 13.7,-13.7c0,-7.57 -6.12,-13.7 -13.7,-13.7c-7.57,0 -13.7,6.12 -13.7,13.7zM19.42,11.83c0,1.12 -0.4,1.94 -1.19,3.04l-1.57,2.15c-0.47,0.63 -0.61,0.96 -0.61,1.75v0.75c0,0.14 -0.09,0.23 -0.23,0.23h-1.54c-0.14,0 -0.23,-0.09 -0.23,-0.23v-0.96c0,-0.94 0.21,-1.43 0.7,-2.11l1.57,-2.15c0.82,-1.12 1.1,-1.66 1.1,-2.46c0,-1.33 -0.94,-2.18 -2.27,-2.18c-1.31,0 -2.15,0.8 -2.43,2.22c-0.02,0.14 -0.12,0.21 -0.26,0.19l-1.47,-0.26c-0.14,-0.02 -0.21,-0.12 -0.19,-0.26c0.35,-2.25 1.96,-3.72 4.4,-3.72c2.52,0 4.23,1.66 4.23,3.98zM15.94,21.54c0.14,0 0.23,0.09 0.23,0.23v2.01c0,0.14 -0.09,0.23 -0.23,0.23h-1.78c-0.14,0 -0.23,-0.09 -0.23,-0.23v-2.01c0,-0.14 0.09,-0.23 0.23,-0.23z" />
                                        </svg>
                                    </div>
                                    <div id="txt_app_config_here_to_help" class="title">
                                        We're here to help
                                    </div>
                                    <div id="txt_app_config_after_launch" class="desc">
                                        After launching your app, our app promotion team will make sure your app turns into an instant success.
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 feature-item">
                                <div class="feature-item-inner">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 34" width="30" height="34">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M7.5,4.75c0,1.8 -1.3,3.33 -3,3.68v15.82c0,2.91 2.34,5.25 5.25,5.25h5.25v-3h1.22l0.21,0.16l3.75,3l0.73,0.59l-0.73,0.59l-3.75,3l-0.21,0.16h-1.22v-3h-5.25c-3.72,0 -6.75,-3.03 -6.75,-6.75v-15.82c-1.71,-0.35 -3,-1.88 -3,-3.68c0,-2.06 1.69,-3.75 3.75,-3.75c2.06,0 3.75,1.69 3.75,3.75zM15,1v3h5.25c3.72,0 6.75,3.03 6.75,6.75v15.82c1.71,0.35 3,1.87 3,3.68c0,2.06 -1.69,3.75 -3.75,3.75c-2.06,0 -3.75,-1.69 -3.75,-3.75c0,-1.8 1.29,-3.33 3,-3.68v-15.82c0,-2.91 -2.34,-5.25 -5.25,-5.25h-5.25v3h-1.22l-0.21,-0.16l-3.75,-3l-0.73,-0.59l0.73,-0.59l3.75,-3l0.21,-0.16zM1.5,4.75c0,1.25 1,2.25 2.25,2.25c1.25,0 2.25,-1 2.25,-2.25c0,-1.25 -1,-2.25 -2.25,-2.25c-1.25,0 -2.25,1 -2.25,2.25zM11.48,4.75l2.02,1.62v-3.23zM24,30.25c0,1.25 1,2.25 2.25,2.25c1.25,0 2.25,-1 2.25,-2.25c0,-1.25 -1,-2.25 -2.25,-2.25c-1.25,0 -2.25,1 -2.25,2.25zM16.5,31.87l2.02,-1.62l-2.02,-1.62z" />
                                        </svg>
                                    </div>
                                    <div id="txt_app_config_web_integration" class="title">
                                        Webstore Integration
                                    </div>
                                    <div id="txt_app_config_plugin_ensure" class="desc">
                                        Our plug-in ensures your webstore to always be synced real-time with your app, keeping both channels up-to-date.
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 feature-item">
                                <div class="feature-item-inner">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 38" width="32" height="38">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M17.18,0c2.29,0 4.15,1.86 4.15,4.15v4.15h8.89c0.98,0 1.78,0.8 1.78,1.78v13.04c0,0.98 -0.8,1.78 -1.78,1.78h-7.25c-0.2,0.61 -0.66,1.66 -1.64,2.56v6.32c0,2.29 -1.86,4.15 -4.15,4.15h-13.04c-2.29,0 -4.15,-1.86 -4.15,-4.15v-29.63c0,-2.29 1.86,-4.15 4.15,-4.15zM1.18,4.15v0.59h18.96v-0.59c0,-1.63 -1.33,-2.96 -2.96,-2.96h-13.04c-1.63,0 -2.96,1.33 -2.96,2.96zM8.89,2.37c0.33,0 0.59,0.26 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.26 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM13.63,2.37c0.33,0 0.59,0.26 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-2.37c-0.33,0 -0.59,-0.26 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM1.18,30.81h18.96v-2.52c-0.62,0.34 -1.38,0.6 -2.29,0.73c-0.03,0 -0.06,0.01 -0.08,0.01c-0.18,0 -0.35,-0.08 -0.46,-0.23c-0.13,-0.17 -0.16,-0.39 -0.09,-0.59c0.71,-1.77 0.74,-2.84 0.68,-3.34h-3.09c-0.98,0 -1.78,-0.8 -1.78,-1.78v-13.04c0,-0.98 0.8,-1.78 1.78,-1.78h5.33v-2.37h-18.96zM14.22,10.07v13.04c0,0.33 0.27,0.59 0.59,0.59h3.56c0.22,0 0.43,0.13 0.53,0.33c0.06,0.13 0.54,1.21 -0.2,3.6c2.75,-0.87 3.22,-3.32 3.24,-3.44c0.05,-0.28 0.3,-0.49 0.58,-0.49h7.7c0.33,0 0.59,-0.27 0.59,-0.59v-13.04c0,-0.33 -0.27,-0.59 -0.59,-0.59h-15.41c-0.33,0 -0.59,0.27 -0.59,0.59zM22.56,10.67c1.62,0 3,1.21 3.22,2.82l0.31,2.36l1.11,2.66c0.16,0.37 0.11,0.78 -0.11,1.11c-0.22,0.33 -0.59,0.53 -0.99,0.53h-2.41c0,0.65 -0.53,1.19 -1.19,1.19c-0.65,0 -1.19,-0.53 -1.19,-1.19h-2.37c-0.4,0 -0.77,-0.2 -0.99,-0.53c-0.22,-0.33 -0.26,-0.75 -0.11,-1.11l1.11,-2.66l0.29,-2.33c0.2,-1.62 1.59,-2.84 3.22,-2.84zM20.44,13.66l-0.22,1.75h2.88c0.33,0 0.59,0.26 0.59,0.59c0,0.33 -0.27,0.59 -0.59,0.59h-3.16l-0.99,2.37h7.15l-1.14,-2.73c-0.02,-0.05 -0.03,-0.1 -0.04,-0.15l-0.33,-2.44c-0.14,-1.02 -1.01,-1.79 -2.04,-1.79h-0.07c-1.04,0 -1.92,0.78 -2.05,1.81zM4.15,26.67v1.19c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.26 -0.59,-0.59v-1.19c0,-0.33 0.26,-0.59 0.59,-0.59c0.33,0 0.59,0.26 0.59,0.59zM7.11,26.67v1.19c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.26 -0.59,-0.59v-1.19c0,-0.33 0.26,-0.59 0.59,-0.59c0.33,0 0.59,0.26 0.59,0.59zM10.07,26.67v1.19c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.26 -0.59,-0.59v-1.19c0,-0.33 0.26,-0.59 0.59,-0.59c0.33,0 0.59,0.26 0.59,0.59zM13.04,26.67v1.19c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.26 -0.59,-0.59v-1.19c0,-0.33 0.26,-0.59 0.59,-0.59c0.33,0 0.59,0.26 0.59,0.59zM16,26.67v1.19c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.26 -0.59,-0.59v-1.19c0,-0.33 0.26,-0.59 0.59,-0.59c0.33,0 0.59,0.26 0.59,0.59zM1.18,33.78c0,1.63 1.33,2.96 2.96,2.96h13.04c1.63,0 2.96,-1.33 2.96,-2.96v-1.78h-18.96zM11.85,34.37c0,0.66 -0.53,1.19 -1.19,1.19c-0.65,0 -1.19,-0.53 -1.19,-1.19c0,-0.66 0.53,-1.19 1.19,-1.19c0.65,0 1.19,0.53 1.19,1.19z" />
                                        </svg>
                                    </div>
                                    <div id="txt_app_config_push_notification" class="title">
                                        Push notifications
                                    </div>
                                    <div id="txt_app_config_make_push_notification" class="desc">
                                        Make push notifications highly effective by sending personalized messages based on customers behavior.
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 feature-item">
                                <div class="feature-item-inner">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29" width="29" height="29">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M22.19,0v1.56c0.92,0.16 1.76,0.53 2.5,1.04l1.04,-1.04l1.56,1.56l-1.04,1.04c0.52,0.74 0.88,1.58 1.04,2.5h1.56v2.22h-1.56c-0.17,0.93 -0.52,1.79 -1.04,2.53l1.08,1.15l-1.63,1.53l-1.04,-1.11c-0.73,0.5 -1.56,0.85 -2.47,1.01v1.56h-2.22v-1.56c-0.92,-0.16 -1.76,-0.53 -2.5,-1.04l-1.15,1.18l-1.6,-1.6l1.18,-1.15c-0.52,-0.74 -0.88,-1.58 -1.04,-2.5h-1.56v-2.22h1.56c0.16,-0.9 0.51,-1.74 1.01,-2.47l-1.11,-1.04l1.53,-1.63l1.15,1.08c0.74,-0.53 1.61,-0.88 2.53,-1.04v-1.56zM16.98,7.78c0,2.28 1.81,4.1 4.1,4.1c2.28,0 4.1,-1.81 4.1,-4.1c0,-2.28 -1.81,-4.1 -4.1,-4.1c-2.28,0 -4.1,1.81 -4.1,4.1zM7.46,12.26c0.59,-0.15 1.21,-0.24 1.84,-0.24c0.63,0 1.25,0.1 1.84,0.24l0.8,-2.01l2.05,0.83l-0.8,2.01c1.05,0.63 1.94,1.52 2.57,2.57l2.01,-0.8l0.83,2.05l-2.01,0.8c0.15,0.59 0.24,1.21 0.24,1.84c0,0.63 -0.1,1.25 -0.24,1.84l2.01,0.8l-0.83,2.05l-2.01,-0.8c-0.63,1.06 -1.52,1.96 -2.57,2.6l0.8,1.98l-2.05,0.83l-0.8,-1.98c-0.59,0.15 -1.21,0.24 -1.84,0.24c-0.64,0 -1.25,-0.1 -1.84,-0.24l-0.8,1.98l-2.05,-0.83l0.8,-1.98c-1.07,-0.64 -1.96,-1.54 -2.6,-2.6l-1.98,0.8l-0.83,-2.05l1.98,-0.8c-0.15,-0.59 -0.24,-1.21 -0.24,-1.84c0,-0.63 0.1,-1.25 0.24,-1.84l-1.98,-0.8l0.83,-2.05l1.98,0.8c0.64,-1.05 1.55,-1.94 2.6,-2.57l-0.8,-2.01l2.05,-0.83zM3.96,19.55c0,2.96 2.39,5.35 5.35,5.35c2.96,0 5.31,-2.39 5.31,-5.35c0,-2.96 -2.35,-5.31 -5.31,-5.31c-2.96,0 -5.35,2.35 -5.35,5.31z" />
                                        </svg>
                                    </div>
                                    <div id="txt_app_config_getting_better" class="title">
                                        Keeps getting better
                                    </div>
                                    <div id="txt_app_config_getting_better_desc" class="desc">
                                        We launch a set of brand new features four times a year. By simply updating your app, you grow along with our innovations.
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 feature-item">
                                <div class="feature-item-inner">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34 32" width="34" height="32">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M31.67,1.33c0,0.74 -0.6,1.33 -1.33,1.33c-0.04,0 -0.08,0 -0.13,0l-5.33,7.46c0.08,0.17 0.12,0.35 0.12,0.54c0,0.74 -0.6,1.33 -1.33,1.33c-0.71,0 -1.29,-0.55 -1.33,-1.25l-4.6,-2.29c-0.21,0.14 -0.46,0.21 -0.73,0.21c-0.17,0 -0.33,-0.03 -0.48,-0.08l-4.87,3.87c0.01,0.07 0.02,0.14 0.02,0.21c0,0.74 -0.6,1.33 -1.33,1.33c-0.31,0 -0.59,-0.11 -0.81,-0.29l-4.54,1.81c-0.09,0.65 -0.64,1.15 -1.31,1.15c-0.74,0 -1.33,-0.6 -1.33,-1.33c0,-0.74 0.6,-1.33 1.33,-1.33c0.31,0 0.59,0.11 0.81,0.29l4.54,-1.83c0.1,-0.64 0.65,-1.12 1.31,-1.12c0.17,0 0.33,0.03 0.48,0.08l4.87,-3.88c-0.01,-0.07 -0.02,-0.14 -0.02,-0.21c0,-0.74 0.6,-1.33 1.33,-1.33c0.71,0 1.29,0.55 1.33,1.25l4.6,2.29c0.21,-0.14 0.46,-0.21 0.73,-0.21c0.04,0 0.08,0 0.13,0l5.33,-7.46c-0.08,-0.17 -0.13,-0.35 -0.13,-0.54c0,-0.74 0.6,-1.33 1.33,-1.33c0.74,0 1.33,0.6 1.33,1.33zM33,8.67v23.33h-5.33v-23.33zM29,30.67h2.67v-20.67h-2.67zM19.67,14.67v17.33h-5.33v-17.33zM15.67,30.67h2.67v-14.67h-2.67zM26.33,18v14h-5.33v-14zM22.33,30.67h2.67v-11.33h-2.67zM13,20v12h-5.33v-12zM9,30.67h2.67v-9.33h-2.67zM6.33,22.67v9.33h-5.33v-9.33zM2.33,30.67h2.67v-6.67h-2.67z" />
                                        </svg>
                                    </div>
                                    <div id="txt_app_config_easy_analytic" class="title">
                                        Easy analytics
                                    </div>
                                    <div id="txt_app_config_easy_analytic_desc" class="desc">
                                        See your company grow in our easy app analytics dashboard, so you can use these insights to your benefits.
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 feature-item">
                                <div class="feature-item-inner">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 82 35" width="82" height="35">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M8.31,0.99l1.58,2.34c1.11,-0.4 2.34,-0.61 3.61,-0.61c1.27,0 2.49,0.21 3.61,0.61l1.58,-2.34c0.09,-0.15 0.25,-0.26 0.42,-0.3c0.27,-0.06 0.56,0.05 0.71,0.29c0.16,0.23 0.15,0.54 -0.02,0.77l-1.43,2.15c2.44,1.28 4.18,3.5 4.58,6.14c0,0.01 0,0.01 0,0.02c0,0.01 0,0.01 0,0.02c0.01,0.06 0.01,0.13 0,0.19v0.93c0.4,-0.23 0.85,-0.38 1.35,-0.38c1.48,0 2.7,1.22 2.7,2.7v8.1c0,1.48 -1.22,2.7 -2.7,2.7c-0.5,0 -0.95,-0.15 -1.35,-0.38v1.73c0,1.11 -0.91,2.02 -2.03,2.02h-0.68v4.05c0,1.48 -1.22,2.7 -2.7,2.7c-1.48,0 -2.7,-1.22 -2.7,-2.7v-4.05h-2.7v4.05c0,1.48 -1.22,2.7 -2.7,2.7c-1.48,0 -2.7,-1.22 -2.7,-2.7v-4.05h-0.68c-1.11,0 -2.02,-0.92 -2.02,-2.02v-1.73c-0.4,0.23 -0.85,0.38 -1.35,0.38c-1.48,0 -2.7,-1.22 -2.7,-2.7v-8.1c0,-1.48 1.22,-2.7 2.7,-2.7c0.5,0 0.95,0.15 1.35,0.38v-0.95c0,-0.03 0,-0.07 0,-0.11c0,-0.01 0,-0.03 0,-0.04c0,-0.02 0,-0.04 0,-0.06c0,-0.01 0,-0.01 0,-0.02c0,-0.01 0,-0.01 0,-0.02c0.41,-2.62 2.15,-4.82 4.58,-6.1l-1.43,-2.15c-0.13,-0.18 -0.17,-0.42 -0.08,-0.64c0.08,-0.21 0.26,-0.37 0.49,-0.42c0.03,-0.01 0.06,-0.02 0.08,-0.02c0.25,-0.02 0.5,0.1 0.63,0.32zM9.98,4.77c-0.04,0.03 -0.08,0.05 -0.13,0.06c-2.07,0.93 -3.57,2.64 -4.16,4.64h15.61c-0.59,-2.02 -2.13,-3.76 -4.24,-4.68c-0.02,-0.01 -0.04,-0.01 -0.06,-0.02c-1.04,-0.45 -2.23,-0.7 -3.5,-0.7c-1.28,0 -2.47,0.24 -3.52,0.7zM10.8,7.11c0,0.56 -0.45,1.01 -1.01,1.01c-0.56,0 -1.01,-0.45 -1.01,-1.01c0,-0.56 0.45,-1.01 1.01,-1.01c0.56,0 1.01,0.45 1.01,1.01zM18.22,7.11c0,0.56 -0.45,1.01 -1.01,1.01c-0.56,0 -1.01,-0.45 -1.01,-1.01c0,-0.56 0.45,-1.01 1.01,-1.01c0.56,0 1.01,0.45 1.01,1.01zM5.4,25.67c0,0.38 0.3,0.67 0.67,0.67h1.2c0.09,-0.02 0.18,-0.02 0.27,0h7.83c0.09,-0.02 0.19,-0.02 0.27,0h5.27c0.38,0 0.67,-0.3 0.67,-0.67v-14.85h-16.2zM1.35,13.52v8.1c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-8.1c0,-0.75 -0.6,-1.35 -1.35,-1.35c-0.75,0 -1.35,0.6 -1.35,1.35zM22.95,13.52v8.1c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-8.1c0,-0.75 -0.6,-1.35 -1.35,-1.35c-0.75,0 -1.35,0.6 -1.35,1.35zM8.1,31.75c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-4.05h-2.7zM16.2,31.75c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-4.05h-2.7z" />
                                            <path id="Shape" fill="none" stroke="#4a90e2" stroke-width="1.2" class="shp1" d="M74.08,4c-1.68,0.1 -3.6,1.03 -4.74,2.22c-1.03,1.08 -1.85,2.69 -1.52,4.24c1.82,0.05 3.65,-0.91 4.74,-2.12c1.02,-1.13 1.8,-2.71 1.52,-4.34zM74.18,10.46c-2.63,0 -3.73,1.62 -5.55,1.62c-1.87,0 -3.59,-1.51 -5.85,-1.51c-3.08,0 -7.77,2.86 -7.77,9.59c0,6.12 5.55,12.92 8.68,12.92c1.9,0.02 2.36,-1.2 4.95,-1.21c2.58,-0.02 3.14,1.23 5.05,1.21c2.15,-0.02 3.82,-2.37 5.05,-4.24c0.88,-1.34 1.24,-2.02 1.92,-3.53c-5.05,-1.28 -6.05,-9.42 0,-11.1c-1.14,-1.95 -4.48,-3.74 -6.46,-3.74z" />
                                        </svg>
                                    </div>
                                    <div id="txt_app_config_native_app" class="title">
                                        Native Android & iOS
                                    </div>
                                    <div id="txt_app_config_native_app_desc" class="desc">
                                        Your app will be native for both iOS & Android and will automatically meet the latest design standards.
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="btn-group">
                        <a id="txt_app_config_btn_get_start" class="btn-get-started" href="#">Get started</a>
                    </div>

                </div>

            </div>
            <div id="pricing" class="tab-content">
                <div class="block1">
                    <div class="title bottom-border" id="txt_app_config_simple_pricing">Simple Pricing</div>
                    <div class="row-price">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="heading">
                                    <div class="title" id="txt_app_config_we_build_app">We build and design your App</div>
                                    <div class="desc" id="txt_app_config_we_do_heavy_lifting">We do all the heavy lifting for you. </div>
                                </div>
                                <div class="heading">
                                    <div class="title" id="txt_app_config_no_hidden_cost">No hidden costs</div>
                                    <div class="desc" id="txt_app_config_hidden_cost_desc">
                                        A simple monthly fee and a nice discount when you pay a year upfront.
                                    </div>
                                </div>
                                <div class="heading">
                                    <div class="title" id="txt_app_config_future_proof">Future Proof</div>
                                    <div class="desc" id="txt_app_config_future_proof_desc">
                                        We continuously add new features to keep your app updated and you have full app control through our Platform.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="price-box">
                                    <div class="price-top">
                                        <div class="price-line1">€ <span>249</span> / mo</div>
                                        <div class="price-line2">+ <span class="price-color">999</span> <span id="txt_config_app_setup_cost">setup costs</span> <span id="one_time_fee_tooltip" class="tTips price-color" data-title="The one-time setup fee includes everything to get you started: the design of your app, an iOS/Android App, launch in own App Stores and even your own App Success Manager"><i class="fa fa-question-circle"></i></span> </div>
                                    </div>
                                    {*<div class="price-bottom">*}
                                        {*<div class="title" id="txt_app_config_intro_offer">Introduction offer:</div>*}
                                        {*<div class="desc" id="txt_app_config_intro_discount">*}
                                            {*€500 discount<br />*}
                                            {*(Free Design of your app)*}
                                        {*</div>*}
                                        {*<div class="date-end" id="txt_app_config_end_date">*}
                                            {*End date: Sept 30*}
                                        {*</div>*}
                                    {*</div>*}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="block2">
                    <div class="pay-price-block">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 62 43" width="62" height="43">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp1">
                                                <path d="M26.62,6h24.75c2.46,0 4.62,2.06 4.62,4.56v15.06c0,2.41 -1.94,4.38 -4.34,4.38h-8.72c-0.22,2.11 -0.83,3.67 -1.84,4.69c-1.16,1.16 -2.67,1.53 -3.97,1.19c-0.27,-0.07 -0.51,-0.17 -0.72,-0.31c-0.07,0.08 -0.14,0.15 -0.25,0.25c-0.62,0.58 -1.28,1.04 -2.09,1.25c-0.81,0.22 -1.71,0.14 -2.63,-0.19c-0.87,-0.31 -1.43,-0.99 -1.75,-1.72c-0.32,0.25 -0.69,0.48 -1.09,0.66c-0.91,0.39 -2.04,0.5 -3.19,0.13c-0.82,-0.27 -1.34,-0.91 -1.59,-1.66c-1.46,0.66 -3.32,1.09 -5.87,1.09c-0.02,0 -0.04,0 -0.06,0l-10.81,0.62c-0.55,0.03 -1.03,-0.39 -1.06,-0.94c-0.04,-0.55 0.39,-1.03 0.94,-1.06l10.88,-0.62h0.06c3.45,0 5.12,-0.67 6.47,-1.69c1.35,-1.02 2.43,-2.52 4.28,-4.09l0.16,-0.12l0.19,-0.06c0,0 3.61,-1.23 5.94,-2.87c1.11,-0.79 1.76,-1.71 2,-2.56c0.24,-0.85 0.12,-1.62 -0.41,-2.31c-0.45,-0.59 -0.98,-0.68 -2.03,-0.47c-1.05,0.21 -2.43,0.86 -3.75,1.56h-0.03l-9.25,4.25c-0.33,0.2 -0.75,0.19 -1.07,-0.02c-0.32,-0.22 -0.49,-0.59 -0.44,-0.98c0.05,-0.38 0.32,-0.7 0.69,-0.81l1.38,-0.63v-11.91c0,-0.14 0.05,-0.27 0.06,-0.41c-0.8,-0.4 -1.23,-0.3 -1.78,0c-0.66,0.36 -1.43,1.22 -2.44,2.06c-1.04,0.87 -3.56,2.71 -5.87,4.38c-2.31,1.67 -4.41,3.12 -4.41,3.12c-0.28,0.27 -0.7,0.35 -1.07,0.2c-0.36,-0.15 -0.61,-0.5 -0.62,-0.9c-0.01,-0.39 0.21,-0.76 0.56,-0.93c0,0 2.04,-1.46 4.34,-3.13c2.3,-1.66 4.89,-3.56 5.75,-4.28c0.84,-0.71 1.64,-1.66 2.78,-2.28c0.57,-0.31 1.23,-0.47 1.97,-0.44c0.45,0.02 0.93,0.13 1.41,0.31c0.15,-0.25 0.33,-0.52 0.53,-0.75c0.78,-0.91 1.96,-1.63 3.41,-1.63zM26.62,8c-0.79,0 -1.39,0.38 -1.87,0.94c-0.48,0.56 -0.75,1.3 -0.75,1.72v0.34h30v-0.44c0,-1.23 -1.38,-2.56 -2.63,-2.56zM24,15v6.62l5.78,-2.66c0.02,-0.01 0.04,-0.02 0.06,-0.03c1.35,-0.72 2.8,-1.4 4.22,-1.69c1.44,-0.29 3.05,-0.07 4.03,1.22c0.89,1.18 1.14,2.68 0.75,4.06c-0.39,1.38 -1.36,2.65 -2.78,3.66c-1.07,0.76 -2.23,1.33 -3.28,1.81h18.87c1.3,0 2.34,-1.04 2.34,-2.38v-10.63zM29,30c-1.14,1.12 -2.09,2.23 -3.37,3.22c0.02,0.46 0.23,0.75 0.41,0.81c0.73,0.23 1.24,0.16 1.75,-0.06c0.51,-0.22 1.01,-0.64 1.44,-1.16c0.05,-0.05 0.1,-0.09 0.16,-0.12c0.01,-0.09 0.02,-0.17 0.03,-0.25c0.12,-0.68 0.22,-1.74 0.28,-2.44zM31.72,30c-0.05,0.63 -0.17,1.84 -0.34,2.78c-0.09,0.48 -0.05,1.06 0.09,1.47c0.14,0.41 0.32,0.63 0.66,0.75c0.64,0.23 1.03,0.26 1.41,0.16c0.38,-0.1 0.79,-0.36 1.28,-0.81c-0.11,0.1 0.18,-0.2 0.34,-0.41c0.08,-0.1 0.1,-0.11 0.16,-0.19c-0.16,-0.78 -0.1,-1.57 -0.03,-2.22c0.05,-0.54 0.12,-1.07 0.19,-1.53zM37.5,30c-0.09,0.49 -0.18,1.11 -0.25,1.75c-0.06,0.59 -0.06,1.24 0.03,1.66c0.09,0.41 0.11,0.47 0.34,0.53c0.69,0.18 1.32,0.09 2.06,-0.66c0.55,-0.55 1.01,-1.61 1.22,-3.28z" />
                                            </clipPath>
                                            <path id="Clip 210" fill="#4a90e2" class="shp0" d="M26.62,6h24.75c2.46,0 4.62,2.06 4.62,4.56v15.06c0,2.41 -1.94,4.38 -4.34,4.38h-8.72c-0.22,2.11 -0.83,3.67 -1.84,4.69c-1.16,1.16 -2.67,1.53 -3.97,1.19c-0.27,-0.07 -0.51,-0.17 -0.72,-0.31c-0.07,0.08 -0.14,0.15 -0.25,0.25c-0.62,0.58 -1.28,1.04 -2.09,1.25c-0.81,0.22 -1.71,0.14 -2.63,-0.19c-0.87,-0.31 -1.43,-0.99 -1.75,-1.72c-0.32,0.25 -0.69,0.48 -1.09,0.66c-0.91,0.39 -2.04,0.5 -3.19,0.13c-0.82,-0.27 -1.34,-0.91 -1.59,-1.66c-1.46,0.66 -3.32,1.09 -5.87,1.09c-0.02,0 -0.04,0 -0.06,0l-10.81,0.62c-0.55,0.03 -1.03,-0.39 -1.06,-0.94c-0.04,-0.55 0.39,-1.03 0.94,-1.06l10.88,-0.62h0.06c3.45,0 5.12,-0.67 6.47,-1.69c1.35,-1.02 2.43,-2.52 4.28,-4.09l0.16,-0.12l0.19,-0.06c0,0 3.61,-1.23 5.94,-2.87c1.11,-0.79 1.76,-1.71 2,-2.56c0.24,-0.85 0.12,-1.62 -0.41,-2.31c-0.45,-0.59 -0.98,-0.68 -2.03,-0.47c-1.05,0.21 -2.43,0.86 -3.75,1.56h-0.03l-9.25,4.25c-0.33,0.2 -0.75,0.19 -1.07,-0.02c-0.32,-0.22 -0.49,-0.59 -0.44,-0.98c0.05,-0.38 0.32,-0.7 0.69,-0.81l1.38,-0.63v-11.91c0,-0.14 0.05,-0.27 0.06,-0.41c-0.8,-0.4 -1.23,-0.3 -1.78,0c-0.66,0.36 -1.43,1.22 -2.44,2.06c-1.04,0.87 -3.56,2.71 -5.87,4.38c-2.31,1.67 -4.41,3.12 -4.41,3.12c-0.28,0.27 -0.7,0.35 -1.07,0.2c-0.36,-0.15 -0.61,-0.5 -0.62,-0.9c-0.01,-0.39 0.21,-0.76 0.56,-0.93c0,0 2.04,-1.46 4.34,-3.13c2.3,-1.66 4.89,-3.56 5.75,-4.28c0.84,-0.71 1.64,-1.66 2.78,-2.28c0.57,-0.31 1.23,-0.47 1.97,-0.44c0.45,0.02 0.93,0.13 1.41,0.31c0.15,-0.25 0.33,-0.52 0.53,-0.75c0.78,-0.91 1.96,-1.63 3.41,-1.63zM26.62,8c-0.79,0 -1.39,0.38 -1.87,0.94c-0.48,0.56 -0.75,1.3 -0.75,1.72v0.34h30v-0.44c0,-1.23 -1.38,-2.56 -2.63,-2.56zM24,15v6.62l5.78,-2.66c0.02,-0.01 0.04,-0.02 0.06,-0.03c1.35,-0.72 2.8,-1.4 4.22,-1.69c1.44,-0.29 3.05,-0.07 4.03,1.22c0.89,1.18 1.14,2.68 0.75,4.06c-0.39,1.38 -1.36,2.65 -2.78,3.66c-1.07,0.76 -2.23,1.33 -3.28,1.81h18.87c1.3,0 2.34,-1.04 2.34,-2.38v-10.63zM29,30c-1.14,1.12 -2.09,2.23 -3.37,3.22c0.02,0.46 0.23,0.75 0.41,0.81c0.73,0.23 1.24,0.16 1.75,-0.06c0.51,-0.22 1.01,-0.64 1.44,-1.16c0.05,-0.05 0.1,-0.09 0.16,-0.12c0.01,-0.09 0.02,-0.17 0.03,-0.25c0.12,-0.68 0.22,-1.74 0.28,-2.44zM31.72,30c-0.05,0.63 -0.17,1.84 -0.34,2.78c-0.09,0.48 -0.05,1.06 0.09,1.47c0.14,0.41 0.32,0.63 0.66,0.75c0.64,0.23 1.03,0.26 1.41,0.16c0.38,-0.1 0.79,-0.36 1.28,-0.81c-0.11,0.1 0.18,-0.2 0.34,-0.41c0.08,-0.1 0.1,-0.11 0.16,-0.19c-0.16,-0.78 -0.1,-1.57 -0.03,-2.22c0.05,-0.54 0.12,-1.07 0.19,-1.53zM37.5,30c-0.09,0.49 -0.18,1.11 -0.25,1.75c-0.06,0.59 -0.06,1.24 0.03,1.66c0.09,0.41 0.11,0.47 0.34,0.53c0.69,0.18 1.32,0.09 2.06,-0.66c0.55,-0.55 1.01,-1.61 1.22,-3.28z" />
                                            <g id="Mask by Clip 210" clip-path="url(#cp1)">
                                                <path id="Fill 209" class="shp1" fill="#4a90e1" d="M0.87,42.18h60.13v-41.18h-60.13z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="desc" id="txt_app_config_price_step1">
                                        1. Pay the setup costs<br />
                                        (one-time fee)
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 51 51" width="51" height="51">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp2">
                                                <path d="M46,5l-0.03,0.89c0,0 -0.12,6.16 -3.06,12.08c-0.95,1.91 -2.24,3.83 -3.95,5.54c-2.08,2.08 -5.58,5.07 -8.89,7.82v7.16c0,0.25 -0.11,0.49 -0.31,0.65l-5.02,4.18c-0.22,0.18 -0.52,0.23 -0.79,0.14c-0.27,-0.09 -0.47,-0.31 -0.55,-0.59l-1.28,-5.07c-0.04,0.03 -0.34,0.26 -0.34,0.26l-0.13,0.11l-0.16,0.05c0,0 -0.29,0.07 -0.58,0.05c-0.09,-0.01 -0.23,-0.03 -0.34,-0.05l-3.06,1.8c0,0 -0.16,0.08 -0.26,0.11c-0.1,0.02 -0.22,0.03 -0.34,0.03c-0.24,-0.01 -0.51,-0.07 -0.84,-0.21c-0.65,-0.29 -1.5,-0.91 -2.82,-2.22c-1.32,-1.32 -1.93,-2.18 -2.22,-2.82c-0.14,-0.32 -0.2,-0.6 -0.21,-0.84c0,-0.12 0,-0.24 0.03,-0.34c0.02,-0.1 0.1,-0.26 0.1,-0.26l1.8,-3.03c-0.03,-0.12 -0.05,-0.26 -0.05,-0.37c-0.02,-0.28 0.05,-0.58 0.05,-0.58l0.05,-0.16l0.1,-0.1c0,0 0.25,-0.32 0.29,-0.37l-5.1,-1.28c-0.27,-0.07 -0.5,-0.28 -0.59,-0.55c-0.09,-0.27 -0.04,-0.56 0.14,-0.79l4.18,-5.02c0.14,-0.18 0.35,-0.29 0.57,-0.31c0.03,0 7.19,0 7.22,0c2.73,-3.29 5.72,-6.77 7.84,-8.89c1.72,-1.72 3.64,-3.02 5.54,-3.97c5.93,-2.95 12.08,-3.03 12.08,-3.03zM44.2,6.8c-1.26,0.07 -5.64,0.39 -10.43,2.77c-1.77,0.89 -3.52,2.06 -5.1,3.63c-2.09,2.09 -5.13,5.63 -7.87,8.94c-0.05,0.1 -0.12,0.19 -0.21,0.26c-3.23,3.9 -6,7.39 -6.17,7.61c0.01,0.1 0.02,0.25 0.13,0.52c0.25,0.62 0.93,1.74 2.56,3.37c1.63,1.63 2.72,2.28 3.35,2.54c0.27,0.11 0.42,0.12 0.52,0.13c0.22,-0.17 3.75,-2.98 7.66,-6.22c0.05,-0.06 0.11,-0.11 0.18,-0.16c3.34,-2.77 6.93,-5.83 8.97,-7.87c1.57,-1.57 2.75,-3.32 3.63,-5.1c2.37,-4.78 2.7,-9.17 2.77,-10.43zM33.42,13.39c2.3,0 4.18,1.88 4.18,4.18c0,2.3 -1.88,4.18 -4.18,4.18c-2.3,0 -4.18,-1.88 -4.18,-4.18c0,-2.3 1.88,-4.18 4.18,-4.18zM33.42,15.07c-1.4,0 -2.51,1.12 -2.51,2.51c0,1.4 1.11,2.51 2.51,2.51c1.4,0 2.51,-1.11 2.51,-2.51c0,-1.39 -1.11,-2.51 -2.51,-2.51zM12.9,22.6l-3.09,3.69l4.55,1.15c0.97,-1.22 2.29,-2.88 3.9,-4.84zM13.6,32.3l-1.07,1.8c0.01,0.03 0,0.02 0.05,0.13c0.15,0.34 0.62,1.09 1.86,2.33c1.24,1.23 1.98,1.71 2.33,1.86c0.11,0.05 0.1,0.05 0.13,0.05l1.8,-1.07c-0.76,-0.48 -1.68,-1.18 -2.8,-2.3c-1.12,-1.12 -1.82,-2.03 -2.3,-2.8zM28.4,32.74c-1.98,1.63 -3.62,2.95 -4.84,3.92l1.15,4.52l3.69,-3.09zM10.33,36.17l0.99,1.31c-1.64,1.26 -2.89,1.54 -3.53,2.17c-0.32,0.31 -0.58,0.72 -0.78,1.57c-0.16,0.65 -0.22,1.82 -0.26,3.03c2.78,-0.11 4.13,-0.56 4.73,-1.18c0.69,-0.7 0.93,-1.92 2.01,-3.4l1.36,0.99c-0.86,1.16 -1.04,2.44 -2.17,3.58c-1.13,1.14 -3.01,1.75 -6.85,1.75h-0.84v-0.84c0,-1.92 0.11,-3.28 0.37,-4.34c0.26,-1.06 0.7,-1.83 1.26,-2.38c1.11,-1.09 2.35,-1.23 3.71,-2.28zM16.77,38.55c-0.03,0.01 -0.08,0.03 -0.08,0.03v0.03z" />
                                            </clipPath>
                                            <path id="Clip 158" fill="#4a90e2" class="shp0" d="M46,5l-0.03,0.89c0,0 -0.12,6.16 -3.06,12.08c-0.95,1.91 -2.24,3.83 -3.95,5.54c-2.08,2.08 -5.58,5.07 -8.89,7.82v7.16c0,0.25 -0.11,0.49 -0.31,0.65l-5.02,4.18c-0.22,0.18 -0.52,0.23 -0.79,0.14c-0.27,-0.09 -0.47,-0.31 -0.55,-0.59l-1.28,-5.07c-0.04,0.03 -0.34,0.26 -0.34,0.26l-0.13,0.11l-0.16,0.05c0,0 -0.29,0.07 -0.58,0.05c-0.09,-0.01 -0.23,-0.03 -0.34,-0.05l-3.06,1.8c0,0 -0.16,0.08 -0.26,0.11c-0.1,0.02 -0.22,0.03 -0.34,0.03c-0.24,-0.01 -0.51,-0.07 -0.84,-0.21c-0.65,-0.29 -1.5,-0.91 -2.82,-2.22c-1.32,-1.32 -1.93,-2.18 -2.22,-2.82c-0.14,-0.32 -0.2,-0.6 -0.21,-0.84c0,-0.12 0,-0.24 0.03,-0.34c0.02,-0.1 0.1,-0.26 0.1,-0.26l1.8,-3.03c-0.03,-0.12 -0.05,-0.26 -0.05,-0.37c-0.02,-0.28 0.05,-0.58 0.05,-0.58l0.05,-0.16l0.1,-0.1c0,0 0.25,-0.32 0.29,-0.37l-5.1,-1.28c-0.27,-0.07 -0.5,-0.28 -0.59,-0.55c-0.09,-0.27 -0.04,-0.56 0.14,-0.79l4.18,-5.02c0.14,-0.18 0.35,-0.29 0.57,-0.31c0.03,0 7.19,0 7.22,0c2.73,-3.29 5.72,-6.77 7.84,-8.89c1.72,-1.72 3.64,-3.02 5.54,-3.97c5.93,-2.95 12.08,-3.03 12.08,-3.03zM44.2,6.8c-1.26,0.07 -5.64,0.39 -10.43,2.77c-1.77,0.89 -3.52,2.06 -5.1,3.63c-2.09,2.09 -5.13,5.63 -7.87,8.94c-0.05,0.1 -0.12,0.19 -0.21,0.26c-3.23,3.9 -6,7.39 -6.17,7.61c0.01,0.1 0.02,0.25 0.13,0.52c0.25,0.62 0.93,1.74 2.56,3.37c1.63,1.63 2.72,2.28 3.35,2.54c0.27,0.11 0.42,0.12 0.52,0.13c0.22,-0.17 3.75,-2.98 7.66,-6.22c0.05,-0.06 0.11,-0.11 0.18,-0.16c3.34,-2.77 6.93,-5.83 8.97,-7.87c1.57,-1.57 2.75,-3.32 3.63,-5.1c2.37,-4.78 2.7,-9.17 2.77,-10.43zM33.42,13.39c2.3,0 4.18,1.88 4.18,4.18c0,2.3 -1.88,4.18 -4.18,4.18c-2.3,0 -4.18,-1.88 -4.18,-4.18c0,-2.3 1.88,-4.18 4.18,-4.18zM33.42,15.07c-1.4,0 -2.51,1.12 -2.51,2.51c0,1.4 1.11,2.51 2.51,2.51c1.4,0 2.51,-1.11 2.51,-2.51c0,-1.39 -1.11,-2.51 -2.51,-2.51zM12.9,22.6l-3.09,3.69l4.55,1.15c0.97,-1.22 2.29,-2.88 3.9,-4.84zM13.6,32.3l-1.07,1.8c0.01,0.03 0,0.02 0.05,0.13c0.15,0.34 0.62,1.09 1.86,2.33c1.24,1.23 1.98,1.71 2.33,1.86c0.11,0.05 0.1,0.05 0.13,0.05l1.8,-1.07c-0.76,-0.48 -1.68,-1.18 -2.8,-2.3c-1.12,-1.12 -1.82,-2.03 -2.3,-2.8zM28.4,32.74c-1.98,1.63 -3.62,2.95 -4.84,3.92l1.15,4.52l3.69,-3.09zM10.33,36.17l0.99,1.31c-1.64,1.26 -2.89,1.54 -3.53,2.17c-0.32,0.31 -0.58,0.72 -0.78,1.57c-0.16,0.65 -0.22,1.82 -0.26,3.03c2.78,-0.11 4.13,-0.56 4.73,-1.18c0.69,-0.7 0.93,-1.92 2.01,-3.4l1.36,0.99c-0.86,1.16 -1.04,2.44 -2.17,3.58c-1.13,1.14 -3.01,1.75 -6.85,1.75h-0.84v-0.84c0,-1.92 0.11,-3.28 0.37,-4.34c0.26,-1.06 0.7,-1.83 1.26,-2.38c1.11,-1.09 2.35,-1.23 3.71,-2.28zM16.77,38.55c-0.03,0.01 -0.08,0.03 -0.08,0.03v0.03z" />
                                            <g id="Mask by Clip 158" clip-path="url(#cp2)">
                                                <path id="Fill 157" class="shp1" fill="#4a90e1" d="M0,51h51v-51h-51z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="desc" id="txt_app_config_price_step2">
                                        2. When your app is<br />
                                        published, your yearly<br />
                                        subscription starts
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 46" width="46" height="46">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp3">
                                                <path d="M38,5c0.83,0 1.5,0.67 1.5,1.5c0,0.83 -0.67,1.5 -1.5,1.5c-0.05,0 -0.09,0 -0.14,0l-6,8.39c0.09,0.19 0.14,0.39 0.14,0.61c0,0.83 -0.67,1.5 -1.5,1.5c-0.8,0 -1.45,-0.62 -1.5,-1.41l-5.18,-2.58c-0.23,0.15 -0.52,0.23 -0.82,0.23c-0.19,0 -0.37,-0.03 -0.54,-0.09l-5.48,4.36c0.01,0.08 0.02,0.15 0.02,0.23c0,0.83 -0.67,1.5 -1.5,1.5c-0.35,0 -0.66,-0.13 -0.91,-0.33l-5.11,2.04c-0.1,0.73 -0.72,1.29 -1.48,1.29c-0.83,0 -1.5,-0.67 -1.5,-1.5c0,-0.83 0.67,-1.5 1.5,-1.5c0.35,0 0.66,0.13 0.91,0.33l5.11,-2.06c0.11,-0.72 0.73,-1.27 1.48,-1.27c0.19,0 0.37,0.03 0.54,0.09l5.48,-4.36c-0.01,-0.08 -0.02,-0.15 -0.02,-0.23c0,-0.83 0.67,-1.5 1.5,-1.5c0.8,0 1.45,0.62 1.5,1.41l5.18,2.58c0.23,-0.15 0.52,-0.23 0.82,-0.23c0.05,0 0.09,0 0.14,0l6,-8.39c-0.08,-0.19 -0.14,-0.39 -0.14,-0.61c0,-0.83 0.67,-1.5 1.5,-1.5zM35,14.75h6v26.25h-6zM36.5,16.25v23.25h3v-23.25zM20,21.5h6v19.5h-6zM21.5,23v16.5h3v-16.5zM27.5,25.25h6v15.75h-6zM29,26.75v12.75h3v-12.75zM12.5,27.5h6v13.5h-6zM14,29v10.5h3v-10.5zM5,30.5h6v10.5h-6zM6.5,32v7.5h3v-7.5z" />
                                            </clipPath>
                                            <path id="Clip 213" fill="#4a90e2" class="shp0" d="M38,5c0.83,0 1.5,0.67 1.5,1.5c0,0.83 -0.67,1.5 -1.5,1.5c-0.05,0 -0.09,0 -0.14,0l-6,8.39c0.09,0.19 0.14,0.39 0.14,0.61c0,0.83 -0.67,1.5 -1.5,1.5c-0.8,0 -1.45,-0.62 -1.5,-1.41l-5.18,-2.58c-0.23,0.15 -0.52,0.23 -0.82,0.23c-0.19,0 -0.37,-0.03 -0.54,-0.09l-5.48,4.36c0.01,0.08 0.02,0.15 0.02,0.23c0,0.83 -0.67,1.5 -1.5,1.5c-0.35,0 -0.66,-0.13 -0.91,-0.33l-5.11,2.04c-0.1,0.73 -0.72,1.29 -1.48,1.29c-0.83,0 -1.5,-0.67 -1.5,-1.5c0,-0.83 0.67,-1.5 1.5,-1.5c0.35,0 0.66,0.13 0.91,0.33l5.11,-2.06c0.11,-0.72 0.73,-1.27 1.48,-1.27c0.19,0 0.37,0.03 0.54,0.09l5.48,-4.36c-0.01,-0.08 -0.02,-0.15 -0.02,-0.23c0,-0.83 0.67,-1.5 1.5,-1.5c0.8,0 1.45,0.62 1.5,1.41l5.18,2.58c0.23,-0.15 0.52,-0.23 0.82,-0.23c0.05,0 0.09,0 0.14,0l6,-8.39c-0.08,-0.19 -0.14,-0.39 -0.14,-0.61c0,-0.83 0.67,-1.5 1.5,-1.5zM35,14.75h6v26.25h-6zM36.5,16.25v23.25h3v-23.25zM20,21.5h6v19.5h-6zM21.5,23v16.5h3v-16.5zM27.5,25.25h6v15.75h-6zM29,26.75v12.75h3v-12.75zM12.5,27.5h6v13.5h-6zM14,29v10.5h3v-10.5zM5,30.5h6v10.5h-6zM6.5,32v7.5h3v-7.5z" />
                                            <g id="Mask by Clip 213" clip-path="url(#cp3)">
                                                <path id="Fill 212" class="shp1" fill="#4a90e1" d="M0,46h46v-46h-46z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="desc" id="txt_app_config_price_step3">
                                        3. Together we make your<br />
                                        app a success
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>

                </div>
                <div class="block3">
                    <div class="block-title">
                        <div class="title bottom-border" id="txt_app_config_what_do_i_get">What do I get for the Setup costs?</div>
                        <div class="desc" id="txt_app_config_what_do_i_get_answer">
                            We know you are busy with your webshop already. So let us do al the heavy lifting that comes with creating a Native Shopping App. We will build, design and test your App. When you are happy with the end result, we help you publish your App in the App Stores.
                        </div>
                    </div>
                    <div class="feature-items">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 41 44" width="41" height="44">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp4">
                                                <path d="M8.75,5c2.06,0 3.75,1.69 3.75,3.75c0,1.8 -1.3,3.33 -3,3.68v15.82c0,2.91 2.34,5.25 5.25,5.25h5.25v-3h1.22l0.21,0.16l3.75,3l0.73,0.59l-0.73,0.59l-3.75,3l-0.21,0.16h-1.22v-3h-5.25c-3.72,0 -6.75,-3.03 -6.75,-6.75v-15.82c-1.71,-0.35 -3,-1.87 -3,-3.68c0,-2.06 1.69,-3.75 3.75,-3.75zM18.78,5h1.22v3h5.25c3.72,0 6.75,3.03 6.75,6.75v15.82c1.71,0.35 3,1.87 3,3.68c0,2.06 -1.69,3.75 -3.75,3.75c-2.06,0 -3.75,-1.69 -3.75,-3.75c0,-1.8 1.29,-3.33 3,-3.68v-15.82c0,-2.91 -2.34,-5.25 -5.25,-5.25h-5.25v3h-1.22l-0.21,-0.16l-3.75,-3l-0.73,-0.59l0.73,-0.59l3.75,-3zM8.75,6.5c-1.25,0 -2.25,1 -2.25,2.25c0,1.25 1,2.25 2.25,2.25c1.25,0 2.25,-1 2.25,-2.25c0,-1.25 -1,-2.25 -2.25,-2.25zM18.5,7.13l-2.02,1.62l2.02,1.62zM31.25,32c-1.25,0 -2.25,1 -2.25,2.25c0,1.25 1,2.25 2.25,2.25c1.25,0 2.25,-1 2.25,-2.25c0,-1.25 -1,-2.25 -2.25,-2.25zM21.5,32.63v3.23l2.02,-1.62z" />
                                            </clipPath>
                                            <path id="Clip 256" class="shp0" fill="#4a90e2" d="M8.75,5c2.06,0 3.75,1.69 3.75,3.75c0,1.8 -1.3,3.33 -3,3.68v15.82c0,2.91 2.34,5.25 5.25,5.25h5.25v-3h1.22l0.21,0.16l3.75,3l0.73,0.59l-0.73,0.59l-3.75,3l-0.21,0.16h-1.22v-3h-5.25c-3.72,0 -6.75,-3.03 -6.75,-6.75v-15.82c-1.71,-0.35 -3,-1.87 -3,-3.68c0,-2.06 1.69,-3.75 3.75,-3.75zM18.78,5h1.22v3h5.25c3.72,0 6.75,3.03 6.75,6.75v15.82c1.71,0.35 3,1.87 3,3.68c0,2.06 -1.69,3.75 -3.75,3.75c-2.06,0 -3.75,-1.69 -3.75,-3.75c0,-1.8 1.29,-3.33 3,-3.68v-15.82c0,-2.91 -2.34,-5.25 -5.25,-5.25h-5.25v3h-1.22l-0.21,-0.16l-3.75,-3l-0.73,-0.59l0.73,-0.59l3.75,-3zM8.75,6.5c-1.25,0 -2.25,1 -2.25,2.25c0,1.25 1,2.25 2.25,2.25c1.25,0 2.25,-1 2.25,-2.25c0,-1.25 -1,-2.25 -2.25,-2.25zM18.5,7.13l-2.02,1.62l2.02,1.62zM31.25,32c-1.25,0 -2.25,1 -2.25,2.25c0,1.25 1,2.25 2.25,2.25c1.25,0 2.25,-1 2.25,-2.25c0,-1.25 -1,-2.25 -2.25,-2.25zM21.5,32.63v3.23l2.02,-1.62z" />
                                            <g id="Mask by Clip 256" clip-path="url(#cp4)">
                                                <path id="Fill 255" fill="#4a90e1" class="shp1" d="M0,43h40v-43h-40z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_integration">
                                        Integration
                                    </div>
                                    <div class="desc" id="txt_app_config_integration_desc">
                                        We will integrate the App with your Backend and make sure that all your products, payment - and shipping methods and so on are synced.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 41" width="42" height="41">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp5">
                                                <path d="M10.96,5l0.46,0.44l9.9,9.9l8.49,-8.49c1.21,-1.2 3.15,-1.21 4.36,0c1.2,1.2 1.2,3.15 0,4.35c0,0 -0.52,0.52 -0.51,0.51l-21.89,21.89c-0.07,0.08 -0.17,0.14 -0.28,0.18l-4.75,1.27c-0.22,0.06 -0.45,-0.01 -0.61,-0.17c-0.16,-0.16 -0.22,-0.39 -0.17,-0.61l1.27,-4.75c0.03,-0.11 0.1,-0.2 0.18,-0.28l7.94,-7.94l-9.9,-9.9l-0.44,-0.46l0.44,-0.46l5.07,-5.07zM10.96,6.8l-4.16,4.16l9.44,9.44l3.35,-3.35l-0.81,-0.81l0.81,-0.81l-0.99,-0.99l-1.45,1.45l-0.91,-0.91l1.45,-1.45l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l0.81,-0.81l-0.99,-0.99l-1.45,1.44l-0.91,-0.91l1.45,-1.44l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l0.81,-0.81zM30.18,8.25l-21.77,21.78l2.57,2.57l21.77,-21.78zM26.2,20.24l9.36,9.34l0.44,0.46l-0.44,0.46l-5.07,5.07l-0.46,0.44l-0.46,-0.44l-9.34,-9.36l0.89,-0.89l8.91,8.89l4.16,-4.16l-0.4,-0.4l-0.81,0.81l-0.91,-0.91l0.81,-0.81l-0.99,-0.99l-1.45,1.45l-0.91,-0.91l1.44,-1.45l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l0.81,-0.81l-0.99,-0.99l-1.45,1.45l-0.91,-0.91l1.45,-1.45l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l1.27,-1.27z" />
                                            </clipPath>
                                            <path id="Clip 259" fill="#4a90e2" class="shp0" d="M10.96,5l0.46,0.44l9.9,9.9l8.49,-8.49c1.21,-1.2 3.15,-1.21 4.36,0c1.2,1.2 1.2,3.15 0,4.35c0,0 -0.52,0.52 -0.51,0.51l-21.89,21.89c-0.07,0.08 -0.17,0.14 -0.28,0.18l-4.75,1.27c-0.22,0.06 -0.45,-0.01 -0.61,-0.17c-0.16,-0.16 -0.22,-0.39 -0.17,-0.61l1.27,-4.75c0.03,-0.11 0.1,-0.2 0.18,-0.28l7.94,-7.94l-9.9,-9.9l-0.44,-0.46l0.44,-0.46l5.07,-5.07zM10.96,6.8l-4.16,4.16l9.44,9.44l3.35,-3.35l-0.81,-0.81l0.81,-0.81l-0.99,-0.99l-1.45,1.45l-0.91,-0.91l1.45,-1.45l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l0.81,-0.81l-0.99,-0.99l-1.45,1.44l-0.91,-0.91l1.45,-1.44l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l0.81,-0.81zM30.18,8.25l-21.77,21.78l2.57,2.57l21.77,-21.78zM26.2,20.24l9.36,9.34l0.44,0.46l-0.44,0.46l-5.07,5.07l-0.46,0.44l-0.46,-0.44l-9.34,-9.36l0.89,-0.89l8.91,8.89l4.16,-4.16l-0.4,-0.4l-0.81,0.81l-0.91,-0.91l0.81,-0.81l-0.99,-0.99l-1.45,1.45l-0.91,-0.91l1.44,-1.45l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l0.81,-0.81l-0.99,-0.99l-1.45,1.45l-0.91,-0.91l1.45,-1.45l-0.99,-0.99l-0.81,0.81l-0.91,-0.91l1.27,-1.27z" />
                                            <g id="Mask by Clip 259" clip-path="url(#cp5)">
                                                <path id="Fill 258" fill="#4a90e1" class="shp1" d="M0,41h41v-41h-41z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_design">
                                        Design
                                    </div>
                                    <div class="desc" id="txt_app_config_design_desc">
                                        We will design your App in the same style as your online shop. This way you can give your customers the same branding/ experience.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 82 35" width="82" height="35">
                                            <path id="Shape" fill="#4a90e2" class="shp0" d="M8.31,0.99l1.58,2.34c1.11,-0.4 2.34,-0.61 3.61,-0.61c1.27,0 2.49,0.21 3.61,0.61l1.58,-2.34c0.09,-0.15 0.25,-0.26 0.42,-0.3c0.27,-0.06 0.56,0.05 0.71,0.29c0.16,0.23 0.15,0.54 -0.02,0.77l-1.43,2.15c2.44,1.28 4.18,3.5 4.58,6.14c0,0.01 0,0.01 0,0.02c0,0.01 0,0.01 0,0.02c0.01,0.06 0.01,0.13 0,0.19v0.93c0.4,-0.23 0.85,-0.38 1.35,-0.38c1.48,0 2.7,1.22 2.7,2.7v8.1c0,1.48 -1.22,2.7 -2.7,2.7c-0.5,0 -0.95,-0.15 -1.35,-0.38v1.73c0,1.11 -0.91,2.02 -2.03,2.02h-0.68v4.05c0,1.48 -1.22,2.7 -2.7,2.7c-1.48,0 -2.7,-1.22 -2.7,-2.7v-4.05h-2.7v4.05c0,1.48 -1.22,2.7 -2.7,2.7c-1.48,0 -2.7,-1.22 -2.7,-2.7v-4.05h-0.68c-1.11,0 -2.02,-0.92 -2.02,-2.02v-1.73c-0.4,0.23 -0.85,0.38 -1.35,0.38c-1.48,0 -2.7,-1.22 -2.7,-2.7v-8.1c0,-1.48 1.22,-2.7 2.7,-2.7c0.5,0 0.95,0.15 1.35,0.38v-0.95c0,-0.03 0,-0.07 0,-0.11c0,-0.01 0,-0.03 0,-0.04c0,-0.02 0,-0.04 0,-0.06c0,-0.01 0,-0.01 0,-0.02c0,-0.01 0,-0.01 0,-0.02c0.41,-2.62 2.15,-4.82 4.58,-6.1l-1.43,-2.15c-0.13,-0.18 -0.17,-0.42 -0.08,-0.64c0.08,-0.21 0.26,-0.37 0.49,-0.42c0.03,-0.01 0.06,-0.02 0.08,-0.02c0.25,-0.02 0.5,0.1 0.63,0.32zM9.98,4.77c-0.04,0.03 -0.08,0.05 -0.13,0.06c-2.07,0.93 -3.57,2.64 -4.16,4.64h15.61c-0.59,-2.02 -2.13,-3.76 -4.24,-4.68c-0.02,-0.01 -0.04,-0.01 -0.06,-0.02c-1.04,-0.45 -2.23,-0.7 -3.5,-0.7c-1.28,0 -2.47,0.24 -3.52,0.7zM10.8,7.11c0,0.56 -0.45,1.01 -1.01,1.01c-0.56,0 -1.01,-0.45 -1.01,-1.01c0,-0.56 0.45,-1.01 1.01,-1.01c0.56,0 1.01,0.45 1.01,1.01zM18.22,7.11c0,0.56 -0.45,1.01 -1.01,1.01c-0.56,0 -1.01,-0.45 -1.01,-1.01c0,-0.56 0.45,-1.01 1.01,-1.01c0.56,0 1.01,0.45 1.01,1.01zM5.4,25.67c0,0.38 0.3,0.67 0.67,0.67h1.2c0.09,-0.02 0.18,-0.02 0.27,0h7.83c0.09,-0.02 0.19,-0.02 0.27,0h5.27c0.38,0 0.67,-0.3 0.67,-0.67v-14.85h-16.2zM1.35,13.52v8.1c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-8.1c0,-0.75 -0.6,-1.35 -1.35,-1.35c-0.75,0 -1.35,0.6 -1.35,1.35zM22.95,13.52v8.1c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-8.1c0,-0.75 -0.6,-1.35 -1.35,-1.35c-0.75,0 -1.35,0.6 -1.35,1.35zM8.1,31.75c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-4.05h-2.7zM16.2,31.75c0,0.75 0.6,1.35 1.35,1.35c0.75,0 1.35,-0.6 1.35,-1.35v-4.05h-2.7z" />
                                            <path id="Shape" fill="none" stroke="#4a90e2" stroke-width="1.2" class="shp1" d="M74.08,4c-1.68,0.1 -3.6,1.03 -4.74,2.22c-1.03,1.08 -1.85,2.69 -1.52,4.24c1.82,0.05 3.65,-0.91 4.74,-2.12c1.02,-1.13 1.8,-2.71 1.52,-4.34zM74.18,10.46c-2.63,0 -3.73,1.62 -5.55,1.62c-1.87,0 -3.59,-1.51 -5.85,-1.51c-3.08,0 -7.77,2.86 -7.77,9.59c0,6.12 5.55,12.92 8.68,12.92c1.9,0.02 2.36,-1.2 4.95,-1.21c2.58,-0.02 3.14,1.23 5.05,1.21c2.15,-0.02 3.82,-2.37 5.05,-4.24c0.88,-1.34 1.24,-2.02 1.92,-3.53c-5.05,-1.28 -6.05,-9.42 0,-11.1c-1.14,-1.95 -4.48,-3.74 -6.46,-3.74z" />
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_publish">
                                        App Stores Publish
                                    </div>
                                    <div class="desc" id="txt_app_config_publish_desc">
                                        When your App is ready, we will help you publish the App in the Google Play and Apple AppStore.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 49 49" width="49" height="49">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp6">
                                                <path d="M24.5,5c0.43,0 0.87,0.14 1.23,0.43l2.97,2.4c0.14,0.12 0.33,0.16 0.51,0.14l3.77,-0.6c0.9,-0.14 1.79,0.37 2.12,1.23l1.37,3.57c0.07,0.17 0.2,0.31 0.37,0.37l3.56,1.37c0.85,0.33 1.37,1.22 1.23,2.12l-0.6,3.77c-0.03,0.18 0.02,0.37 0.14,0.51l2.4,2.97c0.57,0.71 0.57,1.74 0,2.45l-2.4,2.97c-0.12,0.14 -0.16,0.33 -0.14,0.51l0.6,3.77c0.14,0.9 -0.37,1.79 -1.23,2.12l-3.56,1.37c-0.17,0.07 -0.31,0.2 -0.38,0.37l-1.37,3.57c-0.33,0.85 -1.22,1.37 -2.12,1.23l-3.77,-0.6c-0.18,-0.03 -0.37,0.02 -0.51,0.14l-2.97,2.4c-0.36,0.29 -0.79,0.43 -1.22,0.43c-0.43,0 -0.87,-0.14 -1.23,-0.43l-2.97,-2.4c-0.14,-0.12 -0.33,-0.16 -0.51,-0.14l-3.77,0.6c-0.9,0.14 -1.79,-0.37 -2.12,-1.23l-1.37,-3.56c-0.07,-0.17 -0.2,-0.31 -0.37,-0.37l-3.57,-1.37c-0.85,-0.33 -1.37,-1.22 -1.23,-2.12l0.6,-3.77c0.03,-0.18 -0.02,-0.37 -0.14,-0.51l-2.4,-2.97c-0.57,-0.71 -0.57,-1.74 0,-2.45l2.4,-2.97c0.12,-0.14 0.16,-0.33 0.14,-0.51l-0.6,-3.77c-0.14,-0.9 0.38,-1.79 1.23,-2.12l3.57,-1.37c0.17,-0.07 0.31,-0.2 0.37,-0.37l1.37,-3.57c0.33,-0.85 1.22,-1.37 2.12,-1.23l3.77,0.6c0.18,0.03 0.37,-0.02 0.51,-0.14l2.97,-2.4c0.35,-0.29 0.79,-0.43 1.23,-0.43zM24.5,6.29c-0.14,0 -0.29,0.05 -0.41,0.14l-2.97,2.41c-0.43,0.34 -0.98,0.49 -1.53,0.41l-3.77,-0.6c-0.3,-0.05 -0.6,0.12 -0.71,0.41l-1.37,3.57c-0.2,0.51 -0.6,0.92 -1.12,1.12l-3.57,1.37c-0.28,0.11 -0.46,0.4 -0.41,0.71l0.6,3.77c0.02,0.11 0.02,0.26 0.02,0.37h4.82c0.84,0 1.59,0.28 2.16,0.8l3.94,-8.67c0.1,-0.23 0.34,-0.38 0.59,-0.38h0.13c0.96,0 1.86,0.44 2.45,1.2c0.59,0.76 0.8,1.73 0.57,2.67l-1.09,4.39h7.79c1.26,0 2.29,0.98 2.29,2.25v0.13c0,0.74 -0.33,1.4 -0.85,1.85c0.52,0.44 0.85,1.11 0.85,1.84c0,0.93 -0.52,1.74 -1.31,2.15c0.3,0.39 0.49,0.88 0.49,1.41v0.3c0,0.74 -0.36,1.42 -0.93,1.84c0.34,0.39 0.52,0.88 0.52,1.39c0,1.12 -0.88,2.06 -2,2.12l-10.67,0.67c-0.12,0.01 -0.25,0.01 -0.39,0.01c-0.59,0 -1.35,-0.06 -1.83,-0.2c-0.09,-0.02 -0.16,-0.06 -0.23,-0.12c-0.59,0.72 -1.48,1.19 -2.49,1.19h-0.17l1.2,3.12c0.11,0.28 0.41,0.46 0.71,0.41l3.77,-0.6c0.1,-0.02 0.2,-0.03 0.3,-0.03c0.44,0 0.88,0.15 1.23,0.43l2.97,2.4c0.24,0.19 0.58,0.19 0.82,0l2.97,-2.4c0.43,-0.34 0.99,-0.49 1.53,-0.41l3.77,0.6c0.3,0.05 0.6,-0.12 0.71,-0.41l1.37,-3.57c0.2,-0.51 0.6,-0.92 1.12,-1.12l3.57,-1.37c0.28,-0.11 0.46,-0.41 0.41,-0.71l-0.6,-3.77c-0.09,-0.54 0.06,-1.1 0.41,-1.53l2.41,-2.97c0.19,-0.24 0.19,-0.58 0,-0.82l-2.41,-2.97c-0.34,-0.43 -0.49,-0.98 -0.41,-1.53l0.6,-3.77c0.05,-0.3 -0.12,-0.6 -0.41,-0.71l-3.57,-1.37c-0.51,-0.2 -0.92,-0.6 -1.12,-1.12l-1.37,-3.57c-0.11,-0.28 -0.41,-0.46 -0.71,-0.41l-3.77,0.6c-0.54,0.09 -1.1,-0.07 -1.53,-0.41l-2.97,-2.41c-0.12,-0.1 -0.26,-0.14 -0.41,-0.14zM27.76,11.34c0.08,-0.01 0.17,-0.01 0.26,0.02c0.34,0.09 0.55,0.45 0.46,0.79l-0.33,1.25c-0.08,0.29 -0.34,0.48 -0.63,0.48c-0.05,0 -0.11,-0.01 -0.17,-0.02c-0.35,-0.09 -0.55,-0.45 -0.46,-0.79l0.34,-1.25c0.07,-0.26 0.29,-0.44 0.53,-0.47zM31.06,12.63c0.08,0.01 0.16,0.04 0.24,0.08c0.31,0.18 0.42,0.58 0.24,0.89l-0.65,1.12c-0.12,0.21 -0.34,0.32 -0.56,0.32c-0.11,0 -0.22,-0.03 -0.32,-0.09c-0.31,-0.18 -0.42,-0.58 -0.24,-0.89l0.65,-1.12c0.13,-0.23 0.39,-0.35 0.64,-0.32zM21.19,13.03l-3.92,8.63c-0.05,0.1 -0.12,0.18 -0.2,0.24c0.16,0.39 0.26,0.82 0.26,1.26v10.41c0,0.32 -0.06,0.63 -0.15,0.92c0.44,0.11 1.29,0.17 1.76,0.13l10.67,-0.67c0.44,-0.03 0.78,-0.39 0.78,-0.83c0,-0.22 -0.09,-0.43 -0.25,-0.59c-0.16,-0.15 -0.37,-0.24 -0.59,-0.24h-0.16c-0.33,0 -0.61,-0.25 -0.64,-0.58c-0.04,-0.33 0.19,-0.63 0.52,-0.7l0.73,-0.14c0.46,-0.09 0.8,-0.5 0.8,-0.97v-0.3c0,-0.55 -0.45,-0.99 -0.99,-0.99c-0.34,0 -0.62,-0.26 -0.65,-0.59c-0.03,-0.34 0.21,-0.64 0.54,-0.69l0.98,-0.16c0.55,-0.09 0.94,-0.56 0.94,-1.11c0,-0.62 -0.51,-1.13 -1.13,-1.13h-0.69c-0.34,0 -0.62,-0.26 -0.65,-0.59c-0.03,-0.34 0.21,-0.64 0.54,-0.7l0.98,-0.16c0.55,-0.09 0.94,-0.56 0.94,-1.11v-0.13c0,-0.55 -0.45,-1 -0.99,-1h-9.85c-0.36,0 -0.65,-0.29 -0.65,-0.65c0,-0.36 0.29,-0.65 0.65,-0.65h0.73l1.17,-4.66c0.14,-0.55 0.02,-1.11 -0.33,-1.56c-0.29,-0.37 -0.7,-0.61 -1.15,-0.68zM33.66,14.69c0.17,0 0.33,0.06 0.46,0.19c0.26,0.25 0.26,0.66 0,0.91l-0.91,0.92c-0.13,0.12 -0.29,0.19 -0.46,0.19c-0.17,0 -0.33,-0.06 -0.46,-0.19c-0.25,-0.26 -0.25,-0.66 0,-0.92l0.92,-0.91c0.13,-0.13 0.29,-0.19 0.46,-0.19zM35.65,17.37c0.25,-0.03 0.51,0.09 0.64,0.32c0.18,0.31 0.07,0.71 -0.24,0.88l-1.12,0.65c-0.1,0.06 -0.21,0.09 -0.32,0.09c-0.23,0 -0.44,-0.12 -0.56,-0.32c-0.18,-0.31 -0.07,-0.71 0.24,-0.88l1.12,-0.65c0.08,-0.05 0.16,-0.07 0.24,-0.08zM37.11,20.5c0.25,0.04 0.47,0.22 0.54,0.48c0.09,0.35 -0.11,0.7 -0.46,0.79l-1.25,0.34c-0.06,0.02 -0.11,0.02 -0.17,0.02c-0.29,0 -0.55,-0.19 -0.63,-0.48c-0.09,-0.34 0.11,-0.7 0.46,-0.79l1.25,-0.33c0.09,-0.03 0.17,-0.03 0.26,-0.02zM8.8,21.18l-2.36,2.91c-0.19,0.24 -0.19,0.58 0,0.82l2.41,2.97c0.34,0.43 0.5,0.98 0.41,1.53l-0.6,3.77c-0.05,0.3 0.12,0.59 0.41,0.71l3.57,1.37c0.18,0.07 0.33,0.17 0.48,0.28c0.03,-0.01 0.06,-0.02 0.09,-0.02h0.89c1.07,0 1.94,-0.87 1.94,-1.94v-10.41c0,-1.07 -0.87,-1.94 -1.94,-1.94h-5.12c-0.06,0 -0.12,-0.02 -0.17,-0.04zM36.17,23.85h1.3c0.36,0 0.65,0.29 0.65,0.65c0,0.36 -0.29,0.65 -0.65,0.65h-1.3c-0.36,0 -0.65,-0.29 -0.65,-0.65c0,-0.36 0.29,-0.65 0.65,-0.65z" />
                                            </clipPath>
                                            <path id="Clip 285" fill="#4a90e2" class="shp0" d="M24.5,5c0.43,0 0.87,0.14 1.23,0.43l2.97,2.4c0.14,0.12 0.33,0.16 0.51,0.14l3.77,-0.6c0.9,-0.14 1.79,0.37 2.12,1.23l1.37,3.57c0.07,0.17 0.2,0.31 0.37,0.37l3.56,1.37c0.85,0.33 1.37,1.22 1.23,2.12l-0.6,3.77c-0.03,0.18 0.02,0.37 0.14,0.51l2.4,2.97c0.57,0.71 0.57,1.74 0,2.45l-2.4,2.97c-0.12,0.14 -0.16,0.33 -0.14,0.51l0.6,3.77c0.14,0.9 -0.37,1.79 -1.23,2.12l-3.56,1.37c-0.17,0.07 -0.31,0.2 -0.38,0.37l-1.37,3.57c-0.33,0.85 -1.22,1.37 -2.12,1.23l-3.77,-0.6c-0.18,-0.03 -0.37,0.02 -0.51,0.14l-2.97,2.4c-0.36,0.29 -0.79,0.43 -1.22,0.43c-0.43,0 -0.87,-0.14 -1.23,-0.43l-2.97,-2.4c-0.14,-0.12 -0.33,-0.16 -0.51,-0.14l-3.77,0.6c-0.9,0.14 -1.79,-0.37 -2.12,-1.23l-1.37,-3.56c-0.07,-0.17 -0.2,-0.31 -0.37,-0.37l-3.57,-1.37c-0.85,-0.33 -1.37,-1.22 -1.23,-2.12l0.6,-3.77c0.03,-0.18 -0.02,-0.37 -0.14,-0.51l-2.4,-2.97c-0.57,-0.71 -0.57,-1.74 0,-2.45l2.4,-2.97c0.12,-0.14 0.16,-0.33 0.14,-0.51l-0.6,-3.77c-0.14,-0.9 0.38,-1.79 1.23,-2.12l3.57,-1.37c0.17,-0.07 0.31,-0.2 0.37,-0.37l1.37,-3.57c0.33,-0.85 1.22,-1.37 2.12,-1.23l3.77,0.6c0.18,0.03 0.37,-0.02 0.51,-0.14l2.97,-2.4c0.35,-0.29 0.79,-0.43 1.23,-0.43zM24.5,6.29c-0.14,0 -0.29,0.05 -0.41,0.14l-2.97,2.41c-0.43,0.34 -0.98,0.49 -1.53,0.41l-3.77,-0.6c-0.3,-0.05 -0.6,0.12 -0.71,0.41l-1.37,3.57c-0.2,0.51 -0.6,0.92 -1.12,1.12l-3.57,1.37c-0.28,0.11 -0.46,0.4 -0.41,0.71l0.6,3.77c0.02,0.11 0.02,0.26 0.02,0.37h4.82c0.84,0 1.59,0.28 2.16,0.8l3.94,-8.67c0.1,-0.23 0.34,-0.38 0.59,-0.38h0.13c0.96,0 1.86,0.44 2.45,1.2c0.59,0.76 0.8,1.73 0.57,2.67l-1.09,4.39h7.79c1.26,0 2.29,0.98 2.29,2.25v0.13c0,0.74 -0.33,1.4 -0.85,1.85c0.52,0.44 0.85,1.11 0.85,1.84c0,0.93 -0.52,1.74 -1.31,2.15c0.3,0.39 0.49,0.88 0.49,1.41v0.3c0,0.74 -0.36,1.42 -0.93,1.84c0.34,0.39 0.52,0.88 0.52,1.39c0,1.12 -0.88,2.06 -2,2.12l-10.67,0.67c-0.12,0.01 -0.25,0.01 -0.39,0.01c-0.59,0 -1.35,-0.06 -1.83,-0.2c-0.09,-0.02 -0.16,-0.06 -0.23,-0.12c-0.59,0.72 -1.48,1.19 -2.49,1.19h-0.17l1.2,3.12c0.11,0.28 0.41,0.46 0.71,0.41l3.77,-0.6c0.1,-0.02 0.2,-0.03 0.3,-0.03c0.44,0 0.88,0.15 1.23,0.43l2.97,2.4c0.24,0.19 0.58,0.19 0.82,0l2.97,-2.4c0.43,-0.34 0.99,-0.49 1.53,-0.41l3.77,0.6c0.3,0.05 0.6,-0.12 0.71,-0.41l1.37,-3.57c0.2,-0.51 0.6,-0.92 1.12,-1.12l3.57,-1.37c0.28,-0.11 0.46,-0.41 0.41,-0.71l-0.6,-3.77c-0.09,-0.54 0.06,-1.1 0.41,-1.53l2.41,-2.97c0.19,-0.24 0.19,-0.58 0,-0.82l-2.41,-2.97c-0.34,-0.43 -0.49,-0.98 -0.41,-1.53l0.6,-3.77c0.05,-0.3 -0.12,-0.6 -0.41,-0.71l-3.57,-1.37c-0.51,-0.2 -0.92,-0.6 -1.12,-1.12l-1.37,-3.57c-0.11,-0.28 -0.41,-0.46 -0.71,-0.41l-3.77,0.6c-0.54,0.09 -1.1,-0.07 -1.53,-0.41l-2.97,-2.41c-0.12,-0.1 -0.26,-0.14 -0.41,-0.14zM27.76,11.34c0.08,-0.01 0.17,-0.01 0.26,0.02c0.34,0.09 0.55,0.45 0.46,0.79l-0.33,1.25c-0.08,0.29 -0.34,0.48 -0.63,0.48c-0.05,0 -0.11,-0.01 -0.17,-0.02c-0.35,-0.09 -0.55,-0.45 -0.46,-0.79l0.34,-1.25c0.07,-0.26 0.29,-0.44 0.53,-0.47zM31.06,12.63c0.08,0.01 0.16,0.04 0.24,0.08c0.31,0.18 0.42,0.58 0.24,0.89l-0.65,1.12c-0.12,0.21 -0.34,0.32 -0.56,0.32c-0.11,0 -0.22,-0.03 -0.32,-0.09c-0.31,-0.18 -0.42,-0.58 -0.24,-0.89l0.65,-1.12c0.13,-0.23 0.39,-0.35 0.64,-0.32zM21.19,13.03l-3.92,8.63c-0.05,0.1 -0.12,0.18 -0.2,0.24c0.16,0.39 0.26,0.82 0.26,1.26v10.41c0,0.32 -0.06,0.63 -0.15,0.92c0.44,0.11 1.29,0.17 1.76,0.13l10.67,-0.67c0.44,-0.03 0.78,-0.39 0.78,-0.83c0,-0.22 -0.09,-0.43 -0.25,-0.59c-0.16,-0.15 -0.37,-0.24 -0.59,-0.24h-0.16c-0.33,0 -0.61,-0.25 -0.64,-0.58c-0.04,-0.33 0.19,-0.63 0.52,-0.7l0.73,-0.14c0.46,-0.09 0.8,-0.5 0.8,-0.97v-0.3c0,-0.55 -0.45,-0.99 -0.99,-0.99c-0.34,0 -0.62,-0.26 -0.65,-0.59c-0.03,-0.34 0.21,-0.64 0.54,-0.69l0.98,-0.16c0.55,-0.09 0.94,-0.56 0.94,-1.11c0,-0.62 -0.51,-1.13 -1.13,-1.13h-0.69c-0.34,0 -0.62,-0.26 -0.65,-0.59c-0.03,-0.34 0.21,-0.64 0.54,-0.7l0.98,-0.16c0.55,-0.09 0.94,-0.56 0.94,-1.11v-0.13c0,-0.55 -0.45,-1 -0.99,-1h-9.85c-0.36,0 -0.65,-0.29 -0.65,-0.65c0,-0.36 0.29,-0.65 0.65,-0.65h0.73l1.17,-4.66c0.14,-0.55 0.02,-1.11 -0.33,-1.56c-0.29,-0.37 -0.7,-0.61 -1.15,-0.68zM33.66,14.69c0.17,0 0.33,0.06 0.46,0.19c0.26,0.25 0.26,0.66 0,0.91l-0.91,0.92c-0.13,0.12 -0.29,0.19 -0.46,0.19c-0.17,0 -0.33,-0.06 -0.46,-0.19c-0.25,-0.26 -0.25,-0.66 0,-0.92l0.92,-0.91c0.13,-0.13 0.29,-0.19 0.46,-0.19zM35.65,17.37c0.25,-0.03 0.51,0.09 0.64,0.32c0.18,0.31 0.07,0.71 -0.24,0.88l-1.12,0.65c-0.1,0.06 -0.21,0.09 -0.32,0.09c-0.23,0 -0.44,-0.12 -0.56,-0.32c-0.18,-0.31 -0.07,-0.71 0.24,-0.88l1.12,-0.65c0.08,-0.05 0.16,-0.07 0.24,-0.08zM37.11,20.5c0.25,0.04 0.47,0.22 0.54,0.48c0.09,0.35 -0.11,0.7 -0.46,0.79l-1.25,0.34c-0.06,0.02 -0.11,0.02 -0.17,0.02c-0.29,0 -0.55,-0.19 -0.63,-0.48c-0.09,-0.34 0.11,-0.7 0.46,-0.79l1.25,-0.33c0.09,-0.03 0.17,-0.03 0.26,-0.02zM8.8,21.18l-2.36,2.91c-0.19,0.24 -0.19,0.58 0,0.82l2.41,2.97c0.34,0.43 0.5,0.98 0.41,1.53l-0.6,3.77c-0.05,0.3 0.12,0.59 0.41,0.71l3.57,1.37c0.18,0.07 0.33,0.17 0.48,0.28c0.03,-0.01 0.06,-0.02 0.09,-0.02h0.89c1.07,0 1.94,-0.87 1.94,-1.94v-10.41c0,-1.07 -0.87,-1.94 -1.94,-1.94h-5.12c-0.06,0 -0.12,-0.02 -0.17,-0.04zM36.17,23.85h1.3c0.36,0 0.65,0.29 0.65,0.65c0,0.36 -0.29,0.65 -0.65,0.65h-1.3c-0.36,0 -0.65,-0.29 -0.65,-0.65c0,-0.36 0.29,-0.65 0.65,-0.65z" />
                                            <g id="Mask by Clip 285" clip-path="url(#cp6)">
                                                <path id="Fill 284" class="shp1" fill="#4a90e1" d="M0,49h49v-49h-49z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_flying">
                                        Flying start
                                    </div>
                                    <div class="desc" id="txt_app_config_flying_desc">
                                        To make your App a big success, we will provide you with loads of App marketing Tips and best practices. This way you will have a flying start!
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="block4">
                    <div class="block-title">
                        <div class="title bottom-border" id="txt_app_config_subscription_fee_question">What do I get for my subscription fee?</div>
                        <div class="desc" id="txt_app_config_subscription_fee_answer">
                            Your annual fee is not just a subscription payment, you will get a lot in return! You will receive the best support, help with your App Marketing and you will have full access to your own App through our user friendly platform. Our platform allows you to adjust or change elements of your App all by yourself and whenever you want!
                        </div>
                    </div>
                    <div class="feature-items">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 50" width="47" height="50">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp41">
                                                <path d="M22.88,6h1.23c7.61,0 13.84,6.03 14.17,13.57h0.63c1.7,0 3.08,1.38 3.08,3.08v8.63c0,1.7 -1.38,3.08 -3.08,3.08v1.23c0,3.74 -3.04,6.78 -6.78,6.78h-3.79c-0.28,1.06 -1.23,1.85 -2.38,1.85h-1.23c-1.36,0 -2.47,-1.11 -2.47,-2.47c0,-1.36 1.11,-2.47 2.47,-2.47h1.23c1.15,0 2.1,0.79 2.38,1.85h3.79c3.06,0 5.55,-2.49 5.55,-5.55v-1.23h-0.73c-0.25,0.72 -0.93,1.23 -1.73,1.23c-1.02,0 -1.85,-0.83 -1.85,-1.85v-13.57c0,-1.02 0.83,-1.85 1.85,-1.85c0.8,0 1.48,0.52 1.73,1.23h0.08c-0.13,-2.78 -1.13,-5.32 -2.75,-7.38l-2.78,2.75c-0.91,0.89 -2.32,0.96 -3.29,0.16c-1.32,-1.1 -3,-1.7 -4.72,-1.7c-1.73,0 -3.41,0.61 -4.73,1.71c-0.45,0.38 -1,0.56 -1.55,0.56c-0.62,0 -1.25,-0.24 -1.73,-0.71l-2.79,-2.74c-1.6,2.05 -2.6,4.58 -2.73,7.35h0.08c0.26,-0.72 0.94,-1.23 1.74,-1.23c1.02,0 1.85,0.83 1.85,1.85v13.57c0,1.02 -0.83,1.85 -1.85,1.85c-0.8,0 -1.48,-0.52 -1.74,-1.23h-1.96c-1.7,0 -3.08,-1.38 -3.08,-3.08v-8.63c0,-1.7 1.38,-3.08 3.08,-3.08h0.63c0.32,-7.53 6.55,-13.57 14.17,-13.57zM22.88,7.23c-3.69,0 -7.02,1.56 -9.38,4.05l2.85,2.79c0.45,0.45 1.15,0.48 1.63,0.08c1.55,-1.29 3.51,-1.99 5.52,-1.99c2.01,0 3.96,0.71 5.5,1.98c0.48,0.39 1.18,0.36 1.63,-0.09l2.84,-2.81c-2.36,-2.47 -5.68,-4.02 -9.36,-4.02zM23.5,8.47c0.34,0 0.62,0.27 0.62,0.62v1.23c0,0.34 -0.27,0.62 -0.62,0.62c-0.34,0 -0.62,-0.27 -0.62,-0.62v-1.23c0,-0.34 0.27,-0.62 0.62,-0.62zM26.85,8.92c0.08,-0.01 0.16,-0.01 0.25,0.01c0.33,0.09 0.53,0.43 0.44,0.76l-0.32,1.19c-0.07,0.28 -0.33,0.46 -0.6,0.46c-0.05,0 -0.11,-0.01 -0.16,-0.02c-0.33,-0.09 -0.52,-0.43 -0.44,-0.76l0.32,-1.19c0.07,-0.25 0.27,-0.42 0.51,-0.45zM20.15,8.92c0.24,0.03 0.44,0.2 0.51,0.45l0.32,1.19c0.09,0.33 -0.11,0.67 -0.44,0.76c-0.06,0.01 -0.11,0.02 -0.16,0.02c-0.27,0 -0.52,-0.18 -0.6,-0.46l-0.32,-1.19c-0.09,-0.33 0.11,-0.67 0.44,-0.76c0.08,-0.02 0.17,-0.03 0.25,-0.01zM16.79,10.25c0.24,-0.03 0.48,0.08 0.61,0.31l0.62,1.06c0.17,0.3 0.07,0.67 -0.22,0.85c-0.1,0.06 -0.2,0.08 -0.31,0.08c-0.21,0 -0.42,-0.11 -0.53,-0.31l-0.62,-1.06c-0.17,-0.3 -0.07,-0.67 0.22,-0.84c0.07,-0.04 0.15,-0.07 0.23,-0.08zM30.21,10.25c0.08,0.01 0.16,0.03 0.23,0.08c0.29,0.17 0.39,0.55 0.22,0.84l-0.62,1.07c-0.11,0.2 -0.32,0.31 -0.53,0.31c-0.11,0 -0.21,-0.03 -0.31,-0.08c-0.3,-0.17 -0.39,-0.55 -0.22,-0.84l0.62,-1.07c0.13,-0.22 0.37,-0.33 0.61,-0.3zM11.78,19.57c-0.34,0 -0.62,0.28 -0.62,0.62v13.57c0,0.34 0.28,0.62 0.62,0.62c0.34,0 0.62,-0.28 0.62,-0.62v-13.57c0,-0.34 -0.28,-0.62 -0.62,-0.62zM35.22,19.57c-0.34,0 -0.62,0.28 -0.62,0.62v13.57c0,0.34 0.28,0.62 0.62,0.62c0.34,0 0.62,-0.28 0.62,-0.62v-13.57c0,-0.34 -0.28,-0.62 -0.62,-0.62zM8.08,20.8c-1.02,0 -1.85,0.83 -1.85,1.85v8.63c0,1.02 0.83,1.85 1.85,1.85h1.85v-12.33zM37.07,20.8v12.33h1.85c1.02,0 1.85,-0.83 1.85,-1.85v-8.63c0,-1.02 -0.83,-1.85 -1.85,-1.85zM24.73,40.53c-0.68,0 -1.23,0.55 -1.23,1.23c0,0.68 0.55,1.23 1.23,1.23h1.23c0.68,0 1.23,-0.55 1.23,-1.23c0,-0.68 -0.55,-1.23 -1.23,-1.23z" />
                                            </clipPath>
                                            <path id="Clip 353" fill="#4a90e2" class="shp0" d="M22.88,6h1.23c7.61,0 13.84,6.03 14.17,13.57h0.63c1.7,0 3.08,1.38 3.08,3.08v8.63c0,1.7 -1.38,3.08 -3.08,3.08v1.23c0,3.74 -3.04,6.78 -6.78,6.78h-3.79c-0.28,1.06 -1.23,1.85 -2.38,1.85h-1.23c-1.36,0 -2.47,-1.11 -2.47,-2.47c0,-1.36 1.11,-2.47 2.47,-2.47h1.23c1.15,0 2.1,0.79 2.38,1.85h3.79c3.06,0 5.55,-2.49 5.55,-5.55v-1.23h-0.73c-0.25,0.72 -0.93,1.23 -1.73,1.23c-1.02,0 -1.85,-0.83 -1.85,-1.85v-13.57c0,-1.02 0.83,-1.85 1.85,-1.85c0.8,0 1.48,0.52 1.73,1.23h0.08c-0.13,-2.78 -1.13,-5.32 -2.75,-7.38l-2.78,2.75c-0.91,0.89 -2.32,0.96 -3.29,0.16c-1.32,-1.1 -3,-1.7 -4.72,-1.7c-1.73,0 -3.41,0.61 -4.73,1.71c-0.45,0.38 -1,0.56 -1.55,0.56c-0.62,0 -1.25,-0.24 -1.73,-0.71l-2.79,-2.74c-1.6,2.05 -2.6,4.58 -2.73,7.35h0.08c0.26,-0.72 0.94,-1.23 1.74,-1.23c1.02,0 1.85,0.83 1.85,1.85v13.57c0,1.02 -0.83,1.85 -1.85,1.85c-0.8,0 -1.48,-0.52 -1.74,-1.23h-1.96c-1.7,0 -3.08,-1.38 -3.08,-3.08v-8.63c0,-1.7 1.38,-3.08 3.08,-3.08h0.63c0.32,-7.53 6.55,-13.57 14.17,-13.57zM22.88,7.23c-3.69,0 -7.02,1.56 -9.38,4.05l2.85,2.79c0.45,0.45 1.15,0.48 1.63,0.08c1.55,-1.29 3.51,-1.99 5.52,-1.99c2.01,0 3.96,0.71 5.5,1.98c0.48,0.39 1.18,0.36 1.63,-0.09l2.84,-2.81c-2.36,-2.47 -5.68,-4.02 -9.36,-4.02zM23.5,8.47c0.34,0 0.62,0.27 0.62,0.62v1.23c0,0.34 -0.27,0.62 -0.62,0.62c-0.34,0 -0.62,-0.27 -0.62,-0.62v-1.23c0,-0.34 0.27,-0.62 0.62,-0.62zM26.85,8.92c0.08,-0.01 0.16,-0.01 0.25,0.01c0.33,0.09 0.53,0.43 0.44,0.76l-0.32,1.19c-0.07,0.28 -0.33,0.46 -0.6,0.46c-0.05,0 -0.11,-0.01 -0.16,-0.02c-0.33,-0.09 -0.52,-0.43 -0.44,-0.76l0.32,-1.19c0.07,-0.25 0.27,-0.42 0.51,-0.45zM20.15,8.92c0.24,0.03 0.44,0.2 0.51,0.45l0.32,1.19c0.09,0.33 -0.11,0.67 -0.44,0.76c-0.06,0.01 -0.11,0.02 -0.16,0.02c-0.27,0 -0.52,-0.18 -0.6,-0.46l-0.32,-1.19c-0.09,-0.33 0.11,-0.67 0.44,-0.76c0.08,-0.02 0.17,-0.03 0.25,-0.01zM16.79,10.25c0.24,-0.03 0.48,0.08 0.61,0.31l0.62,1.06c0.17,0.3 0.07,0.67 -0.22,0.85c-0.1,0.06 -0.2,0.08 -0.31,0.08c-0.21,0 -0.42,-0.11 -0.53,-0.31l-0.62,-1.06c-0.17,-0.3 -0.07,-0.67 0.22,-0.84c0.07,-0.04 0.15,-0.07 0.23,-0.08zM30.21,10.25c0.08,0.01 0.16,0.03 0.23,0.08c0.29,0.17 0.39,0.55 0.22,0.84l-0.62,1.07c-0.11,0.2 -0.32,0.31 -0.53,0.31c-0.11,0 -0.21,-0.03 -0.31,-0.08c-0.3,-0.17 -0.39,-0.55 -0.22,-0.84l0.62,-1.07c0.13,-0.22 0.37,-0.33 0.61,-0.3zM11.78,19.57c-0.34,0 -0.62,0.28 -0.62,0.62v13.57c0,0.34 0.28,0.62 0.62,0.62c0.34,0 0.62,-0.28 0.62,-0.62v-13.57c0,-0.34 -0.28,-0.62 -0.62,-0.62zM35.22,19.57c-0.34,0 -0.62,0.28 -0.62,0.62v13.57c0,0.34 0.28,0.62 0.62,0.62c0.34,0 0.62,-0.28 0.62,-0.62v-13.57c0,-0.34 -0.28,-0.62 -0.62,-0.62zM8.08,20.8c-1.02,0 -1.85,0.83 -1.85,1.85v8.63c0,1.02 0.83,1.85 1.85,1.85h1.85v-12.33zM37.07,20.8v12.33h1.85c1.02,0 1.85,-0.83 1.85,-1.85v-8.63c0,-1.02 -0.83,-1.85 -1.85,-1.85zM24.73,40.53c-0.68,0 -1.23,0.55 -1.23,1.23c0,0.68 0.55,1.23 1.23,1.23h1.23c0.68,0 1.23,-0.55 1.23,-1.23c0,-0.68 -0.55,-1.23 -1.23,-1.23z" />
                                            <g id="Mask by Clip 353" clip-path="url(#cp41)">
                                                <path id="Fill 352" class="shp1" fill="#4a90e1" d="M0,49.23h47v-48.23h-47z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_full_support">
                                        Full Support
                                    </div>
                                    <div class="desc" id="txt_app_config_full_support_desc">
                                        Whenever something is wrong or you have a question: Our support team is ready to help you.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 49 41" width="49" height="41">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp42">
                                                <path d="M41.22,5c0.98,0 1.78,0.8 1.78,1.78v27.31c0,0.98 -0.8,1.78 -1.78,1.78c-0.98,0 -1.78,-0.8 -1.78,-1.78v-0.13l-15.58,-4.04l-0.73,2.73c-0.21,0.77 -0.7,1.41 -1.38,1.8c-0.46,0.26 -0.97,0.4 -1.48,0.4c-0.26,0 -0.52,-0.03 -0.77,-0.1l-5.41,-1.45c-0.77,-0.21 -1.41,-0.7 -1.8,-1.38c-0.4,-0.69 -0.5,-1.49 -0.29,-2.25l0.71,-2.63l-4.14,-1.07c-0.09,0.9 -0.84,1.61 -1.76,1.61c-0.98,0 -1.78,-0.8 -1.78,-1.78v-10.69c0,-0.98 0.8,-1.78 1.78,-1.78c0.93,0 1.68,0.71 1.76,1.61l30.89,-8.01v-0.13c0,-0.98 0.8,-1.78 1.78,-1.78zM41.22,6.19c-0.33,0 -0.59,0.27 -0.59,0.59v27.31c0,0.33 0.27,0.59 0.59,0.59c0.33,0 0.59,-0.27 0.59,-0.59v-27.31c0,-0.33 -0.27,-0.59 -0.59,-0.59zM39.44,8.14l-1.24,0.32c0.01,0.04 0.02,0.08 0.02,0.13c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.16,0 -0.29,-0.06 -0.4,-0.16l-19.17,4.97v5.85c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.27 -0.59,-0.59v-5.55l-7.12,1.85v8.58l27.54,7.14c0.1,-0.07 0.21,-0.12 0.35,-0.12h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.03 -0.01,0.05 -0.01,0.08l1.23,0.32zM36.44,10.96h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,13.93h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM6.78,14.5c-0.33,0 -0.59,0.27 -0.59,0.59v10.69c0,0.33 0.27,0.59 0.59,0.59c0.33,0 0.59,-0.27 0.59,-0.59v-10.69c0,-0.33 -0.27,-0.59 -0.59,-0.59zM36.44,16.9h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,19.87h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM16.28,21.62c0.33,0 0.59,0.27 0.59,0.59v2.38c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.27 -0.59,-0.59v-2.38c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,22.84h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,25.81h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM13.84,27.32l-0.71,2.64c-0.12,0.46 -0.06,0.94 0.18,1.35c0.24,0.41 0.62,0.7 1.08,0.83l5.41,1.45c0.46,0.12 0.94,0.06 1.35,-0.18c0.41,-0.24 0.71,-0.62 0.83,-1.08l0.73,-2.71zM36.44,28.78h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59z" />
                                            </clipPath>
                                            <path id="Clip 356" class="shp0" fill="#4a90e2" d="M41.22,5c0.98,0 1.78,0.8 1.78,1.78v27.31c0,0.98 -0.8,1.78 -1.78,1.78c-0.98,0 -1.78,-0.8 -1.78,-1.78v-0.13l-15.58,-4.04l-0.73,2.73c-0.21,0.77 -0.7,1.41 -1.38,1.8c-0.46,0.26 -0.97,0.4 -1.48,0.4c-0.26,0 -0.52,-0.03 -0.77,-0.1l-5.41,-1.45c-0.77,-0.21 -1.41,-0.7 -1.8,-1.38c-0.4,-0.69 -0.5,-1.49 -0.29,-2.25l0.71,-2.63l-4.14,-1.07c-0.09,0.9 -0.84,1.61 -1.76,1.61c-0.98,0 -1.78,-0.8 -1.78,-1.78v-10.69c0,-0.98 0.8,-1.78 1.78,-1.78c0.93,0 1.68,0.71 1.76,1.61l30.89,-8.01v-0.13c0,-0.98 0.8,-1.78 1.78,-1.78zM41.22,6.19c-0.33,0 -0.59,0.27 -0.59,0.59v27.31c0,0.33 0.27,0.59 0.59,0.59c0.33,0 0.59,-0.27 0.59,-0.59v-27.31c0,-0.33 -0.27,-0.59 -0.59,-0.59zM39.44,8.14l-1.24,0.32c0.01,0.04 0.02,0.08 0.02,0.13c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.16,0 -0.29,-0.06 -0.4,-0.16l-19.17,4.97v5.85c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.27 -0.59,-0.59v-5.55l-7.12,1.85v8.58l27.54,7.14c0.1,-0.07 0.21,-0.12 0.35,-0.12h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.03 -0.01,0.05 -0.01,0.08l1.23,0.32zM36.44,10.96h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,13.93h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM6.78,14.5c-0.33,0 -0.59,0.27 -0.59,0.59v10.69c0,0.33 0.27,0.59 0.59,0.59c0.33,0 0.59,-0.27 0.59,-0.59v-10.69c0,-0.33 -0.27,-0.59 -0.59,-0.59zM36.44,16.9h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,19.87h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM16.28,21.62c0.33,0 0.59,0.27 0.59,0.59v2.38c0,0.33 -0.26,0.59 -0.59,0.59c-0.33,0 -0.59,-0.27 -0.59,-0.59v-2.38c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,22.84h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM36.44,25.81h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59zM13.84,27.32l-0.71,2.64c-0.12,0.46 -0.06,0.94 0.18,1.35c0.24,0.41 0.62,0.7 1.08,0.83l5.41,1.45c0.46,0.12 0.94,0.06 1.35,-0.18c0.41,-0.24 0.71,-0.62 0.83,-1.08l0.73,-2.71zM36.44,28.78h1.19c0.33,0 0.59,0.27 0.59,0.59c0,0.33 -0.26,0.59 -0.59,0.59h-1.19c-0.33,0 -0.59,-0.27 -0.59,-0.59c0,-0.33 0.26,-0.59 0.59,-0.59z" />
                                            <g id="Mask by Clip 356" clip-path="url(#cp42)">
                                                <path id="Fill 355" class="shp1" fill="#4a90e1" d="M0,40.87h48v-40.88h-48z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_marketing">
                                        App Marketing help
                                    </div>
                                    <div class="desc" id="txt_app_config_marketing_desc">
                                        We will help you with your App marketing by providing you top notch Tips and Best practices. We will set goals together and help you to achieve them!
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 49 44" width="49" height="44">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp43">
                                                <path d="M7.09,5h34.82c1.15,0 2.09,0.94 2.09,2.09v29.25c0,1.15 -0.94,2.09 -2.09,2.09h-34.82c-1.15,0 -2.09,-0.94 -2.09,-2.09v-29.25c0,-1.15 0.94,-2.09 2.09,-2.09zM7.09,6.39c-0.38,0 -0.7,0.31 -0.7,0.7v4.88h28.55c0.39,0 0.7,0.31 0.7,0.7c0,0.38 -0.31,0.7 -0.7,0.7h-28.55v22.98c0,0.38 0.31,0.7 0.7,0.7h34.82c0.38,0 0.7,-0.31 0.7,-0.7v-29.25c0,-0.38 -0.31,-0.7 -0.7,-0.7zM9.18,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM12.66,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM16.14,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM19.62,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM23.11,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM26.59,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM30.07,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM33.55,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM37.04,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM40.52,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM37.73,11.96h2.79c0.39,0 0.7,0.31 0.7,0.7c0,0.38 -0.31,0.7 -0.7,0.7h-2.79c-0.39,0 -0.7,-0.31 -0.7,-0.7c0,-0.38 0.31,-0.7 0.7,-0.7zM40.52,15.45c0.18,0 0.36,0.07 0.49,0.2c0.27,0.27 0.27,0.71 0,0.98l-4.88,4.88c0.13,0.27 0.21,0.57 0.21,0.9c0,1.15 -0.94,2.09 -2.09,2.09c-1.15,0 -2.09,-0.94 -2.09,-2.09c0,-0.14 0.02,-0.28 0.04,-0.42l-3.6,-2.16c-0.36,0.3 -0.82,0.48 -1.32,0.48c-0.36,0 -0.69,-0.1 -0.99,-0.26l-4.04,3.64c0.1,0.25 0.16,0.52 0.16,0.8c0,1.15 -0.94,2.09 -2.09,2.09c-1.15,0 -2.09,-0.94 -2.09,-2.09c0,-0.03 0.01,-0.05 0.01,-0.08l-3.32,-1.33c-0.38,0.44 -0.94,0.72 -1.56,0.72c-0.45,0 -0.86,-0.14 -1.2,-0.38l-3.28,2.34c-0.12,0.09 -0.26,0.13 -0.4,0.13c-0.22,0 -0.43,-0.1 -0.57,-0.29c-0.22,-0.31 -0.15,-0.75 0.16,-0.97l3.28,-2.34c-0.05,-0.18 -0.09,-0.38 -0.09,-0.57c0,-1.15 0.94,-2.09 2.09,-2.09c1.15,0 2.09,0.94 2.09,2.09c0,0.03 -0.01,0.05 -0.01,0.08l3.32,1.33c0.38,-0.44 0.94,-0.72 1.56,-0.72c0.36,0 0.69,0.1 0.99,0.26l4.04,-3.64c-0.1,-0.25 -0.16,-0.52 -0.16,-0.8c0,-1.15 0.94,-2.09 2.09,-2.09c1.15,0 2.09,0.94 2.09,2.09c0,0.14 -0.02,0.28 -0.04,0.42l3.6,2.16c0.36,-0.3 0.82,-0.48 1.32,-0.48c0.32,0 0.62,0.08 0.9,0.21l4.88,-4.88c0.14,-0.14 0.32,-0.2 0.49,-0.2zM27.29,17.42c-0.45,0 -0.81,0.36 -0.81,0.81c0,0.45 0.36,0.81 0.81,0.81c0.45,0 0.81,-0.36 0.81,-0.81c0,-0.45 -0.36,-0.81 -0.81,-0.81zM13.36,20.9c-0.45,0 -0.81,0.36 -0.81,0.81c0,0.45 0.36,0.81 0.81,0.81c0.45,0 0.81,-0.36 0.81,-0.81c0,-0.45 -0.36,-0.81 -0.81,-0.81zM34.25,21.57c-0.46,0 -0.84,0.38 -0.84,0.84c0,0.46 0.38,0.84 0.84,0.84c0.47,0 0.84,-0.38 0.84,-0.84c0,-0.46 -0.38,-0.84 -0.84,-0.84zM20.32,23.69c-0.45,0 -0.81,0.36 -0.81,0.81c0,0.45 0.36,0.81 0.81,0.81c0.45,0 0.81,-0.36 0.81,-0.81c0,-0.45 -0.36,-0.81 -0.81,-0.81zM25.2,24.5h4.18c0.39,0 0.7,0.31 0.7,0.7v9.05h1.39v-4.87c0,-0.38 0.31,-0.7 0.7,-0.7h4.18c0.39,0 0.7,0.31 0.7,0.7v4.87h0.7c0.39,0 0.7,0.31 0.7,0.7c0,0.38 -0.31,0.7 -0.7,0.7h-27.86c-0.39,0 -0.7,-0.31 -0.7,-0.7c0,-0.38 0.31,-0.7 0.7,-0.7h0.7v-6.27c0,-0.38 0.31,-0.7 0.7,-0.7h4.18c0.39,0 0.7,0.31 0.7,0.7v6.27h1.39v-3.48c0,-0.38 0.31,-0.7 0.7,-0.7h4.18c0.39,0 0.7,0.31 0.7,0.7v3.48h1.39v-9.05c0,-0.38 0.31,-0.7 0.7,-0.7zM25.89,25.89v8.36h2.79v-8.36zM11.96,28.68v5.57h2.79v-5.57zM32.86,30.07v4.18h2.79v-4.18zM18.93,31.46v2.79h2.79v-2.79z" />
                                            </clipPath>
                                            <path id="Clip 359" class="shp0" fill="#4a90e2" d="M7.09,5h34.82c1.15,0 2.09,0.94 2.09,2.09v29.25c0,1.15 -0.94,2.09 -2.09,2.09h-34.82c-1.15,0 -2.09,-0.94 -2.09,-2.09v-29.25c0,-1.15 0.94,-2.09 2.09,-2.09zM7.09,6.39c-0.38,0 -0.7,0.31 -0.7,0.7v4.88h28.55c0.39,0 0.7,0.31 0.7,0.7c0,0.38 -0.31,0.7 -0.7,0.7h-28.55v22.98c0,0.38 0.31,0.7 0.7,0.7h34.82c0.38,0 0.7,-0.31 0.7,-0.7v-29.25c0,-0.38 -0.31,-0.7 -0.7,-0.7zM9.18,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM12.66,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM16.14,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM19.62,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM23.11,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM26.59,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM30.07,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM33.55,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM37.04,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM40.52,7.79c0.39,0 0.7,0.31 0.7,0.7v1.39c0,0.38 -0.31,0.7 -0.7,0.7c-0.39,0 -0.7,-0.31 -0.7,-0.7v-1.39c0,-0.38 0.31,-0.7 0.7,-0.7zM37.73,11.96h2.79c0.39,0 0.7,0.31 0.7,0.7c0,0.38 -0.31,0.7 -0.7,0.7h-2.79c-0.39,0 -0.7,-0.31 -0.7,-0.7c0,-0.38 0.31,-0.7 0.7,-0.7zM40.52,15.45c0.18,0 0.36,0.07 0.49,0.2c0.27,0.27 0.27,0.71 0,0.98l-4.88,4.88c0.13,0.27 0.21,0.57 0.21,0.9c0,1.15 -0.94,2.09 -2.09,2.09c-1.15,0 -2.09,-0.94 -2.09,-2.09c0,-0.14 0.02,-0.28 0.04,-0.42l-3.6,-2.16c-0.36,0.3 -0.82,0.48 -1.32,0.48c-0.36,0 -0.69,-0.1 -0.99,-0.26l-4.04,3.64c0.1,0.25 0.16,0.52 0.16,0.8c0,1.15 -0.94,2.09 -2.09,2.09c-1.15,0 -2.09,-0.94 -2.09,-2.09c0,-0.03 0.01,-0.05 0.01,-0.08l-3.32,-1.33c-0.38,0.44 -0.94,0.72 -1.56,0.72c-0.45,0 -0.86,-0.14 -1.2,-0.38l-3.28,2.34c-0.12,0.09 -0.26,0.13 -0.4,0.13c-0.22,0 -0.43,-0.1 -0.57,-0.29c-0.22,-0.31 -0.15,-0.75 0.16,-0.97l3.28,-2.34c-0.05,-0.18 -0.09,-0.38 -0.09,-0.57c0,-1.15 0.94,-2.09 2.09,-2.09c1.15,0 2.09,0.94 2.09,2.09c0,0.03 -0.01,0.05 -0.01,0.08l3.32,1.33c0.38,-0.44 0.94,-0.72 1.56,-0.72c0.36,0 0.69,0.1 0.99,0.26l4.04,-3.64c-0.1,-0.25 -0.16,-0.52 -0.16,-0.8c0,-1.15 0.94,-2.09 2.09,-2.09c1.15,0 2.09,0.94 2.09,2.09c0,0.14 -0.02,0.28 -0.04,0.42l3.6,2.16c0.36,-0.3 0.82,-0.48 1.32,-0.48c0.32,0 0.62,0.08 0.9,0.21l4.88,-4.88c0.14,-0.14 0.32,-0.2 0.49,-0.2zM27.29,17.42c-0.45,0 -0.81,0.36 -0.81,0.81c0,0.45 0.36,0.81 0.81,0.81c0.45,0 0.81,-0.36 0.81,-0.81c0,-0.45 -0.36,-0.81 -0.81,-0.81zM13.36,20.9c-0.45,0 -0.81,0.36 -0.81,0.81c0,0.45 0.36,0.81 0.81,0.81c0.45,0 0.81,-0.36 0.81,-0.81c0,-0.45 -0.36,-0.81 -0.81,-0.81zM34.25,21.57c-0.46,0 -0.84,0.38 -0.84,0.84c0,0.46 0.38,0.84 0.84,0.84c0.47,0 0.84,-0.38 0.84,-0.84c0,-0.46 -0.38,-0.84 -0.84,-0.84zM20.32,23.69c-0.45,0 -0.81,0.36 -0.81,0.81c0,0.45 0.36,0.81 0.81,0.81c0.45,0 0.81,-0.36 0.81,-0.81c0,-0.45 -0.36,-0.81 -0.81,-0.81zM25.2,24.5h4.18c0.39,0 0.7,0.31 0.7,0.7v9.05h1.39v-4.87c0,-0.38 0.31,-0.7 0.7,-0.7h4.18c0.39,0 0.7,0.31 0.7,0.7v4.87h0.7c0.39,0 0.7,0.31 0.7,0.7c0,0.38 -0.31,0.7 -0.7,0.7h-27.86c-0.39,0 -0.7,-0.31 -0.7,-0.7c0,-0.38 0.31,-0.7 0.7,-0.7h0.7v-6.27c0,-0.38 0.31,-0.7 0.7,-0.7h4.18c0.39,0 0.7,0.31 0.7,0.7v6.27h1.39v-3.48c0,-0.38 0.31,-0.7 0.7,-0.7h4.18c0.39,0 0.7,0.31 0.7,0.7v3.48h1.39v-9.05c0,-0.38 0.31,-0.7 0.7,-0.7zM25.89,25.89v8.36h2.79v-8.36zM11.96,28.68v5.57h2.79v-5.57zM32.86,30.07v4.18h2.79v-4.18zM18.93,31.46v2.79h2.79v-2.79z" />
                                            <g id="Mask by Clip 359" clip-path="url(#cp43)">
                                                <path id="Fill 358" class="shp1" fill="#4a90e1" d="M0,43.43h49v-43.43h-49z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_platform">
                                        Customer Platform
                                    </div>
                                    <div class="desc" id="txt_app_config_platform_desc">
                                        You will get access to our user friendly platform which enables you to adjust your App whenever you want. This way you don’t have any additional costs for small App adjustments.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="item">
                                    <div class="image">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44" height="44">
                                            <clipPath clipPathUnits="userSpaceOnUse" id="cp44">
                                                <path d="M22,5c9.38,0 17,7.62 17,17c0,9.38 -7.62,17 -17,17c-9.38,0 -17,-7.62 -17,-17c0,-9.38 7.62,-17 17,-17zM22,6.55c-8.54,0 -15.46,6.91 -15.46,15.45c0,8.54 6.91,15.45 15.46,15.45c8.54,0 15.45,-6.91 15.45,-15.45c0,-8.54 -6.91,-15.45 -15.45,-15.45zM29.72,14.26c0.29,-0.01 0.56,0.15 0.7,0.41c0.14,0.26 0.12,0.57 -0.05,0.81l-8.89,13.11l-6.95,-6.45c-0.21,-0.19 -0.3,-0.47 -0.24,-0.74c0.06,-0.27 0.26,-0.49 0.53,-0.57c0.27,-0.08 0.56,-0.01 0.76,0.18l5.63,5.23l7.88,-11.62c0.14,-0.21 0.38,-0.34 0.63,-0.35z" />
                                            </clipPath>
                                            <path id="Clip 362" fill="#4a90e2" class="shp0" d="M22,5c9.38,0 17,7.62 17,17c0,9.38 -7.62,17 -17,17c-9.38,0 -17,-7.62 -17,-17c0,-9.38 7.62,-17 17,-17zM22,6.55c-8.54,0 -15.46,6.91 -15.46,15.45c0,8.54 6.91,15.45 15.46,15.45c8.54,0 15.45,-6.91 15.45,-15.45c0,-8.54 -6.91,-15.45 -15.45,-15.45zM29.72,14.26c0.29,-0.01 0.56,0.15 0.7,0.41c0.14,0.26 0.12,0.57 -0.05,0.81l-8.89,13.11l-6.95,-6.45c-0.21,-0.19 -0.3,-0.47 -0.24,-0.74c0.06,-0.27 0.26,-0.49 0.53,-0.57c0.27,-0.08 0.56,-0.01 0.76,0.18l5.63,5.23l7.88,-11.62c0.14,-0.21 0.38,-0.34 0.63,-0.35z" />
                                            <g id="Mask by Clip 362" clip-path="url(#cp44)">
                                                <path id="Fill 361" class="shp1" fill="#4a90e1" d="M0,44h44v-44h-44z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="title" id="txt_app_config_up_to_date">
                                        Always up to date
                                    </div>
                                    <div class="desc" id="txt_app_config_up_to_date_desc">
                                        Don’t worry, you can just sit-back and relax with any new updates. Our developers make sure your App is always up to date.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div id="roi-calculator" class="tab-content">-->
            <!--roi-calculator tab content-->
            <!--</div>-->
            <div id="benefits" class="tab-content">
                <div class="block1">
                    <div class="title-block">
                        <div class="title bottom-border" id="txt_app_config_benefits_title">Drive customer engagement with your own shopping app</div>
                        <div class="desc" id="txt_app_config_benefits_desc">60% of mobile customers never come back after their first visit. With smartphones being the most important device in the buyer's journey, it's crucial for retailers to make mobile shoppers come back for more.
                        </div>

                    </div>
                </div>
                <div class="block2">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="heading">
                                <div class="title" id="txt_app_config_benefits_mobile_shopping_title">
                                    Mobile shopping has never been easier
                                </div>
                                <div class="desc" id="txt_app_config_benefits_mobile_shopping_desc">
                                    A native shopping app offers your customers a superior user experience: easy & fast navigation, a secure and hazzle-free checkout and product info made to fit your mobile screen, just to name a few.
                                </div>
                            </div>
                            <div class="heading">
                                <div class="title" id="txt_app_config_benefits_unique_app_platform_title">
                                    An unique app platform
                                </div>
                                <div class="desc" id="txt_app_config_benefits_unique_app_platform_desc">
                                    Our platform is designed for you, the retailer, and provides you with every feature you'll ever need. A deep integration with your Prestashop store will take care of everything; syncing your products, payment & shopping methods, stock info and currencies all in one go. Furthermore, your app is 100% customisable. Simply put, app management has never been easier.
                                </div>
                            </div>
                            <div class="heading">
                                <div class="title" id="txt_app_config_benefits_reach_your_customers_title">
                                    Reach your customers like never before
                                </div>
                                <div class="desc" id="txt_app_config_benefits_reach_your_customers_desc">
                                    With your app icon on your users' mobile screens, your brand is always in the palm of their hands. Simply get your users back to your store by sending out segmented push messages notifying them on new products, a summersale or just as a reminder of their filled shopping cart.
                                </div>
                            </div>



                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="image">
                                <img src="../../../../modules/jmango360api/views/img/backend_pretashop_qs/benefit-img.png" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block3">
                    <a class="btn-get-started" href="#" id="btn_app_config_benefits_get_started">Get started</a>
                </div>


            </div>
            <div id="faq-contact" class="tab-content">
                <div class="block2">
                    <div class="title-block">
                        <div class="title bottom-border" id="txt_app_config_faq">Frequently Asked Questions</div>
                    </div>
                    <div id="faqs-questions" class="faqs-questions">
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#item-faqs-1" class="panel-title collapsed" data-toggle="collapse" id="txt_app_config_faq_q1">
                                    Are there any additional costs?
                                </a>
                            </div>
                            <div id="item-faqs-1" class="panel-collapse collapse">
                                <div class="accordion-content" id="txt_app_config_faq_q1_answer">
                                    Yes, just one thing. In order to publish your app, both Google and Apple require you to have a ‘developer account’. A Google Play Developer account is a $25 one-time fee and a Apple developer account is $99 per year. Once you have these developer accounts, you are able to publish your app under your company’s name. For more info, check out our support docs about developer accounts for Google and Apple.
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#item-faqs-2" class="panel-title collapsed" aria-expanded="false" data-toggle="collapse" id="txt_app_config_faq_q2">
                                    How long does it take before my app is ready?
                                </a>
                            </div>
                            <div id="item-faqs-2" class="panel-collapse collapse">
                                <div class="accordion-content" id="txt_app_config_faq_q2_answer">
                                    Your app will be ready in about 4 weeks. During this period we will integrate, design, test & publish your app. In the event of customization and/or technical issues it will, of course, take a bit longer before your app is ready.
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#item-faqs-3" class="panel-title collapsed" aria-expanded="false" data-toggle="collapse" id="txt_app_config_faq_q3">
                                    When does my annual subscription start?
                                </a>
                            </div>
                            <div id="item-faqs-3" class="panel-collapse collapse" >
                                <div class="accordion-content" id="txt_app_config_faq_q3_answer">
                                    Your annual subscription will start when your app is published. Everything beforehand is included in the one-time set-up fee.
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#item-faqs-4" class="panel-title collapsed" aria-expanded="false" data-toggle="collapse" id="txt_app_config_faq_q4">
                                    What do you need from me during the set-up of my app?
                                </a>
                            </div>
                            <div id="item-faqs-4" class="panel-collapse collapse" >
                                <div class="accordion-content" id="txt_app_config_faq_q4_answer">
                                    We want to make sure your app is exactly how you want it to be. During the set-up we will reach out to you to install our plugin, get your feedback on the design and assist you in publishing your app.
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#item-faqs-5" class="panel-title collapsed" aria-expanded="false" data-toggle="collapse" id="txt_app_config_faq_q5">
                                    Do I need a separate Payment Service Provider for the App?
                                </a>
                            </div>
                            <div id="item-faqs-5" class="panel-collapse collapse">
                                <div class="accordion-content" id="txt_app_config_faq_q5_answer">
                                    No, our plugin will automatically detect and use the Payment Gateway integrated with your online store, including (but not limited to) PayPal, Adyen, MultisafePay, Klarna, AsiaPay and BrainTree. Our platform supports all payment methods that these providers support (creditcards, iDeal, direct debit, ELV, PayPal, Klarna and Afterpay).
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#item-faqs-6" class="panel-title collapsed" aria-expanded="false" data-toggle="collapse" id="txt_app_config_faq_q6">
                                    What happens to my App when a new iOS or Android OS version is launched?
                                </a>
                            </div>
                            <div id="item-faqs-6" class="panel-collapse collapse" >
                                <div class="accordion-content" id="txt_app_config_faq_q6_answer">
                                    Our team of app developers are constantly making improvements to the apps, by adding new features for free and updating the app for the latest versions. So don’t worry, you can just sit-back and relax with any new updates.
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>