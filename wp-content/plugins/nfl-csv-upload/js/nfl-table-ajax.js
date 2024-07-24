jQuery(document).ready(function($) {
    $('form').on('change', 'select[name="sheet_name"]', function() {
        $.ajax({
            url: nflTableAjax.ajax_url,
            type: 'GET',
            data: {
                action: 'filter_nfl_games',
                sheet_name: $(this).val()
            },
            success: function(response) {
                $('#infoEntries').html(response);
            }
        });
    });
});
