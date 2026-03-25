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
    const clinicianId = $form.data('clinician-id');

    $facilitySelect.on('change', function () {
        const selectedId = $(this).val();
        const selectedName = $(this).find('option:selected').text();

        if (selectedId) {
            $pccContainer.show();
            $('.pcc-facility-name').text(selectedName);

            // Clear fields first
            $('#pcc_username').val('').attr('name', `pcc[${selectedId}][username]`);
            $('#pcc_password').val('').attr('name', `pcc[${selectedId}][password]`);

            if (clinicianId) {
                // Fetch credentials via AJAX
                $.ajax({
                    url: `${$form.data('base-url')}admin/clinicians/get-pcc-credentials`,
                    type: 'GET',
                    data: {
                        clinician_id: clinicianId,
                        facility_id: selectedId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#pcc_username').val(response.data.username);
                            $('#pcc_password').val(response.data.password);
                        }
                    }
                });
            }
        } else {
            $pccContainer.hide();
            $('#pcc_username').attr('name', '');
            $('#pcc_password').attr('name', '');
        }
    });

    // Company Work Dynamic Handling
    let companyWorkIndex = $('.company-work-row').length;
    $('#add-company-work').on('click', function () {
        const html = `
            <div class="row mb-2 pb-2 border-bottom company-work-row">
                <div class="col-md-4">
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

    // Handglove User Access Toggle
    $('#create_clinician_access').on('change', function () {
        if ($(this).is(':checked')) {
            $('#handglove-user-access').slideDown();
            // Sync email to username if username is empty
            const email = $('input[name="email"]').val();
            if (email && !$('input[name="handglove_username"]').val()) {
                $('input[name="handglove_username"]').val(email);
            }
        } else {
            $('#handglove-user-access').slideUp();
        }
    });

    $('input[name="email"]').on('keyup change', function () {
        if ($('#create_clinician_access').is(':checked')) {
            $('input[name="handglove_username"]').val($(this).val());
        }
    });

    // Initial check
    updateCompanyRemoveButtons();
    $facilitySelect.trigger('change');

    $(document).on('click', '.toggle-user-access', function () {
        const userId = $(this).data('user-id');
        const status = $(this).data('status');
        const $btn = $(this);

        if (confirm(`Are you sure you want to ${status == 1 ? 'activate' : 'deactivate'} this user access?`)) {
            $.ajax({
                url: `${$form.data('base-url')}admin/clinicians/toggle-user-status`,
                type: 'POST',
                data: {
                    user_id: userId,
                    status: status,
                    [csrfToken]: csrfHash
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

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
