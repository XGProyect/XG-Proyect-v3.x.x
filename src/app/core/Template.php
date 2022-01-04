<?php
/**
 * Template
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace App\core;

use CI_Parser;
use eftec\bladeone\BladeOne;
use Exception;

/**
 * XGPCore Class
 */
class Template
{
    /**
     * @var CI_Parser CodeIgniter Parser Class
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
        $parser_library_path = XGP_ROOT . SYSTEM_PATH . 'ci3_custom' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Parser.php';

        if (!file_exists($parser_library_path)) {
            return;
        }

        // required by the library
        if (!defined('BASEPATH')) {
            define('BASEPATH', XGP_ROOT . APP_PATH);
        }

        // use CI library
        require_once $parser_library_path;

        $this->ciParser = new CI_Parser();
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
