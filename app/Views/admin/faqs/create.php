<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="heading mb-4">
            <h2>Add FAQ</h2>
            <div class="kicker-bottom">Create a new frequently asked question</div>
        </div>

        <form id="faq-form" action="<?= base_url('admin/faqs/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="question" class="form-label">Question</label>
                                        <input type="text" name="question" id="question" class="form-control"
                                            placeholder="Enter the question" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="answer" class="form-label">Answer</label>
                                        <textarea name="answer" id="answer" rows="6" class="form-control"
                                            placeholder="Enter the answer" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm mt-2">
                        <div class="card-body">
                            <div class="text-right">
                                <button type="submit" class="btn thm-btn py-2 btn-save mr-2">Save FAQ</button>
                                <a href="<?= base_url('admin/faqs') ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#faq-form').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let btn = form.find('.btn-save');
            let originalBtnText = btn.html();

            btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        btn.html(originalBtnText).prop('disabled', false);
                        let errorMsg = response.message || 'Something went wrong.';
                        if (response.errors) {
                            errorMsg = Object.values(response.errors).join('<br>');
                        }
                        toastr.error(errorMsg);
                    }
                },
                error: function () {
                    btn.html(originalBtnText).prop('disabled', false);
                    toastr.error('A server error occurred. Please try again.');
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>