/**
 * Global Dynamic Location Loader for DOOTOR ENTERPRISES
 * Handles dynamic fetching of States, Provinces, Regions & Counties
 * Default Country Applying From: Canada
 * Default Country Requested Service: Nigeria
 */

(function () {
    'use strict';

    window.AfricanLocationLoader = window.AfricanLocationLoader || {
        cache: {
            countries: null,
            divisions: {}
        },

        // Division Terms Mapping
        terms: {
            'Canada': 'Province / Territory',
            'Nigeria': 'State',
            'United States': 'State',
            'United Kingdom': 'County / Region',
            'United Arab Emirates': 'Emirate',
            'Ghana': 'Region',
            'South Africa': 'Province',
            'Kenya': 'County',
            'Ethiopia': 'Region',
            'Tanzania': 'Region',
            'Uganda': 'District',
            'Cameroon': 'Region',
            'Senegal': 'Region',
            'Morocco': 'Region',
            'Algeria': 'Province / Wilaya',
            'Egypt': 'Governorate',
            'Benin': 'Department',
            'Zambia': 'Province',
            'Zimbabwe': 'Province'
        },

        // Embedded Fallback Division Data
        fallbackDivisions: {
            'Canada': [
                'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick', 
                'Newfoundland and Labrador', 'Nova Scotia', 'Ontario', 
                'Prince Edward Island', 'Quebec', 'Saskatchewan', 
                'Northwest Territories', 'Nunavut', 'Yukon'
            ],
            'Nigeria': [
                'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno', 
                'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT - Abuja', 'Gombe', 
                'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 
                'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 
                'Taraba', 'Yobe', 'Zamfara'
            ],
            'United States': [
                'California', 'Texas', 'New York', 'Florida', 'Illinois', 'Pennsylvania', 
                'Ohio', 'Georgia', 'North Carolina', 'Michigan', 'Virginia', 'Washington', 
                'Maryland', 'Massachusetts', 'New Jersey'
            ],
            'United Kingdom': [
                'Greater London', 'Greater Manchester', 'West Midlands', 'West Yorkshire', 
                'Scotland', 'Wales', 'Northern Ireland', 'Kent', 'Essex', 'Hampshire'
            ],
            'United Arab Emirates': [
                'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain'
            ]
        },

        getEndpoint: function (type) {
            if (window.AFRICAN_LOCATION_ENDPOINTS && window.AFRICAN_LOCATION_ENDPOINTS[type]) {
                return window.AFRICAN_LOCATION_ENDPOINTS[type];
            }

            const metaEl = document.querySelector('meta[name="api-base-url"]');
            let baseUrl = metaEl ? metaEl.getAttribute('content') : '';

            if (!baseUrl) {
                const pathSegments = window.location.pathname.split('/').filter(Boolean);
                let prefix = '';
                if (pathSegments.length > 0) {
                    const firstSeg = pathSegments[0].toLowerCase();
                    const knownRoutes = ['api', 'register', 'register-client', 'login', 'admin', 'client', 'vendor', 'logout'];
                    if (!knownRoutes.includes(firstSeg)) {
                        prefix = '/' + pathSegments[0];
                    }
                }
                baseUrl = window.location.origin + prefix + '/api/location';
            }

            baseUrl = baseUrl.replace(/\/+$/, '');
            return type === 'countries' ? `${baseUrl}/african-countries` : `${baseUrl}/divisions`;
        },

        init: function (container) {
            const root = container || document;
            const countrySelects = root.querySelectorAll('.african-country-select, [data-african-country], select[name="country_applying_from"], select[name="country"]');

            countrySelects.forEach(countrySelect => {
                if (countrySelect.dataset.africanInit === 'true') return;
                countrySelect.dataset.africanInit = 'true';

                const targetId = countrySelect.dataset.divisionTarget;
                let divisionSelect = null;
                let labelEl = null;

                if (targetId) {
                    divisionSelect = document.getElementById(targetId);
                }
                if (!divisionSelect) {
                    divisionSelect = countrySelect.form
                        ? countrySelect.form.querySelector('.african-division-select, select[name="state"], [data-african-division]')
                        : root.querySelector('.african-division-select, select[name="state"], [data-african-division]');
                }

                const labelId = countrySelect.dataset.labelTarget;
                if (labelId) {
                    labelEl = document.getElementById(labelId);
                }
                if (!labelEl && divisionSelect) {
                    const id = divisionSelect.id;
                    if (id) {
                        labelEl = root.querySelector(`label[for="${id}"]`);
                    }
                    if (!labelEl && divisionSelect.form) {
                        labelEl = divisionSelect.form.querySelector('.african-division-label, [data-african-division-label]');
                    }
                }

                this.setupCountrySelect(countrySelect, divisionSelect, labelEl);
            });
        },

        setupCountrySelect: function (countrySelect, divisionSelect, labelEl) {
            const self = this;
            const isApplyingFrom = countrySelect.name === 'country_applying_from' || countrySelect.id === 'country_applying_from';
            const defaultCountry = isApplyingFrom ? 'Canada' : (countrySelect.dataset.selected || countrySelect.value || 'Canada');

            this.ensureCountriesLoaded(countrySelect, defaultCountry, function () {
                if (divisionSelect) {
                    const initialDivision = divisionSelect.dataset.selected || divisionSelect.value || '';
                    const currentCountry = countrySelect.value || defaultCountry;
                    self.loadDivisions(currentCountry, divisionSelect, labelEl, initialDivision);
                }
            });

            countrySelect.addEventListener('change', function () {
                const selectedCountry = countrySelect.value || 'Canada';
                if (divisionSelect) {
                    // Reset division selection when user changes country
                    self.loadDivisions(selectedCountry, divisionSelect, labelEl, '');
                }
            });
        },

        ensureCountriesLoaded: function (countrySelect, selectedValue, callback) {
            const self = this;

            if (countrySelect.options.length > 5) {
                if (selectedValue && Array.from(countrySelect.options).some(o => o.value.toLowerCase() === selectedValue.toLowerCase())) {
                    countrySelect.value = selectedValue;
                }
                if (!countrySelect.value && countrySelect.options.length > 0) {
                    countrySelect.selectedIndex = 0;
                }
                if (callback) callback();
                return;
            }

            if (this.cache.countries) {
                this.populateCountryDropdown(countrySelect, this.cache.countries, selectedValue);
                if (callback) callback();
                return;
            }

            const endpoint = this.getEndpoint('countries');

            fetch(endpoint)
                .then(res => res.json())
                .then(data => {
                    if (data && data.status === 'success' && Array.isArray(data.countries)) {
                        self.cache.countries = data.countries;
                        self.populateCountryDropdown(countrySelect, data.countries, selectedValue);
                    }
                    if (callback) callback();
                })
                .catch(err => {
                    console.warn('[GlobalLocationLoader] API fetch notice, using select options:', err);
                    if (callback) callback();
                });
        },

        populateCountryDropdown: function (select, countries, selectedValue) {
            const previousVal = selectedValue || select.value || 'Canada';
            select.innerHTML = '';

            countries.forEach(c => {
                const cName = typeof c === 'string' ? c : c.name;
                const opt = document.createElement('option');
                opt.value = cName;
                opt.textContent = cName;
                if (c.code) opt.setAttribute('data-code', c.code);
                if (c.term) opt.setAttribute('data-term', c.term);
                if (cName.toLowerCase() === previousVal.toLowerCase()) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });

            if (!select.value && select.options.length > 0) {
                select.selectedIndex = 0;
            }
        },

        loadDivisions: function (countryName, divisionSelect, labelEl, selectedDivision) {
            const self = this;
            const term = this.terms[countryName] || 'State / Province / Region';

            this.updateLabel(labelEl, countryName, term);

            // 1. Check in-memory cache
            if (this.cache.divisions[countryName]) {
                const cachedData = this.cache.divisions[countryName];
                this.populateDivisionDropdown(divisionSelect, cachedData.divisions, cachedData.term || term, selectedDivision);
                return;
            }

            // 2. Check embedded fallbacks (Instant response for Canada, Nigeria, UK, US, UAE)
            if (this.fallbackDivisions[countryName]) {
                const divisions = this.fallbackDivisions[countryName];
                this.cache.divisions[countryName] = { term: term, divisions: divisions };
                this.populateDivisionDropdown(divisionSelect, divisions, term, selectedDivision);
                return;
            }

            // 3. Dynamic API Fetch for other countries
            divisionSelect.disabled = true;
            divisionSelect.innerHTML = `<option value="">Loading ${term}s...</option>`;

            const endpoint = this.getEndpoint('divisions');
            const url = `${endpoint}?country=${encodeURIComponent(countryName)}`;

            fetch(url)
                .then(res => res.json())
                .then(resData => {
                    if (resData && resData.status === 'success' && resData.data) {
                        const data = resData.data;
                        self.cache.divisions[countryName] = data;
                        self.updateLabel(labelEl, countryName, data.term || term);
                        self.populateDivisionDropdown(divisionSelect, data.divisions || [], data.term || term, selectedDivision);
                    } else {
                        throw new Error('Invalid division payload');
                    }
                })
                .catch(err => {
                    console.warn('[GlobalLocationLoader] API fetch note for ' + countryName + ':', err);
                    divisionSelect.disabled = false;
                    const fallback = self.fallbackDivisions[countryName] || [];
                    self.populateDivisionDropdown(divisionSelect, fallback, term, selectedDivision);
                });
        },

        updateLabel: function (labelEl, countryName, term) {
            if (!labelEl) return;
            const displayTerm = term || this.terms[countryName] || 'State / Province / Region';
            labelEl.textContent = displayTerm;
        },

        populateDivisionDropdown: function (select, divisions, term, selectedDivision) {
            select.disabled = false;
            select.innerHTML = `<option value="">Select ${term || 'State / Province / Region'}</option>`;

            let matchFound = false;
            if (Array.isArray(divisions)) {
                divisions.forEach(div => {
                    const opt = document.createElement('option');
                    opt.value = div;
                    opt.textContent = div;
                    if (selectedDivision && (div.toLowerCase() === selectedDivision.toLowerCase())) {
                        opt.selected = true;
                        matchFound = true;
                    }
                    select.appendChild(opt);
                });
            }

            // If selectedDivision does NOT belong to this country's division list (e.g. Benue for Canada), do NOT append it!
            if (!matchFound) {
                select.value = "";
            }
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.AfricanLocationLoader.init();
        });
    } else {
        window.AfricanLocationLoader.init();
    }
})();
