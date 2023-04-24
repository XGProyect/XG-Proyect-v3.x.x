<?php

namespace App\Models\Adm;

use App\Core\Model;

class Login extends Model
{
    public function getLoginData(string $userEmail): array
    {
        $result = $this->db->queryFetch(
            'SELECT
                `user_id`,
                `user_name`,
                `user_password`
            FROM `' . USERS . "`
            WHERE `user_email` = '" . $this->db->escapeValue($userEmail) . "'
                AND `user_authlevel` >= '1'
            LIMIT 1"
        );

        if ($result) {
            return $result;
        }

        return [];
    }
}
