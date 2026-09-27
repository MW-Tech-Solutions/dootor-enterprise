<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationApiController extends Controller
{
    /**
     * Get list of all supported countries with division terms & metadata.
     */
    public function africanCountries()
    {
        $countries = Cache::remember('global_countries_list_v4', 86400, function () {
            return LocationService::allCountries();
        });

        return response()->json([
            'status' => 'success',
            'default_applying_from' => 'Canada',
            'default_service_requested' => 'Nigeria',
            'countries' => array_values($countries),
        ]);
    }

    /**
     * Dynamically fetch actual states / provinces / regions / counties for a selected country.
     */
    public function divisions(Request $request)
    {
        $country = trim((string) $request->query('country', 'Canada'));
        $cacheKey = 'global_divisions_v4_' . md5(strtolower($country));

        $data = Cache::remember($cacheKey, 86400, function () use ($country) {
            $term = LocationService::getDivisionLabel($country);
            $divisions = LocationService::getDivisions($country);

            return [
                'country' => $country,
                'term' => $term,
                'divisions' => $divisions,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Dynamically fetch cities for a country division.
     */
    public function cities(Request $request)
    {
        $country = trim((string) $request->query('country', 'Canada'));
        $division = trim((string) $request->query('division', ''));

        $cities = LocationService::getCities($country, $division);

        return response()->json([
            'status' => 'success',
            'country' => $country,
            'division' => $division,
            'cities' => $cities,
        ]);
    }
}
