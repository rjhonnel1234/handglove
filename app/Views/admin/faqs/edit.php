<?php echo $this->extend('admin/includes/layout') ?>

<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Edit FAQ</h2>
                <div class="kicker-bottom">Update an existing frequently asked question</div>
            </div>
            <a href="<?= base_url('admin/faqs') ?>" class="btn btn-secondary shadow-sm">Back to List</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="faq-form" action="<?= base_url('admin/faqs/update/' . $faq['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <label for="question" class="form-label">Question</label>
                        <input type="text" name="question" id="question" value="<?= esc($faq['question']) ?>" class="form-control" placeholder="Enter the question" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="answer" class="form-label">Answer</label>
                        <textarea name="answer" id="answer" rows="6" class="form-control" placeholder="Enter the answer" required><?= esc($faq['answer']) ?></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" <?= $faq['status'] == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= $faq['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn thm-btn w-100 py-2 btn-save">Update FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>

<?php echo $this->section('customJS') ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#faq-form').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let btn = form.find('.btn-save');
            let originalBtnText = btn.html();
            
            btn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: response.message_header || 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    } else {
                        btn.html(originalBtnText).prop('disabled', false);
                        let errorMsg = response.message || 'Something went wrong.';
                        if (response.errors) {
                            errorMsg = Object.values(response.errors).join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMsg
                        });
                    }
                },
                error: function() {
                    btn.html(originalBtnText).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'A server error occurred. Please try again.'
                    });
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>
