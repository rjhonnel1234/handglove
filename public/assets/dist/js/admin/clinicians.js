Dropzone.autoDiscover = false;

$(document).ready(function () {
    const $form = $('#clinician-form');
    if ($form.length === 0) return;

    const uploadUrl = $form.data('upload-url');
    const csrfToken = $form.data('csrf-token');
    const csrfHash = $form.data('csrf-hash');
    const existingImage = $form.data('existing-image');

    const myDropzone = new Dropzone("#profile-pic-dropzone", {
        url: uploadUrl,
        maxFiles: 1,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': csrfHash
        },
        params: {
            [csrfToken]: csrfHash
        },
        init: function () {
            this.on("success", function (file, response) {
                if (response.status === 'success') {
                    $('#profile_pic_path').val(response.path);
                    toastr.success('Image uploaded successfully');
                } else {
                    toastr.error(response.message || 'Upload failed');
                    this.removeFile(file);
                }
            });

            this.on("error", function (file, message) {
                toastr.error(typeof message === 'string' ? message : 'Upload failed');
                this.removeFile(file);
            });

            this.on("removedfile", function (file) {
                $('#profile_pic_path').val('');
            });

            // Handle existing image in Edit mode
            if (existingImage && existingImage !== '') {
                const mockFile = { name: "Existing Profile Picture", size: 12345 };
                this.displayExistingFile(mockFile, existingImage);
                // We don't set profile_pic_path here because we only want to update if a NEW one is uploaded.
                // However, the controller handles this fallback.
            }
        }
    });

    // PCC Credentials Dynamic Handling
    const $facilitySelect = $('#client_ids');
    const $pccWrapper = $('#pcc-fields-wrapper');
    const $pccContainer = $('.pcc-credentials-container');

    $facilitySelect.on('change', function () {
        const selectedOptions = $(this).find('option:selected');
        const selectedIds = selectedOptions.map(function () { return $(this).val(); }).get();

        if (selectedIds.length > 0) {
            $pccContainer.show();
        } else {
            $pccContainer.hide();
        }

        // Add missing rows
        selectedOptions.each(function () {
            const id = $(this).val();
            const name = $(this).text();

            if ($pccWrapper.find(`[data-facility-id="${id}"]`).length === 0) {
                const html = `
                    <div class="pcc-facility-row mb-3 pb-2 border-bottom" data-facility-id="${id}">
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <label class="small text-muted">PCC Username</label>
                                <input type="text" name="pcc[${id}][username]" class="form-control form-control-sm" placeholder="Username">
                            </div>
                            <div class="col-12">
                                <label class="small text-muted">PCC Password</label>
                                <input type="password" name="pcc[${id}][password]" class="form-control form-control-sm" placeholder="Password">
                            </div>
                        </div>
                    </div>
                `;
                $pccWrapper.append(html);
            }
        });

        // Remove unselected rows
        $pccWrapper.find('.pcc-facility-row').each(function () {
            const id = $(this).data('facility-id').toString();
            if (!selectedIds.includes(id)) {
                $(this).remove();
            }
        });
    });

    // Company Work Dynamic Handling
    let companyWorkIndex = $('.company-work-row').length;
    $('#add-company-work').on('click', function () {
        const html = `
            <div class="row mb-2 pb-2 border-bottom company-work-row">
                <div class="col-md-4 pl-0">
                    <input type="text" name="company_work[${companyWorkIndex}][company_name]" class="form-control form-control-sm" placeholder="Company Name">
                </div>
                <div class="col-md-3 pl-0">
                    <input type="text" name="company_work[${companyWorkIndex}][supervisor_name]" class="form-control form-control-sm" placeholder="Supervisor">
                </div>
                <div class="col-md-4 pl-0">
                    <input type="text" name="company_work[${companyWorkIndex}][supervisor_contact]" class="form-control form-control-sm" placeholder="Contact">
                </div>
                <div class="col-md-1  pl-0 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-link text-danger remove-company-work"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        $('#company-work-wrapper').append(html);
        companyWorkIndex++;
        updateCompanyRemoveButtons();
    });

    $(document).on('click', '.remove-company-work', function () {
        $(this).closest('.company-work-row').remove();
        updateCompanyRemoveButtons();
    });

    function updateCompanyRemoveButtons() {
        if ($('.company-work-row').length <= 1) {
            $('.remove-company-work').hide();
        } else {
            $('.remove-company-work').show();
        }
    }

    // Initial check
    updateCompanyRemoveButtons();

    // Form submission
    $form.on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const $btn = $form.find('button[type="submit"]');
        const originalBtnText = $btn.text();

        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    setTimeout(function () {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    if (response.errors) {
                        let errorMsg = Object.values(response.errors).join('<br>');
                        toastr.error(errorMsg);
                    } else {
                        toastr.error(response.message || 'Action failed');
                    }
                    $btn.prop('disabled', false).text(originalBtnText);
                }
            },
            error: function () {
                toastr.error('An error occurred. Please try again.');
                $btn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
});
