<?php
/**
 *
 * @author Jmango
 * @copyright opyright 2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class BackOfficeService extends BaseService
{

    public function doExecute()
    {
        $admin_files = "functions.php tabs init.php login.php index.php ajax.php cron_currency_rates.php drawer.php grider.php autoupgrade displayImage.php header.inc.php backup.php get-file-admin.php password.php filemanager footer.inc.php searchcron.php pdf.php ajax-tab.php export import ajax_products_list.php themes backups";

        $admin_path = '';

        try {
            if ($handle = opendir('../')) {
                while (false !== ($entry = readdir($handle))) {
                    $admin_file_count = 0;
                    if ($entry != "." && $entry != "..") {
                        if ($this->folderExist('../' . $entry) && $handle_child = opendir('../' . $entry)) {
                            while (false !== ($child = readdir($handle_child))) {
                                if ($child != "." && $child != "..") {
                                    if (strpos($admin_files, $child) !== false) {
                                        $admin_file_count++;
                                    }

                                    if ($admin_file_count > 20) {
                                        $admin_path = $entry;
                                        break;
                                    }
                                }
                            }

                            closedir($handle_child);

                            if ($admin_path != '') {
                                break;
                            }
                        }
                    }
                }
                closedir($handle);
            }
        } catch (Exception $e) {
            $admin_path = '';
        }

        $module = Module::getInstanceByName($this->module_name);

        $backOfficeResponse = new BackOfficeResponse();
        $backOfficeResponse->admin_path = $admin_path;
        $backOfficeResponse->plugin_version = $module->version;
        $backOfficeResponse->prestashop_version = defined('_PS_VERSION_') ? _PS_VERSION_ : null;
        $this->response = $backOfficeResponse;
    }



    public function folderExist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);

        // If it exist, check if it's a directory
        return ($path !== false and is_dir($path)) ? $path : false;
    }
}
