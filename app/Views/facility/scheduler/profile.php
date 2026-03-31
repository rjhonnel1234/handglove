<style>
    .dropzone {
        border: 2px dashed #007bff;
        border-radius: 5px;
        background: #f8f9fa;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropzone .dz-message {
        font-weight: 400;
        color: #6c757d;
    }

    .dropzone .dz-message span {
        font-size: 16px;
    }
</style>
<?php
echo view('facility/includes/profile_banner');
?>

<div id="profile-bios">
    <div class="container">
        <div class="row">
            <div id="profile-main" class="col-lg-8 col-md-12 col-sm-12 col-12">
                <div id="scheduler-calendar"></div>
            </div>
            <div id="profile-sidebar" class="col-lg-4 col-md-12 col-sm-12 col-12">
                <div id="callout-heatmap">
                    <h4>Call out Heatmap</h4>
                    <div class="heatmap-grid-container">
                        <div class="heatmap-grid">
                            <!-- Labels -->
                            <div class="grid-label"></div>
                            <div class="grid-day">Mon</div>
                            <div class="grid-day">Tue</div>
                            <div class="grid-day">Wed</div>
                            <div class="grid-day">Thu</div>
                            <div class="grid-day">Fri</div>
                            <div class="grid-day">Sat</div>
                            <div class="grid-day">Sun</div>

                            <!-- 3am Row -->
                            <div class="grid-time">3 am</div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-1"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-1"></div>

                            <!-- 6am Row -->
                            <div class="grid-time">6 am</div>
                            <div class="grid-cell lvl-2">2x</div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-3">4x</div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-1"></div>
                            <div class="grid-cell lvl-0"></div>

                            <!-- 9am Row -->
                            <div class="grid-time">9 am</div>
                            <div class="grid-cell lvl-1"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-1"></div>
                            <div class="grid-cell lvl-4">5x</div>
                            <div class="grid-cell lvl-1"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>

                            <!-- 12pm Row -->
                            <div class="grid-time">12 pm</div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-2">2x</div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-2">2x</div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-2">2x</div>
                            <div class="grid-cell lvl-0"></div>

                            <!-- 3pm Row -->
                            <div class="grid-time">3 pm</div>
                            <div class="grid-cell lvl-1"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-0"></div>
                            <div class="grid-cell lvl-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var redirectCheck = setInterval(function() {
            if (typeof Dropzone !== 'undefined' && Dropzone.instances && Dropzone.instances.length > 0) {
                var found = false;
                Dropzone.instances.forEach(function(dz) {
                    if (dz.element && dz.element.id === 'schedule-dropzone') {
                        // Remove existing success handlers if any to avoid duplicates
                        dz.off("success"); 
                        dz.on("success", function(file, response) {
                            var res = response;
                            if (typeof response === 'string') {
                                try { res = JSON.parse(response); } catch(e) { console.error('JSON parse error', e); }
                            }
                            console.log('Upload response:', res);
                            if (res.success && res.redirect) {
                                window.location.href = res.redirect;
                            } else if (res.message && typeof toastr !== 'undefined') {
                                toastr.success(res.message);
                            }
                        });
                        found = true;
                    }
                });
                if (found) clearInterval(redirectCheck);
            }
        }, 500);
        
        // Stop checking after 10 seconds to avoid infinite loop if modal isn't opened
        setTimeout(function() { clearInterval(redirectCheck); }, 10000);
    })();
</script>

<!-- Upload Schedule Modal -->
<div class="modal fade" id="uploadScheduleModal" tabindex="-1" role="dialog" aria-labelledby="uploadScheduleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadScheduleModalLabel">Upload Schedule for <span
                        id="selected-date-display"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadScheduleForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="shift_date" id="selected-date">
                    <div id="schedule-dropzone" class="dropzone">
                        <div class="dz-message" data-dz-message>
                            <span>Drag & Drop PDF Schedule here (PDF) or click to upload</span>
                        </div>
                    </div>
                </div>
                <div class="modal-header d-flex justify-content-end border-0">
                    <a href="<?= base_url('facility/schedules/add') ?>" class="btn thm-btn mr-auto">Add Manually</a>

                    <button type="button" class="btn btn-warning mr-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnUploadSchedule">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>