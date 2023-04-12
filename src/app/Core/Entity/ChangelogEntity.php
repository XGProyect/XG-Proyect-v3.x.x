<?php

declare(strict_types=1);

namespace App\Core\Entity;

use App\Core\Entity;

class ChangelogEntity extends Entity
{
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
