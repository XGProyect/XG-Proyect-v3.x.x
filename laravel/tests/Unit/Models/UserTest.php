<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_can_check_if_banned(): void
    {
        $user = new User([
            'user_banned' => false,
        ]);

        $this->assertFalse($user->isBanned());

        $user->user_banned = true;
        $this->assertTrue($user->isBanned());
    }

    public function test_user_premium_status(): void
    {
        $user = new User();

        // No premium
        $this->assertFalse($user->hasPremium());

        // Premium active
        $user->premium_dark_matter_expire_time = now()->addDays(7);
        $this->assertTrue($user->hasPremium());

        // Premium expired
        $user->premium_dark_matter_expire_time = now()->subDays(1);
        $this->assertFalse($user->hasPremium());
    }

    public function test_user_officer_status(): void
    {
        $user = new User();

        // No officers
        $this->assertFalse($user->hasOfficer('commander'));

        // Active officer
        $user->premium_officer_commander_until = now()->addDays(7);
        $this->assertTrue($user->hasOfficer('commander'));

        // Expired officer
        $user->premium_officer_commander_until = now()->subDays(1);
        $this->assertFalse($user->hasOfficer('commander'));
    }

    public function test_user_vacation_mode(): void
    {
        $user = new User([
            'preference_vacation_mode' => false,
        ]);

        $this->assertFalse($user->isOnVacation());

        $user->preference_vacation_mode = true;
        $user->preference_vacation_mode_until = now()->addDays(7);
        $this->assertTrue($user->isOnVacation());
    }
}
