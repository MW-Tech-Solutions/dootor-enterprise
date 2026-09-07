<?php

namespace App\Constants;

class AfricanCountries
{
    /**
     * All 54 sovereign African countries recognized internationally.
     */
    public static function all(): array
    {
        return array_column(\App\Services\AfricanLocationService::allCountries(), 'name');
    }

    /**
     * Default country selection for all portal forms.
     */
    public static function default(): string
    {
        return 'Nigeria';
    }

    /**
     * Check if a country string is a valid African country.
     */
    public static function isValid(?string $country): bool
    {
        return \App\Services\AfricanLocationService::isValidCountry($country);
    }
}
