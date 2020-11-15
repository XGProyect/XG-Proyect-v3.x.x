<?php
/**
 * Permissions
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\adm;

use App\core\enumerators\AdminPagesEnumerator as AdminPages;
use App\core\enumerators\UserRanksEnumerator as UserRanks;
use App\helpers\ArraysHelper;
use App\libraries\Functions;
use JsonException;

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
     * Defines if the admin role can be modified
     *
     * @var bool
     */
    private const ALLOW_ADMIN_MODIFICATION = false;

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
        try {
            return json_encode($this->permissions, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            die('JSON Error - ' . $e->getMessage() . ' on ' . __CLASS__ . ', line: ' . $e->getLine());
        }
    }

    /**
     * Get a list of roles
     *
     * @return array
     */
    public function getRoles(bool $no_admin = false): array
    {
        $roles = [
            UserRanks::GO,
            UserRanks::SGO,
            UserRanks::ADMIN,
        ];

        if ($no_admin) {
            unset($roles[array_search(UserRanks::ADMIN, $roles)]);
        }

        return $roles;
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
     * Get the admin sections
     *
     * @return array
     */
    public function getAdminSections(): array
    {
        return AdminPages::SECTIONS;
    }

    /**
     * Save permissions to DB
     *
     * @return void
     */
    public function savePermissions(): void
    {
        Functions::updateConfig('admin_permissions', $this->getAllPermissionsAsJsonString());
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
        if ($this->moduleExists($module) && $this->roleExists($role) && $this->isRoleEditable($role)) {
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
        if ($this->moduleExists($module) && $this->roleExists($role) && $this->isRoleEditable($role)) {
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
        return ArraysHelper::inMultiArray($module, $this->getAdminModules());
    }

    /**
     * Check if the role exists
     *
     * @param string $role
     * @return boolean
     */
    public function roleExists(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Check if the role is editable
     *
     * @param integer $role
     * @return boolean
     */
    private function isRoleEditable(int $role): bool
    {
        if ($role == UserRanks::ADMIN) {
            return ALLOW_ADMIN_MODIFICATION;
        }

        return true;
    }

    /**
     * Set the permissions
     *
     * @param string $permissions
     * @return void
     */
    private function setPermissions(string $permissions): void
    {
        try {
            $this->permissions = json_decode($permissions, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            die('JSON Error - ' . $e->getMessage() . ' on ' . __CLASS__ . ', line: ' . $e->getLine());
        }
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
