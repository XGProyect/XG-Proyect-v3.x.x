<?php

declare(strict_types=1);

namespace App\Models\Home;

use App\Core\Model;

class Home extends Model
{
    public function getUserWithProvidedCredentials(string $email): ?array
    {
        return $this->db->queryFetch(
            'SELECT
                u.`user_id`,
                u.`user_name`,
                u.`user_password`,
                b.`banned_longer`
            FROM `' . USERS . '` AS u
            LEFT JOIN `' . BANNED . "` AS b
                ON b.`banned_who` = u.`user_name`
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1"
        );
    }

    public function setUserHomeCurrentPlanet(int $user_id): void
    {
        $this->db->query(
            'UPDATE `' . USERS . "` SET
                `user_current_planet` = `user_home_planet_id`
            WHERE `user_id` ='" . $user_id . "'"
        );
    }

    public function removeBan(string $user_name): void
    {
        $this->db->query(
            'DELETE FROM `' . BANNED . "`
            WHERE `banned_who` = '" . $user_name . "'"
        );
    }
}
