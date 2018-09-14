<?php
wp_enqueue_script('accordion');
//print_r($this->user->roles);
?>

<form enctype="multipart/form-data" method="post" action="" id="update-nav-menu">
<input type="hidden" name="solvease_user_role_cap_action" value="user_role_cap" />
<?php wp_nonce_field('solvease_user_role_cap', 'solvease_user_role_cap_nonce'); ?>
<input type="hidden" name="user_id" value="<?php print $this->user->ID; ?>" />
<div class="wrap nav-menus-php">
    <?php if($message == true) { ?>
    <div class="updated below-h2" id="message"><p><?php _e('Changes Saved.', $this->translation_domain); ?> </p></div>
    <?php } ?>
    <div class="manage-menus">
        <?php _e('Change roles and capabilities for user', $this->translation_domain); ?> : <strong> <?php print $this->user->data->user_login; ?> (<?php print_r($this->user->display_name); ?>) </strong>
    </div>
    
    <div id="nav-menus-frame">
        <div class="metabox-holder" id="menu-settings-column">
            <div class="clear"></div>
            <div class="accordion-container" id="side-sortables">
                <ul class="outer-border">
                    <li id="add-page" class="control-section accordion-section  open add-page">
                        <h3 tabindex="0" class="accordion-section-title hndle">
                            <?php _e('Roles', $this->translation_domain); ?>					
                            <span class="screen-reader-text">Press return or enter to expand</span>
                        </h3>
                        <div class="accordion-section-content"> 
                            <p>
                                <strong><?php _e('Primary Role', $this->translation_domain); ?>: </strong>
                                <select id="primary_role" name="primary_role">
                                    <?php foreach ($user_roles as $key => $role) { ?>
                                        <option value="<?php print $key; ?>" <?php if ($key == $this->user->roles[key($this->user->roles)]) { print "selected"; } ?>> <?php print $role['name']; ?> </option>
                                    <?php } ?>
                                </select> 
                            </p>
                            <p>
                                <strong><?php _e('Secondary Roles', $this->translation_domain); ?>: <br/> </strong>
                                <?php
                                foreach ($user_roles as $key => $role) {
                                   // if ($key != $this->user->roles[0]) {
                                        ?>
                                        <input type="checkbox" name="secondary_roles[<?php print $key; ?>]" <?php if (in_array($key, $this->user->roles) && $key != $this->user->roles[key($this->user->roles)]) { print "checked"; } ?>> <?php print $role['name'] ?> <br/>
                                    <?php
                               // }
                            }
                            ?>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div id="menu-management-liquid">
            <div id="menu-management">

                    <div class="menu-edit ">
                        <div id="nav-menu-header">
                            <div class="major-publishing-actions">
                                <label for="menu-name" class="menu-name-label howto open-label">
                                    <input type="text" class="form-control" id="filter-capability" placeholder="<?php _e('Filter Capability', $this->translation_domain); ?>">
                                </label>
                                <div class="publishing-action">
                                    <input type="submit" value="<?php _e('Save Roles and Capabilities', $this->translation_domain); ?>" class="button button-primary menu-save" id="save_menu_header" name="save_role_cap" >
                                </div>
                            </div>
                        </div>
                        <div id="post-body">
                            <div id="post-body-content">

                                <?php foreach ($this->roles_capabilities_function->solvease_roles_capabilities_get_built_in_capabilities() as $built_in_caps) { ?>
                                <div class="user-role-cap-parent">
                                <h4><?php print $built_in_caps['name']; ?></h4>
                                <?php foreach ($built_in_caps['cap'] as $cap) {?>
                                <div class="user-role-cap">
                                    <input type="checkbox"  name="cap[<?php print $cap['name']; ?>]" <?php print $this->roles_capabilities_function->solvease_roles_capabilities_check_user_cap($this->user, $user_capability_from_role, $cap['name']); ?> />
                                        <span class="cap-name-to-filter"><?php print $cap['name']; ?></span>

                                </div>
                                <?php } ?>
                                </div>
                                 <?php } ?>

                                <div class="user-role-cap-parent">
                                    <h4><?php _e('Custom Capabilities', $this->translation_domain); ?></h4>
                                    <?php foreach ($this->roles_capabilities_function->solvease_roles_capabilities_get_other_capabilities() as $cap){ ?>
                                        <div class="user-role-cap">
                                        <input type="checkbox" name="cap[<?php print $cap; ?>]" <?php print $this->roles_capabilities_function->solvease_roles_capabilities_check_user_cap($this->user, $user_capability_from_role, $cap); ?> >
                                        <span class="cap-name-to-filter"><?php print $cap; ?> </span>
                                        </div>
                                    <?php } ?>

                                     <?php foreach ($hidden_cap as $cap){ ?>
                                        <div class="user-role-cap">
                                        <input type="checkbox" name="cap[<?php print $cap; ?>]" <?php print $this->roles_capabilities_function->solvease_roles_capabilities_check_user_cap($this->user, $user_capability_from_role, $cap); ?> > <?php print $cap; ?>
                                        </div>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                        <div id="nav-menu-footer">
                            <div class="major-publishing-actions solvease-pc">
                                <div class="publishing-action">
                                    <input type="submit" value="<?php _e('Save Roles and Capabilities', $this->translation_domain); ?>" class="button button-primary menu-save" id="save_menu_header" name="save_role_cap" >
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</div>
</form>