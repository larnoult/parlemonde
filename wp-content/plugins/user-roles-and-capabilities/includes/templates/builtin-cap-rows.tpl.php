<?php
$built_in_count = 0;
foreach ($builtin_capabilities as $builtin_capability) {
    ?>
    <tr head-row-id="built-in-<?php echo $built_in_count?>" class="<?php print $this->solvease_roles_capabilities_get_tr_class(); ?> solvease-rnc-head-start" >
        <td colspan="<?php print $this->roles_count + 1; ?>"><strong> <?php print $builtin_capability['name']; ?> </strong> </td>
    </tr>
    <?php foreach ($builtin_capability['cap'] as $key => $value) { ?>
        <tr class="<?php print $this->solvease_roles_capabilities_get_tr_class(); ?>">
            <td class="solvease-rnc-wide-cell"> 
                <div class="cap-name-to-filter built-in-<?php echo $built_in_count?>"><?php print $value['name']; ?> </div>
                <!--<div><?php print $value['desc']; ?> </div> -->
            </td>
            <?php foreach ($this->roles as $rolekey => $role) { ?>
                <td class="solvease-rnc-center-align"><input type="checkbox" name="capability<?php print  '[' . $rolekey . '][' . $key . ']'; ?>" <?php print $this->roles_capabilities_function->solvease_roles_capabilities_get_role_has_capability($this->roles, $rolekey, $key); ?> /></td>
            <?php } ?>
        </tr>
    <?php } ?>
<?php $built_in_count++; } ?>