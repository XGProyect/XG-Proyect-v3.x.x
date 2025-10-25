<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planet\UpdatePlanetRequest;
use App\Http\Resources\PlanetResource;
use App\Models\Planet;
use App\Services\PlanetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanetController extends Controller
{
    public function __construct(
        private readonly PlanetService $planetService
    ) {}

    /**
     * Get all planets for authenticated user.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $planets = Planet::where('user_id', $request->user()->user_id)
            ->orderBy('planet_galaxy')
            ->orderBy('planet_system')
            ->orderBy('planet_planet')
            ->get();

        return PlanetResource::collection($planets);
    }

    /**
     * Get specific planet details.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $planet = Planet::where('planet_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->firstOrFail();

        // Update resources before returning
        $planet = $this->planetService->updatePlanetResources($planet);

        return response()->json([
            'success' => true,
            'data' => new PlanetResource($planet),
        ]);
    }

    /**
     * Update planet (e.g., rename).
     */
    public function update(UpdatePlanetRequest $request, int $id): JsonResponse
    {
        $planet = Planet::where('planet_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->firstOrFail();

        if ($request->has('planet_name')) {
            $planet = $this->planetService->renamePlanet($planet, $request->validated('planet_name'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Planet updated successfully',
            'data' => new PlanetResource($planet),
        ]);
    }
}
