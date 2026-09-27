<?php

namespace App\Services;

class LocationService
{
    /**
     * Map of supported countries with their ISO code, administrative division term, states/provinces, and cities.
     */
    protected static array $globalDataset = [
        'Canada' => [
            'code' => 'CA',
            'term' => 'Province / Territory',
            'divisions' => [
                'Alberta' => ['Calgary', 'Edmonton', 'Red Deer', 'Lethbridge'],
                'British Columbia' => ['Vancouver', 'Victoria', 'Surrey', 'Burnaby', 'Kelowna'],
                'Manitoba' => ['Winnipeg', 'Brandon', 'Steinbach'],
                'New Brunswick' => ['Moncton', 'Saint John', 'Fredericton'],
                'Newfoundland and Labrador' => ['St. John\'s', 'Corner Brook'],
                'Nova Scotia' => ['Halifax', 'Sydney', 'Dartmouth'],
                'Ontario' => ['Toronto', 'Ottawa', 'Mississauga', 'Brampton', 'Hamilton', 'London'],
                'Prince Edward Island' => ['Charlottetown', 'Summerside'],
                'Quebec' => ['Montreal', 'Quebec City', 'Laval', 'Gatineau'],
                'Saskatchewan' => ['Saskatoon', 'Regina', 'Prince Albert'],
                'Northwest Territories' => ['Yellowknife'],
                'Nunavut' => ['Iqaluit'],
                'Yukon' => ['Whitehorse'],
            ],
        ],
        'Nigeria' => [
            'code' => 'NG',
            'term' => 'State',
            'divisions' => [
                'Abia' => ['Aba', 'Umuahia'],
                'Adamawa' => ['Yola', 'Mubi'],
                'Akwa Ibom' => ['Uyo', 'Ikot Ekpene', 'Eket'],
                'Anambra' => ['Awka', 'Onitsha', 'Nnewi'],
                'Bauch' => ['Bauchi', 'Azare'],
                'Bayelsa' => ['Yenagoa'],
                'Benue' => ['Makurdi', 'Gboko'],
                'Borno' => ['Maiduguri'],
                'Cross River' => ['Calabar', 'Ikom'],
                'Delta' => ['Asaba', 'Warri', 'Sapele'],
                'Ebonyi' => ['Abakaliki'],
                'Edo' => ['Benin City', 'Auchi'],
                'Ekiti' => ['Ado-Ekiti'],
                'Enugu' => ['Enugu', 'Nsukka'],
                'FCT - Abuja' => ['Abuja Municipal', 'Gwarinpa', 'Maitama', 'Asokoro', 'Kubwa', 'Lugbe'],
                'Gombe' => ['Gombe'],
                'Imo' => ['Owerri', 'Orlu'],
                'Jigawa' => ['Dutse'],
                'Kaduna' => ['Kaduna', 'Zaria'],
                'Kano' => ['Kano City'],
                'Katsina' => ['Katsina'],
                'Kebbi' => ['Birnin Kebbi'],
                'Kogi' => ['Lokoja'],
                'Kwara' => ['Ilorin'],
                'Lagos' => ['Ikeja', 'Lagos Island', 'Lekki', 'Victoria Island', 'Surulere', 'Ikorodu', 'Yaba'],
                'Nasarawa' => ['Lafia', 'Karu'],
                'Niger' => ['Minna', 'Suleja'],
                'Ogun' => ['Abeokuta', 'Ijebu-Ode', 'Ota'],
                'Ondo' => ['Akure', 'Ondo Town'],
                'Osun' => ['Osogbo', 'Ife'],
                'Oyo' => ['Ibadan', 'Ogbomoso'],
                'Plateau' => ['Jos'],
                'Rivers' => ['Port Harcourt', 'Obio-Akpor'],
                'Sokoto' => ['Sokoto'],
                'Taraba' => ['Jalingo'],
                'Yobe' => ['Damaturu'],
                'Zamfara' => ['Gusau'],
            ],
        ],
        'United Kingdom' => [
            'code' => 'GB',
            'term' => 'County / Region',
            'divisions' => [
                'Greater London' => ['London', 'Westminster', 'Camden', 'Croydon'],
                'Greater Manchester' => ['Manchester', 'Salford', 'Bolton'],
                'West Midlands' => ['Birmingham', 'Coventry', 'Wolverhampton'],
                'West Yorkshire' => ['Leeds', 'Bradford', 'Wakefield'],
                'Scotland' => ['Edinburgh', 'Glasgow', 'Aberdeen', 'Dundee'],
                'Wales' => ['Cardiff', 'Swansea', 'Newport'],
                'Northern Ireland' => ['Belfast', 'Derry'],
            ],
        ],
        'United States' => [
            'code' => 'US',
            'term' => 'State',
            'divisions' => [
                'California' => ['Los Angeles', 'San Francisco', 'San Diego', 'San Jose'],
                'Texas' => ['Houston', 'Dallas', 'Austin', 'San Antonio'],
                'New York' => ['New York City', 'Buffalo', 'Rochester', 'Albany'],
                'Florida' => ['Miami', 'Orlando', 'Tampa', 'Jacksonville'],
                'Illinois' => ['Chicago', 'Aurora', 'Naperville'],
                'Georgia' => ['Atlanta', 'Savannah'],
                'Maryland' => ['Baltimore', 'Silver Spring'],
                'Virginia' => ['Richmond', 'Virginia Beach', 'Arlington'],
            ],
        ],
        'Ghana' => [
            'code' => 'GH',
            'term' => 'Region',
            'divisions' => [
                'Greater Accra' => ['Accra', 'Tema'],
                'Ashanti' => ['Kumasi'],
                'Central' => ['Cape Coast'],
                'Western' => ['Sekondi-Takoradi'],
                'Northern' => ['Tamale'],
            ],
        ],
        'United Arab Emirates' => [
            'code' => 'AE',
            'term' => 'Emirate',
            'divisions' => [
                'Dubai' => ['Dubai City', 'Deira', 'Jumeirah'],
                'Abu Dhabi' => ['Abu Dhabi City', 'Al Ain'],
                'Sharjah' => ['Sharjah City'],
            ],
        ],
        'South Africa' => [
            'code' => 'ZA',
            'term' => 'Province',
            'divisions' => [
                'Gauteng' => ['Johannesburg', 'Pretoria'],
                'Western Cape' => ['Cape Town'],
                'KwaZulu-Natal' => ['Durban'],
            ],
        ],
    ];

    /**
     * Get list of all supported countries.
     */
    public static function allCountries(): array
    {
        $africanList = AfricanLocationService::allCountries();
        
        $countryMap = [];
        foreach (self::$globalDataset as $cName => $cInfo) {
            $countryMap[$cName] = [
                'name' => $cName,
                'code' => $cInfo['code'],
                'term' => $cInfo['term'],
            ];
        }

        foreach ($africanList as $cName => $cInfo) {
            if (!isset($countryMap[$cName])) {
                $countryMap[$cName] = $cInfo;
            }
        }

        ksort($countryMap);
        return $countryMap;
    }

    /**
     * Get administrative division label (e.g. State, Province / Territory, Region, County) for a country.
     */
    public static function getDivisionLabel(string $country): string
    {
        if (isset(self::$globalDataset[$country]['term'])) {
            return self::$globalDataset[$country]['term'];
        }

        $all = self::allCountries();
        return $all[$country]['term'] ?? 'State / Province / Region';
    }

    /**
     * Get divisions (states/provinces) for a country.
     */
    public static function getDivisions(string $country): array
    {
        if (isset(self::$globalDataset[$country]['divisions'])) {
            return array_keys(self::$globalDataset[$country]['divisions']);
        }

        return AfricanLocationService::statesForCountry($country);
    }

    /**
     * Get cities for a country division.
     */
    public static function getCities(string $country, string $division): array
    {
        if (isset(self::$globalDataset[$country]['divisions'][$division])) {
            return self::$globalDataset[$country]['divisions'][$division];
        }

        return [];
    }
}
