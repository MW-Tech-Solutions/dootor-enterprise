@php
    $fieldVal = $value ?? ($application->form_data[$field->field_name] ?? '');
    $fieldName = "form_data[{$field->field_name}]";
    $options = is_array($field->options) ? $field->options : (is_string($field->options) ? array_filter(array_map('trim', explode("\n", $field->options))) : []);
    $isRequired = (bool) $field->is_required;
    $fieldId = 'field_' . $field->id . '_' . \Illuminate\Support\Str::slug($field->field_name, '_');
@endphp

<div class="col-12 {{ in_array($field->field_type, ['textarea', 'address', 'instructions', 'declaration']) ? 'col-12' : 'col-md-6' }} mb-3">
    @if($field->field_type === 'instructions')
        <div class="alert alert-info border-0 shadow-2xs rounded-3 p-3 mb-0" role="alert" style="background-color: #f0fdf4; border: 1px solid #bbf7d0 !important;">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-info-circle-fill text-success fs-5 flex-shrink-0 mt-0.5" style="color: #004225 !important;"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ $field->field_label }}</h6>
                    <p class="small mb-0 text-secondary">{{ $field->help_text ?: $field->placeholder }}</p>
                </div>
            </div>
        </div>
    @elseif($field->field_type === 'declaration')
        <div class="p-3 bg-light rounded-3 border">
            <div class="form-check">
                <input class="form-check-input autosave-field" type="checkbox" name="{{ $fieldName }}" id="{{ $fieldId }}" value="1" {{ $fieldVal ? 'checked' : '' }} {{ $isRequired ? 'required' : '' }}>
                <label class="form-check-label small fw-semibold text-dark" for="{{ $fieldId }}">
                    {{ $field->field_label }} @if($isRequired) <span class="text-danger">*</span> @endif
                </label>
            </div>
            @if($field->help_text || $field->placeholder)
                <p class="text-muted extra-small mb-0 mt-1" style="font-size: 11.5px;">{{ $field->help_text ?: $field->placeholder }}</p>
            @endif
        </div>
    @else
        <label class="form-label small fw-semibold text-dark mb-1" for="{{ $fieldId }}">
            {{ $field->field_label }}
            @if($isRequired) <span class="text-danger">*</span> @endif
        </label>

        @if($field->field_type === 'textarea' || $field->field_type === 'address')
            <textarea name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" rows="3" placeholder="{{ $field->placeholder ?: ($field->field_type === 'address' ? 'Enter full residential street address, city, state, country' : '') }}" {{ $isRequired ? 'required' : '' }}>{{ $fieldVal }}</textarea>

        @elseif($field->field_type === 'dropdown')
            <select name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-select rounded-3 autosave-field" {{ $isRequired ? 'required' : '' }}>
                <option value="">-- Select {{ $field->field_label }} --</option>
                @foreach($options as $opt)
                    <option value="{{ $opt }}" {{ $fieldVal == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>

        @elseif($field->field_type === 'country')
            <select name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-select rounded-3 african-country-select autosave-field" {{ $isRequired ? 'required' : '' }}>
                <option value="">-- Select African Country --</option>
                @foreach(\App\Constants\AfricanCountries::all() as $c)
                    <option value="{{ $c }}" {{ ($fieldVal ?: 'Nigeria') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>

        @elseif($field->field_type === 'state')
            <select name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-select rounded-3 autosave-field" {{ $isRequired ? 'required' : '' }}>
                <option value="">-- Select State / Province --</option>
                @foreach(\App\Services\AfricanLocationService::getDivisionsForCountry('Nigeria') as $state)
                    <option value="{{ $state }}" {{ $fieldVal == $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>

        @elseif($field->field_type === 'lga')
            <input type="text" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" value="{{ $fieldVal }}" placeholder="{{ $field->placeholder ?: 'e.g. Ikeja, Abuja Municipal, etc.' }}" {{ $isRequired ? 'required' : '' }}>

        @elseif($field->field_type === 'radio')
            <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded-3 border">
                @foreach($options as $loopIdx => $rOpt)
                    <div class="form-check">
                        <input class="form-check-input autosave-field" type="radio" name="{{ $fieldName }}" id="{{ $fieldId }}_{{ $loopIdx }}" value="{{ $rOpt }}" {{ $fieldVal == $rOpt ? 'checked' : '' }} {{ $isRequired ? 'required' : '' }}>
                        <label class="form-check-label small" for="{{ $fieldId }}_{{ $loopIdx }}">{{ $rOpt }}</label>
                    </div>
                @endforeach
            </div>

        @elseif($field->field_type === 'checkbox')
            <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded-3 border">
                @if(count($options) > 0)
                    @foreach($options as $loopIdx => $cOpt)
                        @php $checked = is_array($fieldVal) ? in_array($cOpt, $fieldVal) : ($fieldVal == $cOpt); @endphp
                        <div class="form-check">
                            <input class="form-check-input autosave-field" type="checkbox" name="{{ $fieldName }}[]" id="{{ $fieldId }}_{{ $loopIdx }}" value="{{ $cOpt }}" {{ $checked ? 'checked' : '' }}>
                            <label class="form-check-label small" for="{{ $fieldId }}_{{ $loopIdx }}">{{ $cOpt }}</label>
                        </div>
                    @endforeach
                @else
                    <div class="form-check">
                        <input class="form-check-input autosave-field" type="checkbox" name="{{ $fieldName }}" id="{{ $fieldId }}" value="1" {{ $fieldVal ? 'checked' : '' }} {{ $isRequired ? 'required' : '' }}>
                        <label class="form-check-label small" for="{{ $fieldId }}">{{ $field->placeholder ?: 'Yes, I agree' }}</label>
                    </div>
                @endif
            </div>

        @elseif($field->field_type === 'yes_no')
            <div class="d-flex gap-4 p-2 bg-light rounded-3 border">
                <div class="form-check">
                    <input class="form-check-input autosave-field" type="radio" name="{{ $fieldName }}" id="{{ $fieldId }}_yes" value="Yes" {{ $fieldVal === 'Yes' ? 'checked' : '' }} {{ $isRequired ? 'required' : '' }}>
                    <label class="form-check-label small fw-semibold text-dark" for="{{ $fieldId }}_yes"><i class="bi bi-check-circle text-success me-1"></i> Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input autosave-field" type="radio" name="{{ $fieldName }}" id="{{ $fieldId }}_no" value="No" {{ $fieldVal === 'No' ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold text-dark" for="{{ $fieldId }}_no"><i class="bi bi-x-circle text-danger me-1"></i> No</label>
                </div>
            </div>

        @elseif($field->field_type === 'file')
            @php
                $existingDoc = null;
                if (isset($application) && $application->relationLoaded('requestDocuments')) {
                    $existingDoc = $application->requestDocuments->firstWhere('document_name', $field->field_label);
                }
            @endphp
            <div class="p-3 bg-light rounded-3 border border-dashed">
                @if($existingDoc)
                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
                            <div>
                                <span class="fw-bold small text-dark d-block mb-0">{{ $existingDoc->file_name }}</span>
                                <span class="text-success extra-small" style="font-size: 11px;"><i class="bi bi-check-circle me-1"></i> File Attached</span>
                            </div>
                        </div>
                        <a href="{{ asset($existingDoc->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5">View File</a>
                    </div>
                @endif
                <div class="d-flex align-items-center gap-2">
                    <input type="file" name="documents[{{ \Illuminate\Support\Str::slug($field->field_label, '_') }}]" id="{{ $fieldId }}" class="form-control form-control-sm rounded-3 dynamic-file-input" data-doc-name="{{ $field->field_label }}" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" {{ $isRequired && !$existingDoc ? 'required' : '' }}>
                </div>
                <div class="form-text extra-small text-muted mt-1" style="font-size: 11px;">
                    Allowed formats: PDF, JPG, PNG, DOCX (Max 10MB)
                </div>
            </div>

        @elseif($field->field_type === 'passport')
            @php
                $passPhoto = $application->passport_photo_path ?? null;
            @endphp
            <div class="p-3 bg-light rounded-3 border">
                @if($passPhoto)
                    <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-white rounded-3 border">
                        <img src="{{ asset($passPhoto) }}" alt="Passport" class="rounded border shadow-2xs" style="width: 50px; height: 60px; object-fit: cover;">
                        <div>
                            <span class="badge bg-success mb-1">Passport Photo Uploaded</span>
                            <span class="text-muted extra-small d-block" style="font-size: 11px;">Choose a new photo to replace current image.</span>
                        </div>
                    </div>
                @endif
                <input type="file" name="passport_photo" id="{{ $fieldId }}" class="form-control form-control-sm rounded-3 dynamic-passport-input" accept="image/jpeg,image/png,image/webp" {{ $isRequired && !$passPhoto ? 'required' : '' }}>
                <div class="form-text extra-small text-muted mt-1" style="font-size: 11px;">
                    Upload clear passport photo on white background (JPG, PNG max 5MB).
                </div>
            </div>

        @elseif($field->field_type === 'date')
            <input type="date" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" value="{{ $fieldVal }}" {{ $isRequired ? 'required' : '' }}>

        @elseif($field->field_type === 'number')
            <input type="number" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" value="{{ $fieldVal }}" placeholder="{{ $field->placeholder }}" {{ $isRequired ? 'required' : '' }}>

        @elseif($field->field_type === 'email')
            <input type="email" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" value="{{ $fieldVal }}" placeholder="{{ $field->placeholder ?: 'example@domain.com' }}" {{ $isRequired ? 'required' : '' }}>

        @elseif($field->field_type === 'phone')
            <input type="tel" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" value="{{ $fieldVal }}" placeholder="{{ $field->placeholder ?: '+1 (555) 000-0000' }}" {{ $isRequired ? 'required' : '' }}>

        @else
            <input type="text" name="{{ $fieldName }}" id="{{ $fieldId }}" class="form-control rounded-3 autosave-field" value="{{ $fieldVal }}" placeholder="{{ $field->placeholder }}" {{ $isRequired ? 'required' : '' }}>
        @endif

        @if($field->help_text && !in_array($field->field_type, ['instructions', 'declaration']))
            <div class="form-text extra-small text-muted mt-1" style="font-size: 11px;">{{ $field->help_text }}</div>
        @endif
    @endif
</div>
