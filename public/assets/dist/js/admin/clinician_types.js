$(document).ready(function() {
    $('#clinicianTypesTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "admin/clinician-types/list",
            "type": "POST",
            "data": function(d) {
                d[csrfName] = csrfHash;
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [4] }
        ],
        "language": {
            "processing": '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });
});
