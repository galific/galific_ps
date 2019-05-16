<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class LogService
 */
class LogService extends BaseService
{
    /**
     * API entry point
     */
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            $this->response = $this->getLogContent();
        }
    }

    /**
     * Return plain text
     */
    protected function renderOutput()
    {
        if (empty($this->response->errors)) {
            header("Content-Type: text/plain");
            echo $this->response;
        } else {
            echo parent::renderOutput();
        }
    }

    /**
     * Get log content by name
     *
     * @return string|JmResponse
     * @throws
     */
    protected function getLogContent()
    {
        try {
            $fileName = $this->getRequestValue('file');
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            if ($fileExt != 'log') {
                throw new Exception('File not supported');
            }

            $lines = $this->getRequestValue('n', 100);
            $logDir = _PS_CACHE_DIR_ . '/jmango360/';
            if (!is_dir($logDir)) {
                if (!mkdir($logDir)) {
                    throw new Exception('Couldnot create dir: ' . $logDir);
                }
            }

            $logFile = '';
            $files = scandir($logDir);
            foreach ($files as $file) {
                if ($file == $fileName) {
                    $logFile = $logDir . $fileName;
                }
            }

            if (is_file($logFile)) {
                return $this->tail($logFile, $lines);
            } else {
                throw new Exception('Log not found.');
            }
        } catch (Exception $e) {
            $response = new JmResponse();
            $response->errors[] = $e->getMessage();

            return $response;
        }
    }

    /**
     * Get (n) last line(s) of text file
     * Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     *
     * @author Torleif Berger, Lorenzo Stanco
     * @link http://stackoverflow.com/a/15025877/995958
     * @license http://creativecommons.org/licenses/by/3.0/
     *
     * @param $filepath
     * @param $lines
     * @param $adaptive
     * @return string
     */
    protected function tail($filepath, $lines = 100, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return '';
        }

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }

        // Close file and return
        fclose($f);

        return $output;
    }
}
