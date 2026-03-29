$(document).ready(function () {
    $('#leadsTable').DataTable({
        "processing": true,
        "serverSide": false, // Change to true if you want server-side processing
        "ajax": {
            "url": base_url + "admin/leads/list",
            "type": "POST",
            "data": function (d) {
                d[csrfName] = csrfHash;
            }
        },
        "columns": [
            { "data": 0, "orderable": false },
            { "data": 1, "orderable": false },
            { "data": 2 },
            { "data": 3 },
            { "data": 4, "orderable": false }
        ]
    });

    $(document).on('click', '.change-status', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const statusName = $(this).text();

        if (confirm('Are you sure you want to change the status to ' + statusName + '?')) {
            $.ajax({
                url: base_url + 'admin/leads/updateStatus',
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#leadsTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message || 'Update failed');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }
    });
});
