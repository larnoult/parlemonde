<?php
foreach($this->roles as $role_id => $role_val) {
    ?>
    <div class="modal fade" id="solvease-change-role-name-<?php print $role_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"><i class="fa fa-close"></i></span></button>
                    <h4 class="modal-title"
                        id="myModalLabel"><?php _e('Change display name for ', $this->translation_domain); print $role_val['name']; ?></h4>
                </div>
                <form name="solvease_change_default_role_name" method="POST" id="solvease_change_default_role_name">
                    <input type="hidden" name="solvease_role_cap_action" value="change_role_display_name"/>
                    <?php wp_nonce_field('change_role_display_name', 'change_role_display_name_nonce'); ?>
                    <input type="hidden" name="display_role_id" value="<?php print $role_id; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="message-text"
                                   class="control-label"><?php _e('New display name for ', $this->translation_domain); print $role_val['name']; ?>
                                :</label>
                            <input type="text" class="form-control" id="role-display_name" name="role_display_name" value="<?php print $role_val['name'] ?>"
                                   required="required" autocomplete="off">

                            <div
                                class="help-text"><?php _e('Please provide new role display name', $this->translation_domain); ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php print $this->cancel_button; ?></button>
                        <button type="submit" id="btn-solvease-add-role"
                                class="btn btn-primary"><?php _e('Change Role Name', $this->translation_domain); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
}
?>