<?php
//-- Get all ShufflePuzzles from Database
//--------------------------------------
function get_spbars(){
	global $wpdb;
	$disableRow = '';
	$num = 1;
	$table_name = $wpdb->prefix . "shufflepuzzle";
	$msp_data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id");
	foreach ($msp_data as $data) {
		if ($data->active == '1') {
			$active = 'class="sp_button sp_button_deactive dashicons-before"';
            $msp_activate = 'msp_deactivate';
			//$disableRow='style="background-color: rgba(34, 220, 29, 0.21)"';
			$disableRow = '';
		} else {
			if ($data->active == '0') {
				$active = 'class="sp_button sp_button_active dashicons-before"';
                $msp_activate = 'msp_activate';
				$disableRow = 'style="background-color: #ebc3c3"';
			}
		}
		echo '<tr class="msp_tr1" '.$disableRow.'>
			<td style="width: 60px;text-align:center" >'.$num.'</td>
			<td class="solid" style="width: 300px;text-align:center;padding: 0;" valign="middle">
				<input readonly class="mainpage" '.$disableRow.' type="text" onClick="this.setSelectionRange(0, this.value.length)" name="sp-shortcode" value="[sp name=\''. $data->option_name.'\']">
			</td>
			<td style="width: 100px;text-align:center;">
				<a href="?page=editpuzzle&edit='.$data->option_name.'" class="sp_button sp_button_edit dashicons-before"/></a>
				
				<form class="sp_form" method="post" action="?page=shufflepuzzle" name="form1">
                    <input id="'.$msp_activate.'" name="'.$msp_activate.'" type="hidden" value="'.$data->id.'">
                    <button type="submit" '.$active.'></button>
                </form>
                
				<form class="sp_form" method="post" action="?page=shufflepuzzle" name="form1">
                    <input id="msp_delete" name="msp_delete" type="hidden" value="'.$data->option_name.'">
                    <button type="submit" class="sp_button sp_button_delete dashicons-before"></button>
                </form>
			</td>
		</tr>';
		$num++;
	}
	?>
	   <form method="post" action="?page=shufflepuzzle">
			<tr class="msp_tr2"> 
				<td><?php echo ($num); ?> </td>
				<td>
                    <div class="sp_b_h">
                        <span>Name</span>
                        <input type="text" id="msp_option_name" name="msp_option_name" placeholder="Input the name" size="15" />
                    </div>
                    <div class="sp_b_h">
                        <span>Parrent</span>
                        <label for="sp_type"></label>
                        <select id="sp_type" name="sp_type" style="border-color: #aaa;vertical-align: bottom;">
                            <option value="0">Default</option>
                            <option value="1">Message box</option>
                            <option value="2">Timer</option>
                            <option value="3">Counter</option>
                        </select>
                    </div>
				</td>
				<td><input type="submit" class="button-primary msp_addbutton" disabled="disabled" value="Add new shuffle puzzle" /></td>
			</tr>
		</form>
	<?php
}

//-- Add ShufflePuzzle
//-------------------
function add_shufflepuzzle(){
?>
<div id="msp_addwrap">
	<h2><?php _e('Shuffle Puzzle <span>(ver '.msp_get_version().')</span>','msp'); ?></h2>
	<?php
	//ShufflePuzzle Functions
	//----------------------
	if (!empty($_POST['msp_option_name'])) {
		$option = $_POST['msp_option_name'];
		$sp_type = $_POST['sp_type'];
		if (!get_option($_POST['msp_option_name'])) {
			if ($option){
				$option = preg_replace('/[^a-z0-9\s_]/i', '', $option);
				$option = str_replace(" ", "_", $option);
				global $wpdb;
				$table_name = $wpdb->prefix . "shufflepuzzle";
				$options = get_option($option);
				if($options){
					$msp_message = 'Unable to Add Shuffle Puzzle, please try another name';
					$class = 'warning';
				}else{
					$sql = "INSERT INTO " . $table_name . " values ('','".$option."','1');";
					if ($wpdb->query( $sql )){
						//add_option($option, msp_main($option));
                        if ($sp_type == 0) {
                            add_option($option, str_replace('%sp_name%', $option, msp_main($option)));
                        } else if ($sp_type == 1) {
                            add_option($option, str_replace('%sp_name%', $option, msp_with_message_box($option)));
                        } else if ($sp_type == 2) {
                            add_option($option, str_replace('%sp_name%', $option, msp_with_timer($option)));
                        } else if ($sp_type == 3) {
                            add_option($option, str_replace('%sp_name%', $option, msp_with_counter($option)));
                        }
                        
                        $msp_message = '"'.$option.'" was successfully added';
						$class = '';
					}else{
						$msp_message = 'Unable to Add Shuffle Puzzle';
						$class = 'warning';
					}
				};
			} else {
				$msp_message = 'Unable to Add Shuffle Puzzle';
				$class = 'warning';
			}
		} else {
			$msp_message = 'Unable to Add Shuffle Puzzle, please try another name';
			$class = 'warning';
		}
		_e('<div id="msp_message" class="msp_updated msp_add '.$class.'">'.$msp_message.'</div>','msp');
	}

	if (!empty($_POST["msp_delete"])) {
        //if(isset($_GET['msp_delete'])){
		$option = $_POST['msp_delete'];
		delete_option($option);
		global $wpdb;
		$table_name = $wpdb->prefix . "shufflepuzzle";
		$sql = "DELETE FROM " . $table_name . " WHERE option_name='".$option."';";
		$wpdb->query( $sql );
		echo '<div class="msp_updated msp_add" id="msp_message">"'.$option.'" is deleted</div>';
	}

	if(!empty($_POST['msp_deactivate'])){
		$id=$_POST['msp_deactivate'];
		global $wpdb;
		$table_name = $wpdb->prefix . "shufflepuzzle";
		$sql = "UPDATE " . $table_name . " SET active='0' WHERE id='".$id."';";
		$wpdb->query( $sql );
		echo '<div class="msp_updated msp_add" id="msp_message">Puzzle is deactivated</div>';
	}

	if(!empty($_POST['msp_activate'])){
		$id = $_POST['msp_activate'];
		global $wpdb;
		$table_name = $wpdb->prefix . "shufflepuzzle";
		$sql = "UPDATE " . $table_name . " SET active='1' WHERE id='".$id."';";
		$wpdb->query( $sql );
		echo "<div class=\"msp_updated msp_add\" id=\"msp_message\">Puzzle is activated</div>";
	}
	?>
	<table cellspacing="0" class="msp_struct">
		<thead>
			<tr>
				<th colspan="1"></th>
				<th colspan="1">Table of Puzzles</th>
				<th colspan="1"></th>
			</tr>
			<tr class="msp_tr">
				<td>ID</td>
				<td class="solid">Shortcode</td>
				<td>Actions</td>
			</tr>
		</thead>
		<tbody>
			<?php
				get_spbars();
			?>
		</tbody>
	</table>
</div>
<?php
}
?>