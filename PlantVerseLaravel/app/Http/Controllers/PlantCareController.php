<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class PlantCareController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function getAdvice(Request $request, $plantId)
    {
        $plant = Plant::findOrFail($plantId);

        $validated = $request->validate([
            'question' => 'required|string',
            'location' => 'nullable|string',
            'environmental_factors' => 'nullable|string',
        ]);

        try {
            $result = $this->geminiService->getPersonalizedCareAdvice(
                $plant->species,
                $validated['question'],
                $validated['location'] ?? 'Unknown',
                $validated['environmental_factors'] ?? 'Standard indoor conditions'
            );

            if ($result['success'] ?? false) {
                return response()->json([
                    'success' => true,
                    'advice' => $result['data']['advice'] ?? $result['data']['response'] ?? '',
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to get advice',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
