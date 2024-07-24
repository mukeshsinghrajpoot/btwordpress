jQuery(document).ready(function($) {
    $('.save-button').on('click', function() {
        var $row = $(this).closest('tr');
        var id = $row.data('id');
        var data = {};

        $row.find('.inline-edit').each(function() {
            var column = $(this).data('column');
            var value = $(this).val();
            data[column] = value;
        });

        $.ajax({
            url: multitabCsv.ajax_url,
            type: 'POST',
            data: {
                action: 'update_row',
                nonce: multitabCsv.nonce,
                id: id,
                data: data
            },
            success: function(response) {
                if (response.success) {
                    alert('Data updated successfully.');
                } else {
                    alert('Error updating data.');
                }
            },
            error: function() {
                alert('Error with AJAX request.');
            }
        });
    });
    // Inline deletion
    $(document).on('click', '.delete-button', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var id = $row.data('id');

        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: multitabCsv.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_row',
                    nonce: multitabCsv.nonce,
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        $row.remove();
                        alert('Item deleted successfully.');
                    } else {
                        alert('Error deleting item.');
                    }
                },
                error: function() {
                    alert('Error with AJAX request.');
                }
            });
        }
    });
});
