<?php

class Capsman_PP_UI {

	function __construct() {
	
	}

	function get_metagroup_caps( $default ) {
		global $wpdb;

		if ( defined( 'PPC_VERSION' ) ) {
			$query = $wpdb->prepare( "SELECT role_name FROM $wpdb->ppc_roles AS r INNER JOIN $wpdb->pp_groups AS g ON g.ID = r.agent_id AND r.agent_type = 'pp_group' WHERE g.metagroup_type = 'wp_role' AND g.metagroup_id = %s", $default );
			$pp_supplemental_roles = $wpdb->get_col( $query );
		} else {
			$query = $wpdb->prepare( "SELECT role_name FROM $wpdb->pp_roles AS r INNER JOIN $wpdb->pp_groups AS g ON g.ID = r.group_id AND r.group_type = 'pp_group' AND r.scope = 'site' WHERE g.metagroup_type = 'wp_role' AND g.metagroup_id = %s", $default );
			$pp_supplemental_roles = $wpdb->get_col( $query );
		}

		$pp_filtered_types = pp_get_enabled_types('post');
		$pp_metagroup_caps = array();
		$pp_cap_caster = pp_init_cap_caster();

		foreach( $pp_supplemental_roles as $_role_name ) {
			$role_specs = explode( ':', $_role_name );
			if ( empty($role_specs[2]) || ! in_array( $role_specs[2], $pp_filtered_types ) )
				continue;

			// add all type-specific caps whose base property cap is included in this pattern role
			// i.e. If 'edit_posts' is in the pattern role, grant $type_obj->cap->edit_posts
			$pp_metagroup_caps = array_merge( $pp_metagroup_caps, array_fill_keys( $pp_cap_caster->get_typecast_caps( $_role_name, 'site' ), true ) );
		}
	
		return $pp_metagroup_caps;
	}
	
	function show_capability_hints( $default ) {					
		if ( pp_get_option('display_hints') ) {
			$cme_id = 'capsman';
		
			echo '<ul class="ul-disc" style="margin-top:10px">';
			
			if ( defined( 'PPCE_VERSION' ) || ! defined( 'PPC_VERSION' ) || in_array( $default, array( 'subscriber', 'contributor', 'author', 'editor' ) ) ) {
				echo '<li>';
				if ( defined( 'PPCE_VERSION' ) || ! defined( 'PPC_VERSION' ) ) {
					if ( pp_get_option( 'advanced_options' ) )
						$parenthetical = ' (' . sprintf( __( 'see %1$sRole Usage%2$s: "Pattern Roles"', 'capsman-enhanced' ), "<a href='" . admin_url('admin.php?page=pp-role-usage') . "'>", '</a>' ) . ')';
					else
						$parenthetical = ' (' . sprintf( __( 'activate %1$sAdvanced settings%2$s, see Role Usage', 'capsman-enhanced' ), "<a href='" . admin_url('admin.php?page=pp-settings&pp_tab=advanced') . "'>", '</a>' ). ')';
				} else
					$parenthetical = '';

				if ( defined( 'PPC_VERSION' ) )
					printf( __( '"Posts" capabilities selected here also define type-specific role assignment for Permission Groups%s.', $cme_id ), $parenthetical ) ;
				else
					printf( __( '"Posts" capabilities selected here also define type-specific role assignment for Permit Groups%s.', $cme_id ), $parenthetical ) ;

				echo '</li>';
			}
			
			$status_hint = '';
			if ( defined( 'PPC_VERSION' ) )
				if ( defined( 'PPS_VERSION' ) )
					$status_hint = sprintf( __( 'Capabilities for custom statuses can be manually added here (see Post Statuses > Status > Capability Mapping for applicable names). However, it is usually more convenient to use Permission Groups to assign a supplemental status-specific role.', $cme_id ), "<a href='" . admin_url('?page=pp-role-usage') . "'>", '</a>' ) ;
				elseif ( pp_get_option( 'display_extension_hints' ) )
					$status_hint = sprintf( __( 'Capabilities for custom statuses can be manually added here. Or activate the PP Custom Post Statuses extension to assign status-specific supplemental roles.', $cme_id ), "<a href='" . admin_url('?page=pp-role-usage') . "'>", '</a>' ) ;
			
			elseif ( defined( 'PP_VERSION' ) )
				$status_hint = sprintf( __( 'Capabilities for custom statuses can be manually added to a role here (see Conditions > Status > Capability Mapping for applicable names). However, it is usually more convenient to use Permit Groups to assign a supplemental status-specific role.', $cme_id ), "<a href='" . admin_url('?page=pp-role-usage') . "'>", '</a>' ) ;
			
			if ( $status_hint )
				echo "<li>$status_hint</li>";

			echo '</ul>';
		}
	}
	
	function pp_only_roles_ui( $default ) {
		$support_pp_only_roles = defined('PPC_VERSION') || version_compare( PP_VERSION, '1.0-beta1.4', '>=');
		?>
		
		<?php if ( $support_pp_only_roles && ! in_array( $default, array( /*'subscriber', 'contributor', 'author', 'editor',*/ 'administrator' ) ) ) : ?>
		<div style="float:right">
			<?php
			pp_refresh_options();
			$pp_only = (array) pp_get_option( 'supplemental_role_defs' );
			$checked = ( in_array( $default, $pp_only ) ) ? 'checked="checked"': '';
			?>
			<label for="pp_only_role" title="<?php _e('Make role available for supplemental assignment to Permission Groups only', 'capsman-enhanced');?>"><input type="checkbox" name="pp_only_role" id="pp_only_role" value="1" <?php echo $checked;?>> <?php _e('hidden role', 'capsman-enhanced'); ?> </label>
		</div>
		<?php endif; ?>
	<?php
		return $support_pp_only_roles;
	}
	
	function pp_types_ui( $defined ) {
		if ( current_user_can( 'pp_manage_settings' ) ) :?>
		<dl>
			<dt><?php _e('Force Type-Specific Capabilities', 'capsman-enhanced'); ?></dt>
			<dd style="text-align:center;">
				<?php
				$caption = __( 'Force unique capability names for:', 'capsman-enhanced' );
				echo "<p>$caption</p>";
				
				if ( pp_get_option( 'display_hints' ) ) :?>
				<div class="cme-subtext" style="margin-top:0">
				<?php _e( '(PP Filtered Post Types, Taxonomies)', 'capsman-enhanced' );?>
				</div>
				<?php endif;
				
				echo "<table style='width:100%'><tr>";
				
				$unfiltered['type'] = apply_filters( 'pp_unfiltered_post_types', array('forum','topic','reply') );			// bbPress' dynamic role def requires additional code to enforce stored caps
				$unfiltered['taxonomy'] = apply_filters( 'pp_unfiltered_taxonomies', array( 'post_status', 'topic-tag' ) );  // avoid confusion with Edit Flow administrative taxonomy
				$hidden['type'] = apply_filters( 'pp_hidden_post_types', array() );
				$hidden['taxonomy'] = apply_filters( 'pp_hidden_taxonomies', array() );
				
				foreach( array_keys($defined) as $item_type ) {	
					echo '<td style="width:50%">';
					$option_name = ( 'taxonomy' == $item_type ) ? 'enabled_taxonomies' : 'enabled_post_types';

					$enabled = pp_get_option( $option_name );
					
					foreach( $defined[$item_type] as $key => $type_obj ) {
						if ( ! $key )
							continue;

						if ( in_array( $key, $unfiltered[$item_type] ) )
							continue;
							
						$id = "$option_name-" . $key;
						?>
						<div style="text-align:left">
						<?php if ( ! empty( $hidden[$item_type][$key] ) ) :?>
							<input name="<?php echo($id);?>" type="hidden" id="<?php echo($id);?>" value="1" />
							<input name="<?php echo("{$option_name}-options[]");?>" type="hidden" value="<?php echo($key)?>" />
						
						<?php else: ?>
							<div class="agp-vspaced_input">
							<label for="<?php echo($id);?>" title="<?php echo($key);?>">
							<input name="<?php echo("{$option_name}-options[]");?>" type="hidden" value="<?php echo($key)?>" />
							<input name="<?php echo($id);?>" type="checkbox" id="<?php echo($id);?>" value="1" <?php checked('1', ! empty($enabled[$key]) );?> /> <?php echo($type_obj->label);?>
							
							<?php 
							echo ('</label></div>');

						endif;  // displaying checkbox UI
						
						echo '</div>';
					}
					echo '</td>';
				}
				?>
				</tr>
				</table>
				
				<?php if( pp_wp_ver( '3.5' ) ) :
					$define_create_posts_cap = pp_get_option( 'define_create_posts_cap' );
				?>
					<div>
					<label for="pp_define_create_posts_cap">
					<input name="pp_define_create_posts_cap" type="checkbox" id="pp_define_create_posts_cap" value="1" <?php checked('1', $define_create_posts_cap );?> /> <?php _e('Use create_posts capability');?>
					</label>
					</div>
				<?php endif; ?>
				
				<input type="submit" name="update_filtered_types" value="<?php _e('Update', 'capsman-enhanced') ?>" class="button" />
			</dd>
		</dl>
		<?php endif;
	}
}

