<?php

declare (strict_types = 1);

/**
 * Controller
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\core;

use application\core\enumerators\SwitchIntEnumerator as SwitchInt;
use application\core\enumerators\UserRanksEnumerator;
use application\core\Template;
use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Controller Class
 */
abstract class Controller extends XGPCore
{
    /**
     * Contains the current user data
     *
     * @var array
     */
    private $current_user = [];

    /**
     * Contains the current planet data
     *
     * @var array
     */
    private $current_planet = [];

    /**
     * Contains the whole set of objects by request
     *
     * @var array
     */
    private $objects_list = [];

    /**
     *
     * @var \Template
     */
    private $template = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setUserData();
        $this->setPlanetData();
        $this->setObjects();
        $this->setTemplate();

        $this->isServerOpen();
    }

    /**
     * Set the user Data
     *
     * @return void
     */
    private function setUserData(): void
    {
        $this->current_user = parent::$users->getUserData();
    }

    /**
     * Set the planet Data
     *
     * @return void
     */
    private function setPlanetData(): void
    {
        $this->current_planet = parent::$users->getPlanetData();
    }

    /**
     * Set objects data
     *
     * @return void
     */
    private function setObjects(): void
    {
        $this->objects_list = parent::$objects;
    }

    /**
     * Set template data
     *
     * @return void
     */
    private function setTemplate(): void
    {
        $this->template = new Template();
    }

    /**
     * Check if the server is open
     *
     * @return void
     */
    private function isServerOpen(): void
    {
        if (!defined('IN_INSTALL') && !defined('IN_ADMIN')) {
            $user_level = isset($this->current_user['user_authlevel']) ?? 0;

            if (FunctionsLib::readConfig('game_enable') == SwitchInt::off
                && $user_level < UserRanksEnumerator::ADMIN) {
                FunctionsLib::message(FunctionsLib::readConfig('close_reason'), '', '', false, false);
                die();
            }
        }
    }

    /**
     * Return the user data
     *
     * @return array
     */
    protected function getUserData(): array
    {
        return $this->current_user;
    }

    /**
     * Return the planet data
     *
     * @return array
     */
    protected function getPlanetData(): array
    {
        return $this->current_planet;
    }

    /**
     * Return the objects data
     *
     * @return \Objects
     */
    protected function getObjects(): Objects
    {
        return $this->objects_list;
    }

    /**
     * Returns the template
     *
     * @return \Template
     */
    protected function getTemplate(): Template
    {
        return $this->template;
    }
}
