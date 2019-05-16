<?php
/**
 * @author Jmango
 * @copyright  2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

include_once 'JapiOrchardConnecterLoggerInterface.php';
require_once _PS_MODULE_DIR_ . '/jmango360api/vendor/kien/prestashop-api-lib/src/classes/log/FileLoggerService.php';

/**
 * Class JapiOrchardConnecterLogger
 */
class JapiOrchardConnecterLogger implements JapiOrchardConnecterLoggerInterface
{
    protected $logger;

    /**
     * JapiOrchardConnecterLogger constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->logger = new FileLoggerService();
        $logDir = _PS_CACHE_DIR_ . '/jmango360';
        if (!is_dir($logDir)) {
            if (!mkdir($logDir)) {
                throw new Exception('Couldnot create dir: ' . $logDir);
            }
        }
        $filename = _PS_CACHE_DIR_ . '/jmango360/pushmessage.log';
        $this->logger->setFilename($filename);
    }

    /**
     * @param $message
     * @return JapiOrchardConnecterLoggerInterface|void
     */
    public function log($message)
    {
        $this->logger->logDebug($message);
    }
}
