<?php
/**
 * General Admin for Capability Manager.
 * Provides admin pages to create and manage roles and capabilities.
 *
 * @version		$Rev: 198515 $
 * @author		Jordi Canals, Kevin Behrens
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals, (C) 2012-2013 Kevin Behrens
 * @license		GNU General Public License version 2
 * @link		http://agapetry.net
 *

	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
	Modifications Copyright 2012-2015, Kevin Behrens <kevin@agapetry.net>
	
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

$roles = $this->roles;
$default = $this->current;

if( defined('PP_ACTIVE') ) {
	require_once( dirname(__FILE__).'/pp-ui.php' );
	$pp_ui = new Capsman_PP_UI();
	$pp_metagroup_caps = $pp_ui->get_metagroup_caps( $default );
} else
	$pp_metagroup_caps = array();
?>
<div class="wrap">
	<?php if( defined('PP_ACTIVE') ) :
		pp_icon();
		$style = 'style="height:60px;"';
	?>
	<?php else:
		$style = '';
	?>
	<div id="icon-capsman-admin" class="icon32"></div>
	<?php endif; ?>
	
	<h1 <?php echo $style;?>><?php _e('Roles and Capabilities', 'capsman-enhanced') ?></h1>
	
	<form method="post" action="admin.php?page=<?php echo $this->ID ?>">
	<?php wp_nonce_field('capsman-general-manager'); ?>
	<fieldset>
	<table id="akmin">
	<tr>
		<td class="content">
		<dl>
			<dt><?php printf(__('Capabilities for %s', 'capsman-enhanced'), $roles[$default]); ?></dt>
			<dd>
				<div>
				<?php _e( 'View and modify capabilities WordPress associates with each role. Changes <strong>remain in the database</strong> even if you deactivate this plugin.', 'capsman-enhanced' ); ?>
				</div>

				<?php
				if ( defined( 'PP_ACTIVE' ) ) {
					$pp_ui->show_capability_hints( $default );
				} else {
					global $capsman;
					$img_url = $capsman->mod_url . '/images/';
				
					echo '<div style="margin-top:5px">';
					_e( "To further customize editing or viewing access, consider stepping up to <a href='#pp-more'>Press Permit</a>.", 'capsman-enhanced' );
					echo '</div>';
					?>
					<script type="text/javascript">
					/* <![CDATA[ */
					jQuery(document).ready( function($) {
						$('a[href=#pp-more]').click( function() {
							$('#pp_features').show();
							return false;
						});
						$('a[href=#pp-hide]').click( function() {
							$('#pp_features').hide();
							return false;
						});
					});
					/* ]]> */
					</script>
					<style>
					#pp_features {display:none;border:1px solid #eee;padding:5px;text-align:center;min-width:600px}
					div.pp-logo { text-align: center }
					div.features-wrap { margin-left: auto; margin-right: auto; text-align: center; width: 540px; }
					ul.pp-features { list-style: none; padding-top:10px; text-align:left; margin-left: auto }
					ul.pp-features li:before { content: "\2713\0020"; }
					ul.pp-features li { padding-bottom: 5px }
					img.cme-play { margin-bottom: -3px; margin-left: 5px;}
					</style>
	
					<?php /* play.png icon by Pavel: http://kde-look.org/usermanager/search.php?username=InFeRnODeMoN */ ?>
					
					<br /><div id="pp_features"><div class="pp-logo"><a href="http://presspermit.com"><img src="<?php echo $img_url;?>pp-logo.png" /></a></div><div class="features-wrap"><ul class="pp-features">
					<li>
					<?php _e( "Automatically define type-specific capabilities for your custom post types and taxonomies", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/regulate-post-type-access" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Assign standard WP roles supplementally for a specific post type", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/regulate-post-type-access" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Assign custom WP roles supplementally for a specific post type <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/custom-role-usage" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Customize reading permissions per-category or per-post", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/category-exceptions" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Customize editing permissions per-category or per-post <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/page-editing-exceptions" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Custom Post Visibility statuses, fully implemented throughout wp-admin <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/custom-post-visibility" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Custom Moderation statuses for access-controlled, multi-step publishing workflow <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/multi-step-moderation" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Regulate permissions for Edit Flow post statuses <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/edit-flow-integration" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Customize the moderated editing of published content with Revisionary or Post Forking <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/published-content-revision" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Grant Spectator, Participant or Moderator access to specific bbPress forums <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/bbpress-exceptions" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "Grant supplemental content permissions to a BuddyPress group <em>(Pro)</em>", 'capsman-enhanced' );?>
					<a href="http://presspermit.com/tutorial/buddypress-content-permissions" target="_blank"><img class="cme-play" src="<?php echo $img_url;?>play.png" /></a></li>
					
					<li>
					<?php _e( "WPML integration to mirror permissions to translations <em>(Pro)</em>", 'capsman-enhanced' );?>
					</li>
					
					<li>
					<?php _e( "Member support forum", 'capsman-enhanced' );?>
					</li>
					
					</ul></div>
					<?php
					echo '<div>';
					printf( __('%1$sgrab%2$s %3$s', 'capsman-enhanced'), '<strong>', '</strong>', '<span class="plugins update-message"><a href="' . cme_plugin_info_url('press-permit-core') . '" class="thickbox" title="' . sprintf( __('%s (free install)', 'capsman-enhanced'), 'Press Permit Core' ) . '">Press&nbsp;Permit&nbsp;Core</a></span>' );
					echo '&nbsp;&nbsp;&bull;&nbsp;&nbsp;';
					printf( __('%1$sbuy%2$s %3$s', 'capsman-enhanced'), '<strong>', '</strong>',  '<a href="http://presspermit.com" target="_blank" title="' . sprintf( __('%s info/purchase', 'capsman-enhanced'), 'Press Permit Pro' ) . '">Press&nbsp;Permit&nbsp;Pro</a>' );
					echo '&nbsp;&nbsp;&bull;&nbsp;&nbsp;';
					echo '<a href="#pp-hide">hide</a>';
					echo '</div></div>';
				}
				
				if ( MULTISITE ) {
					global $wp_roles;
					global $wpdb;
						
					if ( ! empty($_REQUEST['cme_net_sync_role'] ) ) {
						switch_to_blog(1);
						wp_cache_delete( $wpdb->prefix . 'user_roles', 'options' );
					}
						
					( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
				}
				
				global $capsman;
				$capsman->reinstate_db_roles();
				
				$current = get_role($default);
				
				//print_r($current);
				
				$rcaps = $current->capabilities;

				// ========= Begin Kevin B mod ===========
				$is_administrator = current_user_can( 'administrator' );
				
				$custom_types = get_post_types( array( '_builtin' => false ), 'names' );
				$custom_tax = get_taxonomies( array( '_builtin' => false ), 'names' );
				
				$defined = array();
				$defined['type'] = get_post_types( array( 'public' => true ), 'object' );
				$defined['taxonomy'] = get_taxonomies( array( 'public' => true ), 'object' );
				
				$unfiltered['type'] = apply_filters( 'pp_unfiltered_post_types', array('forum','topic','reply') );  // bbPress' dynamic role def requires additional code to enforce stored caps
				$unfiltered['taxonomy'] = apply_filters( 'pp_unfiltered_taxonomies', array( 'post_status', 'topic-tag' ) );  // avoid confusion with Edit Flow administrative taxonomy
				/*
				if ( ( count($custom_types) || count($custom_tax) ) && ( $is_administrator || current_user_can( 'manage_pp_settings' ) ) ) {
					$cap_properties[''] = array();
					$force_distinct_ui = true;
				}
				*/
				
				$cap_properties['edit']['type'] = array( 'edit_posts' );
				
				foreach( $defined['type'] as $type_obj ) {
					if ( 'attachment' != $type_obj->name ) {
						if ( isset( $type_obj->cap->create_posts ) && ( $type_obj->cap->create_posts != $type_obj->cap->edit_posts ) ) {
							$cap_properties['edit']['type'][]= 'create_posts';
							break;
						}
					}
				}
				
				$cap_properties['edit']['type'][]= 'edit_others_posts';
				$cap_properties['edit']['type'] = array_merge( $cap_properties['edit']['type'], array( 'publish_posts', 'edit_published_posts', 'edit_private_posts' ) );
				
				$cap_properties['edit']['taxonomy'] = array( 'manage_terms' );
				
				if ( ! defined( 'PP_ACTIVE' ) )
					$cap_properties['edit']['taxonomy'] = array_merge( $cap_properties['edit']['taxonomy'], array( 'edit_terms', 'assign_terms' ) );
	
				$cap_properties['delete']['type'] = array( 'delete_posts', 'delete_others_posts' );
				$cap_properties['delete']['type'] = array_merge( $cap_properties['delete']['type'], array( 'delete_published_posts', 'delete_private_posts' ) );
				
				if ( ! defined( 'PP_ACTIVE' ) )
					$cap_properties['delete']['taxonomy'] = array( 'delete_terms' );
				else
					$cap_properties['delete']['taxonomy'] = array();
	
				$cap_properties['read']['type'] = array( 'read_private_posts' );
				$cap_properties['read']['taxonomy'] = array();
	
				$stati = get_post_stati( array( 'internal' => false ) );
	
				//if ( count($stati) > 5 ) {
					$cap_type_names = array(
						'' => __( '&nbsp;', 'capsman-enhanced' ),
						'read' => __( 'Reading', 'capsman-enhanced' ),
						'edit' => __( 'Editing Capabilities', 'capsman-enhanced' ),
						'delete' => __( 'Deletion Capabilities', 'capsman-enhanced' )
					);
	
				//} else {
					
				//}
	
				$cap_tips = array( 
					'read_private' => __( 'can read posts which are currently published with private visibility', 'capsman-enhanced' ),
					'edit' => __( 'has basic editing capability (but may need other capabilities based on post status and ownership)', 'capsman-enhanced' ),
					'edit_others' => __( 'can edit posts which were created by other users', 'capsman-enhanced' ),
					'edit_published' => __( 'can edit posts which are currently published', 'capsman-enhanced' ),
					'edit_private' => __( 'can edit posts which are currently published with private visibility', 'capsman-enhanced' ),
					'publish' => __( 'can make a post publicly visible', 'capsman-enhanced' ),
					'delete' => __( 'has basic deletion capability (but may need other capabilities based on post status and ownership)', 'capsman-enhanced' ),
					'delete_others' => __( 'can delete posts which were created by other users', 'capsman-enhanced' ),
					'delete_published' => __( 'can delete posts which are currently published', 'capsman-enhanced' ),
					'delete_private' => __( 'can delete posts which are currently published with private visibility', 'capsman-enhanced' ),
				);
	
				$default_caps = array( 'read_private_posts', 'edit_posts', 'edit_others_posts', 'edit_published_posts', 'edit_private_posts', 'publish_posts', 'delete_posts', 'delete_others_posts', 'delete_published_posts', 'delete_private_posts',
									   'read_private_pages', 'edit_pages', 'edit_others_pages', 'edit_published_pages', 'edit_private_pages', 'publish_pages', 'delete_pages', 'delete_others_pages', 'delete_published_pages', 'delete_private_pages',
									   'manage_categories'
									   );
				$type_caps = array();
				
				// Role Scoper and PP1 adjust attachment access based only on user's capabilities for the parent post
				if ( defined('SCOPER_VERSION') || ( defined( 'PP_ACTIVE' ) && ! defined( 'PPC_VERSION' ) ) )
					unset( $defined['type']['attachment'] );

				echo '<ul class="cme-listhoriz">';
				
				// cap_types: read, edit, deletion
				foreach( array_keys($cap_properties) as $cap_type ) {
					echo '<li>';
					echo '<h3>' . $cap_type_names[$cap_type] . '</h3>';
					echo '<table class="cme-typecaps">';
					
					foreach( array_keys($defined) as $item_type ) {
						if ( ( 'delete' == $cap_type ) && ( 'taxonomy' == $item_type ) ) {
							if ( defined('SCOPER_VERSION') || defined('PP_ACTIVE') )
								continue;
								
							$any_term_deletion_caps = false;
							foreach( array_keys($defined['taxonomy']) as $_tax ) {
								if ( isset( $defined['taxonomy'][$_tax]->cap->delete_terms ) && ( 'manage_categories' != $defined['taxonomy'][$_tax]->cap->delete_terms ) && ! in_array( $_tax, $unfiltered['taxonomy'] ) ) {
									$any_term_deletion_caps = true;
									break;
								}
							}
							
							if ( ! $any_term_deletion_caps )
								continue;
						}
						
						//if ( ! $cap_type ) {

						//} else {
							echo '<th></th>';
						
							if ( ! count( $cap_properties[$cap_type][$item_type] ) )
								continue;
						
							// label cap properties
							foreach( $cap_properties[$cap_type][$item_type] as $prop ) {
								$prop = str_replace( '_posts', '', $prop );
								$prop = str_replace( '_pages', '', $prop );
								$prop = str_replace( '_terms', '', $prop );
								$tip = ( isset( $cap_tips[$prop] ) ) ? "title='{$cap_tips[$prop]}'" : '';
								$prop = str_replace( '_', '<br />', $prop );
								$th_class = ( 'taxonomy' == $item_type ) ? ' class="term-cap"' : ' class="post-cap"';
								echo "<th $tip{$th_class}>";
								echo ucwords($prop);
								echo '</th>';
							}

							foreach( $defined[$item_type] as $key => $type_obj ) {
								if ( in_array( $key, $unfiltered[$item_type] ) )
									continue;
								
								$row = "<tr class='cme_type_{$key}'>";
								
								if ( $cap_type ) {
									if ( empty($force_distinct_ui) && empty( $cap_properties[$cap_type][$item_type] ) )
										continue;
								
									$row .= "<td><a class='cap_type' href='#toggle_type_caps'>" . $type_obj->labels->name . '</a>';
									$row .= '<a href="#" class="neg-type-caps">&nbsp;x&nbsp;</a>';
									$row .= '</td>';
								
									$display_row = ! empty($force_distinct_ui);

									foreach( $cap_properties[$cap_type][$item_type] as $prop ) {
										$td_classes = array();
										$checkbox = '';
										
										if ( ! empty($type_obj->cap->$prop) && ( in_array( $type_obj->name, array( 'post', 'page' ) ) 
										|| ! in_array( $type_obj->cap->$prop, $default_caps ) 
										|| ( ( 'manage_categories' == $type_obj->cap->$prop ) && ( 'manage_terms' == $prop ) && ( 'category' == $type_obj->name ) ) ) ) {
			
											// if edit_published or edit_private cap is same as edit_posts cap, don't display a checkbox for it
											if ( ( ! in_array( $prop, array( 'edit_published_posts', 'edit_private_posts', 'create_posts' ) ) || ( $type_obj->cap->$prop != $type_obj->cap->edit_posts ) ) 
											&& ( ! in_array( $prop, array( 'delete_published_posts', 'delete_private_posts' ) ) || ( $type_obj->cap->$prop != $type_obj->cap->delete_posts ) )
											) {
												$cap_name = $type_obj->cap->$prop;
												
												if ( 'taxonomy' == $item_type )
													$td_classes []= "term-cap";
												else
													$td_classes []= "post-cap";
												
												if ( ! empty($pp_metagroup_caps[$cap_name]) )
													$td_classes []='cm-has-via-pp';	
											
												if ( $is_administrator || current_user_can($cap_name) ) {
													if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
														$title_text = sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name );
													} else {
														$title_text = $cap_name;
													}
													
													$disabled = '';
													$checked = checked(1, ! empty($rcaps[$cap_name]), false );
													
													$checkbox = '<input id=caps[' . $cap_name . '] type="checkbox" title="' . $title_text . '" name="caps[' . $cap_name . ']" value="1" ' . $checked . $disabled . ' />';
													$type_caps [$cap_name] = true;
													$display_row = true;
												} 
											} else
												$td_classes []= "cap-unreg";
											
											if ( isset($rcaps[$cap_name]) && empty($rcaps[$cap_name]) )
												$td_classes []= "cap-neg";
										} else
											$td_classes []= "cap-unreg";
										
										$td_class = ( $td_classes ) ? 'class="' . implode(' ', $td_classes) . '"' : '';
										
										$row .= "<td $td_class><span class='cap-x'>X</span>$checkbox";
										
										if ( false !== strpos( $td_class, 'cap-neg' ) )
											$row .= '<input type="hidden" class="cme-negation-input" name="caps[' . $cap_name . ']" value="" />';

										$row .= "</td>";
									}
								}
								
								if ( $display_row ) {
									$row .= '</tr>';
									echo $row;
								}
							}
						//} // endif this iteration is for type caps checkbox display
					
					} // end foreach item type
					
					echo '</table>';
					
					echo '</li>';
				}

				echo '</ul>';
				
				// clicking on post type name toggles corresponding checkbox selections
				?>
				<script type="text/javascript">
				/* <![CDATA[ */
				jQuery(document).ready( function($) {
					$('a[href="#toggle_type_caps"]').click( function() {
						var chks = $(this).closest('tr').find('input');
						$(chks).prop( 'checked', ! $(chks).first().is(':checked') );
						return false;
					});
				});
				/* ]]> */
				</script>
				<?php

				$core_caps = array_fill_keys( array( 'switch_themes', 'edit_themes', 'activate_plugins', 'edit_plugins', 'edit_users', 'edit_files', 'manage_options', 'moderate_comments', 
					'manage_links', 'upload_files', 'import', 'unfiltered_html', 'read', 'delete_users', 'create_users', 'unfiltered_upload', 'edit_dashboard',
					'update_plugins', 'delete_plugins', 'install_plugins', 'update_themes', 'install_themes', 
					'update_core', 'list_users', 'remove_users', 'add_users', 'promote_users', 'edit_theme_options', 'delete_themes', 'export' ), true );
					
				ksort( $core_caps );
				
				echo '<p>&nbsp;</p><h3>' . __( 'Other WordPress Core Capabilities', 'capsman-enhanced' ) . '</h3>';
				echo '<table width="100%" class="form-table cme-checklist"><tr>';
				
				
				$checks_per_row = get_option( 'cme_form-rows', 5 );
				$i = 0; $first_row = true;

				foreach( array_keys($core_caps) as $cap_name ) {
					if ( ! $is_administrator && ! current_user_can($cap_name) )
						continue;
				
					if ( $i == $checks_per_row ) {
						echo '</tr><tr>';
						$i = 0;
					}

					if ( ! isset( $rcaps[$cap_name] ) )
						$class = 'cap-no';
					else
						$class = ( $rcaps[$cap_name] ) ? 'cap-yes' : 'cap-neg';
					
					if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
						$class .= ' cap-metagroup';
						$title_text = sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name );
					} else {
						$title_text = $cap_name;
					}
					
					$disabled = '';
					$checked = checked(1, ! empty($rcaps[$cap_name]), false );
					?>
					<td class="<?php echo $class; ?>"><span class="cap-x">X</span><label for="caps[<?php echo $cap_name; ?>]" title="<?php echo $title_text;?>"><input id=caps[<?php echo $cap_name; ?>] type="checkbox" name="caps[<?php echo $cap_name; ?>]" value="1" <?php echo $checked . $disabled;?> />
					<span>
					<?php
					echo str_replace( '_', ' ', $cap_name );
					?>
					</span></label><a href="#" class="neg-cap">&nbsp;x&nbsp;</a>
					<?php if ( false !== strpos( $class, 'cap-neg' ) ) :?>
						<input type="hidden" class="cme-negation-input" name="caps[<?php echo $cap_name; ?>]" value="" />
					<?php endif; ?>
					</td>
				
					<?php
					++$i;
				}
				
				if ( $i == $checks_per_row ) {
					echo '</tr><tr>';
					$i = 0;
				} elseif ( ! $first_row ) {
					// Now close a wellformed table
					for ( $i; $i < $checks_per_row; $i++ ){
						echo '<td>&nbsp;</td>';
					}
					echo '</tr>';
				}
				?>
				
				<tr class="cme-bulk-select">
				<td colspan="<?php echo $checks_per_row;?>">
				<span style="float:right">
				<input type="checkbox" class="cme-check-all" title="<?php _e('check/uncheck all', 'capsman-enhanced');?>">&nbsp;&nbsp;<a class="cme-neg-all" href="#" title="<?php _e('negate all (storing as disabled capabilities)', 'capsman-enhanced');?>">X</a> <a class="cme-switch-all" href="#" title="<?php _e('negate none (add/remove all capabilities normally)', 'capsman-enhanced');?>">X</a>
				</span>
				</td></tr>
				
				</table>
				
				<?php
				echo '<p>&nbsp;</p><h3>' . __( 'Additional Capabilities', 'capsman-enhanced' ) . '</h3>';
	
				?>
				<table width='100%' class="form-table cme-checklist">
				<tr>
				<?php
				$i = 0; $first_row = true;
				
				$all_capabilities = apply_filters( 'capsman_get_capabilities', array_keys( $this->capabilities ), $this->ID );
				$all_capabilities = apply_filters( 'members_get_capabilities', $all_capabilities );
				
				foreach( $all_capabilities as $cap_name ) {
					if ( ! isset($this->capabilities[$cap_name]) ) 
						$this->capabilities[$cap_name] = str_replace( '_', ' ', $cap_name );
				}

				uasort( $this->capabilities, 'strnatcasecmp' );  // sort by array values, but maintain keys );
				
				foreach ( $this->capabilities as $cap_name => $cap ) :
					if ( isset( $type_caps[$cap_name] ) || isset($core_caps[$cap_name]) )
						continue;
				
					if ( ! $is_administrator && ! current_user_can($cap_name) )
						continue;
				
					// ============ End Kevin B mod ===============
				
					// Levels are not shown.
					if ( preg_match( '/^level_(10|[0-9])$/i', $cap_name ) ) {
						continue;
					}

					if ( $i == $checks_per_row ) {
						echo '</tr><tr>';
						$i = 0; $first_row = false;
					}
					
					if ( ! isset( $rcaps[$cap_name] ) )
						$class = 'cap-no';
					else
						$class = ( $rcaps[$cap_name] ) ? 'cap-yes' : 'cap-neg';
					
					if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
						$class .= ' cap-metagroup';
						$title_text = sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name );
					} else {
						$title_text = $cap_name;
					}
					
					$disabled = '';
					$checked = checked(1, ! empty($rcaps[$cap_name]), false );
					
					if ( 'manage_capabilities' == $cap_name ) {
						if ( ! current_user_can('administrator') ) {
							continue;
						} elseif ( 'administrator' == $default ) {
							$class .= ' cap-locked';
							$lock_manage_caps_capability = true;
							$disabled = 'disabled="disabled"';
						}
					}
				?>
					<td class="<?php echo $class; ?>"><span class="cap-x">X</span><label for="caps[<?php echo $cap_name; ?>]" title="<?php echo $title_text;?>"><input id=caps[<?php echo $cap_name; ?>] type="checkbox" name="caps[<?php echo $cap_name; ?>]" value="1" <?php echo $checked . $disabled;?> />
					<span>
					<?php
					echo str_replace( '_', ' ', $cap );
					?>
					</span></label><a href="#" class="neg-cap">&nbsp;x&nbsp;</a>
					<?php if ( false !== strpos( $class, 'cap-neg' ) ) :?>
						<input type="hidden" class="cme-negation-input" name="caps[<?php echo $cap_name; ?>]" value="" />
					<?php endif; ?>
					</td>
				<?php
					$i++;
				endforeach;

				if ( ! empty($lock_manage_caps_capability) ) {
					echo '<input type="hidden" name="caps[manage_capabilities]" value="1" />';
				}
				
				if ( $i == $checks_per_row ) {
					echo '</tr><tr>';
					$i = 0;
				} else {
					if ( ! $first_row ) {
						// Now close a wellformed table
						for ( $i; $i < $checks_per_row; $i++ ){
							echo '<td>&nbsp;</td>';
						}
						echo '</tr>';
					}
				}
				?>
				
				<tr class="cme-bulk-select">
				<td colspan="<?php echo $checks_per_row;?>">
				<span>
				<?php
				$level = ak_caps2level($rcaps);
				?>
				<?php _e('Level:', 'capsman-enhanced') ;?><select name="level">
				<?php for ( $l = $this->max_level; $l >= 0; $l-- ) {?>
						<option value="<?php echo $l; ?>" style="text-align:right;"<?php selected($level, $l); ?>>&nbsp;<?php echo $l; ?>&nbsp;</option>
					<?php }
					?>
				</select>
				</span>
				
				<span style="float:right">
				<input type="checkbox" class="cme-check-all" title="<?php _e('check/uncheck all', 'capsman-enhanced');?>">&nbsp;&nbsp;<a class="cme-neg-all" href="#" title="<?php _e('negate all (storing as disabled capabilities)', 'capsman-enhanced');?>">X</a> <a class="cme-switch-all" href="#" title="<?php _e('negate none (add/remove all capabilities normally)', 'capsman-enhanced');?>">X</a>
				</span>
				</td></tr>
				
				</table>
				
				<br />
				<?php if ( ! defined('PP_ACTIVE') || pp_get_option('display_hints') ) :?>
				<div class="cme-subtext">
					<?php _e( 'Note: Underscores replace spaces in stored capability name ("edit users" => "edit_users").', 'capsman-enhanced' ); ?>
				</div>
				<?php endif;?>
				</span>
				
			</dd>
		</dl>

		<?php
		$support_pp_only_roles = ( defined('PP_ACTIVE') ) ? $pp_ui->pp_only_roles_ui( $default ) : false;
		cme_network_role_ui( $default );
		?>
		
		<p class="submit">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="current" value="<?php echo $default; ?>" />
			<input type="submit" name="SaveRole" value="<?php _e('Save Changes', 'capsman-enhanced') ?>" class="button-primary" /> &nbsp;
			
			<?php if ( current_user_can('administrator') && 'administrator' != $default ) : ?>
				<a class="ak-delete" title="<?php echo esc_attr(__('Delete this role', 'capsman-enhanced')) ?>" href="<?php echo wp_nonce_url("admin.php?page={$this->ID}&amp;action=delete&amp;role={$default}", 'delete-role_' . $default); ?>" onclick="if ( confirm('<?php echo esc_js(sprintf(__("You are about to delete the %s role.\n 'Cancel' to stop, 'OK' to delete.", 'capsman-enhanced'), $roles[$default])); ?>') ) { return true;}return false;"><?php _e('Delete Role', 'capsman-enhanced')?></a>
			<?php endif; ?>
		</p>
		
		<br />
		<?php agp_admin_footer(); ?>
		<br />
		
		</td>
		<td class="sidebar">
			<?php agp_admin_authoring($this->ID); ?>

			<dl>
				<dt><?php if ( defined('WPLANG') && WPLANG ) _e('Select New Role', 'capsman-enhanced'); else echo('Select Role to View / Edit'); ?></dt>
				<dd style="text-align:center;">
					<p><select name="role">
					<?php
					foreach ( $roles as $role => $name ) {
						echo '<option value="' . $role .'"'; selected($default, $role); echo '> ' . $name . ' &nbsp;</option>';
					}
					?>
					</select><span style="margin-left:20px"><input type="submit" name="LoadRole" value="<?php if ( defined('WPLANG') && WPLANG ) _e('Change', 'capsman-enhanced'); else echo('Load'); ?>" class="button" /></span></p>
				</dd>
			</dl>
			
			<dl>
				<dt><?php _e('Create New Role', 'capsman-enhanced'); ?></dt>
				<dd style="text-align:center;">
					<?php $class = ( $support_pp_only_roles ) ? 'tight-text' : 'regular-text'; ?>
					<p><input type="text" name="create-name"" class="<?php echo $class;?>" placeholder="<?php _e('Name of new role', 'capsman-enhanced') ?>" />
					
					<?php if( $support_pp_only_roles ) : ?>
					<label for="new_role_pp_only" title="<?php _e('Make role available for supplemental assignment to Permission Groups only', 'capsman-enhanced');?>"> <input type="checkbox" name="new_role_pp_only" id="new_role_pp_only" value="1"> <?php _e('hidden', 'capsman-enhanced'); ?> </label>
					<?php endif; ?>
					
					<br />
					<input type="submit" name="CreateRole" value="<?php _e('Create', 'capsman-enhanced') ?>" class="button" />
					</p>
				</dd>
			</dl>

			<dl>
				<dt><?php defined('WPLANG') && WPLANG ? _e('Copy this role to', 'capsman-enhanced') : printf( 'Copy %s Role', $roles[$default] ); ?></dt>
				<dd style="text-align:center;">
					<?php $class = ( $support_pp_only_roles ) ? 'tight-text' : 'regular-text'; ?>
					<p><input type="text" name="copy-name"  class="<?php echo $class;?>" placeholder="<?php _e('Name of copied role', 'capsman-enhanced') ?>" />
					
					<?php if( $support_pp_only_roles ) : ?>
					<label for="copy_role_pp_only" title="<?php _e('Make role available for supplemental assignment to Permission Groups only', 'capsman-enhanced');?>"> <input type="checkbox" name="copy_role_pp_only" id="copy_role_pp_only" value="1"> <?php _e('hidden', 'capsman-enhanced'); ?> </label>
					<?php endif; ?>
					
					<br />
					<input type="submit" name="CopyRole" value="<?php _e('Copy', 'capsman-enhanced') ?>" class="button" />
					</p>
				</dd>
			</dl>

			<dl>
				<dt><?php _e('Add Capability', 'capsman-enhanced'); ?></dt>
				<dd style="text-align:center;">
					<p><input type="text" name="capability-name" class="regular-text" placeholder="<?php _e('capability name', 'capsman-enhanced') ?>" /><br />
					<input type="submit" name="AddCap" value="<?php _e('Add to role', 'capsman-enhanced') ?>" class="button" /></p>
				</dd>
			</dl>
			
			<?php if ( defined('PP_ACTIVE') )
				$pp_ui->pp_types_ui( $defined );
			?>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>
</div>

<?php
function cme_network_role_ui( $default ) {
	if ( ! is_multisite() || ! is_super_admin() || ( 1 != get_current_blog_id() ) )
		return false;
	?>

	<div style="float:right;margin-left:10px;margin-right:10px">
		<?php
		if ( ! $autocreate_roles = get_site_option( 'cme_autocreate_roles' ) )
			$autocreate_roles = array();
		
		$checked = ( in_array( $default, $autocreate_roles ) ) ? 'checked="checked"': '';
		?>
		<div style="margin-bottom: 5px">
		<label for="cme_autocreate_role" title="<?php _e('Create this role definition in new (future) sites', 'capsman-enhanced');?>"><input type="checkbox" name="cme_autocreate_role" id="cme_autocreate_role" value="1" <?php echo $checked;?>> <?php _e('include in new sites', 'capsman-enhanced'); ?> </label>
		</div>
		<div>
		<label for="cme_net_sync_role" title="<?php echo esc_attr(__('Copy / update this role definition to all sites now', 'capsman-enhanced'));?>"><input type="checkbox" name="cme_net_sync_role" id="cme_net_sync_role" value="1"> <?php _e('sync role to all sites now', 'capsman-enhanced'); ?> </label>
		</div>
	</div>
<?php
	return true;
}

function cme_plugin_info_url( $plugin_slug ) {
	return self_admin_url( "plugin-install.php?tab=plugin-information&plugin=$plugin_slug&TB_iframe=true&width=640&height=678" );
}