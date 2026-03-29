$(document).ready(function () {
    $('#institutionsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "admin/institutions/list",
            "type": "POST",
            "data": function (d) {
                d[csrfName] = csrfHash;
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [2] }
        ],
        "language": {
            "processing": '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });
});
