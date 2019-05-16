<?php
/**
 * @author Tien Hoang <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JMPwaModuleFrontControllerextends
 */
class Jmango360apiPwaModuleFrontController extends ModuleFrontController
{
    public function display()
    {
        $url = '/modules/jmango360api/vendor/kien/prestashop-pwa-lib/src/views/';
        $html = <<<HTML
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>JMango360 PWA</title>
                <base href="/">
                <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
                <link rel="icon" type="image/x-icon" href="favicon.ico">
                <link rel="manifest" href="manifest.json">
                <meta name="theme-color" content="#1976d2">
                <!-- For Apple Safari -->
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="default">
                <link rel="apple-touch-icon" href="./assets/icons/icon-jmango360-pwa-192x192.png">
                <link rel="apple-touch-icon" sizes="192x192" href="./assets/icons/icon-jmango360-pwa-192x192.png">
                <link rel="apple-touch-icon" sizes="512x512" href="./assets/icons/icon-jmango360-pwa-512x512.png">
                <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
            
                <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAyyK_UuY8hwRJasWehOSSs7yEo-nRjRQw"></script>
            </head>
            <body>
                
            <app-root></app-root>
            <noscript>Please enable JavaScript to continue using this application.</noscript>
            <script type="text/javascript" src="$url/runtime.js"></script><script type="text/javascript" src="$url/polyfills.js"></script><script type="text/javascript" src="$url/styles.js"></script><script type="text/javascript" src="$url/scripts.js"></script><script type="text/javascript" src="$url/vendor.js"></script><script type="text/javascript" src="$url/main.js"></script></body>
            </html>
HTML;

        header('Content-Type: text/html');
        echo $html;
        return true;
    }
    // /**
    //  * Initialize parent order controller
    //  * @see FrontController::init()
    //  */
    // public function init()
    // {
    //     /**
    //      * Spoof 'redirect to the good order process' logic
    //      */

    //     parent::init();

    //     $this->display_footer = false;
    // }

    // public function initContent()
    // {
    //     parent::initContent();

    //     $this->setTemplate(_PS_MODULE_DIR_ . 'jmango360pwa/views/templates/front/pwa.tpl');
    // }


    // public function initHeader()
    // {
    //     parent::initHeader();

    //     $this->context->smarty->assign('content_only', 1);
    // }
}
