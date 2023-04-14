<?php

namespace App\Core;

use CiParser;
use eftec\bladeone\BladeOne;
use Exception;

class Template
{
    /**
     * @var CiParser CodeIgniter Parser Class
     *
     * @deprecated 4.0.0
     */
    private $ciParser = null;

    /**
     *
     * @var Blade
     */
    private $bladeParser = null;

    public function __construct()
    {
        $this->createNewParser();
        $this->createNewBladeParser();
    }

    public function get(string $template_name): ?string
    {
        try {
            $route = XGP_ROOT . VIEWS_DIR . strtr($template_name, ['/' => DIRECTORY_SEPARATOR, '.' => DIRECTORY_SEPARATOR]) . '.php';
            $template = @file_get_contents($route);

            if ($template) { // We got something
                return $template; // Return
            } else {
                // Throw Exception
                throw new Exception('<p>Template not found or empty: <strong>' . $template_name . '</strong><br />
                    Location: <strong>' . $route . '</strong></p>');
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function set(string $template = '', array $data = [], bool $return = false): string
    {
        $route = XGP_ROOT . VIEWS_DIR . strtr($template, ['/' => DIRECTORY_SEPARATOR, '.' => DIRECTORY_SEPARATOR]) . '.blade.php';

        if (file_exists($route)) {
            return $this->setBlade($template, $data);
        }

        return $this->ciParser->parse($this->get($template), $data, $return);
    }

    private function setBlade(string $template = '', array $data = []): string
    {
        return $this->bladeParser
            ->setView(strtr($template, ['/' => '.']))
            ->share($data)
            ->run();
    }

    private function createNewParser(): void
    {
        // require email library
        $parser_library_path = XGP_ROOT . LIB_PATH . 'Ci' . DIRECTORY_SEPARATOR . 'CiParser.php';

        if (!file_exists($parser_library_path)) {
            return;
        }

        // required by the library
        if (!defined('BASEPATH')) {
            define('BASEPATH', XGP_ROOT . RESOURCES_PATH);
        }

        // use CI library
        require_once $parser_library_path;

        $this->ciParser = new CiParser();
    }

    private function createNewBladeParser(): void
    {
        // require email library
        $bladePath = XGP_ROOT . VENDOR_PATH . 'eftec' . DIRECTORY_SEPARATOR . 'bladeone' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'BladeOne.php';

        if (!file_exists($bladePath)) {
            return;
        }

        // use CI library
        require_once $bladePath;

        $this->bladeParser = new BladeOne(XGP_ROOT . VIEWS_DIR);
    }
}
