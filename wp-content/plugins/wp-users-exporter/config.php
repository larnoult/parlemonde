<?php
// prefixo que será usado nos slugs
global $wpdb, $wp_roles;
$wpue_options = wpue_getDefaultConfig();

$wpue_config = wpue_getConfig();

// buddypress edition
if(wpue_isBP()){
	$bp_fields = wpue_bp_getProfileFields();
}
?>
<style>

.wpue-field label{ width:auto; display: inline; vertical-align: auto;}
</style>

<div class="wrap">

<h2><?php _e('WP Users Exporter Settings', 'wp-users-exporter'); ?></h2>

<form method="post">
    
    <input type="hidden" name='<?php echo WPUE_PREFIX?>action' value='save-config' />
    
    <h3 class="title"><?php _e('Who can export users?', 'wp-users-exporter')?></h3>

    <?php 
    foreach ($wp_roles->roles as $role) {
        
        if ($role['name'] != 'Administrator') {
            echo "<input value='1' type='checkbox' name='{$role['name']}' id='{$role['name']}'";
            if ($role['capabilities']['use-wp-users-exporter'])
                echo 'checked';
            echo "> <label for='{$role['name']}'>{$role['name']}</label> <br />";
        }
    }
    ?>

    <h3 class="title"><?php _e('Which users may be exported?', 'wp-users-exporter')?></h3>
    
    <table class="wp-list-table widefat fixed">
        <thead>
            <tr class="manage-column column-name desc">
                <th><?php _e('User Role', 'wp-users-exporter'); ?></th>
                <th><?php _e('Display as', 'wp-users-exporter'); ?></th>
            </tr>
            
        </thead>
        
        <tbody>
        <?php foreach ($wpue_options->roles as $k => $n): ?>
            <tr class="alternate">
                <td>
                    <label><input type="checkbox" name='roles[<?php echo $k?>]' value='<?php echo $k?>' <?php if(isset($wpue_config->roles[$k])) echo 'checked="checked"';?>/> <?php echo $k?> </label> 
                </td>
                <td>
                    <input type="text" name="roles_desc[<?php echo $k?>]" value='<?php echo isset($wpue_config->userdata[$k]) ? htmlentities(utf8_decode($wpue_config->userdata[$k])) : htmlentities(utf8_decode($n))?>'>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
        
    </table>
    
    <h3 class="title"><?php _e('In what formats data can be exported?', 'wp-users-exporter')?></h3>
    
    <table class="wp-list-table widefat fixed">
        <thead>
            <tr class="manage-column column-name desc">
                <th><?php _e('Format', 'wp-users-exporter'); ?></th>
                <th><?php _e('Options', 'wp-users-exporter'); ?></th>
            </tr>
            
        </thead>
        
        <tbody>

        
        <?php foreach ($wpue_options->exporters as $k):
            eval("\$file_extension = {$k}::getFileExtension();");
            eval("\$file_extensionDescription = {$k}::getFileExtensionDescription();");
            $desc = ".$file_extension ($file_extensionDescription)";
        ?>
            <tr class="alternate">
                <td>
                    <label><input type="checkbox" name='exporters[<?php echo $k?>]' value='<?php echo $k?>' <?php if(in_array($k, $wpue_config->exporters)) echo 'checked="checked"';?>/> <?php echo $desc?> </label>
                </td>
                <td>
                    <?php eval("\$file_extensionDescription = {$k}::printOptionsForm();"); ?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
        
    </table>
    
    
    <h3 class="title"><?php _e('What fields may be exported?', 'wp-users-exporter')?></h3>
    
    <table class="wp-list-table widefat fixed">
        <thead>
            <tr class="manage-column column-name desc">
                <th><?php _e('Field', 'wp-users-exporter'); ?></th>
                <th><?php _e('Display as', 'wp-users-exporter'); ?></th>
            </tr>
        </thead>
        
        <tfoot>
            <tr class="manage-column column-name desc">
                <th><?php _e('Field', 'wp-users-exporter'); ?></th>
                <th><?php _e('Display as', 'wp-users-exporter'); ?></th>
            </tr>
        </tfoot>
        
        <tbody>
        
        
        
        <tr>
            <td colspan="2"><b><?php _e('Core fields', 'wp-users-exporter'); ?></b></td>
        </tr>
        
        <?php foreach ($wpue_options->userdata as $k => $n):?>
            
            <tr>
                <td>
                    <label><input type="checkbox" name='userdata[<?php echo $k?>]' value='<?php echo $k?>' <?php if(isset($wpue_config->userdata[$k])) echo 'checked="checked"';?>/> <?php echo $k?> </label> 
                </td>
                <td>
                    <input type="text" name="userdata_desc[<?php echo $k?>]" value='<?php echo isset($wpue_config->userdata[$k]) ? htmlentities(utf8_decode($wpue_config->userdata[$k])) : htmlentities(utf8_decode($n))?>'> <br/>
                </td>
            </tr>
        
        <?php endforeach;?>
        
        
        
        
        
        <?php if(wpue_isBP()): ?>
        
            <tr>
                <td colspan="2"><b><?php _e('BuddyPress fields', 'wp-users-exporter'); ?></b></td>
            </tr>
            
            <?php foreach($bp_fields as $field):?>
            
                <tr>
                    <td>
                        <label><input type="checkbox" name='bp_fields[<?php echo $field->id?>]' value='<?php echo $field->id?>' <?php if(isset($wpue_config->bp_fields[$field->id])) echo 'checked="checked"';?>/> <?php echo $field->name?> </label><br/> 
                    </td>
                    
                    <td></td>
                </tr>
            
            <?php endforeach;?>
        
        <?php endif; ?>
        
        
        
        
        
        <tr>
            <td colspan="2"><b><?php _e('User Metadata', 'wp-users-exporter'); ?></b></td>
        </tr>
        
        <?php foreach ($wpue_options->metadata as $k => $n): if($k[0] != '_'):?>
            
            <tr>
                <td>
                    <label><input type="checkbox" name='metadata[<?php echo $k?>]' value='<?php echo $k?>' <?php if(isset($wpue_config->metadata[$k])) echo 'checked="checked"';?>/> <?php echo $k?> </label> 
                </td>
                <td>
                    <input type="text" name="metadata_desc[<?php echo $k?>]" value='<?php echo isset($wpue_config->metadata[$k]) ? htmlentities(utf8_decode($wpue_config->metadata[$k])) : htmlentities(utf8_decode($n))?>'> <br/>
                </td>
            </tr>
        
        <?php endif; endforeach;?>
        
        </tbody>
        
    </table>
    
    <h3 class="title"><?php _e('In what formats dates are going to be exported?', 'wp-users-exporter')?></h3>
    
    <?php _e('date format','wp-users-exporter')?>:
    <input type='text' name='date_format' value='<?php echo htmlentities(utf8_decode($wpue_config->date_format))?>' />
    <?php _e('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date and time formatting</a>.', 'wp-users-exporter'); ?>
    
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Settings','wp-users-exporter')?>" />
    </p>
    
</form>
</div>
