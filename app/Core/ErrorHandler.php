<?php

declare(strict_types=1);

namespace App\Core;

use App\Libraries\DebugLib as Debug;

final class ErrorHandler
{
    private Debug $debug;

    public function __construct()
    {
        // report all errors
        error_reporting(E_ALL);
        ini_set('display_errors', '0');

        $this->createNewDebugObject();

        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'fatalErrorShutdownFunction']);
    }

    private function createNewDebugObject(): void
    {
        $this->debug = new Debug();
    }

    final public function errorHandler(int $code, string $description, string $file, int $line): bool
    {
        $displayErrors = strtolower(ini_get('display_errors'));

        if (error_reporting() === 0 || $displayErrors === 'on') {
            return false;
        }

        $this->debug->log($code, $description, $file, $line, 'php');
        $this->debug->error($code, $description, $file, $line, 'php');

        return true;
    }

    final public function fatalErrorShutdownFunction(): void
    {
        $last_error = error_get_last();

        if (!empty($last_error)) {
            $this->errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}
