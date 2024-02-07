<?php
/**
 * DataUp Master Desheetech
 *
 * @package       DATAUPMAST
 * @author        raviranjan kumar
 * @license       GPL-2.0-or-later
 * @version       1.0
 *
 * @wordpress-plugin
 * Plugin Name:   DataUp Master Desheetech
 * Plugin URI:    https://desheetek.xyz/backup-plugin-info
 * Description:   This plugin provides the functionality to backup your data, theme files, and plugins in SQL format.
 * Version:       1.0
 * Author:        raviranjan kumar
 * Author URI:    https://desheetek.xyz/author-info
 * Text Domain:   dataup-master-desheetech
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with DataUp Master Desheetech. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Plugin code continues...



// Add admin menu for database and theme backup
function dataup_master_menu() {
    add_menu_page(
        'DataUp Master', // Page title
        'DataUp Master', // Menu title
        'manage_options', // Capability
        'dataup-master', // Menu slug
        'dataup_master_page', // Callback function
        'dashicons-backup' // Icon
    );
}
add_action('admin_menu', 'dataup_master_menu');

// Callback function to display backup options and social network follow links
function dataup_master_page() {
    ?>
    <style>
        .dataup-section {
            width: 80%;
            display: inline-block;
            vertical-align: top;
        }

        .backup-section {
            text-align: left;
            margin-left: 0px;
            margin-top: 10px;
        }

        .social-networks-section {
            text-align: center; /* Align text to the center */
            margin-top: 0px;
        }

        .backup-section form,
        .social-networks-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .backup-section li,
        .social-networks-section li {
            margin-right: 10px;
            display: inline-block;
        }

        .backup-section i,
        .social-networks-section i {
            margin-right: 5px;
            font-size: 38px;
            transition: color 0.3s;
        }

        .backup-section a,
        .social-networks-section a {
            text-decoration: none;
            font-weight: bold;
        }

        .backup-section a:hover,
        .social-networks-section a:hover {
            color: #3498db;
        }

        .dataup-section form {
            margin-bottom: 20px;
        }

        .wrap:after {
            content: "";
            display: table;
            clear: both;
        }
        
        .dataup-master-wrapper {
            background-color: #fafafa; /* Background color */
            padding: 20px;
            border-radius: 10px;
            text-align: center; /* Center align content */
        }

        .desheetek-logo {
            display: block; /* Display logo as block element */
            margin: 0 auto 20px; /* Center align logo and add margin at bottom */
            max-width: 70px; /* Maximum width of the logo */
            height: auto; /* Maintain aspect ratio of the logo */
        }

        .desheetek-html-section {
            text-align: left; /* Left align content */
        }
    </style>

    <div class="wrap dataup-master-wrapper">
        <img src="<?php echo plugins_url( 'your-logo.png', __FILE__ ); ?>" alt="Custom Logo" class="desheetek-logo">
        <div class="dataup-section">
            <h2>DataUp Master</h2>
            <p>Click the buttons below to backup your data, theme files, and plugins.</p>
            <div class="backup-section">
                <form method="post">
                    <?php wp_nonce_field('backup_data_action', 'backup_data_nonce'); ?>
                    <input type="submit" name="backup_data" class="button button-primary" value="Backup Data">
                </form>
                <form method="post">
                    <?php wp_nonce_field('backup_theme_action', 'backup_theme_nonce'); ?>
                    <input type="submit" name="backup_theme" class="button button-primary" value="Backup Theme">
                </form>
                <form method="post">
                    <?php wp_nonce_field('backup_plugin_action', 'backup_plugin_nonce'); ?>
                    <input type="submit" name="backup_plugin" class="button button-primary" value="Backup Plugin">
                </form>
            </div>
            
            <!-- Scheduled Backup Form -->
            <form method="post">
                <?php wp_nonce_field('scheduled_backup_action', 'scheduled_backup_nonce'); ?>
                <label for="backup_interval">Scheduled Backups:</label>
                <select name="backup_interval" id="backup_interval">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <!-- Add more options as needed -->
                </select>
                <input type="submit" name="schedule_backup" class="button button-primary" value="Schedule Backup">
            </form>
        </div>

        <div class="social-networks-section">
            <h2>Follow Us</h2>
            <ul>
                <li><i class="fab fa-facebook"></i><a href="https://www.facebook.com/your-facebook-page" target="_blank">Facebook</a></li>
                <li><i class="fab fa-twitter"></i><a href="https://twitter.com/your-twitter-account" target="_blank">Twitter</a></li>
                <li><i class="fab fa-instagram"></i><a href="https://www.instagram.com/your-instagram-account" target="_blank">Instagram</a></li>
            </ul>
        </div>
    </div>
    <?php
}

// Handle scheduled backup form submission
add_action('admin_init', 'handle_scheduled_backup_request');
function handle_scheduled_backup_request() {
    if (isset($_POST['schedule_backup']) && wp_verify_nonce($_POST['scheduled_backup_nonce'], 'scheduled_backup_action')) {
        // Process the scheduled backup form
        $backup_interval = sanitize_text_field($_POST['backup_interval']);
        schedule_backup_event($backup_interval);
    }
}

// Schedule backup event based on user-selected interval
function schedule_backup_event($interval) {
    if (!wp_next_scheduled('dataup_scheduled_backup')) {
        wp_schedule_event(time(), $interval, 'dataup_scheduled_backup');
    }
}

// Hook to handle scheduled backup
add_action('dataup_scheduled_backup', 'scheduled_backup');
function scheduled_backup() {
    // Perform the backup operations here
    backup_database(); // You can replace this with the appropriate backup function
}

// Function to upload backup files to Google Drive
function upload_to_google_drive($backup_file_paths) {
    // Include the Google API Client Library
    require_once 'path/to/google-api-php-client/vendor/autoload.php';

    // Set up Google Client
    $client = new Google_Client();
    $client->setApplicationName('Backup Upload');
    $client->setScopes(Google_Service_Drive::DRIVE_FILE);
    $client->setAuthConfig('path/to/your/client_secret.json');
    $client->setAccessType('offline');

    // Create Google Drive service
    $service = new Google_Service_Drive($client);

    // Upload each backup file to Google Drive
    foreach ($backup_file_paths as $backup_file_path) {
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => basename($backup_file_path)
        ));
        $content = file_get_contents($backup_file_path);
        $file = $service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart'
        ));
        // Print file ID if needed
        // echo 'File ID: ' . $file->getId();
    }
}

// Function to send backup notification email
function send_backup_notification_email($success) {
    $to = 'kyawebde@gmail.com'; // Your email address here
    $subject = 'Backup Notification';
    $message = $success ? 'Backup completed successfully.' : 'Backup failed.';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $attachments = array(
        ABSPATH . 'wp-content/uploads/database_backup.sql',
        ABSPATH . 'wp-content/uploads/theme_backup.zip',
        ABSPATH . 'wp-content/uploads/plugin_backup.zip'
    );

    // Send email with attachments
    $sent = wp_mail($to, $subject, $message, $headers, $attachments);

    // Check if email is sent successfully
    if ($sent) {
        error_log('Backup notification email sent successfully.');
    } else {
        error_log('Failed to send backup notification email.');
    }
}

// Hook to handle backup completion
function handle_backup_completion_notification($success) {
    // Upload backup files to Google Drive
    $backup_files = array(
        ABSPATH . 'wp-content/uploads/database_backup.sql',
        ABSPATH . 'wp-content/uploads/theme_backup.zip',
        ABSPATH . 'wp-content/uploads/plugin_backup.zip'
    );
    upload_to_google_drive($backup_files);

    // Send backup completion notification email
    send_backup_notification_email($success);
}
add_action('backup_completion_hook', 'handle_backup_completion_notification');










// Hook to handle database, theme, and plugin backup on form submission
function handle_backup_request() {
    if (isset($_POST['backup_data']) || isset($_POST['backup_theme']) || isset($_POST['backup_plugin'])) {
        if (!current_user_can('manage_options')) {
            return;
        }
        if (isset($_POST['backup_data']) && wp_verify_nonce($_POST['backup_data_nonce'], 'backup_data_action')) {
            backup_database(); // Call the function to backup database
        }
        if (isset($_POST['backup_theme']) && wp_verify_nonce($_POST['backup_theme_nonce'], 'backup_theme_action')) {
            backup_theme(); // Call the function to backup theme files
        }
        if (isset($_POST['backup_plugin']) && wp_verify_nonce($_POST['backup_plugin_nonce'], 'backup_plugin_action')) {
            backup_plugin(); // Call the function to backup plugins
        }
    }
}
add_action('admin_init', 'handle_backup_request');

// Define backup functions

function backup_database() {
    global $wpdb;

    // Get all tables in the database
    $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
    if (empty($tables)) {
        return; // No tables found
    }

    // Set the filename and path for the backup file
    $backup_file = ABSPATH . 'wp-content/uploads/database_backup.sql';

    // Open the backup file for writing
    $handle = fopen($backup_file, 'w');
    if (!$handle) {
        return; // Unable to open the file
    }

    // Iterate through each table and write its structure and data to the backup file
    foreach ($tables as $table) {
        $table_name = $table[0];

        // Retrieve table structure
        $table_structure = $wpdb->get_row("SHOW CREATE TABLE $table_name", ARRAY_N);
        fwrite($handle, "DROP TABLE IF EXISTS `$table_name`;\n");
        fwrite($handle, $table_structure[1] . ";\n\n");

        // Retrieve table data
        $rows = $wpdb->get_results("SELECT * FROM `$table_name`", ARRAY_A);
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $row_data = implode("', '", array_map('esc_sql', $row));
                fwrite($handle, "INSERT INTO `$table_name` VALUES ('$row_data');\n");
            }
        }
    }

    // Close the backup file
    fclose($handle);

    // Provide download link to the user
    $file_url = site_url('/wp-content/uploads/database_backup.sql');
    echo '<div class="notice notice-success"><p>Database backup completed successfully. <a href="' . $file_url . '" download>Download backup file</a></p></div>';
}


function backup_theme() {
    // Get the theme directory path
    $theme_path = get_stylesheet_directory();

    // Create a new ZipArchive object
    $zip = new ZipArchive();

    // Set the name and path for the backup zip file
    $zip_file = ABSPATH . 'wp-content/uploads/theme_backup.zip';

    // Open the zip file for writing
    if ($zip->open($zip_file, ZipArchive::CREATE) !== TRUE) {
        return; // Unable to open the zip file
    }

    // Add all files and directories from the theme directory to the zip file
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($theme_path),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (we only want files)
        if (!$file->isDir()) {
            // Get the relative path of the file from the theme directory
            $file_path = $file->getRealPath();
            $relative_path = substr($file_path, strlen($theme_path) + 1);

            // Add the file to the zip archive
            $zip->addFile($file_path, $relative_path);
        }
    }

    // Close the zip file
    $zip->close();

    // Provide download link to the user
    $file_url = site_url('/wp-content/uploads/theme_backup.zip');
    echo '<div class="notice notice-success"><p>Theme backup completed successfully. <a href="' . $file_url . '" download>Download backup file</a></p></div>';
}


function backup_plugin() {
    // Get the plugin directory path
    $plugin_path = WP_PLUGIN_DIR;

    // Create a new ZipArchive object
    $zip = new ZipArchive();

    // Set the name and path for the backup zip file
    $zip_file = ABSPATH . 'wp-content/uploads/plugin_backup.zip';

    // Open the zip file for writing
    if ($zip->open($zip_file, ZipArchive::CREATE) !== TRUE) {
        return; // Unable to open the zip file
    }

    // Add all files and directories from the plugin directory to the zip file
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($plugin_path),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (we only want files)
        if (!$file->isDir()) {
            // Get the relative path of the file from the plugin directory
            $file_path = $file->getRealPath();
            $relative_path = substr($file_path, strlen($plugin_path) + 1);

            // Add the file to the zip archive
            $zip->addFile($file_path, $relative_path);
        }
    }

    // Close the zip file
    $zip->close();

    // Provide download link to the user
    $file_url = site_url('/wp-content/uploads/plugin_backup.zip');
    echo '<div class="notice notice-success"><p>Plugin backup completed successfully. <a href="' . $file_url . '" download>Download backup file</a></p></div>';
}


?>