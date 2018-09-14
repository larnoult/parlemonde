<?php
/**
 * Class For WP Quiz Import Export page
 */
class WP_Quiz_Page_Import_Export {

	public static function admin_print_styles() {
		wp_enqueue_style( 'pro-popup-css', wp_quiz()->plugin_url() . 'assets/css/pro-popup.css', array(), wp_quiz()->version );
		?>
			<style>
				ul.tabs { width: 100%; display: table; border-collapse: separate; }
				ul.tabs li { font-weight: bold; text-align: center; margin: 0; cursor: pointer; display: 				table-cell; line-height: 14px; border: 1px solid #eee; background-color: #eee; }
				ul.tabs li.active { background-color: #fff }
				ul.tabs li a { padding: 10px 5px; display: block; text-decoration: none; font-weight: bold; }
				ul.tabs li a:focus { box-shadow: none }
				.tab-content>.tab-pane { display: none }
				.tab-content>.active { display: block }
				.demo-container { width: 280px; padding: 10px; display: inline-block; margin-right: 20px; background: #FAFBFD; border: 1px solid #eaeaeb; border-radius: 3px; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1); }
				.demo-thumb img { width: 100% }
				#import hr { margin: 30px 0 }

			</style>
		<?php

	}

	public static function display_messages() {

		$message = false;
		if ( isset( $_REQUEST['message'] ) && ( $msg = (int) $_REQUEST['message'] ) ) {
			if ( 3 === $msg ) {
				$message = esc_html__( 'Quiz imported successfully', 'wp-quiz' );
			} else if ( 4 === $msg ) {
				$message = esc_html__( 'Failed to import Quiz', 'wp-quiz' );
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

		if ( isset( $_POST['action'] ) ) {
			switch ( $_POST['action'] ) {
				case 'import':

					if ( ! current_user_can( 'manage_options' ) ) {
						break;
					}

					$location = admin_url() . 'edit.php?post_type=wp_quiz&page=wp_quiz_ie';
					if ( isset( $_POST['demo'] ) && 'true' == $_POST['demo'] ) {
						$status = self::import_wp_quiz_demo();
					} else {
						$status = self::import_wp_quiz();
					}

					if ( $status ) {
						$location = add_query_arg( 'message', 3, $location );
						wp_redirect( $location );
					} else {
						$location = add_query_arg( array( 'error' => true, 'message' => 4 ), $location );
						wp_redirect( $location );
					}
					exit;
			}
		}

		//needed javascripts to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'wp_quiz-bootstrap', wp_quiz()->plugin_url() . 'assets/js/bootstrap.min.js', array( 'jquery' ), wp_quiz()->version, true );

		$screen = get_current_screen();
		add_meta_box( 'import-export-content', __( 'Import and Export Quizzes', 'wp-quiz' ), array( __CLASS__, 'import_export_content' ), $screen->id, 'normal', 'core' );
	}

	public static function page() {

		$screen		= get_current_screen();
		$columns	= absint( $screen->get_columns() );
		$columns_css	= '';

		if ( $columns ) {
			$columns_css = " columns-$columns";
		} ?>
			<div class="wrap" id="config-page">
				<h2><?php esc_html_e( 'Import/Export', 'wp-quiz' ); ?></h2>
				<?php self::display_messages(); ?>
				<?php wp_nonce_field( 'wp_quiz_ie_page' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<input type="hidden" name="page" value="wp_quiz_config" />
				<div id="poststuff">
					<div id="post-body" class="metabox-holder <?php echo $columns_css ?>">
						<div id="postbox-container-2" class="postbox-container">
							<?php
								do_meta_boxes( $screen->id, 'normal', '' );
							?>
						</div>
					</div>
				</div>
				<?php include_once( wp_quiz()->plugin_dir() . '/inc/pro-popup-template.php' ); ?>
			</div>
			<script type="text/javascript">
				//<![CDATA[
					jQuery(document).ready( function($) {
						// close postboxes that should be closed
						$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
						// postboxes setup
						postboxes.add_postbox_toggles('<?php echo $screen->id ?>');

						$('#tabs').tab();

						$('#export_tab').on('click', function() {
							$('.pro-popup').trigger("click")
						})

					});
				//]]>
			</script>
		<?php
	}

	public static function import_export_content() {
		?>
			<div>
				<ul id="tabs" class="tabs" data-tabs="tabs">
					<li class="active"><a href="#import" data-toggle="tab"><?php esc_html_e( 'Import', 'wp-quiz' ); ?></a></li>
					<li><a id="export_tab" href="#export" data-toggle="tab"><?php esc_html_e( 'Export', 'wp-quiz' ); ?></a></li>
				</ul>
				<div class='tab-content'>
					<div class="tab-pane active" id="import">
						<form action="<?php echo admin_url( 'edit.php?post_type=wp_quiz&page=wp_quiz_ie' ); ?>" method="post" onsubmit="return confirm('Are you sure you want to import demo?');">
							<div>
								<div class="demo-container">
									<div class="demo-thumb">
										<a href="http://demo.mythemeshop.com/wp-quiz/" target="_blank"><img src="<?php echo wp_quiz()->plugin_url() . 'demo/import.jpg'; ?>" /></a>
									</div>
								</div>
							</div>
							<p>
								<input type="hidden" name="demo" value="true" />
								<input type="hidden" name="action" value="import" />
								<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_html_e( 'Import Demo','wp-quiz' ) ?>">&nbsp;
							</p>
						</form>
						<hr/>
						<p><?php esc_html_e( 'Import Quiz File', 'wp-quiz' ); ?></p>
						<form action="<?php echo admin_url( 'edit.php?post_type=wp_quiz&page=wp_quiz_ie' ); ?>" method="post" enctype="multipart/form-data">
							<p><input type="file" name="wp_quizzes" />
							<p>
								<input type="hidden" name="action" value="import" />
								<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_html_e( 'Import File','wp-quiz' ) ?>">&nbsp;
							</p>
						</form>
					</div>
					<div class="tab-pane" id="export">
						<p><?php esc_html_e( 'To use this feature please upgrade to pro version.','wp-quiz' ) ?></p>
					</div>
				</div>
			</div>
		<?php
	}


	public static function import_wp_quiz() {

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		global $wpdb;

		$imported_file             	= $_FILES['wp_quizzes'];
		$wq_upload_dir              = wp_upload_dir();
		$wq_upload_dir['basedir'] 	= $wq_upload_dir['basedir'] . '/wp_quiz-import';
		$wq_upload_dir['baseurl']   = $wq_upload_dir['baseurl'] . '/wp_quiz-import';

		if ( ! move_uploaded_file( $imported_file['tmp_name'], $wq_upload_dir['basedir'] . '/' . $imported_file['name'] ) ) {
			return false;
		}

		// Get JSON File and It's contents
		$quiz_obj = file_get_contents( $wq_upload_dir['basedir'] . '/' . $imported_file['name'] );
		$quiz_obj = json_decode( $quiz_obj, true );

		foreach ( $quiz_obj as $qok => $qov ) {
			// Questions
			foreach ( $qov['questions'] as $qk => $qv ) {
				if ( ! empty( $qv['image'] ) ) {
					$new_src = self::download_image_file( $qv['image'] );
					if ( $new_src && ! empty( $new_src ) ) {
						$qov['questions'][ $qk ]['image']  = $new_src;
					}
				}

				if ( ! empty( $qv['backImage'] ) ) {
					$new_src = self::download_image_file( $qv['backImage'] );
					if ( $new_src && ! empty( $new_src ) ) {
						$qov['questions'][ $qk ]['backImage'] = $new_src;
					}
				}

				if ( ! empty( $qv['answers'] ) ) {
					foreach ( $qv['answers'] as $ak => $av ) {
						if ( ! empty( $av['image'] ) ) {
							$new_src = self::download_image_file( $av['image'] );
							if ( $new_src && ! empty( $new_src ) ) {
								$qov['questions'][ $qk ]['answers'][ $ak ]['image'] = $new_src;
							}
						}
					}
				}
			}

			// Results
			if ( ! empty( $qv['results'] ) ) {
				foreach ( $qov['results'] as $rk => $rv ) {
					if ( ! empty( $rv['image'] ) ) {
						$new_src = self::download_image_file( $rv['image'] );
						if ( $new_src && ! empty( $new_src ) ) {
							$qov['results'][ $rk ]['image'] = $new_src;
						}
					}
				}
			}

			$questions	= $qov['questions'];
			$results    = $qov['results'];
			$settings	= $qov['settings'];
			$type		= $qov['type'];

			$post_id    = wp_insert_post(array(
				'post_content'   => '<p></p>',
				'post_name'      => $qov['title'],
				'post_title'     => $qov['title'],
				'post_status'    => 'publish',
				'post_type'      => 'wp_quiz',
			));

			update_post_meta( $post_id, 'questions', $questions );
			update_post_meta( $post_id, 'results', $results );
			update_post_meta( $post_id, 'settings', $settings );
			update_post_meta( $post_id, 'quiz_type', $type );

		}

		unlink( $wq_upload_dir['basedir'] . '/' . $imported_file['name'] );
		return true;
	}

	public static function import_wp_quiz_demo() {

		global $wpdb;

		// Get JSON File and It's contents
		$quiz_obj = file_get_contents( wp_quiz()->plugin_url() . 'demo/wp_quiz_demo.json' );
		
		$quiz_obj = json_decode( $quiz_obj, true );

		foreach ( $quiz_obj as $qok => $qov ) {

			// Questions
			foreach ( $qov['questions'] as $qk => $qv ) {
				if ( ! empty( $qv['image'] ) ) {
					$new_src = self::download_image_file( $qv['image'] );
					if ( $new_src && ! empty( $new_src ) ) {
						$qov['questions'][ $qk ]['image']  = $new_src;
					}
				}

				if ( ! empty( $qv['backImage'] ) ) {
					$new_src = self::download_image_file( $qv['backImage'] );
					if ( $new_src && ! empty( $new_src ) ) {
						$qov['questions'][ $qk ]['backImage'] = $new_src;
					}
				}

				if ( ! empty( $qv['answers'] ) ) {
					foreach ( $qv['answers'] as $ak => $av ) {
						if ( ! empty( $av['image'] ) ) {
							$new_src = self::download_image_file( $av['image'] );
							if ( $new_src && ! empty( $new_src ) ) {
								$qov['questions'][ $qk ]['answers'][ $ak ]['image'] = $new_src;
							}
						}
					}
				}
			}

			// Results
			if ( ! empty( $qv['results'] ) ) {
				foreach ( $qov['results'] as $rk => $rv ) {
					if ( ! empty( $rv['image'] ) ) {
						$new_src = self::download_image_file( $rv['image'] );
						if ( $new_src && ! empty( $new_src ) ) {
							$qov['results'][ $rk ]['image'] = $new_src;
						}
					}
				}
			}

			$questions	= $qov['questions'];
			$results    = $qov['results'];
			$settings	= $qov['settings'];
			$type		= $qov['type'];

			$post_id    = wp_insert_post(array(
				'post_content'   => '<p></p>',
				'post_name'      => $qov['title'],
				'post_title'     => $qov['title'],
				'post_status'    => 'publish',
				'post_type'      => 'wp_quiz',
			));

			update_post_meta( $post_id, 'questions', $questions );
			update_post_meta( $post_id, 'results', $results );
			update_post_meta( $post_id, 'settings', $settings );
			update_post_meta( $post_id, 'quiz_type', $type );
		}

		return true;
	}

	public static function download_image_file( $file, $path = false, $post_id = '', $desc = '' ) {

		// Need to require these files
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		if ( ! empty( $file ) && self::is_image_file( $file ) ) {
			// Download file to temp location
			$tmp = download_url( $file );

			// Set variables for storage, fix file filename for query strings.
			preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches );
			$file_array['name'] = basename( $matches[0] );
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
				return false;
			}

			$desc = $file_array['name'];
			$id = media_handle_sideload( $file_array, $post_id, $desc );

			// If error storing permanently, unlink
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				return false;
			}

			return wp_get_attachment_url( $id );
		}
	}

	public static function is_image_file( $file ) {

		$check = false;
		$filetype = wp_check_filetype( $file );
		$valid_exts = array( 'jpg', 'jpeg', 'gif', 'png' );
		if ( in_array( strtolower( $filetype['ext'] ), $valid_exts ) ) {
			$check = true;
		}

		return $check;
	}
}
