<div class="modal fade" id="solvease-import-capability" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i
                            class="fa fa-close"></i></span></button>
                <h4 class="modal-title"
                    id="myModalLabel"><?php _e('Import Roles and Capabilities', $this->translation_domain); ?></h4>
            </div>
            <form name="solvease_import_capability" method="POST" id="solvease_import_capability"
                  enctype="multipart/form-data">
                <input type="hidden" name="solvease_role_cap_action" value="import_capability"/>
                <?php wp_nonce_field('solvease_import_capability', 'solvease_import_capability_nonce'); ?>
                <div class="modal-body">

                    <div class="form-group">
                    <span class="btn btn-default btn-file" style="width: 100%">
                       <strong>Select File to import </strong>
                        <input type="file" name="caps_file">
                    </span>
                    </div>
                    <div class="form-group">
                        <label for="message-text"
                               class="control-label"><?php _e('Select import options', $this->translation_domain); ?>
                            :</label>
                        <select name="import_option" class="form-control">
                            <option
                                value="overwrite"> <?php _e('Overwrite capabilities', $this->translation_domain); ?></option>
                            <option
                                value="merge"> <?php _e('Merge capabilities', $this->translation_domain); ?></option>
                        </select>
                        <div class="help-text">
                            <br/>
                            <p> <?php _e('<b>Overwrite capabilities</b>: Overwrite capabilities of existing roles with imported file.', $this->translation_domain); ?> </p>
                            <p> <?php _e('<b>Merge capabilities</b>: Merge capabilities of existing roles with imported file.', $this->translation_domain); ?> </p>
                        </div>

                    </div>
                </div>
                <div class="modal-footer"></span>
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php print $this->cancel_button; ?> </button>
                    <button type="submit" id="btn-solvease-export-role"
                            class="btn btn-primary"><?php _e('Import Roles and Capabilities', $this->translation_domain); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>