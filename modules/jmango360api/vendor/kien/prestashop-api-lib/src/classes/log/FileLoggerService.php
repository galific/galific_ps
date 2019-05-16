<?php
/**
 * @author JMango Operations BV
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class FileLoggerService
 */
class FileLoggerService extends AbstractLogger
{
    protected $filename = '';

    /**
     * FileLoggerService constructor.
     */
    public function __construct()
    {
        parent::__construct(self::DEBUG);
    }

    /**
     * Write the message in the log file
     *
     * @param string message
     * @param int level
     * @return bool
     * @throws
     */
    protected function logMessage($message, $level)
    {
        if (!is_string($message)) {
            $message = print_r($message, true);
        }
        $formatted_message = sprintf(
            "%s %s: %s\r\n",
            $this->level_value[$level],
            date('Y/m/d - H:i:s'),
            $message
        );
        return (bool)file_put_contents($this->getFilename(), $formatted_message, FILE_APPEND);
    }

    /**
     * Check if the specified filename is writable and set the filename
     *
     * @param string $filename
     * @throws
     */
    public function setFilename($filename)
    {
        if (is_writable(dirname($filename))) {
            $this->filename = $filename;
        } else {
            throw new Exception('Directory ' . dirname($filename) . ' is not writable');
        }
    }

    /**
     * Get log file
     *
     * @param string message
     * @return string
     * @throws
     */
    public function getFilename()
    {
        if (empty($this->filename)) {
            throw new Exception('Log filename is empty.');
        }

        return $this->filename;
    }
}
