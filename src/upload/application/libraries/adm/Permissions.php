<?php
/**
 * Permissions
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\adm;

use application\core\enumerators\AdminPagesEnumerator as AdminPages;
use application\core\enumerators\UserRanksEnumerator as UserRanks;

/**
 * Permissions Class
 *
 * @category Classes
 * @package  buildings
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Permissions
{
    /**
     * Contains the permissions array
     *
     * @var array
     */
    private $permissions = [];

    /**
     * Constructor
     *
     * @param string $permissions
     */
    public function __construct(string $permissions)
    {
        try {
            if (is_array($permissions)) {
                throw new Exception('JSON Expected!');
            }

            $this->setPermissions($permissions);
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Get all the permissions as an Array
     *
     * @return array
     */
    public function getAllPermissionsAsArray(): array
    {
        return $this->permissions;
    }

    /**
     * Get all the permissions as a JSON
     *
     * @return string
     */
    public function getAllPermissionsAsJsonString(): string
    {
        return json_encode($this->permissions);
    }

    /**
     * Get a list of roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        return [
            UserRanks::GO,
            UserRanks::SGO,
            UserRanks::ADMIN,
        ];
    }

    /**
     * Get list of admin modules (pages)
     *
     * @return array
     */
    public function getAdminModules(): array
    {
        return [
            AdminPages::CONFIGURATION,
            AdminPages::INFORMATION,
            AdminPages::EDITION,
            AdminPages::TOOLS,
            AdminPages::MAINTENANCE,
        ];
    }

    /**
     * Check if access is allowed, returns true if it is
     *
     * @param string $module
     * @param integer $role
     * @return boolean
     */
    public function isAccessAllowed(string $module, int $role): bool
    {
        return ($role === UserRanks::ADMIN or (isset($this->permissions[$module][$role]) && $this->permissions[$module][$role] === 1));
    }

    /**
     * Grant a role access to a new module
     *
     * @param string $module
     * @param integer $role
     * @return void
     */
    public function grantAccess(string $module, int $role): void
    {
        if ($this->moduleExists($module) && $this->roleExists($role)) {
            $this->permissions[$module][$role] = 1;
        }
    }

    /**
     * Remove access to a role from a module
     *
     * @param string $module
     * @param integer $role
     * @return void
     */
    public function removeAccess(string $module, int $role): void
    {
        if ($this->moduleExists($module) && $this->roleExists($role)) {
            $this->permissions[$module][$role] = 0;
        }
    }

    /**
     * Check if module exists
     *
     * @param string $module
     * @return boolean
     */
    public function moduleExists(string $module): bool
    {
        return in_array($module, $this->getAdminModules());
    }

    /**
     * Check if the role exists
     *
     * @param string $module
     * @return boolean
     */
    public function roleExists(string $module): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Set the permissions
     *
     * @param string $permissions
     * @return void
     */
    private function setPermissions(string $permissions): void
    {
        $this->permissions = json_decode($permissions, true);
    }

    /**
     * Get the permissions
     *
     * @return array
     */
    private function getPermissions(): array
    {
        return $this->permissions;
    }
}

/* end of permissions.php */
