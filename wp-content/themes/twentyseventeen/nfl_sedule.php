<?php
// Enable SVG Uploads
function add_svg_to_upload_mimes( $upload_mimes ) {
    $upload_mimes['svg'] = 'image/svg+xml';
    return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes' );

// Fix SVG MIME Type Check
function fix_svg_mime_type( $data, $file, $filename, $mimes ) {
    $ext = pathinfo( $filename, PATHINFO_EXTENSION );
    if ( 'svg' === $ext ) {
        $data['ext'] = 'svg';
        $data['type'] = 'image/svg+xml';
    }
    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 4 );

add_shortcode( 'NFL','nfl_sedule');
function nfl_sedule() 
{  
wp_enqueue_style( 'mycustom', get_theme_file_uri( '/nfl.css', __FILE__ ) );
wp_enqueue_script('nfl-schedule-script', get_template_directory_uri() . '/nfl.js', array('jquery'), null, true);
    wp_localize_script('nfl-schedule-script', 'nflScheduleAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
?>
<?php
$args1 = array(
    'post_type' => 'nfl-schedule',
    'posts_per_page' => -1
);
$nfl_schedule_query1 = new WP_Query($args1);

// Initialize arrays to store unique values
$unique_years = array();
$unique_weeks = array();

if ($nfl_schedule_query1->have_posts()) {
    while ($nfl_schedule_query1->have_posts()) {
        $nfl_schedule_query1->the_post();
        
        // Retrieve values
        $select_year = get_field('select_year');
        $select_week = get_field('select_week');
        
        // Check if year is unique
        if (!in_array($select_year, $unique_years)) {
            // Store the unique year
            $unique_years[] = $select_year;
        }
        
        // Check if week is unique
        if (!in_array($select_week, $unique_weeks)) {
            // Store the unique week
            $unique_weeks[] = $select_week;
        }
    }
    // Sort years descending (assuming years are integers)
    rsort($unique_years, SORT_NUMERIC);
    // Sort weeks alphabetically or as needed
    sort($unique_weeks, SORT_NATURAL);
    
    wp_reset_postdata();
} else {
    echo 'No NFL schedules found.';
}
?>

<div class="Schedule-container">
    <div class="Schedule-container-inner">
    <h2>NFL Schedule</h2>
    <h1 class="schedule-heading"><span id='select_year'></span> &mdash; <span id='select_week'></span></h1>
    <div class="selectors">
    <select id="select-year">
        <?php foreach ($unique_years as $year) : ?>
            <option value="<?php echo esc_attr($year); ?>" <?php selected($year, 2024); ?>><?php echo esc_html($year); ?></option>
        <?php endforeach; ?>
    </select>
    <select id="select-week">
        <?php foreach ($unique_weeks as $week) : ?>
            <option value="<?php echo esc_attr($week); ?>" <?php selected($week, 'WEEK 1'); ?>><?php echo esc_html($week); ?></option>
        <?php endforeach; ?>
    </select>
   </div>
   <div class="table-container">
  <div id="nfl-schedule-container">
    <?php get_nfl_schedule('2024', 'WEEK 1'); // Set default schedule ?>
 </div>
</div>
</div>
</div>
<?php } 
function get_nfl_schedule($year = '', $week = '', $paged = 1) {
    $meta_query = array();

    if ($year) {
        $meta_query[] = array(
            'key' => 'select_year',
            'value' => $year,
            'compare' => '='
        );
    }

    if ($week) {
        $meta_query[] = array(
            'key' => 'select_week',
            'value' => $week,
            'compare' => '='
        );
    }

    $args = array(
        'post_type' => 'nfl-schedule',
        'posts_per_page' => 2, // Display one schedule per page
        'paged' => $paged,
        'meta_query' => $meta_query
    );

    $nfl_schedule_query = new WP_Query($args);

    if ($nfl_schedule_query->have_posts()) {
        echo '<table class="nfl-schedule-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Date</th>';
        echo '<th>Time (ET)</th>';
        echo '<th>Home Team</th>';
        echo '<th>Visitor Team</th>';
        echo '<th>Result</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        while ($nfl_schedule_query->have_posts()) {
            $nfl_schedule_query->the_post();
            $game_date = get_field('date'); // Replace 'date' with your actual ACF field name
            $game_time = get_field('time_et'); // Replace 'time_et' with your actual ACF field name
            $home_team = get_field('home_team'); // Replace 'home_team' with your actual ACF field name
            $away_team = get_field('visitor_team'); // Replace 'visitor_team' with your actual ACF field name
            $result = get_field('result'); // Replace 'result' with your actual ACF field name

            echo '<tr>';
            echo '<td>' . esc_html($game_date) . '</td>';
            echo '<td>' . esc_html($game_time) . '</td>';
            echo '<td>' . esc_html($home_team) . '</td>';
            echo '<td>' . esc_html($away_team) . '</td>';
            echo '<td>' . esc_html($result) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

        // Pagination
        $total_pages = $nfl_schedule_query->max_num_pages;
        if ($total_pages > 1) {
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total' => $nfl_schedule_query->max_num_pages,
                'current' => max(1, $paged),
                'prev_text' => __('« Prev'),
                'next_text' => __('Next »'),
                 'prev_next' => false
            ));
            echo '</div>';
        }

        wp_reset_postdata();
    } else {
        echo '<span class="notfound-content">No NFL schedules found.</span>';
    }
}


function ajax_get_nfl_schedule() {
    $year = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $week = isset($_POST['week']) ? sanitize_text_field($_POST['week']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    ob_start();
    get_nfl_schedule($year, $week, $paged);
    $content = ob_get_clean();

    echo $content;
    wp_die();
}
add_action('wp_ajax_get_nfl_schedule', 'ajax_get_nfl_schedule');
add_action('wp_ajax_nopriv_get_nfl_schedule', 'ajax_get_nfl_schedule');
?>

