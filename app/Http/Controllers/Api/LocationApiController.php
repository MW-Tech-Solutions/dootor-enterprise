<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AfricanLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationApiController extends Controller
{
    /**
     * Get list of all 54 African countries with division terms & metadata.
     */
    public function africanCountries()
    {
        $countries = Cache::remember('african_countries_list_v2', 86400, function () {
            return AfricanLocationService::allCountries();
        });

        return response()->json([
            'status' => 'success',
            'default' => 'Nigeria',
            'countries' => $countries,
        ]);
    }

    /**
     * Dynamically fetch actual states / provinces / regions / counties for a selected country.
     */
    public function divisions(Request $request)
    {
        $country = $request->query('country', 'Nigeria');
        $cacheKey = 'african_divisions_v2_' . md5(strtolower(trim($country)));

        $data = Cache::remember($cacheKey, 86400, function () use ($country) {
            $info = AfricanLocationService::getCountryInfo($country);
            if (!$info) {
                // Fallback to Nigeria if unknown country requested
                $info = AfricanLocationService::getCountryInfo('Nigeria');
            }
            return [
                'country' => $info['name'] ?? $country,
                'code' => $info['code'] ?? 'NG',
                'term' => $info['term'] ?? 'State',
                'divisions' => $info['divisions'] ?? [],
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
