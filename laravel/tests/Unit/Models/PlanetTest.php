<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Planet;
use Tests\TestCase;

class PlanetTest extends TestCase
{
    public function test_planet_coordinates_attribute(): void
    {
        $planet = new Planet([
            'planet_galaxy' => 1,
            'planet_system' => 100,
            'planet_planet' => 5,
        ]);

        $this->assertEquals('1:100:5', $planet->coordinates);
    }

    public function test_planet_has_resources_check(): void
    {
        $planet = new Planet([
            'planet_metal' => 1000,
            'planet_crystal' => 500,
            'planet_deuterium' => 100,
        ]);

        $this->assertTrue($planet->hasResources(500, 200, 50));
        $this->assertFalse($planet->hasResources(2000, 100, 50));
        $this->assertFalse($planet->hasResources(500, 1000, 50));
        $this->assertFalse($planet->hasResources(500, 200, 200));
    }

    public function test_planet_deduct_resources(): void
    {
        $planet = new Planet([
            'planet_metal' => 1000,
            'planet_crystal' => 500,
            'planet_deuterium' => 100,
        ]);

        // Should fail - not enough resources
        $result = $planet->deductResources(2000, 100, 50);
        $this->assertFalse($result);

        // Deduct resources successfully
        $result = $planet->deductResources(500, 200, 50);
        $this->assertTrue($result);
        $this->assertEquals(500, $planet->planet_metal);
        $this->assertEquals(300, $planet->planet_crystal);
        $this->assertEquals(50, $planet->planet_deuterium);
    }
}
