<div class="modal fade" id="solvease_un_used_role_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Delete Unused Roles', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_un_used_role"  method="POST" id="solvease_un_used_role" enctype="multipart/form-data"  >
                <input type="hidden" name="solvease_role_cap_action" value="delete_role" />
                <?php wp_nonce_field('solvease_un_used_role','solvease_un_used_role_nonce'); ?>
                <div class="modal-body">
                    <?php $unused_roles =   $this->roles_capabilities_function->solvease_roles_capabilities_get_unused_roles() ?>
                    <?php if(!empty($unused_roles)) { ?>
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php _e('Select Roles to Delete', $this->translation_domain); ?>:</label>
                        <select name="delete_role[]" class="form-control" multiple="multiple">
                            <?php foreach ( $unused_roles as $rolekey=> $role) { ?>
                                <option value="<?php print $rolekey; ?>"> <?php print $role; ?> </option>
                            <?php } ?>
                        </select> 
                    </div>
                    <?php } else { ?>
                        There are no unused role to Delete. 
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-default" data-dismiss="modal"><?php print $this->cancel_button; ?></button>
                    <?php if(!empty($unused_roles)) { ?>
                    <button type="submit" id="btn-solvease-add-role"  class="btn btn-primary"><?php _e('Delete Role', $this->translation_domain); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>