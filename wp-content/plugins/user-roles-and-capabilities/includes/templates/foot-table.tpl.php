</tbody>    
<tfoot>
        <tr>
            <th id="columnname" class="manage-column column-columnname solvease-rnc-wide-head" scope="col">Capabilities</th>
            <?php if (!empty($this->roles)) { ?>
                <?php foreach ($this->roles as $role) { ?>
                    <th id="columnname" class="manage-column column-columnname solvease-rnc-center-align" scope="col"><?php print $role['name']; ?></th>
                <?php } ?>
            <?php } ?>
        </tr>
    </tfoot>
</table>
</form>