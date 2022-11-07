<?php

class LaybuySupportModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $module = Module::getInstanceByName('laybuy');
        $debug = Configuration::get('LAYBUY_DEBUG');

        $ret = [
            'platform' => 'presta',
            'meta' => [
                'website' => Tools::getHttpHost(true) . __PS_BASE_URI__,
                'date' => date('Y-m-d H:i:s', time())
            ],
            'env' => [
                'presta_version' => _PS_VERSION_,
                'module_version' => $module->version
            ],
            'settings' => [
                'LAYBUY_ENABLED' => (int) Configuration::get('LAYBUY_ENABLED'),
                'LAYBUY_GLOBAL' => Configuration::get('LAYBUY_GLOBAL'),
                'LAYBUY_CURRENCY' => json_decode(Configuration::get('LAYBUY_CURRENCY'), true),
                'LAYBUY_API_ENVIRONMENT' => Configuration::get('LAYBUY_API_ENVIRONMENT'),
                'LAYBUY_DEBUG' => Configuration::get('LAYBUY_DEBUG'),
            ],
            'log' => null
        ];

        if ($debug) {
            $db = \Db::getInstance();

            $query = 'SELECT message FROM `' . _DB_PREFIX_ . 'log` order by id limit 500';

            /** @var array $result */
            $result = $db->executeS($query);

            var_dump($result, $query);exit;
        }

        print_r($ret);exit;
    }

    protected function _tailFile($filepath, $lines = 1, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

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
        return trim($output);
    }
}