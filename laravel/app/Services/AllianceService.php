<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Alliance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AllianceService
{
    /**
     * Create a new alliance.
     */
    public function createAlliance(User $user, array $data): Alliance
    {
        return DB::transaction(function () use ($user, $data) {
            $alliance = Alliance::create([
                'alliance_name' => $data['alliance_name'],
                'alliance_tag' => $data['alliance_tag'],
                'alliance_owner' => $user->user_id,
                'alliance_description' => $data['alliance_description'] ?? null,
                'alliance_web' => $data['alliance_web'] ?? null,
                'alliance_request_notallow' => $data['alliance_request_notallow'] ?? false,
            ]);

            // Add creator as member
            $user->update(['alliance_id' => $alliance->alliance_id]);

            return $alliance;
        });
    }

    /**
     * Join alliance (for user).
     */
    public function joinAlliance(User $user, Alliance $alliance): void
    {
        if (!$alliance->acceptsApplications()) {
            throw new \Exception('Alliance is not accepting applications');
        }

        if ($user->alliance_id) {
            throw new \Exception('User is already in an alliance');
        }

        $user->update(['alliance_id' => $alliance->alliance_id]);
    }

    /**
     * Leave alliance (for user).
     */
    public function leaveAlliance(User $user): void
    {
        if (!$user->alliance_id) {
            throw new \Exception('User is not in an alliance');
        }

        $alliance = $user->alliance;

        // Cannot leave if owner (must transfer ownership first)
        if ($alliance->alliance_owner === $user->user_id) {
            throw new \Exception('Owner cannot leave alliance. Transfer ownership first.');
        }

        $user->update(['alliance_id' => null]);
    }

    /**
     * Kick member from alliance.
     */
    public function kickMember(Alliance $alliance, User $member): void
    {
        if ($member->alliance_id !== $alliance->alliance_id) {
            throw new \Exception('User is not a member of this alliance');
        }

        if ($alliance->alliance_owner === $member->user_id) {
            throw new \Exception('Cannot kick alliance owner');
        }

        $member->update(['alliance_id' => null]);
    }

    /**
     * Transfer alliance ownership.
     */
    public function transferOwnership(Alliance $alliance, User $newOwner): void
    {
        if ($newOwner->alliance_id !== $alliance->alliance_id) {
            throw new \Exception('New owner must be a member of the alliance');
        }

        $alliance->update(['alliance_owner' => $newOwner->user_id]);
    }

    /**
     * Delete alliance (only if owner and no other members).
     */
    public function deleteAlliance(Alliance $alliance): void
    {
        $memberCount = $alliance->members()->count();

        if ($memberCount > 1) {
            throw new \Exception('Cannot delete alliance with members. Remove all members first.');
        }

        DB::transaction(function () use ($alliance) {
            // Remove owner from alliance
            $alliance->members()->update(['alliance_id' => null]);

            // Delete alliance
            $alliance->delete();
        });
    }
}
