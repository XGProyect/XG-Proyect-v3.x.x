<?php
/**
 * User entity
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\core\entities;

use App\core\Entity;

/**
 * UserEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class UserEntity extends Entity
{
    /**
     * Constructor
     *
     * @param array $data Data
     *
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the user id
     *
     * @return integer
     */
    public function getUserId(): int
    {
        return (int) $this->data['user_id'];
    }

    /**
     * Return the user name
     *
     * @return string
     */
    public function getUserName(): string
    {
        return (string) $this->data['user_name'];
    }

    /**
     * Return the user password
     *
     * @return string
     */
    public function getUserPassword(): string
    {
        return (string) $this->data['user_password'];
    }

    /**
     * Return the user email
     *
     * @return string
     */
    public function getUserEmail(): string
    {
        return (string) $this->data['user_email'];
    }

    /**
     * Return the user authlevel
     *
     * @return integer
     */
    public function getUserAuthlevel(): int
    {
        return (int) $this->data['user_authlevel'];
    }

    /**
     * Return the user home planet id
     *
     * @return integer
     */
    public function getUserHomePlanetId(): int
    {
        return (int) $this->data['user_home_planet_id'];
    }

    /**
     * Return the user home planet galaxy
     *
     * @return integer
     */
    public function getUserGalaxy(): int
    {
        return (int) $this->data['user_galaxy'];
    }

    /**
     * Return the user home planet system
     *
     * @return integer
     */
    public function getUserSystem(): int
    {
        return (int) $this->data['user_system'];
    }

    /**
     * Return the user home planet position
     *
     * @return integer
     */
    public function getUserPlanet(): int
    {
        return (int) $this->data['user_planet'];
    }

    /**
     * Return the user current planet
     *
     * @return integer
     */
    public function getUserCurrentPlanet(): int
    {
        return (int) $this->data['user_current_planet'];
    }

    /**
     * Return the user last ip address
     *
     * @return string
     */
    public function getUserLastip(): string
    {
        return (string) $this->data['user_lastip'];
    }

    /**
     * Return the user ip during registration
     *
     * @return string
     */
    public function getUserIpAtReg(): string
    {
        return (string) $this->data['user_ip_at_reg'];
    }

    /**
     * Return the user agent
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return (string) $this->data['user_agent'];
    }

    /**
     * Return the user current viewing page
     *
     * @return string
     */
    public function getUserCurrentPage(): string
    {
        return (string) $this->data['user_current_page'];
    }

    /**
     * Return the user registration date
     *
     * @return integer
     */
    public function getUserRegisterTime(): int
    {
        return (int) $this->data['user_register_time'];
    }

    /**
     * Return the user last connection date
     *
     * @return integer
     */
    public function getUserOnlinetime(): int
    {
        return (int) $this->data['user_online_time'];
    }

    /**
     * Return the user fleet shortcuts
     *
     * @return string
     */
    public function getUserFleetShortcuts(): string
    {
        return (string) $this->data['user_fleet_shortcuts'];
    }

    /**
     * Return the user alliance id
     *
     * @return integer
     */
    public function getUserAllyId(): int
    {
        return (int) $this->data['user_ally_id'];
    }

    /**
     * Return the user alliance request
     *
     * @return integer
     */
    public function getUserAllyRequest(): int
    {
        return (int) $this->data['user_ally_request'];
    }

    /**
     * Return the user alliance request text
     *
     * @return string
     */
    public function getUserAllyRequestText(): string
    {
        return (string) $this->data['user_ally_request_text'];
    }

    /**
     * Return the user alliance registration date
     *
     * @return integer
     */
    public function getUserAllyRegisterTime(): int
    {
        return (int) $this->data['user_ally_register_time'];
    }

    /**
     * Return the user alliance rank id
     *
     * @return integer
     */
    public function getUserAllyRankId(): int
    {
        return (int) $this->data['user_ally_rank_id'];
    }

    /**
     * Return the user banned time
     *
     * @return integer
     */
    public function getUserBanned(): int
    {
        return (int) $this->data['user_banned'];
    }
}
