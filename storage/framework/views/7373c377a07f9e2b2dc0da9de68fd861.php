<?php
    $fieldVal = $value ?? ($application->form_data[$field->field_name] ?? '');
    $fieldName = "form_data[{$field->field_name}]";
    $options = is_array($field->options) ? $field->options : (is_string($field->options) ? array_filter(array_map('trim', explode("\n", $field->options))) : []);
    $isRequired = (bool) $field->is_required;
    $fieldId = 'field_' . $field->id . '_' . \Illuminate\Support\Str::slug($field->field_name, '_');

    $fieldType = strtolower(trim((string) ($field->field_type ?? 'text')));
    $labelLower = strtolower(trim((string) ($field->field_label ?? '')));
    $nameLower = strtolower(trim((string) ($field->field_name ?? '')));

    // Smart field type detection
    $isPassportPhoto = ($fieldType === 'passport') 
        || str_contains($labelLower, 'passport photo') 
        || str_contains($labelLower, 'passport photograph')
        || (str_contains($labelLower, 'passport') && str_contains($labelLower, 'photo'));

    $isFileUpload = (!$isPassportPhoto && $fieldType === 'file') 
        || str_contains($labelLower, 'datapage') 
        || str_contains($labelLower, 'passport datapage')
        || str_contains($labelLower, 'certificate')
        || str_contains($labelLower, 'upload')
        || str_contains($labelLower, 'document')
        || str_contains($labelLower, 'attachment')
        || str_contains($labelLower, 'scanned')
        || str_contains($labelLower, 'copy');

    $isCountry = ($fieldType === 'country') 
        || str_contains($labelLower, 'country') 
        || str_contains($nameLower, 'country');

    $isState = ($fieldType === 'state')
        || (str_contains($labelLower, 'state') && !str_contains($labelLower, 'statement'));

    $isLga = ($fieldType === 'lga')
        || str_contains($labelLower, 'lga')
        || str_contains($labelLower, 'local government')
        || str_contains($labelLower, 'district');

    $isGender = str_contains($labelLower, 'gender') || str_contains($nameLower, 'gender');

    $isDate = ($fieldType === 'date')
        || str_contains($labelLower, 'date of birth')
        || str_contains($labelLower, 'dob')
        || str_contains($labelLower, 'birth date');
?>

<div class="col-12 <?php echo e((in_array($fieldType, ['textarea', 'address', 'instructions', 'declaration']) || $isPassportPhoto || $isFileUpload) ? 'col-12' : 'col-md-6'); ?> mb-3">
    <?php if($fieldType === 'instructions'): ?>
        <div class="alert alert-info border-0 shadow-2xs rounded-3 p-3 mb-0" role="alert" style="background-color: #f0fdf4; border: 1px solid #bbf7d0 !important;">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-info-circle-fill text-success fs-5 flex-shrink-0 mt-0.5" style="color: #004225 !important;"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-dark"><?php echo e($field->field_label); ?></h6>
                    <p class="small mb-0 text-secondary"><?php echo e($field->help_text ?: $field->placeholder); ?></p>
                </div>
            </div>
        </div>
    <?php elseif($fieldType === 'declaration'): ?>
        <div class="p-3 bg-light rounded-3 border">
            <div class="form-check">
                <input class="form-check-input autosave-field" type="checkbox" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" value="1" <?php echo e($fieldVal ? 'checked' : ''); ?> <?php echo e($isRequired ? 'required' : ''); ?>>
                <label class="form-check-label small fw-semibold text-dark" for="<?php echo e($fieldId); ?>">
                    <?php echo e($field->field_label); ?> <?php if($isRequired): ?> <span class="text-danger">*</span> <?php endif; ?>
                </label>
            </div>
            <?php if($field->help_text || $field->placeholder): ?>
                <p class="text-muted extra-small mb-0 mt-1" style="font-size: 11.5px;"><?php echo e($field->help_text ?: $field->placeholder); ?></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <label class="form-label small fw-semibold text-dark mb-1" for="<?php echo e($fieldId); ?>">
            <?php echo e($field->field_label); ?>

            <?php if($isRequired): ?> <span class="text-danger">*</span> <?php endif; ?>
        </label>

        <?php if($isPassportPhoto): ?>
            <?php
                $passPhoto = $application->passport_photo_path ?? null;
            ?>
            <div class="p-3 bg-light rounded-3 border">
                <?php if($passPhoto): ?>
                    <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-white rounded-3 border">
                        <img src="<?php echo e(asset($passPhoto)); ?>" alt="Passport Photo" class="rounded border shadow-2xs" style="width: 55px; height: 65px; object-fit: cover;">
                        <div>
                            <span class="badge bg-success mb-1"><i class="bi bi-check-circle me-1"></i> Passport Photo Uploaded</span>
                            <span class="text-muted extra-small d-block" style="font-size: 11px;">Select a new file below to replace your existing photo.</span>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="passport_photo" id="<?php echo e($fieldId); ?>" class="form-control form-control-sm rounded-3 dynamic-passport-input" accept="image/jpeg,image/png,image/webp" <?php echo e($isRequired && !$passPhoto ? 'required' : ''); ?>>
                <div class="form-text extra-small text-muted mt-1" style="font-size: 11px;">
                    <i class="bi bi-info-circle me-1"></i> Upload recent passport photograph with clear white background (JPG, PNG max 5MB).
                </div>
            </div>

        <?php elseif($isFileUpload): ?>
            <?php
                $existingDoc = null;
                if (isset($application) && $application->relationLoaded('requestDocuments')) {
                    $existingDoc = $application->requestDocuments->firstWhere('document_name', $field->field_label);
                }
            ?>
            <div class="p-3 bg-light rounded-3 border border-dashed">
                <?php if($existingDoc): ?>
                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
                            <div>
                                <span class="fw-bold small text-dark d-block mb-0"><?php echo e($existingDoc->file_name); ?></span>
                                <span class="text-success extra-small" style="font-size: 11px;"><i class="bi bi-check-circle me-1"></i> File Attached & Received</span>
                            </div>
                        </div>
                        <a href="<?php echo e(asset($existingDoc->file_path)); ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 font-semibold" style="font-size: 11.5px;">View File</a>
                    </div>
                <?php endif; ?>
                <div class="d-flex align-items-center gap-2">
                    <input type="file" name="documents[<?php echo e(\Illuminate\Support\Str::slug($field->field_label, '_')); ?>]" id="<?php echo e($fieldId); ?>" class="form-control form-control-sm rounded-3 dynamic-file-input" data-doc-name="<?php echo e($field->field_label); ?>" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" <?php echo e($isRequired && !$existingDoc ? 'required' : ''); ?>>
                </div>
                <div class="form-text extra-small text-muted mt-1" style="font-size: 11px;">
                    Allowed file formats: PDF, JPG, PNG, DOCX (Max 10MB)
                </div>
            </div>

        <?php elseif($isCountry): ?>
            <select name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-select rounded-3 african-country-select autosave-field" <?php echo e($isRequired ? 'required' : ''); ?>>
                <option value="">-- Select Country --</option>
                <?php $__currentLoopData = \App\Constants\AfricanCountries::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c); ?>" <?php echo e(($fieldVal ?: 'Nigeria') === $c ? 'selected' : ''); ?>><?php echo e($c); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

        <?php elseif($isGender): ?>
            <select name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-select rounded-3 autosave-field" <?php echo e($isRequired ? 'required' : ''); ?>>
                <option value="">-- Select Gender --</option>
                <option value="Male" <?php echo e($fieldVal === 'Male' ? 'selected' : ''); ?>>Male</option>
                <option value="Female" <?php echo e($fieldVal === 'Female' ? 'selected' : ''); ?>>Female</option>
            </select>

        <?php elseif($isState): ?>
            <select name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-select rounded-3 autosave-field" <?php echo e($isRequired ? 'required' : ''); ?>>
                <option value="">-- Select State / Province --</option>
                <?php $__currentLoopData = \App\Services\AfricanLocationService::getDivisionsForCountry('Nigeria'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($state); ?>" <?php echo e($fieldVal == $state ? 'selected' : ''); ?>><?php echo e($state); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

        <?php elseif($isLga): ?>
            <input type="text" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" value="<?php echo e($fieldVal); ?>" placeholder="<?php echo e($field->placeholder ?: 'e.g. Ikeja, Abuja Municipal, Port Harcourt'); ?>" <?php echo e($isRequired ? 'required' : ''); ?>>

        <?php elseif($fieldType === 'textarea' || $fieldType === 'address'): ?>
            <textarea name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" rows="3" placeholder="<?php echo e($field->placeholder ?: ($fieldType === 'address' ? 'Enter full residential street address, city, state, country' : '')); ?>" <?php echo e($isRequired ? 'required' : ''); ?>><?php echo e($fieldVal); ?></textarea>

        <?php elseif($fieldType === 'dropdown'): ?>
            <select name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-select rounded-3 autosave-field" <?php echo e($isRequired ? 'required' : ''); ?>>
                <option value="">-- Select <?php echo e($field->field_label); ?> --</option>
                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($opt); ?>" <?php echo e($fieldVal == $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

        <?php elseif($fieldType === 'radio'): ?>
            <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded-3 border">
                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loopIdx => $rOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-check">
                        <input class="form-check-input autosave-field" type="radio" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>_<?php echo e($loopIdx); ?>" value="<?php echo e($rOpt); ?>" <?php echo e($fieldVal == $rOpt ? 'checked' : ''); ?> <?php echo e($isRequired ? 'required' : ''); ?>>
                        <label class="form-check-label small" for="<?php echo e($fieldId); ?>_<?php echo e($loopIdx); ?>"><?php echo e($rOpt); ?></label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        <?php elseif($fieldType === 'checkbox'): ?>
            <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded-3 border">
                <?php if(count($options) > 0): ?>
                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loopIdx => $cOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $checked = is_array($fieldVal) ? in_array($cOpt, $fieldVal) : ($fieldVal == $cOpt); ?>
                        <div class="form-check">
                            <input class="form-check-input autosave-field" type="checkbox" name="<?php echo e($fieldName); ?>[]" id="<?php echo e($fieldId); ?>_<?php echo e($loopIdx); ?>" value="<?php echo e($cOpt); ?>" <?php echo e($checked ? 'checked' : ''); ?>>
                            <label class="form-check-label small" for="<?php echo e($fieldId); ?>_<?php echo e($loopIdx); ?>"><?php echo e($cOpt); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="form-check">
                        <input class="form-check-input autosave-field" type="checkbox" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" value="1" <?php echo e($fieldVal ? 'checked' : ''); ?> <?php echo e($isRequired ? 'required' : ''); ?>>
                        <label class="form-check-label small" for="<?php echo e($fieldId); ?>"><?php echo e($field->placeholder ?: 'Yes, I agree'); ?></label>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif($fieldType === 'yes_no'): ?>
            <div class="d-flex gap-4 p-2 bg-light rounded-3 border">
                <div class="form-check">
                    <input class="form-check-input autosave-field" type="radio" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>_yes" value="Yes" <?php echo e($fieldVal === 'Yes' ? 'checked' : ''); ?> <?php echo e($isRequired ? 'required' : ''); ?>>
                    <label class="form-check-label small fw-semibold text-dark" for="<?php echo e($fieldId); ?>_yes"><i class="bi bi-check-circle text-success me-1"></i> Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input autosave-field" type="radio" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>_no" value="No" <?php echo e($fieldVal === 'No' ? 'checked' : ''); ?>>
                    <label class="form-check-label small fw-semibold text-dark" for="<?php echo e($fieldId); ?>_no"><i class="bi bi-x-circle text-danger me-1"></i> No</label>
                </div>
            </div>

        <?php elseif($isDate || $fieldType === 'date'): ?>
            <input type="date" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" value="<?php echo e($fieldVal); ?>" <?php echo e($isRequired ? 'required' : ''); ?>>

        <?php elseif($fieldType === 'number'): ?>
            <input type="number" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" value="<?php echo e($fieldVal); ?>" placeholder="<?php echo e($field->placeholder); ?>" <?php echo e($isRequired ? 'required' : ''); ?>>

        <?php elseif($fieldType === 'email'): ?>
            <input type="email" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" value="<?php echo e($fieldVal); ?>" placeholder="<?php echo e($field->placeholder ?: 'example@domain.com'); ?>" <?php echo e($isRequired ? 'required' : ''); ?>>

        <?php elseif($fieldType === 'phone'): ?>
            <input type="tel" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" value="<?php echo e($fieldVal); ?>" placeholder="<?php echo e($field->placeholder ?: '+1 (555) 000-0000'); ?>" <?php echo e($isRequired ? 'required' : ''); ?>>

        <?php else: ?>
            <input type="text" name="<?php echo e($fieldName); ?>" id="<?php echo e($fieldId); ?>" class="form-control rounded-3 autosave-field" value="<?php echo e($fieldVal); ?>" placeholder="<?php echo e($field->placeholder); ?>" <?php echo e($isRequired ? 'required' : ''); ?>>
        <?php endif; ?>

        <?php if($field->help_text && !in_array($fieldType, ['instructions', 'declaration'])): ?>
            <div class="form-text extra-small text-muted mt-1" style="font-size: 11px;"><?php echo e($field->help_text); ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\partials\dynamic-form-field.blade.php ENDPATH**/ ?>