<?php
/**
 * Plugin Name: WP CSV TO DB
 * Plugin URI: https://www.tipsandtricks-hq.com/wp-csv-to-database-plugin-import-excel-file-content-into-wordpress-database-2116
 * Description: Import CSV file content directly into your WordPress database table.
 * Version: 2.4
 * Author: Tips and Tricks HQ, josh401
 * Author URI: https://www.tipsandtricks-hq.com
 * License: GPL2
*/

class wp_csv_to_db {

	// Setup options variables
	protected $option_name = 'wp_csv_to_db';  // Name of the options array
	protected $data = array(  // Default options values
		'jq_theme' => 'smoothness'
	);
	
	
	public function __construct() {
		
		// Check if is admin
		// We can later update this to include other user roles
		if (is_admin()) {
                        add_action( 'plugins_loaded', array( $this, 'wp_csv_to_db_plugins_loaded' ));//Handles tasks that need to be done at plugins loaded stage.
			add_action( 'admin_menu', array( $this, 'wp_csv_to_db_register' ));  // Create admin menu page
			add_action( 'admin_init', array( $this, 'wp_csv_to_db_settings' ) ); // Create settings
			register_activation_hook( __FILE__ , array($this, 'wp_csv_to_db_activate')); // Add settings on plugin activation
		}
	}
	
        public function wp_csv_to_db_plugins_loaded(){
            $this->handle_csv_export_action();
        }
        
	public function wp_csv_to_db_activate() {
		update_option($this->option_name, $this->data);
	}
	
	public function wp_csv_to_db_register(){
    	$wp_csv_to_db_page = add_submenu_page( 'options-general.php', __('WP CSV/DB','wp_csv_to_db'), __('WP CSV/DB','wp_csv_to_db'), 'manage_options', 'wp_csv_to_db_menu_page', array( $this, 'wp_csv_to_db_menu_page' )); // Add submenu page to "Settings" link in WP
		add_action( 'admin_print_scripts-' . $wp_csv_to_db_page, array( $this, 'wp_csv_to_db_admin_scripts' ) );  // Load our admin page scripts (our page only)
		add_action( 'admin_print_styles-' . $wp_csv_to_db_page, array( $this, 'wp_csv_to_db_admin_styles' ) );  // Load our admin page stylesheet (our page only)
	}
	
	public function wp_csv_to_db_settings() {
		register_setting('wp_csv_to_db_options', $this->option_name, array($this, 'wp_csv_to_db_validate'));
	}
	
	public function wp_csv_to_db_validate($input) {
		$valid = array();
		/*
		$valid['jq_theme'] = sanitize_text_field($input['jq_theme']);
		if (strlen($valid['jq_theme']) == 0) {
			add_settings_error(
					'jq_theme',                      // Setting title
					'jq_theme_texterror',            // Error ID
					'Please select a jQuery theme.', // Error message
					'error'                          // Type of message
			);
	
			// Set it to the default value
			$valid['jq_theme'] = $this->data['jq_theme'];
		}
		*/
		$valid['jq_theme'] = $input['jq_theme'];

    	return $valid;
	}
	
	public function wp_csv_to_db_admin_scripts() {
		wp_enqueue_script('media-upload');  // For WP media uploader
		wp_enqueue_script('thickbox');  // For WP media uploader
		wp_enqueue_script('jquery-ui-tabs');  // For admin panel page tabs
		wp_enqueue_script('jquery-ui-dialog');  // For admin panel popup alerts
		
		wp_enqueue_script( 'wp_csv_to_db', plugins_url( '/js/admin_page.js', __FILE__ ), array('jquery') );  // Apply admin page scripts
		wp_localize_script( 'wp_csv_to_db', 'wp_csv_to_db_pass_js_vars', array( 'ajax_image' => plugin_dir_url( __FILE__ ).'images/loading.gif', 'ajaxurl' => admin_url('admin-ajax.php') ) );
	}
	
	public function wp_csv_to_db_admin_styles() {
		wp_enqueue_style('thickbox');  // For WP media uploader
		wp_enqueue_style('sdm_admin_styles', plugins_url( '/css/admin_page.css', __FILE__ ));  // Apply admin page styles
		
		// Get option for jQuery theme
		$options = get_option($this->option_name);
		$select_theme = isset($options['jq_theme']) ? $options['jq_theme'] : 'smoothness';
		?><link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/<?php echo $select_theme; ?>/jquery-ui.css"><?php  // For jquery ui styling - Direct from jquery
	}

        public function handle_csv_export_action(){
            	if ((isset($_POST['export_to_csv_button'])) && (!empty($_POST['table_select']))) {
                    if(!current_user_can('manage_options')){
                        wp_die('Error! Only site admin can perform this operation');
                    }

                    $this->CSV_GENERATE($_POST['table_select']);
		}
        }
        
	// Helper function for .csv file exportation
	public function CSV_GENERATE($getTable) {
		ob_end_clean();
		global $wpdb;
		$field='';
		$getField ='';
	
		if($getTable){
			$result = $wpdb->get_results("SELECT * FROM $getTable");
			$requestedTable = mysql_query("SELECT * FROM ".$getTable);
	
			$fieldsCount = mysql_num_fields($requestedTable);
	
			for($i=0; $i<$fieldsCount; $i++){
				$field = mysql_fetch_field($requestedTable);
				$field = (object) $field;         
				$getField .= $field->name.',';
			}
	
			$sub = substr_replace($getField, '', -1);
			$fields = $sub; // Get fields names
			$each_field = explode(',', $sub);
			$csv_file_name = $getTable.'_'.date('Ymd_His').'.csv'; 
	
			// Get fields values with last comma excluded
			foreach($result as $row){
				for($j = 0; $j < $fieldsCount; $j++){
					if($j == 0) $fields .= "\n"; // Force new line if loop complete
					$value = str_replace(array("\n", "\n\r", "\r\n", "\r"), "\t", $row->$each_field[$j]); // Replace new line with tab
					$value = str_getcsv ( $value , ",", "\"" , "\\"); // SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
					$fields .= $value[0].','; // Separate fields with comma
				}
				$fields = substr_replace($fields, '', -1); // Remove extra space at end of string
			}
	
			//header("Content-type: text/x-csv");
			header("Content-type: text/csv");
			header("Content-Transfer-Encoding: binary");
			header("Content-Disposition: attachment; filename=".$csv_file_name);
			header("Content-type: application/x-msdownload");
			header("Pragma: no-cache");
			header("Expires: 0"); 
	
			echo $fields; 
			exit;
		}
	}
	
	public function wp_csv_to_db_menu_page() {

                if(!current_user_can('manage_options')){
                    wp_die('Error! Only site admin can perform this operation');
                }
            
		// Set variables		
		global $wpdb;
		$error_message = '';
		$success_message = '';
		$message_info_style = '';
		
		//
		// If Delete Table button was pressed
		if(!empty($_POST['delete_db_button_hidden'])) {
			
			$del_qry = 'DROP TABLE '.$_POST['table_select'];
			$del_qry_success = $wpdb->query($del_qry);
			
			if($del_qry_success) {
				$success_message .= __('Congratulations!  The database table has been deleted successfully.','wp_csv_to_db');
			}
			else {
				$error_message .= '* '.__('Error deleting table. Please verify the table exists.','wp_csv_to_db');
			}
		}
		
		if ((isset($_POST['export_to_csv_button'])) && (empty($_POST['table_select']))) {
			$error_message .= '* '.__('No Database Table was selected to export. Please select a Database Table for exportation.','wp_csv_to_db').'<br />';
		}
		
		// If button is pressed to "Import to DB"
		if (isset($_POST['execute_button'])) {
			
			// If the "Select Table" input field is empty
			if(empty($_POST['table_select'])) {
				$error_message .= '* '.__('No Database Table was selected. Please select a Database Table.','wp_csv_to_db').'<br />';
			}
			// If the "Select Input File" input field is empty
			if(empty($_POST['csv_file'])) {
				$error_message .= '* '.__('No Input File was selected. Please enter an Input File.','wp_csv_to_db').'<br />';
			}
			// Check that "Input File" has proper .csv file extension
			$ext = pathinfo($_POST['csv_file'], PATHINFO_EXTENSION);
			if($ext !== 'csv') {
				$error_message .= '* '.__('The Input File does not contain the .csv file extension. Please choose a valid .csv file.','wp_csv_to_db');
			}
			
			// If all fields are input; and file is correct .csv format; continue
			if(!empty($_POST['table_select']) && !empty($_POST['csv_file']) && ($ext === 'csv')) {
				
				// If "disable auto_inc" is checked.. we need to skip the first column of the returned array (or the column will be duplicated)
				if(isset($_POST['remove_autoinc_column'])) {
					$db_cols = $wpdb->get_col( "DESC " . $_POST['table_select'], 0 );  
					unset($db_cols[0]);  // Remove first element of array (auto increment column)
				} 
				// Else we just grab all columns
				else {
					$db_cols = $wpdb->get_col( "DESC " . $_POST['table_select'], 0 );  // Array of db column names
				}
				// Get the number of columns from the hidden input field (re-auto-populated via jquery)
				$numColumns = $_POST['num_cols'];
				
				// Open the .csv file and get it's contents
				$myCSV = $_POST['csv_file'];
				$path = parse_url($myCSV, PHP_URL_PATH);
				$myCSV = $_SERVER['DOCUMENT_ROOT'] . $path;

				if(( $fh = @fopen($myCSV, 'r')) !== false) {
					
					// Set variables
					$values = array();
					$too_many = '';  // Used to alert users if columns do not match
					
					while(( $row = fgetcsv($fh)) !== false) {  // Get file contents and set up row array
						if(count($row) == $numColumns) {  // If .csv column count matches db column count
                                                        $row = array_map(function($v) { return esc_sql($v) ;}, $row) ;
							$values[] = '("' . implode('", "', $row) . '")';  // Each new line of .csv file becomes an array
						}
					}
					
					// If user elects to input a starting row for the .csv file
					if(isset($_POST['sel_start_row']) && (!empty($_POST['sel_start_row']))) {
						
						// Get row number from user
						$num_var = $_POST['sel_start_row'] - 1;  // Subtract one to make counting easy on the non-techie folk!  (1 is actually 0 in binary)
						
						// If user input number exceeds available .csv rows
						if($num_var > count($values)) {
							$error_message .= '* '.__('Starting Row value exceeds the number of entries being updated to the database from the .csv file.','wp_csv_to_db').'<br />';
							$too_many = 'true';  // set alert variable
						}
						// Else splice array and remove number (rows) user selected
						else {
							$values = array_slice($values, $num_var);
						}
					}
					
					// If there are no rows in the .csv file AND the user DID NOT input more rows than available from the .csv file
					if( empty( $values ) && ($too_many !== 'true')) {
						$error_message .= '* '.__('Columns do not match.','wp_csv_to_db').'<br />';
						$error_message .= '* '.__('The number of columns in the database for this table does not match the number of columns attempting to be imported from the .csv file.','wp_csv_to_db').'<br />';
						$error_message .= '* '.__('Please verify the number of columns attempting to be imported in the "Select Input File" exactly matches the number of columns displayed in the "Table Preview".','wp_csv_to_db').'<br />';
					}
					else {
						// If the user DID NOT input more rows than are available from the .csv file
						if($too_many !== 'true') {
							
							$db_query_update = '';
							$db_query_insert = '';
								
							// Format $db_cols to a string
							$db_cols_implode = implode(',', $db_cols);
								
							// Format $values to a string
							$values_implode = implode(',', $values);
							
							
							// If "Update DB Rows" was checked
							if (isset($_POST['update_db'])) {
								
								// Setup sql 'on duplicate update' loop
								$updateOnDuplicate = ' ON DUPLICATE KEY UPDATE ';
								foreach ($db_cols as $db_col) {
									$updateOnDuplicate .= "$db_col=VALUES($db_col),";
								}
								$updateOnDuplicate = rtrim($updateOnDuplicate, ',');
								
								
								$sql = 'INSERT INTO '.$_POST['table_select'] . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode.$updateOnDuplicate;
								$db_query_update = $wpdb->query($sql);
							}
							else {
								$sql = 'INSERT INTO '.$_POST['table_select'] . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode;
								$db_query_insert = $wpdb->query($sql);
							}
							
							// If db db_query_update is successful
							if ($db_query_update) {
								$success_message = __('Congratulations!  The database has been updated successfully.','wp_csv_to_db');
							}
							// If db db_query_insert is successful
							elseif ($db_query_insert) {
								$success_message = __('Congratulations!  The database has been updated successfully.','wp_csv_to_db');
								$success_message .= '<br /><strong>'.count($values).'</strong> '.__('record(s) were inserted into the', 'wp_csv_to_db').' <strong>'.$_POST['table_select'].'</strong> '.__('database table.','wp_csv_to_db');
							}
							// If db db_query_insert is successful AND there were no rows to udpate
							elseif( ($db_query_update === 0) && ($db_query_insert === '') ) {
								$message_info_style .= '* '.__('There were no rows to update. All .csv values already exist in the database.','wp_csv_to_db').'<br />';
							}
							else {
								$error_message .= '* '.__('There was a problem with the database query.','wp_csv_to_db').'<br />';
								$error_message .= '* '.__('A duplicate entry was found in the database for a .csv file entry.','wp_csv_to_db').'<br />';
								$error_message .= '* '.__('If necessary; please use the option below to "Update Database Rows".','wp_csv_to_db').'<br />';
							}
						}
					}
				}
				else {
					$error_message .= '* '.__('No valid .csv file was found at the specified url. Please check the "Select Input File" field and ensure it points to a valid .csv file.','wp_csv_to_db').'<br />';
				}
			}
		}
		
		// If there is a message - info-style
		if(!empty($message_info_style)) {
			echo '<div class="info_message_dismiss">';
			echo $message_info_style;
			echo '<br /><em>('.__('click to dismiss','wp_csv_to_db').')</em>';
			echo '</div>';
		}
		
		// If there is an error message	
		if(!empty($error_message)) {
			echo '<div class="error_message">';
			echo $error_message;
			echo '<br /><em>('.__('click to dismiss','wp_csv_to_db').')</em>';
			echo '</div>';
		}
		
		// If there is a success message
		if(!empty($success_message)) {
			echo '<div class="success_message">';
			echo $success_message;
			echo '<br /><em>('.__('click to dismiss','wp_csv_to_db').')</em>';
			echo '</div>';
		}
		?>
		<div class="wrap">
        
            <h2><?php _e('WordPress CSV to Database Options','wp_csv_to_db'); ?></h2>
            
            <p>This plugin allows you to insert CSV file data into your WordPress database table. You can also export the content of a database using this plugin.</p>
			<p><a href="https://www.tipsandtricks-hq.com/wp-csv-to-database-plugin-import-excel-file-content-into-wordpress-database-2116" target="_blank">Visit the plugin page</a> for more details and usage instruction.</p>
            
            <div id="tabs">
                <ul>
    				<li><a href="#tabs-1"><?php _e('Settings','wp_csv_to_db'); ?></a></li>
    				<li><a href="#tabs-2"><?php _e('Guide','wp_csv_to_db'); ?></a></li>
    				<li><a href="#tabs-3"><?php _e('Preview','wp_csv_to_db'); ?></a></li>
    				<li><a href="#tabs-4"><?php _e('Options','wp_csv_to_db'); ?></a></li>
                </ul>
                
                <div id="tabs-1">
                
        			<form id="wp_csv_to_db_form" method="post" action="">
                    <table class="form-table"> 
                        
                        <tr valign="top"><th scope="row"><?php _e('Select Database Table:','wp_csv_to_db'); ?></th>
                            <td>
                                <select id="table_select" name="table_select" value="">
                                <option name="" value=""></option>
                                
                                <?php  // Get all db table names
                                global $wpdb;
                                $sql = "SHOW TABLES";
                                $results = $wpdb->get_results($sql);
                                $repop_table = isset($_POST['table_select']) ? $_POST['table_select'] : null;
                                
                                foreach($results as $index => $value) {
                                    foreach($value as $tableName) {
                                        ?><option name="<?php echo $tableName ?>" value="<?php echo $tableName ?>" <?php if($repop_table === $tableName) { echo 'selected="selected"'; } ?>><?php echo $tableName ?></option><?php
                                    }
                                }
                                ?>
                            </select>
                            </td> 
                        </tr>
                        <tr valign="top"><th scope="row"><?php _e('Select Input File:','wp_csv_to_db'); ?></th>
                            <td>
                                <?php $repop_file = isset($_POST['csv_file']) ? $_POST['csv_file'] : null; ?>
                                <?php $repop_csv_cols = isset($_POST['num_cols_csv_file']) ? $_POST['num_cols_csv_file'] : '0'; ?>
                                <input id="csv_file" name="csv_file"  type="text" size="70" value="<?php echo $repop_file; ?>" />
                                <input id="csv_file_button" type="button" value="Upload" />
                                <input id="num_cols" name="num_cols" type="hidden" value="" />
                                <input id="num_cols_csv_file" name="num_cols_csv_file" type="hidden" value="" />
                                <br><?php _e('File must end with a .csv extension.','wp_csv_to_db'); ?>
                                <br><?php _e('Number of .csv file Columns:','wp_csv_to_db'); echo ' '; ?><span id="return_csv_col_count"><?php echo $repop_csv_cols; ?></span>
                            </td>
                        </tr>
                        <tr valign="top"><th scope="row"><?php _e('Select Starting Row:','wp_csv_to_db'); ?></th>
                            <td>
                            	<?php $repop_row = isset($_POST['sel_start_row']) ? $_POST['sel_start_row'] : null; ?>
                                <input id="sel_start_row" name="sel_start_row" type="text" size="10" value="<?php echo $repop_row; ?>" />
                                <br><?php _e('Defaults to row 1 (top row) of .csv file.','wp_csv_to_db'); ?>
                            </td>
                        </tr>
                        <tr valign="top"><th scope="row"><?php _e('Disable "auto_increment" Column:','wp_csv_to_db'); ?></th>
                            <td>
                                <input id="remove_autoinc_column" name="remove_autoinc_column" type="checkbox" />
                                <br><?php _e('Bypasses the "auto_increment" column;','wp_csv_to_db'); ?>
                                <br><?php _e('This will reduce (for the purposes of importation) the number of DB columns by "1".','wp_csv_to_db'); ?>
                            </td>
                        </tr>
                        <tr valign="top"><th scope="row"><?php _e('Update Database Rows:','wp_csv_to_db'); ?></th>
                            <td>
                                <input id="update_db" name="update_db" type="checkbox" />
                                <br><?php _e('Will update exisiting database rows when a duplicated primary key is encountered.','wp_csv_to_db'); ?>
                                <br><?php _e('Defaults to all rows inserted as new rows.','wp_csv_to_db'); ?>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input id="execute_button" name="execute_button" type="submit" class="button-primary" value="<?php _e('Import to DB', 'wp_csv_to_db') ?>" />
                        <input id="export_to_csv_button" name="export_to_csv_button" type="submit" class="button-secondary" value="<?php _e('Export to CSV', 'wp_csv_to_db') ?>" />
                        <input id="delete_db_button" name="delete_db_button" type="button" class="button-secondary" value="<?php _e('Delete Table', 'wp_csv_to_db') ?>" />
                        <input type="hidden" id="delete_db_button_hidden" name="delete_db_button_hidden" value="" />
                    </p>
                    </form>
                </div> <!-- End tab 1 -->
                <div id="tabs-2">
                	<?php _e('Step 1 (Select Database Table):','wp_csv_to_db'); ?>
                    <ul>
                        <li><?php _e('All WP database tables will be queried and listed in the dropdown box.','wp_csv_to_db'); ?></li>
                        <li><?php _e('Select the table name which will be used for the query.','wp_csv_to_db'); ?></li>
                        <li><?php _e('Once the table is selected; the "Table Preview" will display the structure of the table.','wp_csv_to_db'); ?></li>
                        <li><?php _e('By structure, this means all column names will be listed in the order they appear in the database.','wp_csv_to_db'); ?></li>
                        <li><?php _e('This can be used to match the .csv file prior to execution; and verify it contains the same structure of columns.','wp_csv_to_db'); ?></li>
                    </ul>
                    <br /><br />
                    <?php _e('Step 2 (Select Input File):','wp_csv_to_db'); ?>
                    <ul>
                        <li><?php _e('The option will be used to locate the file to be used for execution.','wp_csv_to_db'); ?></li>
                        <li><?php _e('A direct url to a .csv file may be entered into the text field.','wp_csv_to_db'); ?></li>
                        <li><?php _e('Alternatively, the "Upload" button may be used to initiate the WordPress uploader and manager.','wp_csv_to_db'); ?></li>
                        <li><?php _e('From here, the file can be uploaded from a computer or selected from the media library.','wp_csv_to_db'); ?></li>
                        <li><?php _e('The "Number of .csv file Columns" will populate when the Input File field contains a valid .csv file.','wp_csv_to_db'); ?></li>
                    </ul>
                    <br /><br />
                    <?php _e('Step 3 (Select Starting Row):','wp_csv_to_db'); ?>
                    <ul>
                        <li><?php _e('The .csv file will contain rows, which get converted to database table entries.','wp_csv_to_db'); ?></li>
                        <li><?php _e('This option will allow customization of the starting row of the .csv file to be used during the importation.','wp_csv_to_db'); ?></li>
                        <li><?php _e('Row 1 is always the top row of the .csv file.','wp_csv_to_db'); ?></li>
                        <li><?php _e('For example: If the .csv file contains column headers (column names), it would most likely be desirable to start with row 2 of the .csv file; preventing importation of the column names row.','wp_csv_to_db'); ?></li>
                    </ul>
                    <br /><br />
                    <?php _e('Step 4 (Disable "auto_increment" Column):','wp_csv_to_db'); ?>
                    <ul>
                        <li><?php _e('This option will only become available when a database table is selected which contains an auto-incremented column.','wp_csv_to_db'); ?></li>
                        <li><?php _e('If importing a file which already has an auto-incremented column... this setting most likely will not be needed.','wp_csv_to_db'); ?></li>
                    </ul>
                    <br /><br />
                    <?php _e('Step 5 (Update Database Rows):','wp_csv_to_db'); ?>
                    <ul>
                        <li><?php _e('By default, the plugin will add each .csv row as a new entry in the database.','wp_csv_to_db'); ?></li>
                        <li><?php _e('If the database uses a primary key (auto-increment) column, it will assign each new row with a new primary key value.','wp_csv_to_db'); ?></li>
                        <li><?php _e('This is typically how entries are added to a database.','wp_csv_to_db'); ?></li>
                        <li><?php _e('However; if a duplicate primary key is encountered, the import will stop at that exact point (and fail).','wp_csv_to_db'); ?></li>
                        <li><?php _e('If this option is checked, the import process will "update" this row rather than adding a new row.','wp_csv_to_db'); ?></li>
                    </ul>
                </div> <!-- End tab 2 -->
                <div id="tabs-3">
                	<?php _e('The Table Preview may be useful when comparing the .csv file against the database table structure.','wp_csv_to_db'); ?>
                    <ul>
                        <li><?php _e('The Table Preview will display all table column names, in the order they appear in the database.','wp_csv_to_db'); ?></li>
                        <li><?php _e('If a table column is set to "auto_increment"; this column will appear in the color "red".','wp_csv_to_db'); ?></li>
                        <li><?php _e('This plugin assumes any "auto_increment" column will appear first in the database table. Please double-check the order of the column names to ensure they match.','wp_csv_to_db'); ?></li>
                        <li><?php _e('If the option is checked to "Disable auto-increment Column"; the number of database columns will be decreased by "1"; for the purposes of importing the .csv file.','wp_csv_to_db'); ?></li>
                        <li><?php _e('Ultimately... the number of database columns MUST match the number of columns being imported from the .csv file.','wp_csv_to_db'); ?></li>
                    </ul>
                </div> <!-- End tab 3 -->
                
                <div id="tabs-4">
                	<?php $options = get_option($this->option_name); ?>
                	<?php _e('Options Settings:','wp_csv_to_db'); ?>
                    
                    <form method="post" action="options.php">
						<?php settings_fields('wp_csv_to_db_options'); ?>
                        <table class="form-table">
                            <tr valign="top"><th scope="row"><?php _e('jQuery Theme','wp_csv_to_db'); ?></th>
                                <td>
                                	<!-- <input type="text" name="<?php //echo $this->option_name?>[jq_theme]" value="<?php //echo $options['jq_theme']; ?>" /> -->
                                	<select name="<?php echo $this->option_name?>[jq_theme]"/>
                                    	<?php
                        				$jquery_themes = array('base','black-tie','blitzer','cupertino','dark-hive','dot-luv','eggplant','excite-bike','flick','hot-sneaks','humanity','le-frog','mint-choc','overcast','pepper-grinder','redmond','smoothness','south-street','start','sunny','swanky-purse','trontastic','ui-darkness','ui-lightness','vader');
										
										foreach($jquery_themes as $jquery_theme) {
											$selected = ($options['jq_theme']==$jquery_theme) ? 'selected="selected"' : '';
											echo "<option value='$jquery_theme' $selected>$jquery_theme</option>";
										}
										?>
                                	</select>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>
                    </form>
                </div> <!-- End tab 4 -->
            </div> <!-- End #tabs -->
        </div> <!-- End page wrap -->
        
        <h3><?php _e('Table Preview:','wp_csv_to_db'); ?><input id="repop_table_ajax" name="repop_table_ajax" value="<?php _e('Reload Table Preview','wp_csv_to_db'); ?>" type="button" style="margin-left:20px;" /></h3>
            
        <div id="table_preview">
        </div>
        
        <p><?php _e('After selecting a database table from the dropdown above; the table column names will be shown.','wp_csv_to_db'); ?>
        <br><?php _e('This may be used as a reference when verifying the .csv file is formatted properly.','wp_csv_to_db'); ?>
        <br><?php _e('If an "auto-increment" column exists; it will be rendered in the color "red".','wp_csv_to_db'); ?>
        
        <!-- Delete table warning - jquery dialog -->
        <div id="dialog-confirm" title="<?php _e('Delete database table?','wp_csv_to_db'); ?>">
        	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php _e('This table will be permanently deleted and cannot be recovered. Proceed?','wp_csv_to_db'); ?></p>
        </div>
        
        <!-- Alert invalid .csv file - jquery dialog -->
        <div id="dialog_csv_file" title="<?php _e('Invalid File Extension','wp_csv_to_db'); ?>" style="display:none;">
        	<p><?php _e('This is not a valid .csv file extension.','wp_csv_to_db'); ?></p>
        </div>
        
        <!-- Alert select db table - jquery dialog -->
        <div id="dialog_select_db" title="<?php _e('Database Table not Selected','wp_csv_to_db'); ?>" style="display:none;">
        	<p><?php _e('First, please select a database table from the dropdown list.','wp_csv_to_db'); ?></p>
        </div>
        <?php
	}
	
}
$wp_csv_to_db = new wp_csv_to_db();

//  Ajax call for showing table column names
add_action( 'wp_ajax_wp_csv_to_db_get_columns', 'wp_csv_to_db_get_columns_callback' );
function wp_csv_to_db_get_columns_callback() {
	
	// Set variables
	global $wpdb;
	$sel_val = isset($_POST['sel_val']) ? $_POST['sel_val'] : null;
	$disable_autoinc = isset($_POST['disable_autoinc']) ? $_POST['disable_autoinc'] : 'false';
	$enable_auto_inc_option = 'false';
	$content = '';
	
	// Ran when the table name is changed from the dropdown
	if ($sel_val) {
		
		// Get table name
		$table_name = $sel_val;
		
		// Setup sql query to get all column names based on table name
		$sql = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'.$wpdb->dbname.'" AND TABLE_NAME ="'.$table_name.'" AND EXTRA like "%auto_increment%"';
		
		// Execute Query
		$run_qry = $wpdb->get_results($sql);
		
		//
		// Begin response content
		$content .= '<table id="ajax_table"><tr>';
		
		// If the db query contains an auto_increment column
		if((isset($run_qry[0]->EXTRA)) && (isset($run_qry[0]->COLUMN_NAME))) {
			//$content .= 'auto: '.$run_qry[0]->EXTRA.'<br />';
			//$content .= 'column: '.$run_qry[0]->COLUMN_NAME.'<br />';
			
			// If user DID NOT check 'disable_autoinc'; we need to add that column back with unique formatting 
			if($disable_autoinc === 'false') {
				$content .= '<td class="auto_inc"><strong>'.$run_qry[0]->COLUMN_NAME.'</strong></td>';
			}
			
			// Get all column names from database for selected table
			$column_names = $wpdb->get_col( 'DESC ' . $table_name, 0 );
			$counter = 0;
			
			//
			// IMPORTANT - If the db results contain an auto_increment; we remove the first column below; because we already added it above.
			foreach ( $column_names as $column_name ) {
				if( $counter++ < 1) continue;  // Skip first iteration since 'auto_increment' table data cell will be duplicated
			    $content .= '<td><strong>'.$column_name.'</strong></td>';
			}
		}
		// Else get all column names from database (unfiltered)
		else {
			$column_names = $wpdb->get_col( 'DESC ' . $table_name, 0 );
			foreach ( $column_names as $column_name ) {
			  $content .= '<td><strong>'.$column_name.'</strong></td>';
			}
		}
		$content .= '</tr></table><br />';
		$content .= __('Number of Database Columns:','wp_csv_to_db').' <span id="column_count"><strong>'.count($column_names).'</strong></span><br />';
		
		// If there is an auto_increment column in the returned results
		if((isset($run_qry[0]->EXTRA)) && (isset($run_qry[0]->COLUMN_NAME))) {
			// If user DID NOT click the auto_increment checkbox
			if($disable_autoinc === 'false') {
				$content .= '<div class="warning_message">';
				$content .= __('This table contains an "auto increment" column.','wp_csv_to_db').'<br />';
				$content .= __('Please be sure to use unique values in this column from the .csv file.','wp_csv_to_db').'<br />';
				$content .= __('Alternatively, the "auto increment" column may be bypassed by clicking the checkbox above.','wp_csv_to_db').'<br />';
				$content .= '</div>';
				
				// Send additional response
				$enable_auto_inc_option = 'true';
			}
			// If the user clicked the auto_increment checkbox
			if($disable_autoinc === 'true') {
				$content .= '<div class="info_message">';
				$content .= __('This table contains an "auto increment" column that has been removed via the checkbox above.','wp_csv_to_db').'<br />';
				$content .= __('This means all new .csv entries will be given a unique "auto incremented" value when imported (typically, a numerical value).','wp_csv_to_db').'<br />';
				$content .= __('The Column Name of the removed column is','wp_csv_to_db').' <strong><em>'.$run_qry[0]->COLUMN_NAME.'</em></strong>.<br />';
				$content .= '</div>';
				
				// Send additional response 
				$enable_auto_inc_option = 'true';
			}
		}
	}
	else {
		$content = '';
		$content .= '<table id="ajax_table"><tr><td>';
		$content .= __('No Database Table Selected.','wp_csv_to_db');
		$content .= '<br />';
		$content .= __('Please select a database table from the dropdown box above.','wp_csv_to_db');
		$content .= '</td></tr></table>';
	}
	
	// Set response variable to be returned to jquery
	$response = json_encode( array( 'content' => $content, 'enable_auto_inc_option' => $enable_auto_inc_option ) );
	header( "Content-Type: application/json" );
	echo $response;
	die();
}

// Ajax call to process .csv file for column count
add_action('wp_ajax_wp_csv_to_db_get_csv_cols','wp_csv_to_db_get_csv_cols_callback');
function wp_csv_to_db_get_csv_cols_callback() {
	
	// Get file upload url
	$file_upload_url = $_POST['file_upload_url'];
	
	// Open the .csv file and get it's contents
	if(( $fh = @fopen($_POST['file_upload_url'], 'r')) !== false) {
		
		// Set variables
		$values = array();
		
		// Assign .csv rows to array
		while(( $row = fgetcsv($fh)) !== false) {  // Get file contents and set up row array
			//$values[] = '("' . implode('", "', $row) . '")';  // Each new line of .csv file becomes an array
			$rows[] = array(implode('", "', $row));
		}
		
		// Get a single array from the multi-array... and process it to count the individual columns
		$first_array_elm = reset($rows);
		$xplode_string = explode(", ", $first_array_elm[0]);
		
		// Count array entries
		$column_count = count($xplode_string);
	}
	else {
		$column_count = 'There was an error extracting data from the.csv file. Please ensure the file is a proper .csv format.';
	}
	
	// Set response variable to be returned to jquery
	$response = json_encode( array( 'column_count' => $column_count ) );
	header( "Content-Type: application/json" );
	echo $response;
	die();
}

// Add plugin settings link to plugins page
add_filter( 'plugin_action_links', 'wp_csv_to_db_plugin_action_links', 10, 4 );
function wp_csv_to_db_plugin_action_links( $links, $file ) {
	
	$plugin_file = 'wp_csv_to_db/main.php';
	if ( $file == $plugin_file ) {
		$settings_link = '<a href="' .
			admin_url( 'options-general.php?page=wp_csv_to_db_menu_page' ) . '">' .
			__( 'Settings', 'wp_csv_to_db' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

// Load plugin language localization
add_action('plugins_loaded', 'wp_csv_to_db_lang_init');
function wp_csv_to_db_lang_init() {
	load_plugin_textdomain( 'wp_csv_to_db', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
