<?php
/**
 *
 * @author Jmango
 * @copyright opyright 2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Class JapiOrchardConnecter
 */
class JapiOrchardConnecter
{
    /**
     * @var JapiOrchardConnecterLoggerInterface
     */
    protected $logger;

    /**
     * @var string Base Orchard url (PRO, UAT, INT)
     */
    protected $baseUrl;

    /**
     * @var string Orchard ticket ID
     */
    protected $ticket;

    /**
     * @var string Orchard app key
     */
    protected $appKey;

    /**
     * JapiOrchardConnecter constructor.
     *
     * @param $baseUrl
     * @param $ticket
     * @param $appKey
     * @throws
     */
    public function __construct($baseUrl, $ticket, $appKey)
    {
        if (!$baseUrl || !$ticket || !$appKey) {
            throw new Exception('Orchard URL or Ticket ID or App Key are missing!');
        }

        $this->baseUrl = $baseUrl;
        $this->ticket = $ticket;
        $this->appKey = $appKey;
    }

    /**
     * Set logger
     *
     * @param JapiOrchardConnecterLoggerInterface $logger
     */
    public function setLogger(JapiOrchardConnecterLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get logger
     *
     * @return JapiOrchardConnecterLoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Call push message to Orchard server
     *
     * @param $data
     * @return array
     * @throws
     */
    public function pushMessage($data)
    {
        $url = $this->_getPushMessageUrl();
        $data['ticket'] = $this->ticket;
        $data['appKey'] = $this->appKey;
        $headers = array(
            'Content-type: application/json'
        );

        try {
            $response = $this->_send('POST', $url, json_encode($data), $headers);
            $response = json_decode($response);

            return $response;
        } catch (Exception $e) {
            return array(
                'error' => 1,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Get both category tree and modules in 1 request
     *
     * @param $data
     * @return array
     * @throws
     */
    public function getCategoryTreeAndModules($data = array())
    {
        $url = $this->_getCategoryTreeAndModuleUrl();
        $data['ticket'] = $this->ticket;
        $data['appKey'] = $this->appKey;
        $headers = array(
            'Content-type: application/json'
        );

        try {
            $output = array();

            $response = $this->_send('POST', $url, json_encode($data), $headers);
            $response = json_decode($response, true);
            if (!empty($response['categories'])) {
                $output['categories'] = $this->_parseCategoryTree($response['categories']);
            }
            if (!empty($response['modules'])) {
                $output['modules'] = $this->_parseModule($response['modules']);
            }

            return $output;
        } catch (Exception $e) {
            return array(
                'error' => 1,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Parse module list
     *
     * @param $modules
     * @return array
     */
    protected function _parseModule($modules)
    {
        if (!is_array($modules)) {
            return array();
        }
        $output = array();
        foreach ($modules as $module) {
            if (empty($module['id'])) {
                continue;
            }
            $output[] = array(
                'id' => $module['id'],
                'text' => $module['name'],
                'otype' => $module['type']
            );
        }

        return $output;
    }

    /**
     * Parse category tree data from Orchard API
     *
     * @param $categories
     * @return array
     */
    protected function _parseCategoryTree($categories)
    {
        if (!is_array($categories)) {
            return array();
        }

        $output = array();
        foreach ($categories as $category) {
            if (empty($category['id'])) {
                continue;
            }
            $tmp = array(
                'id' => $category['id'],
                'text' => $category['name'],
                'otype' => $category['type']
            );

            if (!empty($category['categoryTree'])) {
                $tmp['children'] = $this->_parseCategoryTree($category['categoryTree']);
            }

            $output[] = $tmp;
        }

        return $output;
    }

    /**
     * Get push message endpoint
     *
     * @return string
     */
    protected function _getPushMessageUrl()
    {
        return $this->baseUrl . '/integration/pushmessage/sendmessage';
    }

    /**
     * Get category tree and module endpoint
     *
     * @return string
     */
    protected function _getCategoryTreeAndModuleUrl()
    {
        return $this->baseUrl . '/integration/pushmessage/module';
    }

    /**
     * Make HTTP request using cURL extension
     *
     * @param $method
     * @param $url
     * @param $data
     * @param $headers
     * @return string
     * @throws Exception
     */
    protected function _send($method, $url, $data, $headers = array())
    {
        if (!function_exists('curl_init')) {
            throw new Exception('PHP cURL extension is missing.');
        }

        if (Tools::strtolower($method == 'get') && $data) {
            $url .= is_array($data) ? http_build_query($data) : $data;
        }

        //init and config request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (Tools::strtolower($method) == 'post' && $data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        //log
        if ($this->logger) {
            $this->logger->log(sprintf('%s %s', Tools::strtoupper($method), $url));
            $this->logger->log(sprintf('Params: %s', print_r($data, true)));
        }

        //execute
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //get error if occurred
        $error = curl_error($ch);
        //free resources
        curl_close($ch);

        //log
        if ($this->logger) {
            $this->logger->log(sprintf('Response code: %s', $responseCode));
        }

        //return
        if ($response) {
            return $response;
        } else {
            throw new Exception($error);
        }
    }
}
