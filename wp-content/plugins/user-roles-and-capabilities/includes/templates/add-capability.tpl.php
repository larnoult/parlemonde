<div class="modal fade" id="solvease-add-capability" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Add Capability', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_add_capability_form"  method="POST" id="solvease_add_capability_form" enctype="multipart/form-data"  >
                <input type="hidden" name="solvease_role_cap_action" value="add_capability" />
                <?php wp_nonce_field('solvease_add_capability','solvease_add_capability_nonce'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label"><?php _e('Capability Name', $this->translation_domain); ?>:</label>
                        <input type="text" class="form-control" id="cap_name" name="cap_name" autocomplete="off">
                    </div>
                   
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php _e('Select Roles to add this capability', $this->translation_domain); ?>:</label>
                        <select name="cap_role[]" class="form-control" multiple="multiple">
                            <?php foreach ($this->roles as $rolekey=> $role) { ?>
                                <option value="<?php print $rolekey; ?>"> <?php print $role['name']; ?> </option>
                            <?php } ?>
                        </select> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-default" data-dismiss="modal"><?php print $this->cancel_button; ?></button>
                    <button type="submit" id="btn-solvease-add-role"  class="btn btn-primary"><?php _e('Add Capability', $this->translation_domain); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>