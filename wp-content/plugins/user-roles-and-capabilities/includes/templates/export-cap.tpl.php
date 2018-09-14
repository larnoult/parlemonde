<div class="modal fade" id="solvease-export-capability" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i
                            class="fa fa-close"></i></span></button>
                <h4 class="modal-title"
                    id="myModalLabel"><?php _e('Export Roles and Capability', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_export_capability" method="POST" id="solvease_export_capability"
                  enctype="multipart/form-data">
                <input type="hidden" name="solvease_role_cap_action" value="export_capability"/>
                <?php wp_nonce_field('solvease_export_capability', 'solvease_export_capability_nonce'); ?>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="message-text"
                               class="control-label"><?php _e('Selected roles capabilities will be exported', $this->translation_domain); ?>
                            :</label>
                        <select name="cap_role_export" class="form-control" multiple="multiple" id="cap_role_export">
                            <?php foreach ($this->roles as $rolekey => $role) { ?>
                                <option value="<?php print $rolekey; ?>"> <?php print $role['name']; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"></span>
                    <button type="button" class="btn btn-default export-close"
                            data-dismiss="modal"><?php print $this->cancel_button; ?> </button>
                    <button type="submit" id="btn-solvease-export-role"
                            class="btn btn-primary"><?php _e('Export Capability', $this->translation_domain); ?></button>
                    <i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw margin-bottom loading-icon" aria-hidden="true"
                       style="float: right; margin-top: 5px; display: none"></i>
                </div>
            </form>
        </div>
    </div>
</div>