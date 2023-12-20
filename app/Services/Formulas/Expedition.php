<?php

namespace App\Services\Formulas;

class Expedition
{
    /**
     * Also applies for resources
     */
    public function getMaxExpeditionPoints(int $topPlayerPoints): int
    {
        if ($topPlayerPoints < 100000) {
            return 2500;
        }

        if ($topPlayerPoints < 1000000) {
            return 6000;
        }

        if ($topPlayerPoints < 5000000) {
            return 9000;
        }

        if ($topPlayerPoints < 25000000) {
            return 12000;
        }

        if ($topPlayerPoints < 50000000) {
            return 15000;
        }

        if ($topPlayerPoints < 75000000) {
            return 18000;
        }

        if ($topPlayerPoints < 100000000) {
            return 21000;
        }

        return 25000;
    }

    public function getMaxShipsExpeditionPoints(int $topPlayerPoints): int
    {
        return $this->getMaxExpeditionPoints($topPlayerPoints) * 100;
    }

    public function calculateExpeditionPoints(int $structuralIntegrity): int
    {
        return ($structuralIntegrity * 5 / 1000);
    }

    public function getExpeditionResult(): string
    {
        // probability ratios - we add a weight
        $events = [
            'darkMatter' => 900, // 9%
            'ships' => 2200, // 22%
            'resources' => 3250, // 32.5%
            'pirates' => 580, // 5.8%
            'aliens' => 260, // 2.6%
            'delay' => 700, // 7%
            'early' => 200, // 2%
            'nothing' => 1860, // 18.6%
            'merchant' => 17, // 0.17%
            'blackHole' => 33, // 0.33%
        ];
        $randomNumber = mt_rand(0, array_sum($events));
        $sum = 0;

        foreach ($events as $event => $probability) {
            $sum += $probability;

            if ($randomNumber <= $sum) {
                return $event;
            }
        }

        // fallback
        return 'nothing';
    }

    public function calculateDarkMatterSourceSize(): string
    {
        // probability ratios - we add a weight
        $discoveryTypes = [
            'small' => 8900, // 89%
            'medium' => 2400, // 10%
            'large' => 750, // 1%
        ];
        $randomNumber = mt_rand(0, array_sum($discoveryTypes));
        $sum = 0;

        foreach ($discoveryTypes as $discovery => $probability) {
            $sum += $probability;

            if ($randomNumber <= $sum) {
                return $discovery;
            }
        }

        // fallback
        return 'small';
    }

    public function getDarkMatterSourceSize(string $discoveryType): int
    {
        if ($discoveryType === 'medium') {
            return mt_rand(500, 700);
        }

        if ($discoveryType === 'large') {
            return mt_rand(1000, 1800);
        }

        return mt_rand(300, 400); // $discoveryType === 'small'
    }

    public function calculateResourceTypeObtained(): string
    {
        // probability ratios - we add a weight
        $resources = [
            'metal' => 6850, // 68,5%
            'crystal' => 2400, // 24%
            'deuterium' => 750, // 7.5%
        ];
        $randomNumber = mt_rand(0, array_sum($resources));
        $sum = 0;

        foreach ($resources as $resource => $probability) {
            $sum += $probability;

            if ($randomNumber <= $sum) {
                return $resource;
            }
        }

        // fallback
        return 'metal';
    }

    public function calculateResourceSourceSize(): string
    {
        // probability ratios - we add a weight
        $discoveryTypes = [
            'normal' => 8900, // 89%
            'large' => 2400, // 10%
            'xl' => 750, // 1%
        ];
        $randomNumber = mt_rand(0, array_sum($discoveryTypes));
        $sum = 0;

        foreach ($discoveryTypes as $discovery => $probability) {
            $sum += $probability;

            if ($randomNumber <= $sum) {
                return $discovery;
            }
        }

        // fallback
        return 'normal';
    }

    public function getResourceSourceSizeMultChances(string $discoveryType): int
    {
        if ($discoveryType === 'large') {
            return mt_rand(50, 100);
        }

        if ($discoveryType === 'xl') {
            return mt_rand(100, 200);
        }

        return mt_rand(10, 50); // $discoveryType === 'normal'
    }

    public function getResourceFoundAmount(int $chancesMultiplier, int $expeditionPoints, string $resourceType): int
    {
        $resource = [
            'metal' => 1,
            'crystal' => 2,
            'deuterium' => 3,
        ];

        return floor($chancesMultiplier * $expeditionPoints / $resource[$resourceType]);
    }

    public function calculateShipFoundAmount(int $chancesMultiplier, int $expeditionPoints): int
    {
        return floor($chancesMultiplier * $expeditionPoints / 2);
    }

    /**
     * Only these ships are computed for the expeditions points
     */
    public function getPossibleShips(): array
    {
        return [
            202, // ship_small_cargo_ship
            203, // ship_big_cargo_ship
            204, // ship_light_fighter
            205, // ship_heavy_fighter
            206, // ship_cruiser
            207, // ship_battleship
            210, // ship_espionage_probe
            211, // ship_bomber
            213, // ship_destroyer
            215, // ship_battlecruiser
        ];
    }

    /**
     * Only these ships are obtainable on an expedition
     */
    public function getShipsObtainableChances(): array
    {
        return [
            202 => 0.1, // ship_small_cargo_ship
            203 => 0.1, // ship_big_cargo_ship
            204 => 0.1, // ship_light_fighter
            205 => 0.5, // ship_heavy_fighter
            206 => 0.25, // ship_cruiser
            207 => 0.125, // ship_battleship
            210 => 0.1, // ship_espionage_probe
            211 => 0.0625, // ship_bomber
            213 => 0.0625, // ship_destroyer
            215 => 0.0625, // ship_battlecruiser
        ];
    }

    public function getFleetDeplay(): int
    {
        // probability ratios - we add a weight
        $resources = [
            2 => 8900, // 89%
            3 => 2400, // 10%
            5 => 750, // 1%
        ];
        $randomNumber = mt_rand(0, array_sum($resources));
        $sum = 0;

        foreach ($resources as $resource => $probability) {
            $sum += $probability;

            if ($randomNumber <= $sum) {
                return $resource;
            }
        }

        // fallback
        return 2;
    }
}
