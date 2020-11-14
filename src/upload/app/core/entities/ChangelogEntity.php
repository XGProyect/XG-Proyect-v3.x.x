<?php declare (strict_types = 1);

/**
 * Changelog entity
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
 * ChangelogEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class ChangelogEntity extends Entity
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
     * Get the changelog ID
     *
     * @return int
     */
    public function getChangelogId(): int
    {
        return (int) $this->data['changelog_id'];
    }

    /**
     * Get the changelog lang ID
     *
     * @return int
     */
    public function getChangelogLangId(): int
    {
        return (int) $this->data['changelog_lang_id'];
    }

    /**
     * Get the changelog version
     *
     * @return string
     */
    public function getChangelogVersion(): string
    {
        return (string) $this->data['changelog_version'];
    }

    /**
     * Get the changelog date
     *
     * @return int
     */
    public function getChangelogDate(): string
    {
        return (string) $this->data['changelog_date'];
    }

    /**
     * Get the changelog description
     *
     * @return string
     */
    public function getChangelogDescription(): string
    {
        return (string) $this->data['changelog_description'];
    }
}
