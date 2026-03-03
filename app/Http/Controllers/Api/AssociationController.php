<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cagnote;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AssociationController extends Controller
{
    /**
     * Get all associations grouped by country with their campaigns
     * GET /api/associations-by-country
     */
    public function getAssociationsByCountry(): \Illuminate\Http\JsonResponse
    {
        try {
            // Get all associations
            $associations = User::where('type', 'association')
                ->select('id', 'name', 'country', 'category', 'logo_path', 'phone_number', 'description')
                ->get();

            // Group by country
            $groupedByCountry = [];
            foreach ($associations as $association) {
                $country = $association->country ?? 'Unknown';
                
                if (!isset($groupedByCountry[$country])) {
                    $groupedByCountry[$country] = [];
                }

                // Get campaigns for this association
                $campaigns = Cagnote::where('user_id', $association->id)
                    ->select('id', 'title', 'description', 'image_url', 'objective_amount', 'collected_amount', 'category')
                    ->get()
                    ->map(function ($campaign) {
                        return [
                            'id' => $campaign->id,
                            'title' => $campaign->title,
                            'description' => $campaign->description,
                            'image_url' => $campaign->image_url,
                            'goal' => $campaign->objective_amount,
                            'collected' => $campaign->collected_amount,
                            'category' => $campaign->category,
                        ];
                    })
                    ->toArray();

                $groupedByCountry[$country][] = [
                    'id' => $association->id,
                    'name' => $association->name,
                    'category' => $association->category,
                    'logo_path' => $association->logo_path,
                    'phone_number' => $association->phone_number,
                    'description' => $association->description,
                    'campaigns' => $campaigns
                ];
            }

            // Count campaigns per country for markers
            $countryCampaignCount = [];
            foreach ($groupedByCountry as $country => $assocs) {
                $total = 0;
                foreach ($assocs as $assoc) {
                    $total += count($assoc['campaigns']);
                }
                $countryCampaignCount[$country] = $total;
            }

            return response()->json([
                'success' => true,
                'message' => 'Associations récupérées avec succès',
                'data' => $groupedByCountry,
                'campaignCounts' => $countryCampaignCount
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching associations by country: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des associations',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get campaigns for a specific association
     * GET /api/associations/{associationId}/campaigns
     */
    public function getAssociationCampaigns($associationId): \Illuminate\Http\JsonResponse
    {
        try {
            $campaigns = Cagnote::where('user_id', $associationId)
                ->select('id', 'title', 'description', 'image_url', 'objective_amount', 'collected_amount', 'category')
                ->get()
                ->map(function ($campaign) {
                    return [
                        'id' => $campaign->id,
                        'title' => $campaign->title,
                        'description' => $campaign->description,
                        'image_url' => $campaign->image_url,
                        'goal' => $campaign->objective_amount,
                        'collected' => $campaign->collected_amount,
                        'category' => $campaign->category,
                    ];
                })
                ->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Campagnes récupérées avec succès',
                'data' => $campaigns
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching campaigns: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des campagnes',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
