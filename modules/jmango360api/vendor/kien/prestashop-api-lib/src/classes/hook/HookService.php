<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class HookService
 */
class HookService extends BaseService
{
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            $this->response = $this->getHooks();
        }
    }

    /**
     * Get module register hook
     *
     * @return HookResponse
     */
    protected function getHooks()
    {
        $response = new HookResponse();

        $hookNames = explode(',', $this->getRequestValue('names'));
        foreach ($hookNames as $hookName) {
            if (!$hookName) {
                continue;
            }

            $response->hooks[$hookName] = array();

            try {
                $modules = Hook::getHookModuleExecList($hookName);
            } catch (Exception $e) {
                $modules = array();
            }

            if (is_array($modules)) {
                foreach ($modules as $module) {
                    $response->hooks[$hookName][] = isset($module['module']) ? $module['module'] : 'N/A';
                }
            }
        }

        return $response;
    }
}
