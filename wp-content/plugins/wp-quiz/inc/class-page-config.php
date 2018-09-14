<?php
/**
 * Class For WP_Quiz Configuration page
 */
class WP_Quiz_Page_Config {

	public static function admin_print_styles() {

		wp_enqueue_style( 'semantic-checkbox-css', wp_quiz()->plugin_url() . 'assets/css/checkbox.min.css', array(), wp_quiz()->version );
		wp_enqueue_style( 'chosen-css', wp_quiz()->plugin_url() . 'assets/css/chosen.min.css', array(), wp_quiz()->version );
		wp_enqueue_style( 'pro-popup-css', wp_quiz()->plugin_url() . 'assets/css/pro-popup.css', array(), wp_quiz()->version );
		?>
			<style type="text/css" media="screen">
				table#quiz_type_settings input[type=checkbox], table#global_settings input[type=checkbox] { float: right; }
				#config-page input[type=text]{ width: 100%; }
				#config-page label{ display: block; font-size: 14px; color: #666; }
				table#quiz_type_settings tr td , table#global_settings tr td { padding: 10px; color: #666; font-size: 14px; padding-left: 0; }
				table#quiz_type_settings tr td .ui, table#global_settings tr td .ui { float: right; }
				table#quiz_type_settings tr td #select, table#quiz_type_settings tr td input, table#global_settings tr td input { border-radius: 2px; width: 50%; }
				table#quiz_type_settings, table#global_settings { border: none; }
				#ad_code_setting .add-new-h2 { top: 0; position: relative; margin-bottom: 5px; float: left; clear: both; }
				.ad_row{ margin-bottom: 25px; }
				.ad_row textarea{ width: 100%; }
				.ad_action a{ text-decoration: none; color: #a00; }
				.ad_action a:hover, .ad_action a:active{ color: red; text-decoration: none; }
				.chosen-container{ float: right; width: 100% !important; }
				.ui.toggle.checkbox.disabled label:hover::before{ background: rgba(0,0,0,.05); }
				.pro-text { color: #00cc66; }
				small { color: #a7a7a7; }
				.ui.toggle.checkbox .box, .ui.toggle.checkbox label { padding-left: 4.15em!important }
				.disabled span{ pointer-events: none; }
				#analytics_content div:first-of-type{ margin-bottom: 10px; }
			</style>
		<?php
	}

	public static function save_post_form() {

		//set default options if button clicked
		if ( isset( $_POST['submit'] ) ) {

			$settings_key = array( 'rand_questions', 'rand_answers', 'restart_questions', 'promote_plugin', 'show_ads', 'auto_scroll', 'share_meta' );

			foreach ( $settings_key as  $key ) {
				if ( isset( $_POST['defaults'][ $key ] )  && '1' == $_POST['defaults'][ $key ] ) {
					$_POST['defaults'][ $key ] = 1;
				} else {
					$_POST['defaults'][ $key ] = 0;
				}
			}

			$settings = array(
				'defaults' => $_POST['defaults'],
			);

			update_option( 'wp_quiz_default_settings', $settings );
		}
	}

	public static function display_messages() {

		$message = false;
		if ( isset( $_REQUEST['message'] ) && ( $msg = (int) $_REQUEST['message'] ) ) {
			if ( 3 === $msg ) {
				$message = esc_html__( 'Settings saved', 'wp-quiz' );
			}
		}
		$class = isset( $_REQUEST['error'] ) ? 'error' : 'updated';

		if ( $message ) :
		?>
			<div id="message" class="<?php echo $class; ?> notice is-dismissible"><p><?php echo $message; ?></p></div>
		<?php
		endif;
	}

	public static function load() {

		// Needed javascripts to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		wp_enqueue_script( 'semantic-checkbox-js', wp_quiz()->plugin_url() . 'assets/js/checkbox.min.js', array( 'jquery' ), wp_quiz()->version, false );
		wp_enqueue_script( 'chosen-js', wp_quiz()->plugin_url() . 'assets/js/chosen.jquery.min.js', array( 'jquery' ), wp_quiz()->version, false );

		$screen = get_current_screen();
		add_meta_box( 'google-analytics-content', __( 'Google Analytics', 'wp-quiz' ), array( __CLASS__, 'google_analytics_content' ), $screen->id, 'normal', 'core' );
		add_meta_box( 'default-quiz-settings', __( 'Default Quiz Settings', 'wp-quiz' ), array( __CLASS__, 'default_settings_content' ), $screen->id, 'normal', 'core' );
		add_meta_box( 'ad-code', __( 'Ad Code', 'wp-quiz' ), array( __CLASS__, 'ad_code_content' ), $screen->id, 'normal', 'core' );
		add_meta_box( 'global-settings', __( 'Global', 'wp-quiz' ), array( __CLASS__, 'global_settings_content' ), $screen->id, 'normal', 'core' );
	}

	public static function page() {

		$screen = get_current_screen();
		$columns = absint( $screen->get_columns() );
		$columns_css = '';
		if ( $columns ) {
			$columns_css = " columns-$columns";
		}
		?>
			<div class="wrap" id="config-page">
				<h2><?php esc_html_e( 'General Settings', 'wp-quiz' ); ?></h2>
				<?php self::display_messages(); ?>
				<form action="<?php echo admin_url( 'admin-post.php?action=wp_quiz' ); ?>" method="post">
					<?php wp_nonce_field( 'wp_quiz_config_page' ); ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
					<input type="hidden" name="page" value="wp_quiz_config" />
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="postbox-container-2" class="postbox-container">
							<?php
								$settings = get_option( 'wp_quiz_default_settings' );
								do_meta_boxes( $screen->id, 'normal', $settings );
							?>
							</div>
							<div id="postbox-container-1" class="postbox-container">
								<a href="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=General+Settings+Banner&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg" target="_blank"><img  style="width:100%" src="<?php echo wp_quiz()->plugin_url() . 'assets/image/wp-quiz-pro.jpg' ?>" /></a>
								<a href="https://community.mythemeshop.com/?utm_source=WP+Quiz+Free&utm_medium=General+Settings+Banner&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg" target="_blank"><img  style="width:100%" src="<?php echo wp_quiz()->plugin_url() . 'assets/image/have-a-question.jpg' ?>" /></a>
							</div>
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'wp-quiz' ); ?>">&nbsp;
								<!-- <input type="submit" name="default_settings" id="default_settings" class="button-secondary" value="Reset all settings to default">-->
							</p>
							<?php include_once( wp_quiz()->plugin_dir() . '/inc/pro-popup-template.php' ); ?>
						</div>
					</div>
				</form>
			</div>
			<script type="text/javascript">
				//<![CDATA[
					jQuery(document).ready( function($) {
						// close postboxes that should be closed
						$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
						// postboxes setup
						postboxes.add_postbox_toggles('<?php echo $screen->id ?>');

						//$('#new_add_code').on( 'click', function(e) {
							//e.preventDefault();
							//$html = '<div class="ad_row"><textarea disabled rows="4" id="input_ad_code" name="ad_code[]" ></textarea><span class="ad_action"><a href="#" onclick="remove_add(this, event);">Delete</a></span</div>';
							//$('#ad_code_container').append($html);
						//});
						remove_add = function(control, e) {
							e.preventDefault();
							$(control).parent().parent().remove();
						}
						$('.ui.toggle').checkbox();
						$('#share_buttons').chosen();

						$('.disabled, #new_add_code').on('click', function(e) {
							e.preventDefault();
							var url = $(this).find('input').data('url');
							if(url == undefined) {
								url = $(this).data('url');
							}
							$('#pro-popup-notice').find('a').attr('href',url);
							$('.pro-popup').trigger("click")
						})
					});
				//]]>
			</script>
		<?php
	}

	/**
	 * Html analytics content
	 */
	public static function google_analytics_content( $settings ) {
	?>
		<div id="analytics_content">
			<label for="profile_name"><?php esc_html_e( 'Profile Name', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></label>
			<div class="disabled"><span><input type="text"  id="profile_name" value="" disabled /></span></div>

			<label for="tracking_id"><?php esc_html_e( 'Tracking ID', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></label>
			<div class="disabled"><span><input type="text" id="tracking_id" value="" /></span></div>
		</div>
	<?php
	}


	/**
	 * Html default settings content
	 */
	public static function default_settings_content( $settings ) {
		$settings = $settings['defaults'];
		if ( ! isset( $settings['restart_questions'] ) ) {
			$settings['restart_questions'] = false;
		}
		?>
			<table id="quiz_type_settings" width="100%" frame="border">
				<tr>
					<td><?php esc_html_e( 'Restart Questions', 'wp-quiz-' ); ?></td>
					<td>
						<div class="ui toggle checkbox">
							<input name="defaults[restart_questions]" type="checkbox" value="1" <?php checked( $settings['restart_questions'], true, true ) ?>>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Promote the plugin', 'wp-quiz' ); ?><br/><small><?php esc_html_e( 'Earn 70% commision on every sale by referring your friends and readers.','wp-quiz' ) ?></small></td>
					<td>
						<div class="ui toggle checkbox">
							<input name="defaults[promote_plugin]" id="quiz_promote_checkbox" type="checkbox" value="1" <?php checked( $settings['promote_plugin'], true, true ) ?>>
						</div>
					</td>
				</tr>
				<?php
					$desc = empty( $settings['mts_username'] ) ? '<a href="https://mythemeshop.com/#signup" target="_blank">' . __( 'Signup', 'wp-quiz' ) . '</a>' . __( ' and get your referral ID (username) if you don\'t have it already!', 'wp-quiz' ) : __( 'Check your affiliate earning by following ', 'wp-quiz' ) . '<a href="https://mythemeshop.com/go/aff/member/stats" target="_blank">' . __( 'this link', 'wp-quiz' ) . '</a>';
				?>
				<tr id="quiz_edit_mts_username_row">
					<td><?php esc_html_e( 'MyThemeShop username', 'wp-quiz' ); ?><br /><small><?php echo $desc; ?></small></td>
					<td>
						<input style="width:100%;" class="ui" name="defaults[mts_username]" type="text" value="<?php echo esc_attr( $settings['mts_username'] ); ?>" >
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Auto scroll to next question (applies to one page layout)', 'wp-quiz' ); ?><br/></td>
					<td>
						<div class="ui toggle checkbox">
							<input name="defaults[auto_scroll]" type="checkbox" value="1" <?php checked( $settings['auto_scroll'], true, true ) ?>>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Share buttons', 'wp-quiz' ); ?></td>
					<td>
						<?php $settings['share_buttons'] = isset( $settings['share_buttons'] ) ? $settings['share_buttons'] : array(); ?>
						<select name="defaults[share_buttons][]" id="share_buttons" data-placeholder="None" multiple>
							<option value="fb" <?php echo in_array( 'fb', $settings['share_buttons'] ) ? 'selected' : '' ?>><?php esc_html_e( 'Facebook', 'wp-quiz' ); ?></option>
							<option value="tw" <?php echo in_array( 'tw', $settings['share_buttons'] ) ? 'selected' : '' ?>><?php esc_html_e( 'Twitter', 'wp-quiz' ); ?></option>
							<option value="g+" <?php echo in_array( 'g+', $settings['share_buttons'] ) ? 'selected' : '' ?>><?php esc_html_e( 'Google +', 'wp-quiz' ); ?></option>
							<option value="vk" <?php echo in_array( 'vk', $settings['share_buttons'] ) ? 'selected' : '' ?>><?php esc_html_e( 'VK', 'wp-quiz' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Enable or disable Open Graph and Twitter Cards meta tags in single quiz head tag.', 'wp-quiz' ); ?></small></td>
					<td>
						<div class="ui toggle checkbox">
							<input name="defaults[share_meta]" type="checkbox" value="1" <?php checked( $settings['share_meta'], true, true ) ?>>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Facebook App ID', 'wp-quiz' ); ?><br/><small><?php printf( wp_kses( __( 'Learn how to create Facebook App ID <a href="%s" target="_blank">here</a>.', 'wp-quiz' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ), 'https://youtu.be/MI1QLsDJhJ8?t=3m48s' ); ?></small></td>
					<td>
						<input style="width:100%;" class="ui" name="defaults[fb_app_id]" type="text" value="<?php echo $settings['fb_app_id'] ?>" >
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Randomize Questions', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<div class="ui toggle checkbox disabled">
							<input name="" disabled type="checkbox" value="1" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Randomize+Questions&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg">
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Randomize Answers', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<div class="ui toggle checkbox disabled">
							<input name="" disabled type="checkbox" value="1" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Randomize+Answers&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg">
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Show embed code toggle', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<div class="ui toggle checkbox disabled">
							<input disabled name="" type="checkbox" value="1" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Show+Embed&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg">
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Show Ads', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<div class="ui toggle checkbox disabled">
							<input disabled name="" type="checkbox" value="1" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Show+Ads&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg">
						</div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Show Ads after every nth question', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<div class="disabled"><input disabled style="width:100%;" class="ui" name="" type="number" value="" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Show_Ads&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg"></div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Countdown timer [Seconds/question]', 'wp-quiz' ) ?><br/><small><?php esc_html_e( 'Works only on trivia quiz type when multiple page layout is selected', 'wp-quiz' ) ?></small><br/><small class="pro-text"><?php esc_html_e( ' Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<div class="disabled"><input disabled style="width:100%;" class="ui" name="" type="number" value="" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Countdown+Timer&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg"></div>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Force action to see the results', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
					<td>
						<select  class="ui" id="select" name="">
							<option disabled value="0" ><?php esc_html_e( 'No Action', 'wp-quiz' ); ?></option>
							<option disabled value="1" ><?php esc_html_e( 'Capture Email', 'wp-quiz' ); ?></option>
							<option disabled value="2" ><?php esc_html_e( 'Facebook Share', 'wp-quiz' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					$( '#quiz_promote_checkbox' ).change( function( event ) {
						if ( this.checked ) {
							jQuery('#quiz_edit_mts_username_row td').show();
						} else {
							jQuery('#quiz_edit_mts_username_row td').hide();
						}
					}).change();
				});
			</script>
		<?php

	}

	/**
	 * Html Ad Code content
	 */
	public static function ad_code_content( $settings ) {
		?>
		<div id="ad_code_setting">
			<div style="margin-bottom:5px;"><a style="margin-left:0" href="#" id="new_add_code" class="add-new-h2" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Ad+Code&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg"><?php esc_html_e( 'Add New', 'wp-quiz' ); ?></a><small style="padding-left: 5px;" class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ); ?></small></div>
			<div>
				<div id="ad_code_container">
				</div>
			</div>
		</div>
	<?php
	}

	public static function global_settings_content( $settings ) {
		?>
		<table id="global_settings" width="100%" frame="border">

			<tr>
				<td><?php esc_html_e( 'Enable or disable players tracking.', 'wp-quiz' ); ?><br/><small class="pro-text"><?php esc_html_e( 'Pro feature', 'wp-quiz' ) ?></small></td>
				<td>
					<div class="ui toggle checkbox disabled">
						<input disabled name="players_tracking" type="checkbox" value="1" data-url="https://mythemeshop.com/plugins/wp-quiz-pro/?utm_source=WP+Quiz+Free&utm_medium=Player+Tracking&utm_content=WP+Quiz+Pro+LP&utm_campaign=WordPressOrg">
					</div>
				</td>
			</tr>
		</table>
	<?php
	}
}
