<form name="solvease_capability_form" method="POST" id="solvease_capability_form">
    <input type="hidden" name="solvease_role_cap_action" value="save_cap_changes"/>
    <?php wp_nonce_field('solvease_save_capability', 'solvease_verify_capability_nonce'); ?>
    <table class="widefat solvease-rnc-table-head" cellspacing="0">
        <thead>
        <tr>
            <th id="toolbox" colspan="<?php print $this->roles_count + 1; ?>" id="columnname"
                class="manage-column column-columnname solvease-rnc-wide-head" scope="col">
                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php print $message['type']; ?>">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <?php print $message['message']; ?>
                    </div>
                <?php } ?>

                <div class="clearfix solvease-rnc-role-list">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter-capability"
                                       placeholder="<?php _e('Filter Capability', $this->translation_domain); ?>">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="solvease-nav-menu">
                                <!--- new Menu -->
                                <ul class="nav navbar-nav">
                                    <li class="dropdown">
                                        <a aria-expanded="true" aria-haspopup="true" role="button"
                                           data-toggle="dropdown"
                                           class="dropdown-toggle" href="#"> Actions <span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <?php if (current_user_can($this->plugin_caps['add_new_role'])) { ?>
                                                <li>
                                                    <a data-target="#solvease-add-role" data-toggle="modal" href="#">
                                                        <span class="icons">
                                                            <i class="fa fa-plus"></i>
                                                        </span>

                                                        <span class="actions">
                                                            <?php _e('Add New Role', $this->translation_domain); ?>
                                                        </span>
                                                    </a>
                                                    <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Option to add new role. You will be able to copy capabilities from other role during new role creation."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>


                                            <?php if (current_user_can($this->plugin_caps['delete_role'])) { ?>
                                                <li>
                                                    <a data-target="#solvease_un_used_role_delete" data-toggle="modal"
                                                       href="#">
                                                          <span class="icons">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                         <span class="actions">
                                                            <?php _e('Delete Role', $this->translation_domain); ?>
                                                         </span>
                                                    </a>
                                                     <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Options to delete role. You will only able to delete roles, which are created by this plugin."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>

                                            <?php if (current_user_can($this->plugin_caps['change_default_role'])) { ?>
                                                <li>
                                                    <a data-target="#solvease-change-default-role" data-toggle="modal"
                                                       href="#">
                                                        <span class="icons">
                                                            <i class="fa fa-pencil-square"></i>
                                                        </span>
                                                        <span class="actions">
                                                            <?php _e('Change Default Role', $this->translation_domain); ?>
                                                        </span>
                                                    </a>
                                                    <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip"
                                                               data-placement="left"
                                                               title="Option to change default role. This role will be set when a user will register to your site."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>


                                            <?php if (current_user_can($this->plugin_caps['add_new_capability'])) { ?>
                                                <li>
                                                    <a data-target="#solvease-add-capability" data-toggle="modal"
                                                       href="#">
                                                       <span class="icons">
                                                            <i class="fa fa-plus-square"></i>
                                                        </span>
                                                         <span class="actions">
                                                             <?php _e('Add Capability', $this->translation_domain); ?>
                                                         </span>
                                                    </a>
                                                     <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Option to add capability. Create as many as capabilities you wish. You can use these capabilities in your custom module or theme to manage user permissions."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>

                                            <?php if (current_user_can($this->plugin_caps['remove_capability'])) { ?>
                                                <li>
                                                    <a data-target="#solvease_rc_delete_cap" data-toggle="modal"
                                                       href="#">
                                                        <span class="icons">
                                                            <i class="fa fa-remove"></i>
                                                        </span>
                                                        <span class="actions">
                                                            <?php _e('Remove Capability', $this->translation_domain); ?>
                                                        </span>
                                                    </a>
                                                     <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Option to remove capability. You will be able to delete capabilities created by this plugin."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>


                                            <?php if (current_user_can($this->plugin_caps['import'])) { ?>
                                                <li>
                                                    <a data-target="#solvease-import-capability" data-toggle="modal"
                                                       href="#">
                                                        <span class="icons">
                                                            <i class="fa fa-arrow-up"></i>
                                                        </span>
                                                        <span class="actions">
                                                            <?php _e('Import Roles and Capabilities', $this->translation_domain); ?>
                                                        </span>
                                                    </a>
                                                    <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Import roles and capabilities."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>

                                            <?php if (current_user_can($this->plugin_caps['export'])) { ?>
                                                <li>
                                                    <a data-target="#solvease-export-capability" data-toggle="modal"
                                                       href="#">
                                                        <span class="icons">
                                                            <i class="fa fa-arrow-down"></i>
                                                        </span>
                                                        <span class="actions">
                                                            <?php _e('Export Roles and Capability', $this->translation_domain); ?>
                                                        </span>
                                                    </a>
                                                    <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Export roles and capabilities."></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php } ?>

                                            <li class="last">
                                                <a href="mailto:project.mahabub@gmail.com">
                                                    <span class="icons">
                                                        <i class="fa fa-comments"></i>
                                                    </span>
                                                    <span class="actions" style="color: red;">Feedback / Bug report / Development </span>
                                                </a>
                                                 <span class="help">
                                                        <a href="#">
                                                            <i class="fa fa-info-circle rnc-item-popover-desc"
                                                               data-toggle="tooltip" data-placement="left"
                                                               title="Please let me know if you see any bug in this plugin. Also let me know about your feedback or if you want any new feature. I am also available to develop any custom plugin for WordPress, if you have such work please let me know. My name is Mahabub and email address is mahabub@solvease.com"></i>
                                                        </a>
                                                    </span>
                                            </li>


                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <div class="solvease-rnc-btn-block">
                                <input type="submit" name="submit" class="btn btn-primary"
                                       value="<?php _e('Save Changes', $this->translation_domain); ?>">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                        </div>
                    </div>
                </div>
            </th>
        </tr>

        <tr class="main-table">
            <th id="columnname" class="manage-column column-columnname solvease-rnc-wide-head" scope="col">
                Capabilities
            </th>
            <?php if (!empty($this->roles)) { ?>
                <?php foreach ($this->roles as $roleid => $role) { ?>
                    <th id="columnname" class="manage-column column-columnname solvease-rnc-center-align" scope="col">
                        <strong>
                            <?php print $role['name']; ?>
                            <?php if (current_user_can($this->plugin_caps['rename_role'])) { ?>
                                <i title="change role name" style="cursor: pointer;"
                                   class="fa fa-pencil-square rnc-change-role-pname"
                                   data-target="#solvease-change-role-name-<?php print $roleid; ?>"
                                   data-toggle="modal"></i>
                            <?php } ?>
                        </strong>
                        <div class="role-options role-opertaion" role-id="<?php print $roleid; ?>">
                            <span><a title="Select All" class="select-all"><i
                                        class="fa fa-check-square-o"></i></a></span>
                            <span><a title="Unselect All" class="un-select-all"><i
                                        class="fa fa-square-o"></i></a></span>
                            <!-- <span><a title="Reverse" class="reverse"><i class="fa fa-rotate-left"></i></a></span> -->
                        </div>

                    </th>
                <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <!-- </table> -->