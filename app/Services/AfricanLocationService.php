<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AfricanLocationService
{
    /**
     * Map of 54 internationally recognized African countries to their ISO2 codes and administrative division terms.
     */
    protected static ?array $data = null;

    protected static function initializeData(): array
    {
        if (self::$data !== null) {
            return self::$data;
        }

        self::$data = [
            'Algeria' => ['code' => 'DZ', 'term' => 'Province / Wilaya'],
            'Angola' => ['code' => 'AO', 'term' => 'Province'],
            'Benin' => ['code' => 'BJ', 'term' => 'Department'],
            'Botswana' => ['code' => 'BW', 'term' => 'District'],
            'Burkina Faso' => ['code' => 'BF', 'term' => 'Region'],
            'Burundi' => ['code' => 'BI', 'term' => 'Province'],
            'Cabo Verde' => ['code' => 'CV', 'term' => 'Municipality'],
            'Cameroon' => ['code' => 'CM', 'term' => 'Region'],
            'Central African Republic' => ['code' => 'CF', 'term' => 'Prefecture'],
            'Chad' => ['code' => 'TD', 'term' => 'Region'],
            'Comoros' => ['code' => 'KM', 'term' => 'Island'],
            'Congo' => ['code' => 'CG', 'term' => 'Department'],
            'Democratic Republic of the Congo' => ['code' => 'CD', 'term' => 'Province'],
            'Djibouti' => ['code' => 'DJ', 'term' => 'Region'],
            'Egypt' => ['code' => 'EG', 'term' => 'Governorate'],
            'Equatorial Guinea' => ['code' => 'GQ', 'term' => 'Province'],
            'Eritrea' => ['code' => 'ER', 'term' => 'Region'],
            'Eswatini' => ['code' => 'SZ', 'term' => 'Region'],
            'Ethiopia' => ['code' => 'ET', 'term' => 'Region'],
            'Gabon' => ['code' => 'GA', 'term' => 'Province'],
            'Gambia' => ['code' => 'GM', 'term' => 'Division'],
            'Ghana' => ['code' => 'GH', 'term' => 'Region'],
            'Guinea' => ['code' => 'GN', 'term' => 'Region'],
            'Guinea-Bissau' => ['code' => 'GW', 'term' => 'Region'],
            'Ivory Coast' => ['code' => 'CI', 'term' => 'District'],
            'Kenya' => ['code' => 'KE', 'term' => 'County'],
            'Lesotho' => ['code' => 'LS', 'term' => 'District'],
            'Liberia' => ['code' => 'LR', 'term' => 'County'],
            'Libya' => ['code' => 'LY', 'term' => 'District'],
            'Madagascar' => ['code' => 'MG', 'term' => 'Region'],
            'Malawi' => ['code' => 'MW', 'term' => 'Region'],
            'Mali' => ['code' => 'ML', 'term' => 'Region'],
            'Mauritania' => ['code' => 'MR', 'term' => 'Region'],
            'Mauritius' => ['code' => 'MU', 'term' => 'District'],
            'Morocco' => ['code' => 'MA', 'term' => 'Region'],
            'Mozambique' => ['code' => 'MZ', 'term' => 'Province'],
            'Namibia' => ['code' => 'NA', 'term' => 'Region'],
            'Niger' => ['code' => 'NE', 'term' => 'Region'],
            'Nigeria' => ['code' => 'NG', 'term' => 'State'],
            'Rwanda' => ['code' => 'RW', 'term' => 'Province'],
            'Sao Tome and Principe' => ['code' => 'ST', 'term' => 'District'],
            'Senegal' => ['code' => 'SN', 'term' => 'Region'],
            'Seychelles' => ['code' => 'SC', 'term' => 'District'],
            'Sierra Leone' => ['code' => 'SL', 'term' => 'Province'],
            'Somalia' => ['code' => 'SO', 'term' => 'Region'],
            'South Africa' => ['code' => 'ZA', 'term' => 'Province'],
            'South Sudan' => ['code' => 'SS', 'term' => 'State'],
            'Sudan' => ['code' => 'SD', 'term' => 'State'],
            'Tanzania' => ['code' => 'TZ', 'term' => 'Region'],
            'Togo' => ['code' => 'TG', 'term' => 'Region'],
            'Tunisia' => ['code' => 'TN', 'term' => 'Governorate'],
            'Uganda' => ['code' => 'UG', 'term' => 'District'],
            'Zambia' => ['code' => 'ZM', 'term' => 'Province'],
            'Zimbabwe' => ['code' => 'ZW', 'term' => 'Province'],
        ];

        return self::$data;
    }

    /**
     * Get list of all 54 African countries with ISO codes & administrative division terms.
     * Integrates CountriesNow API (https://countriesnow.space/api/v0.1/countries).
     */
    public static function allCountries(): array
    {
        return Cache::remember('countriesnow_african_countries_v4', 86400, function () {
            $dataset = self::initializeData();

            $isoMap = [];
            foreach ($dataset as $cName => $cInfo) {
                $isoMap[$cInfo['code']] = [
                    'name' => $cName,
                    'code' => $cInfo['code'],
                    'term' => $cInfo['term'],
                ];
            }

            try {
                $response = Http::timeout(5)->get('https://countriesnow.space/api/v0.1/countries');
                if ($response->successful()) {
                    $apiData = $response->json('data') ?? [];
                    $filtered = [];
                    $seenCodes = [];

                    foreach ($apiData as $item) {
                        $iso2 = strtoupper($item['iso2'] ?? '');
                        if (isset($isoMap[$iso2])) {
                            $filtered[] = $isoMap[$iso2];
                            $seenCodes[$iso2] = true;
                        }
                    }

                    foreach ($isoMap as $code => $info) {
                        if (!isset($seenCodes[$code])) {
                            $filtered[] = $info;
                        }
                    }

                    if (count($filtered) >= 50) {
                        return $filtered;
                    }
                }
            } catch (\Throwable $e) {
                logger()->warning("[AfricanLocationService] Failed fetching countries from CountriesNow API: " . $e->getMessage());
            }

            $list = [];
            foreach ($dataset as $countryName => $info) {
                $list[] = [
                    'name' => $countryName,
                    'code' => $info['code'],
                    'term' => $info['term'],
                ];
            }
            return $list;
        });
    }

    /**
     * Get list of all 54 African country names as strings.
     */
    public static function countryNames(): array
    {
        $countries = self::allCountries();
        return array_column($countries, 'name');
    }

    public static function isValidCountry(?string $country): bool
    {
        if (empty($country)) {
            return false;
        }

        $names = self::countryNames();
        foreach ($names as $n) {
            if (strcasecmp(trim($n), trim($country)) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get details and divisions for a specific African country.
     */
    public static function getCountryInfo(?string $country): ?array
    {
        if (empty($country)) {
            return null;
        }

        $dataset = self::initializeData();
        if (isset($dataset[$country])) {
            $info = $dataset[$country];
            $info['name'] = $country;
            $info['divisions'] = self::getDivisions($country);
            return $info;
        }

        foreach ($dataset as $name => $info) {
            if (strcasecmp($name, $country) === 0 || strcasecmp($info['code'], $country) === 0) {
                $merged = array_merge(['name' => $name], $info);
                $merged['divisions'] = self::getDivisions($name);
                return $merged;
            }
        }

        return null;
    }

    /**
     * Get real divisions (states/provinces/regions/counties/etc.) dynamically from CountriesNow API.
     */
    public static function getDivisions(?string $country): array
    {
        if (empty($country)) {
            return [];
        }

        $dataset = self::initializeData();
        $matchedName = $country;

        foreach ($dataset as $name => $info) {
            if (strcasecmp($name, $country) === 0 || strcasecmp($info['code'], $country) === 0) {
                $matchedName = $name;
                break;
            }
        }

        $cacheKey = 'countriesnow_states_v4_' . md5(strtolower(trim($matchedName)));

        return Cache::remember($cacheKey, 86400, function () use ($matchedName) {
            try {
                $response = Http::timeout(5)->post('https://countriesnow.space/api/v0.1/countries/states', [
                    'country' => $matchedName,
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (!($json['error'] ?? true) && !empty($json['data']['states'])) {
                        $apiStates = [];
                        foreach ($json['data']['states'] as $st) {
                            $rawName = trim($st['name'] ?? '');
                            if ($rawName !== '') {
                                $cleanName = preg_replace('/\s+(State|County|Province|Region|Governorate|District|Department|Wilaya)$/i', '', $rawName);
                                $apiStates[] = $cleanName ?: $rawName;
                            }
                        }
                        if (count($apiStates) > 0) {
                            return array_values(array_unique($apiStates));
                        }
                    }
                }
            } catch (\Throwable $e) {
                logger()->warning("[AfricanLocationService] CountriesNow API states fetch failed for {$matchedName}: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Validate whether a division/state belongs to a specific African country using dynamic API response.
     */
    public static function isValidPair(?string $country, ?string $division): bool
    {
        if (empty($country) || empty($division)) {
            return false;
        }

        $divisions = self::getDivisions($country);
        if (empty($divisions)) {
            return true; // Graceful pass if API division list is unavailable
        }

        $trimmedTarget = strtolower(trim($division));
        $cleanTarget = preg_replace('/\s+(state|county|province|region|governorate|district|department|wilaya)$/i', '', $trimmedTarget);

        foreach ($divisions as $validDiv) {
            $trimmedValid = strtolower(trim($validDiv));
            $cleanValid = preg_replace('/\s+(state|county|province|region|governorate|district|department|wilaya)$/i', '', $trimmedValid);

            if ($trimmedTarget === $trimmedValid || $cleanTarget === $cleanValid) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get normalized location info array.
     */
    public static function getNormalized(?string $country, ?string $division): array
    {
        $info = self::getCountryInfo($country);
        if (!$info) {
            return [
                'country' => $country,
                'country_code' => null,
                'division' => $division,
                'division_term' => 'State / Region',
                'is_valid' => false,
            ];
        }

        $validDivName = $division;
        $isValidPair = self::isValidPair($country, $division);

        if ($isValidPair && !empty($info['divisions'])) {
            foreach ($info['divisions'] as $validDiv) {
                if (strcasecmp(trim($validDiv), trim($division)) === 0) {
                    $validDivName = $validDiv;
                    break;
                }
            }
        }

        return [
            'country' => $info['name'] ?? $country,
            'country_code' => $info['code'] ?? null,
            'division' => $validDivName,
            'division_term' => $info['term'] ?? 'State / Region',
            'is_valid' => $isValidPair,
        ];
    }
}
