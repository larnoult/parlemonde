<tr head-row-id="other-caps" class="<?php print $this->solvease_roles_capabilities_get_tr_class(); ?> solvease-rnc-head-start" >
    <td colspan="<?php print $this->roles_count + 1; ?>"><strong> Other Capabilities </strong> </td>
</tr>

<?php foreach ($other_cpas as $other_cap) { ?>
    <tr class="<?php print $this->solvease_roles_capabilities_get_tr_class(); ?>">
        <td class="solvease-rnc-wide-cell"> 
            <div class="cap-name-to-filter other-caps"> <?php print $other_cap; ?> </div>
        </td>
        <?php foreach ($this->roles as $rolekey => $role) { ?>
            <td class="solvease-rnc-center-align"><input type="checkbox" name="capability<?php print '[' .$rolekey . '][' . $other_cap . ']'; ?>" <?php print $this->roles_capabilities_function->solvease_roles_capabilities_get_role_has_capability($this->roles, $rolekey, $other_cap); ?> /></td>
            <?php } ?>
    </tr>
<?php } ?>
        