<?php
/**
 * @author Jmango
 * @copyright  2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Interface JapiOrchardConnecterLoggerInterface
 */
interface JapiOrchardConnecterLoggerInterface
{
    /**
     * Log message to file
     *
     * @param $message
     * @return $this
     */
    public function log($message);
}
