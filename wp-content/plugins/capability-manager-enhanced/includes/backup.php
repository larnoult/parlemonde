<?php
/**
 * Capability Manager Backup Tool.
 * Provides backup and restore functionality to Capability Manager.
 *
 * @version		$Rev: 198515 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	CapsMan
 *

	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

?>
<div class="wrap">
	<div id="icon-capsman-admin" class="icon32"></div>
	<h2><?php _e('Backup Tool for Capability Manager', 'capsman-enhanced') ?></h2>

	<form method="post" action="tools.php?page=<?php echo $this->ID ?>-tool">
	<?php wp_nonce_field('capsman-backup-tool'); ?>
	<fieldset>
	<table id="akmin">
	<tr>
		<td class="content">
		<dl>
			<dt><?php _e('Backup and Restore', 'capsman-enhanced'); ?></dt>
			<dd>
				<table width='100%' class="form-table">
				<tr>
					<th scope="row"><?php _e('Select action:', 'capsman-enhanced'); ?></th>
					<td>
						<select name="action">
							<option value="backup"> <?php _e('Backup roles and capabilities', 'capsman-enhanced'); ?> </option>
							<option value="restore"> <?php _e('Restore last saved backup', 'capsman-enhanced'); ?> </option>
						</select> &nbsp;
						<input type="submit" name="Perform" value="<?php _e('Do Action', 'capsman-enhanced') ?>" class="button-primary" />
					</td>
				</tr>
				</table>
			</dd>
		</dl>

		<dl>
			<dt><?php if ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) _e('Reset WordPress Defaults', 'capsman-enhanced'); else echo 'Reset Roles to WordPress Defaults';?></dt>
			<dd>
				<p style="text-align:center;"><strong><span style="color:red;"><?php _e('WARNING:', 'capsman-enhanced'); ?></span> <?php if ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) _e('Reseting default Roles and Capabilities will set them to the WordPress install defaults.', 'capsman-enhanced'); else echo 'This will delete and/or modify stored role definitions.'; ?></strong><br />
					<br />
					<?php 
					_e('If you have installed any plugin that adds new roles or capabilities, these will be lost.', 'capsman-enhanced')?><br />
					<strong><?php if ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) _e('It is recommended to use this only as a last resource!'); else echo('It is recommended to use this only as a last resort!');?></strong></p>
				<p style="text-align:center;"><a class="ak-delete" title="<?php echo esc_attr(__('Reset Roles and Capabilities to WordPress defaults', 'capsman-enhanced')) ?>" href="<?php echo wp_nonce_url("tools.php?page={$this->ID}-tool&amp;action=reset-defaults", 'capsman-reset-defaults'); ?>" onclick="if ( confirm('<?php echo esc_js(__("You are about to reset Roles and Capabilities to WordPress defaults.\n 'Cancel' to stop, 'OK' to reset.", 'capsman-enhanced')); ?>') ) { return true;}return false;"><?php _e('Reset to WordPress defaults', 'capsman-enhanced')?></a>

			</dd>
		</dl>

		<?php agp_admin_footer(); ?>

		</td>

		<td class="sidebar">
			<?php agp_admin_authoring($this->ID); ?>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>
</div>
