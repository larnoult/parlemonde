<div class="modal fade" id="solvease-change-default-role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Change Default Role', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_change_default_role"  method="POST" id="solvease_change_default_role" >
                <input type="hidden" name="solvease_role_cap_action" value="change_default_role" />
                <?php wp_nonce_field('solvease_change_default_role','solvease_change_default_role_nonce'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php _e('Select Role to make as Default', $this->translation_domain); ?>:</label>
                        <select name="default_role" class="form-control">
                            <?php foreach ($this->roles as $rolekey=> $role) { ?>
                                <option value="<?php print $rolekey; ?>" <?php if(get_option( 'default_role') == $rolekey) { print 'selected'; }?>> <?php print $role['name']; ?> </option>
                            <?php } ?>
                        </select> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-default" data-dismiss="modal"><?php print $this->cancel_button; ?></button>
                    <button type="submit" id="btn-solvease-add-role"  class="btn btn-primary"><?php _e('Change Default Role', $this->translation_domain); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>