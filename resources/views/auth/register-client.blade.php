@extends('layouts.app')

@section('title', 'Register Client - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .btn-brand-primary {
        background-color: #004225 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.25s ease;
    }
    .btn-brand-primary:hover {
        background-color: #002411 !important;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.2);
        transform: translateY(-1px);
    }
    .text-brand-success {
        color: #004225 !important;
        font-weight: 600;
    }
    .text-brand-success:hover {
        color: #d4af37 !important;
        text-decoration: underline !important;
    }
    .form-control:focus {
        border-color: #004225 !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 66, 37, 0.15) !important;
    }
</style>
@endsection

@section('body')
<div class="container-fluid p-0 min-vh-100 d-flex flex-column flex-lg-row">
    <!-- Left Pane: Branding & Service Features (hidden on mobile, shown on lg) -->
    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%); min-height: 100vh;">
        <!-- Glowing background decoration -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.15), transparent 60%); pointer-events: none;"></div>
        
        <div>
            <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-white mb-5">
                @if($settings && $settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded" style="height: 48px; width: 48px; object-fit: contain;">
                @else
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoRegClient)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                        <defs>
                            <linearGradient id="goldLogoRegClient" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                @endif
                <span class="fw-bold fs-4 tracking-tight text-white">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
            </a>
            
            <h2 class="display-6 fw-bold text-white mb-3" style="line-height: 1.2;">Access Vetted Document Consultancy</h2>
            <p class="text-white-50 mb-5 fs-6">Register a secure client profile to order and track official documents vetting, verification, and authentication supports.</p>
            
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-shield-check" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">Secure Profile Registration</h5>
                        <p class="text-white-50 small mb-0">Your database entries are protected. Only verified agents assigned to your request will access file processing details.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-clock-history" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">Expedited Tracking Timeline</h5>
                        <p class="text-white-50 small mb-0">Track files through order placements, vetted validations, processing, and courier deliveries.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-20 pt-4 mt-5">
            <span class="d-block small text-white-50" style="font-size: 11px; line-height: 1.4;">WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF THE NIGERIAN HIGH COMMISSION.</span>
        </div>
    </div>

    <!-- Right Pane: Client Registration Form Container -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center bg-light py-5 px-3 px-sm-5" style="flex-grow: 1; min-height: 100vh;">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white" style="max-width: 520px; width: 100%;">
            <!-- Mobile Brand Logo header (hidden on desktop) -->
            <div class="text-center mb-4 d-block d-lg-none">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark mb-2">
                    @if($settings && $settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded" style="height: 38px; width: 38px; object-fit: contain;">
                    @else
                        <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoRegClientMobile)" />
                            <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                            <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                            <defs>
                                <linearGradient id="goldLogoRegClientMobile" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#FFE57F" />
                                    <stop offset="50%" stop-color="#D4AF37" />
                                    <stop offset="100%" stop-color="#AA820A" />
                                </linearGradient>
                            </defs>
                        </svg>
                    @endif
                    <span class="fw-bold text-dark fs-5">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
                </a>
            </div>

            <div class="mb-4">
                <h1 class="h3 fw-bold text-dark mb-1">Create Client Account</h1>
                <p class="text-secondary small">Register to book verified document services</p>
            </div>

            <form action="{{ route('register.client') }}" method="POST">
                @csrf

                <!-- Name Fields -->
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label for="first_name" class="form-label small fw-medium">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control bg-light rounded-3 @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="Abiodun" style="border-color: #dee2e6;" required>
                        @error('first_name')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="last_name" class="form-label small fw-medium">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control bg-light rounded-3 @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Okonkwo" style="border-color: #dee2e6;" required>
                        @error('last_name')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-medium">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control bg-light rounded-3 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="abiodun@dootor.com" style="border-color: #dee2e6;" required>
                    @error('email')
                        <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label small fw-medium">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control bg-light rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+1 416..." style="border-color: #dee2e6;">
                    @error('phone')
                        <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Location Fields -->
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label for="country" class="form-label small fw-medium">Country</label>
                        <select name="country" id="country" class="form-select bg-light rounded-3" style="border-color: #dee2e6;" required>
                            <option value="">Select Country</option>
                            @php
                                $countries = [
                                    "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
                                ];
                            @endphp
                            @foreach($countries as $c)
                                <option value="{{ $c }}" {{ old('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="state" class="form-label small fw-medium">State / Province</label>
                        <select name="state" id="state" class="form-select bg-light rounded-3" style="border-color: #dee2e6;" required>
                            <option value="">Select State/Province</option>
                        </select>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="row g-2 mb-4">
                    <div class="col-sm-6">
                        <label for="password" class="form-label small fw-medium">Password</label>
                        <input type="password" name="password" id="password" class="form-control bg-light rounded-3 @error('password') is-invalid @enderror" placeholder="••••••••" style="border-color: #dee2e6;" required>
                        @error('password')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="password_confirmation" class="form-label small fw-medium">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-light rounded-3" placeholder="••••••••" style="border-color: #dee2e6;" required>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-brand-primary w-100 py-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span>Register as Client</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="text-center text-secondary small mt-3 pt-3 border-top border-light">
                Already have an account? <a href="{{ route('login') }}" class="text-decoration-none text-brand-success fw-semibold">Sign In</a> <br>
                Looking to offer services? <a href="{{ route('register') }}" class="text-decoration-none text-brand-success fw-semibold">Register as Vendor</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const countrySelect = document.getElementById("country");
        const stateSelect = document.getElementById("state");

        const locationData = {
            "Nigeria": [
                "Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", 
                "Cross River", "Delta", "Ebonyi", "Edo", "Ekiti", "Enugu", "FCT - Abuja", "Gombe", 
                "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos", 
                "Nasarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", 
                "Taraba", "Yobe", "Zamfara"
            ],
            "Canada": [
                "Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", 
                "Nova Scotia", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan", 
                "Northwest Territories", "Nunavut", "Yukon"
            ],
            "United Kingdom": [
                "England", "Scotland", "Wales", "Northern Ireland"
            ],
            "United States": [
                "Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", 
                "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", 
                "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", 
                "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", 
                "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", 
                "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", 
                "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", 
                "Wisconsin", "Wyoming"
            ]
        };

        const genericStates = [
            "Federal Capital Territory / Capital Region",
            "Central Region / Province",
            "Eastern Region / Province",
            "Western Region / Province",
            "Northern Region / Province",
            "Southern Region / Province",
            "Main Region / Province",
            "Other / Default Subdivision"
        ];

        function updateStates(selectedCountry, savedState = '') {
            stateSelect.innerHTML = '<option value="">Select State/Province</option>';
            if (!selectedCountry) {
                return;
            }

            const list = locationData[selectedCountry] || genericStates;
            
            list.forEach(function(state) {
                const option = document.createElement("option");
                option.value = state;
                option.textContent = state;
                if (state === savedState) {
                    option.selected = true;
                }
                stateSelect.appendChild(option);
            });
        }

        countrySelect.addEventListener("change", function() {
            updateStates(this.value);
        });

        // Initialize on page load with old value
        const initialCountry = countrySelect.value;
        const initialState = "{{ old('state') }}";
        if (initialCountry) {
            updateStates(initialCountry, initialState);
        }
    });
</script>
@endsection
