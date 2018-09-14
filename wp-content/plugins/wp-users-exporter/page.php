<?php 
global $wpdb;

$wpue_config = wpue_getConfig();
?>
<style>
.wpue-field { float:left; margin:10px;}
.wpue-filters {  padding:10px; border-bottom: 2px solid #888; }
.wpue-filters .capabilities { margin-top:10px; }
#wpue-fields-list li, #wpue-fields-list label {cursor:move !important;}
</style>

<div class="wrap">

<h2><?php _e('Export Users', 'wp-users-exporter'); ?></h2>

<form method="post">
    <input type="hidden" name='<?php echo WPUE_PREFIX?>action' value='export-users' />
    <div class="wpue-filters">
        <h3><?php _e('Filters', 'wp-users-exporter'); ?>:</h3>
        <select name='filter'>
            <option value=''><?php _e('do not filter', 'wp-users-exporter')?></option>
            <?php foreach ($wpue_config->userdata as $udata => $dataname): if($udata === __ROLE__) continue; ?>
                <option value='<?php echo $udata?>'><?php echo $dataname?></option>
            <?php endforeach;?>
            
            <?php foreach ($wpue_config->metadata as $udata => $dataname): ?>
                <option value='_<?php echo $udata?>'><?php echo $dataname?></option>
            <?php endforeach;?>
        </select>
        <select name='operator'>
            <option value='eq'><?php _e('equal','wp-users-exporter')?></option>
            <option value='dif'><?php _e('not equal','wp-users-exporter')?></option>
            <option value='like'><?php _e('has','wp-users-exporter')?></option>
            <option value='not-like'><?php _e('hasnt','wp-users-exporter')?></option>
        </select>
        <input name='filter_value' value=''> 
        
        <div class='capabilities'>
        <?php _e('User Roles','wp-users-exporter')?>:
        <?php foreach ($wpue_config->roles as $cap => $capname): ?>
            <label><input type='checkbox' name='roles[]' value='<?php echo $cap?>' <?php if(!isset($_POST['userdata']) || isset($_POST['roles'][$cap])) echo 'checked="checked"'; ?>/> <?php echo $capname?></label> 
        <?php endforeach;?>
        </div>
    </div>
       
    <div class='wpue-field'> 
        <h4><?php _e('Fields','wp-users-exporter')?> <small>(<?php _e('Drag & drop to change order','wp-users-exporter')?>)</small></h4>
        
        <ul id="wpue-fields-list">
        <?php foreach ($wpue_config->userdata as $udata => $dataname): ?>
            <li><input type="hidden" name="display_order[]" value="<?php echo $udata?>" /><label><input type='checkbox' name='userdata[]' value='<?php echo $udata?>' <?php if(!isset($_POST['userdata']) || isset($_POST['userdata'][$udata])) echo 'checked="checked"'; ?>/><?php echo $dataname?></label></li>
        <?php endforeach;?>
        
        <?php if(wpue_isBP()): $bp_fields = wpue_bp_getProfileFields();?>
	     
        <?php foreach ($wpue_config->bp_fields as $field_id): ?>
            <li><input type="hidden" name="display_order[]" value="bp_<?php echo $field_id?>" /><label><input type='checkbox' name='bp_fields[]' value='<?php echo $field_id?>' <?php if(!isset($_POST['bp_fields']) || isset($_POST['bp_fields'][$field_id])) echo 'checked="checked"'; ?>/><?php echo $bp_fields[$field_id]->name?></label></li>
        <?php endforeach;?>
        <?php endif;?>
	        
        <?php foreach ($wpue_config->metadata as $udata => $dataname): ?>
            <li><input type="hidden" name="display_order[]" value="<?php echo $udata?>" /><label><input type='checkbox' name='metadata[]' value='<?php echo $udata?>' <?php if(!isset($_POST['userdata']) || isset($_POST['metadata'][$udata])) echo 'checked="checked"'; ?>/><?php echo $dataname?></label></li>
        <?php endforeach;?>
        </ul>
        
        <a href="<?php echo admin_url('options-general.php?page=wpue-config'); ?>">
        <?php _e('Visit settings page to choose what fields you see here','wp-users-exporter')?>
        </a>
        
    </div>
    <div class='wpue-field'>
        <h4><?php _e('File format','wp-users-exporter')?>:</strong></h4>
        <?php
        $first = true; 
        foreach ($wpue_config->exporters as $exporter): 
            eval("\$extension = {$exporter}::getFileExtension();");
            eval("\$description = {$exporter}::getFileExtensionDescription();");
            
        ?>
            <label><input type="radio" name='exporter' value='<?php echo $exporter?>' <?php if((!isset($_POST['exporter']) && $first) || (isset($_POST['exporter']) && $_POST['exporter'] == $exporter)) echo 'checked="checked"'?>/><?php echo $description;?> <em>(.<?php echo $extension?>)</em></label> <br/>
        <?php 
            $first = false;
        endforeach;
        ?>
        
        
        <h4><?php _e('Order by','wp-users-exporter')?>:</strong></h4>
        <select name='order'>
        <?php
        $first = true;
        $defaultConfig = wpue_getDefaultConfig(); 
        foreach ($defaultConfig->userdata as $udata => $dataname): if($udata === __ROLE__) continue;?>
            <option value='<?php echo $udata?>' <?php if((!isset($_POST['order']) && $first) || (isset($_POST['order']) && $_POST['order'] == $dataname)) echo 'checked="checked"'?>/><?php echo $dataname?></option>
        <?php 
             $first = false;
        endforeach;
        ?>
        
        
        <?php /* foreach ($wpue_config->metadata as $udata => $dataname): ?>
            <label><input type='checkbox' name='metadata[]' value='<?php echo $udata?>' <?php if(!isset($_POST['userdata']) || isset($_POST['metadata'][$udata])) echo 'checked="checked"'; ?>/><?php echo $dataname?></label><br />
        <?php endforeach; */?>
        
        </select> 
        <select name='oby'>
            <option value='ASC'><?php _e('Ascending', 'wp-users-exporter');?></option>
            <option value='DESC'><?php _e('Descending', 'wp-users-exporter');?></option>
        </select>
        
        <br/><br/>
        <input type="submit" class="button-primary" value="<?php _e('Export!', 'wp-users-exporter')?>">
    </div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function(){
        order = <?php echo json_encode(A_UserExporter::getFieldsOrder()); ?>;
       
        // sort fields based on the last order used by the user if any
        if (order) {
            jQuery("#wpue-fields-list li").sort(sortFields).appendTo('#wpue-fields-list');
        }
        
        jQuery("#wpue-fields-list").sortable();
    });
    
    function sortFields(a, b) {
        return order.indexOf(jQuery(a).find('input[type=hidden]').val()) > order.indexOf(jQuery(b).find('input[type=hidden]').val()) ? 1 : -1;
    }
</script>
</div>
