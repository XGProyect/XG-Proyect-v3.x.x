<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\Libraries\NoobsProtectionLib as NoobsProtectionLibModel;

class NoobsProtectionLib
{
    private NoobsProtectionLibModel $noobsprotectionlibModel;
    private $protection;
    private $protectiontime;
    private $protectionmulti;
    private $allowed_level;

    public function __construct()
    {
        $this->noobsprotectionlibModel = new NoobsProtectionLibModel();

        // set configs
        $this->setAllSettings();
    }

    public function setAllSettings(): void
    {
        $configs = $this->noobsprotectionlibModel->readAllConfigs();

        $this->protection = (bool) $configs['noobprotection'];
        $this->protectiontime = (int) $configs['noobprotectiontime'];
        $this->protectionmulti = (int) $configs['noobprotectionmulti'];
        $this->allowed_level = (int) $configs['stat_admin_level'];
    }

    public function isWeak(int $current_points, int $other_points): bool
    {
        if ($this->protection) {
            if ($this->protectionmulti == 0) {
                $this->protectionmulti = 1;
            }

            if ($current_points > $other_points * $this->protectionmulti) {
                if ($other_points > $this->protectiontime && $this->protectiontime > 0) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    public function isStrong(int $current_points, int $other_points): bool
    {
        if ($this->protection) {
            if ($this->protectionmulti == 0) {
                $this->protectionmulti = 1;
            }

            if ($current_points * $this->protectionmulti < $other_points) {
                if ($current_points > $this->protectiontime && $this->protectiontime > 0) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    public function returnPoints(int $current_user_id, int $other_user_id): array
    {
        return $this->noobsprotectionlibModel->returnBothPartiesPoints($current_user_id, $other_user_id);
    }

    public function isRankVisible(int $user_auth_level): bool
    {
        return ($user_auth_level <= $this->allowed_level);
    }
}
