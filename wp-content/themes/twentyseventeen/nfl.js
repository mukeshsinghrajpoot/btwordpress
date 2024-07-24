jQuery(document).ready(function($) {
    function fetchSchedules(year, week, page) {
        $('#select_year').text(year);
        $('#select_week').text(week);
        $.ajax({
            type: 'POST',
            url: nflScheduleAjax.ajaxurl,
            data: {
                action: 'get_nfl_schedule',
                year: year,
                week: week,
                paged: page // Include paged parameter for pagination
            },
            success: function(response) {
                $('#nfl-schedule-container').html(response);
            }
        });
    }

    // Fetch default schedules on page load
    var defaultYear = $('#select-year').val();
    var defaultWeek = $('#select-week').val();
    fetchSchedules(defaultYear, defaultWeek, 1); // Start with page 1

    // Fetch schedules on select change
    $('#select-year, #select-week').on('change', function() {
        var year = $('#select-year').val();
        var week = $('#select-week').val();
        fetchSchedules(year, week, 1); // Reset to page 1 when filters change
    });

    // Pagination click event
    $(document).on('click', '.pagination .page-numbers', function(e) {
        e.preventDefault();
        var page = $(this).text(); // Get page number from the pagination link text
        var year = $('#select-year').val();
        var week = $('#select-week').val();
        fetchSchedules(year, week, page);
    });
});
