<?php declare (strict_types = 1);

/**
 * Options Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\core;

use App\core\Model;

/**
 * Options Class
 */
class Options extends Model
{
    /**
     * Get all options from the table
     *
     * @return mysqli_result
     */
    public function getAllOptions(): \mysqli_result
    {
        return $this->db->query(
            "SELECT * FROM `" . OPTIONS . "`;"
        );
    }

    /**
     * Get a single option from the table
     *
     * @param string $option
     * @return string|null
     */
    public function getOption(string $option): ?string
    {
        return $this->db->queryFetch(
            "SELECT *
                FROM `" . OPTIONS . "`
                WHERE `option_name` = '" . $option . "';"
        )['option_value'];
    }

    /**
     * Insert/Update a new option
     *
     * @param string $option
     * @param string $value
     * @return boolean
     */
    public function writeOption(string $option, string $value): bool
    {
        return $this->db->query(
            "INSERT INTO `" . OPTIONS . "`
                (`option_name`, `option_value`)
            VALUES('" . $option . "', '" . $value . "')
            ON DUPLICATE KEY UPDATE
                `option_name` = VALUES(option_name),
                `option_value` = '" . $value . "';"
        );
    }

    /**
     * Delete an option
     *
     * @param string $option
     * @return boolean
     */
    public function deleteOption(string $option): bool
    {
        return $this->db->query(
            "DELETE `" . OPTIONS . "`
                WHERE `option_name` = '" . $option . "';"
        );
    }
}
