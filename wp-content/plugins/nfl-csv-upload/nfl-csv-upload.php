<?php
/*
Plugin Name: NFL CSV Upload
Description: A plugin to upload and manage CSV files with multiple tabs.
Version: 1.0
Author: Amrendra Singh
*/

global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require 'vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

function multitab_csv_create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'multitab_csv_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        sheet_name varchar(255) NOT NULL,
        date varchar(255) NULL,
        time_et varchar(255) NULL,
        home_team varchar(255) NOT NULL,
        visitor_team varchar(255) NOT NULL,
        result varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    dbDelta($sql);
}

register_activation_hook(__FILE__, 'multitab_csv_create_table');

add_action('admin_menu', 'multitab_csv_upload_menu');

function multitab_csv_upload_menu() {
    add_menu_page(
        'NFL CSV Upload', 
        'NFL CSV Upload', 
        'manage_options', 
        'multitab-csv-upload', 
        'multitab_csv_upload_page',
        'dashicons-upload', 
        6
    );
    add_submenu_page(
        'multitab-csv-upload',
        'Add New Data',
        'Add New Data',
        'manage_options',
        'multitab-csv-add-new-data',
        'multitab_csv_add_new_data_page'
    );
    add_submenu_page(
        'multitab-csv-upload',
        'Manage Data',
        'Manage Data',
        'manage_options',
        'multitab-csv-manage-data',
        'multitab_csv_manage_data_page'
    );
}

function multitab_csv_upload_page() {
    // Define the URL of the sample CSV file
    $sample_csv_url = plugins_url('sample.xlsx', __FILE__);
    ?>
    <div class="wrap">
        <h1>Multitab CSV Upload</h1>
        <form method="post" enctype="multipart/form-data" action="">
            <input type="file" name="csv_file" accept=".csv, .xlsx">
            <?php submit_button('Upload CSV'); ?>
        </form>
        
        <form method="get" action="<?php echo esc_url($sample_csv_url); ?>">
            <button type="submit" class="button button-secondary">Download Sample files</button>
        </form>

        <?php
        if (isset($_FILES['csv_file'])) {
            multitab_csv_upload_handle_file($_FILES['csv_file']);
        }
        ?>
    </div>
    <?php
}


function multitab_csv_upload_handle_file($file) {
    $upload_dir = wp_upload_dir();
    $upload_file = $upload_dir['path'] . '/' . basename($file['name']);
    
    if (move_uploaded_file($file['tmp_name'], $upload_file)) {
        echo "<p>File uploaded successfully: " . esc_html($file['name']) . "</p>";
        multitab_csv_process_file($upload_file);
    } else {
        echo "<p>File upload failed.</p>";
    }
}

function multitab_csv_process_file($file_path) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'multitab_csv_data';

    // Truncate the table to remove previous data
    $wpdb->query("TRUNCATE TABLE $table_name");

    try {
        $spreadsheet = IOFactory::load($file_path);
    } catch (Exception $e) {
        echo 'Error loading file: ', $e->getMessage();
        return;
    }

    $sheetCount = $spreadsheet->getSheetCount();

    for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
        $spreadsheet->setActiveSheetIndex($sheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetName = $sheet->getTitle();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            // Skip header row
            if ($index === 0) {
                continue;
            }

            if (count($row) < 5) {
                // Skip rows with insufficient data
                continue;
            }

            $date = sanitize_text_field($row[0]);
            $time_et = sanitize_text_field($row[1]);
            $home_team = sanitize_text_field($row[2]);
            $visitor_team = sanitize_text_field($row[3]);
            $result = sanitize_text_field($row[4]);

            // Skip rows with empty required fields
            if (empty($home_team) || empty($visitor_team)) {
                continue;
            }

            $wpdb->insert(
                $table_name,
                array(
                    'sheet_name' => $sheetName,
                    'date' => $date,
                    'time_et' => $time_et,
                    'home_team' => $home_team,
                    'visitor_team' => $visitor_team,
                    'result' => $result
                )
            );
        }
    }

    echo "<p>Data inserted successfully from all tabs.</p>";
}




// Enqueue scripts and styles
function nfl_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('nfl-table-ajax', plugins_url('js/nfl-table-ajax.js', __FILE__), array('jquery'), null, true);

    // Localize the script with Ajax URL
    wp_localize_script('nfl-table-ajax', 'nflTableAjax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'nfl_enqueue_scripts');

// Shortcode to display table
function nfl_games_table_shortcode($atts) {
    global $wpdb;

    // Set default attributes and parse incoming attributes
    $atts = shortcode_atts(array(
        'sheet_name' => 'Week 1', // Default to 'Week 1'
    ), $atts);

    // Fetch distinct sheet names for the dropdown
    $sheet_names = $wpdb->get_col("SELECT DISTINCT sheet_name FROM {$wpdb->prefix}multitab_csv_data");

    // Build the HTML table with filter form
    ob_start();
    ?>
    <form method="get" action="">
        <select name="sheet_name">
            <?php foreach ($sheet_names as $sheet_name): ?>
                <option value="<?php echo esc_attr($sheet_name); ?>" <?php selected($atts['sheet_name'], $sheet_name); ?>>
                    <?php echo esc_html($sheet_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <div id="infoEntries">
        <?php
        // Fetch data based on selected sheet_name
        $selected_sheet_name = isset($_GET['sheet_name']) ? sanitize_text_field($_GET['sheet_name']) : $atts['sheet_name'];
        $query = "SELECT * FROM {$wpdb->prefix}multitab_csv_data";
        if ($selected_sheet_name) {
            $query .= $wpdb->prepare(" WHERE sheet_name = %s", $selected_sheet_name);
        }
        $query .= " ORDER BY date, time_et";
        $results = $wpdb->get_results($query);

        // Generate table
        ?>
        <table border="0" cellpadding="0" cellspacing="0" class="data table table-condensed table-striped table-bordered ordenable justify-center">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time (ET)</th>
                    <th>Home Team</th>
                    <th>Visitor Team</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td data-title="Date"><?php echo esc_html(date('M j, Y', strtotime($row->date))); ?></td>
                            <td data-title="Time"><?php echo esc_html(date('g:i A', strtotime($row->time_et))); ?></td>
                            <td data-title="Home Team"><?php echo esc_html($row->home_team); ?></td>
                            <td data-title="Visitor Team"><?php echo esc_html($row->visitor_team); ?></td>
                            <td data-title="Result"><?php echo esc_html($row->result); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No data available for the selected sheet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('nfl_games_table', 'nfl_games_table_shortcode');

// Handle the AJAX request
function filter_nfl_games() {
    global $wpdb;

    $sheet_name = isset($_GET['sheet_name']) ? sanitize_text_field($_GET['sheet_name']) : '';

    // Fetch data based on selected sheet_name
    $query = "SELECT * FROM {$wpdb->prefix}multitab_csv_data";
    if ($sheet_name) {
        $query .= $wpdb->prepare(" WHERE sheet_name = %s", $sheet_name);
    }
    $query .= " ORDER BY date, time_et";
    $results = $wpdb->get_results($query);

    // Generate table rows
    ob_start();
    ?>
    <table border="0" cellpadding="0" cellspacing="0" class="data table table-condensed table-striped table-bordered ordenable justify-center">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time (ET)</th>
                <th>Home Team</th>
                <th>Visitor Team</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td data-title="Date"><?php echo esc_html($row->date); ?></td>
                        <td data-title="Time"><?php echo esc_html($row->time_et); ?></td>
                        <td data-title="Home Team"><?php echo esc_html($row->home_team); ?></td>
                        <td data-title="Visitor Team"><?php echo esc_html($row->visitor_team); ?></td>
                        <td data-title="Result"><?php echo esc_html($row->result); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No data available for the selected sheet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    echo ob_get_clean();
    wp_die(); // This is required to terminate immediately and return a proper response
}
add_action('wp_ajax_filter_nfl_games', 'filter_nfl_games');
add_action('wp_ajax_nopriv_filter_nfl_games', 'filter_nfl_games');

function multitab_csv_add_new_data_page() {
    ?>
    <div class="wrap">
        <h1>Add New Data</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Sheet Name</th>
                    <td><input type="text" name="sheet_name" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Date</th>
                    <td><input type="text" name="date" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Time (ET)</th>
                    <td><input type="text" name="time_et" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Home Team</th>
                    <td><input type="text" name="home_team" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Visitor Team</th>
                    <td><input type="text" name="visitor_team" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Result</th>
                    <td><input type="text" name="result" required /></td>
                </tr>
            </table>
            <?php submit_button('Add Data'); ?>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            multitab_csv_handle_new_data();
        }
        ?>
    </div>
    <?php
}

function multitab_csv_handle_new_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'multitab_csv_data';

    $sheet_name = sanitize_text_field($_POST['sheet_name']);
    $date = sanitize_text_field($_POST['date']);
    $time_et = sanitize_text_field($_POST['time_et']);
    $home_team = sanitize_text_field($_POST['home_team']);
    $visitor_team = sanitize_text_field($_POST['visitor_team']);
    $result = sanitize_text_field($_POST['result']);

    $wpdb->insert(
        $table_name,
        array(
            'sheet_name' => $sheet_name,
            'date' => $date,
            'time_et' => $time_et,
            'home_team' => $home_team,
            'visitor_team' => $visitor_team,
            'result' => $result
        )
    );

    echo "<p>Data added successfully.</p>";
}

function multitab_csv_manage_data_page() {
    wp_enqueue_style('multitab-csv-custom-styles', plugins_url('css/multitab-csv-custom-styles.css', __FILE__));
    global $wpdb;
    $table_name = $wpdb->prefix . 'multitab_csv_data';

    $items_per_page = 15;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $items_per_page);

    $data = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name LIMIT %d OFFSET %d",
        $items_per_page,
        $offset
    ));

    ?>
    <div class="wrap">
        <h1>Manage Data</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sheet Name</th>
                    <th>Date</th>
                    <th>Time (ET)</th>
                    <th>Home Team</th>
                    <th>Visitor Team</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr data-id="<?php echo esc_attr($row->id); ?>">
                            <td><?php echo esc_html($row->id); ?></td>
                            <td><input type="text" class="inline-edit" data-column="sheet_name" value="<?php echo esc_attr($row->sheet_name); ?>" /></td>
                            <td><input type="text" class="inline-edit" data-column="date" value="<?php echo esc_attr($row->date); ?>" /></td>
                            <td><input type="text" class="inline-edit" data-column="time_et" value="<?php echo esc_attr($row->time_et); ?>" /></td>
                            <td><input type="text" class="inline-edit" data-column="home_team" value="<?php echo esc_attr($row->home_team); ?>" /></td>
                            <td><input type="text" class="inline-edit" data-column="visitor_team" value="<?php echo esc_attr($row->visitor_team); ?>" /></td>
                            <td><input type="text" class="inline-edit" data-column="result" value="<?php echo esc_attr($row->result); ?>" /></td>
                            <td>
                                <button class="save-button button">Save</button> | 
                                 <button class="delete-button button">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $pagination_args = array(
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
        );
        multitab_csv_pagination($pagination_args);
        ?>
    </div>
    <?php
}
function multitab_csv_pagination($args) {
    $big = 999999999;
    $pagination_links = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, $args['current_page']),
        'total' => $args['total_pages'],
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
    ));
    if ($pagination_links) {
        echo '<div class="tablenav"><div class="tablenav-pages">' . $pagination_links . '</div></div>';
    }
}


function multitab_csv_enqueue_scripts() {

    wp_enqueue_script('multitab-csv-inline-edit', plugin_dir_url(__FILE__) . 'js/inline-edit.js', array('jquery'), null, true);
    wp_localize_script('multitab-csv-inline-edit', 'multitabCsv', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('multitab_csv_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'multitab_csv_enqueue_scripts');


function multitab_csv_ajax_update_row() {
    check_ajax_referer('multitab_csv_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'multitab_csv_data';

    $id = intval($_POST['id']);
    $data = $_POST['data'];

    if (empty($id) || !is_array($data)) {
        wp_send_json_error('Invalid data.');
    }

    $wpdb->update(
        $table_name,
        $data,
        array('id' => $id)
    );

    wp_send_json_success();
}
add_action('wp_ajax_update_row', 'multitab_csv_ajax_update_row');

function multitab_csv_ajax_delete_row() {
    check_ajax_referer('multitab_csv_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'multitab_csv_data';

    $id = intval($_POST['id']);

    if (empty($id)) {
        wp_send_json_error('Invalid ID.');
    }

    $deleted = $wpdb->delete($table_name, array('id' => $id));

    if ($deleted !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Error deleting data.');
    }
}
add_action('wp_ajax_delete_row', 'multitab_csv_ajax_delete_row');
