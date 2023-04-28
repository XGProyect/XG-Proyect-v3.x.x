<?php

namespace App\Models\Game;

use App\Core\Model;

class Changelog extends Model
{
    /**
     * Get changelog entries
     *
     * @return array
     */
    public function getAllChangelogEntries()
    {
        return $this->db->queryFetchAll(
            'SELECT
                c.`changelog_version`,
                c.`changelog_date`,
                c.`changelog_description`
            FROM `' . CHANGELOG . '` c
            LEFT JOIN `' . LANGUAGES . '` l
                ON l.`language_id` = c.`changelog_lang_id`
            WHERE l.`language_name` = (
                SELECT o.`option_value` FROM `' . OPTIONS . "` o WHERE `option_name` = 'lang'
            )
            ORDER BY c.`changelog_date` DESC"
        );
    }
}
