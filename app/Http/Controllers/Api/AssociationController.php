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
            $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');

            $campaigns = Cagnote::with([
                    'user:id,name,logo_path,phone_number,description',
                ])
                ->where('publication_status', 'approved')
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->select(
                    'id',
                    'user_id',
                    'title',
                    'description',
                    'image_url',
                    'photos',
                    'objective_amount',
                    'collected_amount',
                    'category',
                    'location'
                )
                ->get();

            $groupedByCountry = [];

            foreach ($campaigns as $campaign) {
                $association = $campaign->user;
                if (!$association) {
                    continue;
                }

                $country = $campaign->location ?? 'Unknown';

                if (!isset($groupedByCountry[$country])) {
                    $groupedByCountry[$country] = [];
                }

                if (!isset($groupedByCountry[$country][$association->id])) {
                    $logoPath = $association->logo_path;
                    if ($logoPath && !str_starts_with($logoPath, 'http')) {
                        $logoPath = $baseUrl . '/' . ltrim($logoPath, '/');
                    }

                    $groupedByCountry[$country][$association->id] = [
                        'id' => $association->id,
                        'name' => $association->name,
                        'logo_path' => $logoPath,
                        'phone_number' => $association->phone_number,
                        'description' => $association->description,
                        'campaigns' => [],
                    ];
                }

                $imageUrl = $campaign->image_url;

                // Some campaigns store media in `photos` while `image_url` is empty.
                if (!$imageUrl && !empty($campaign->photos) && is_array($campaign->photos)) {
                    $firstPhoto = $campaign->photos[0] ?? null;
                    if (is_string($firstPhoto) && $firstPhoto !== '') {
                        $imageUrl = $firstPhoto;
                    }
                }

                if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = $baseUrl . '/' . ltrim($imageUrl, '/');
                }

                $groupedByCountry[$country][$association->id]['campaigns'][] = [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'image_url' => $imageUrl,
                    'goal' => $campaign->objective_amount,
                    'collected' => $campaign->collected_amount,
                    'category' => $campaign->category,
                ];
            }

            foreach ($groupedByCountry as $country => $associations) {
                $groupedByCountry[$country] = array_values($associations);
            }

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
