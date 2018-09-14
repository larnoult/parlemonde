<div class="modal fade" id="solvease_rc_delete_cap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Delete Capability', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_un_used_role"  method="POST" id="solvease_delete_cap_form" enctype="multipart/form-data"  >
                <input type="hidden" name="solvease_role_cap_action" value="delete_cap" />
                <?php wp_nonce_field('solvease_rc_delete_cap','solvease_rc_delete_cap_nonce'); ?>
                <div class="modal-body">
                    <?php $custom_cap =   $this->roles_capabilities_function->solvease_roles_capabilities_get_custom_cap() ?>
                    <?php if(!empty($custom_cap)) { ?>
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php _e('Select Capabilities to Delete', $this->translation_domain); ?>:</label>
                        <select name="delete_cap[]" class="form-control" multiple="multiple">
                            <?php foreach ( $custom_cap as $key => $cap) { ?>
                                <option value="<?php print $cap; ?>"> <?php print $cap; ?> </option>
                            <?php } ?>
                        </select> 
                    </div>
                    <?php } else { ?>
                        <?php _e('There are no capabilities to delete created by this plugin.', $this->translation_domain); ?>
                         
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-default" data-dismiss="modal"><?php print $this->cancel_button; ?></button>
                    <?php if(!empty($custom_cap)) { ?>
                    <button type="submit" id="btn-solvease-add-role"  class="btn btn-primary"><?php _e('Delete Capability', $this->translation_domain); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>