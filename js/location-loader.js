/**
 * African Dynamic Location Loader
 * Strictly dynamic fetching of African Countries and their real Administrative Divisions from CountriesNow API
 */

(function () {
    'use strict';

    window.AfricanLocationLoader = window.AfricanLocationLoader || {
        cache: {
            countries: null,
            divisions: {}
        },

        // Fallback division terms
        terms: {
            'Nigeria': 'State',
            'Kenya': 'County',
            'South Africa': 'Province',
            'Ghana': 'Region',
            'Ethiopia': 'Region',
            'Tanzania': 'Region',
            'Uganda': 'District',
            'Cameroon': 'Region',
            'Senegal': 'Region',
            'Morocco': 'Region',
            'Algeria': 'Province / Wilaya',
            'Egypt': 'Governorate',
            'Benin': 'Department',
            'Botswana': 'District',
            'Zambia': 'Province',
            'Zimbabwe': 'Province'
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
            const countrySelects = root.querySelectorAll('.african-country-select, [data-african-country]');

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
            const initialCountry = countrySelect.dataset.selected || countrySelect.value || 'Nigeria';

            this.ensureCountriesLoaded(countrySelect, initialCountry, function () {
                if (divisionSelect) {
                    const initialDivision = divisionSelect.dataset.selected || divisionSelect.value || '';
                    self.loadDivisions(countrySelect.value || 'Nigeria', divisionSelect, labelEl, initialDivision);
                }
            });

            countrySelect.addEventListener('change', function () {
                const selectedCountry = countrySelect.value || 'Nigeria';
                if (divisionSelect) {
                    self.loadDivisions(selectedCountry, divisionSelect, labelEl, '');
                }
            });
        },

        ensureCountriesLoaded: function (countrySelect, selectedValue, callback) {
            const self = this;

            if (countrySelect.options.length > 5) {
                if (selectedValue) countrySelect.value = selectedValue;
                if (!countrySelect.value) countrySelect.value = 'Nigeria';
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
                    console.warn('[AfricanLocationLoader] Failed to fetch African countries list:', err);
                    if (callback) callback();
                });
        },

        populateCountryDropdown: function (select, countries, selectedValue) {
            const previousVal = selectedValue || select.value || 'Nigeria';
            select.innerHTML = '';

            countries.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.name;
                opt.textContent = c.name;
                if (c.code) opt.setAttribute('data-code', c.code);
                if (c.term) opt.setAttribute('data-term', c.term);
                if (c.name.toLowerCase() === previousVal.toLowerCase() || (previousVal === '' && c.name === 'Nigeria')) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });

            if (!select.value) {
                select.value = 'Nigeria';
            }
        },

        loadDivisions: function (countryName, divisionSelect, labelEl, selectedDivision) {
            const self = this;
            const term = this.terms[countryName] || 'State / Region';

            this.updateLabel(labelEl, countryName, term);

            if (this.cache.divisions[countryName]) {
                const cachedData = this.cache.divisions[countryName];
                this.populateDivisionDropdown(divisionSelect, cachedData.divisions, cachedData.term || term, selectedDivision);
                return;
            }

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
                        throw new Error('Invalid API response');
                    }
                })
                .catch(err => {
                    console.error('[AfricanLocationLoader] Error loading divisions from ' + url + ':', err);
                    divisionSelect.disabled = false;
                    divisionSelect.innerHTML = `<option value="">Unable to load ${term}s. Click to retry</option>`;

                    const retryHandler = function () {
                        divisionSelect.removeEventListener('click', retryHandler);
                        self.loadDivisions(countryName, divisionSelect, labelEl, selectedDivision);
                    };
                    divisionSelect.addEventListener('click', retryHandler, { once: true });
                });
        },

        updateLabel: function (labelEl, countryName, term) {
            if (!labelEl) return;
            const displayTerm = term || this.terms[countryName] || 'State / Region';
            labelEl.textContent = displayTerm;
        },

        populateDivisionDropdown: function (select, divisions, term, selectedDivision) {
            select.disabled = false;
            select.innerHTML = `<option value="">Select ${term || 'State / Region'}</option>`;

            let matchFound = false;
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

            if (selectedDivision && !matchFound) {
                const customOpt = document.createElement('option');
                customOpt.value = selectedDivision;
                customOpt.textContent = selectedDivision;
                customOpt.selected = true;
                select.appendChild(customOpt);
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
