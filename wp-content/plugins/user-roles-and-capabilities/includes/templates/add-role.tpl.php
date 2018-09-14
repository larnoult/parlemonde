<div class="modal fade" id="solvease-add-role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Add new role', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_add_role_form"  method="POST" id="solvease_add_role_form" >
                <input type="hidden" name="solvease_role_cap_action" value="add_role" />
                <?php wp_nonce_field('solvease_add_role','solvease_add_role_nonce'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label"><?php _e('Role ID', $this->translation_domain); ?>:</label>
                        <input type="text" class="form-control" id="role-id" name="role-id" autocomplete="off">
                        <div class="help-text"><?php _e('Role ID can contain characters, digits, hyphens or underscore only', $this->translation_domain); ?>.</div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php _e('Role name (Display Name)', $this->translation_domain); ?>:</label>
                        <input type="text" class="form-control" id="role-name" name="role-name" autocomplete="off">
                        <div class="help-text"><?php _e('Display name for the role', $this->translation_domain); ?>.</div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php _e('Copy Capabilities From', $this->translation_domain); ?>:</label>
                        <select name="copy-from" class="form-control">
                            <option value=""> <?php _e('Select Role', $this->translation_domain); ?></option>
                            <?php foreach ($this->roles as $rolekey=> $role) { ?>
                                <option value="<?php print $rolekey; ?>"> <?php print $role['name']; ?> </option>
                            <?php } ?>
                        </select> 
                        <div class="help-text"><?php _e('Copies Capabilities from Role', $this->translation_domain); ?>.</div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-default" data-dismiss="modal"><?php print $this->cancel_button; ?></button>
                    <button type="submit" id="btn-solvease-add-role"  class="btn btn-primary"><?php _e('Add Role', $this->translation_domain); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>