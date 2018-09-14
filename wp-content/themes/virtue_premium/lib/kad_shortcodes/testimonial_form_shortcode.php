<?php
function kad_testimonial_form($atts, $content = null) {
	extract(shortcode_atts(array(
		'location' => false,
		'position' => false,
		'link' => false,
		'image' => false,
		'login' => false,
		'enable_math' => true,
		'consent_checkbox' => false,
		'email' => '',
		'name_label' => __('Name', 'virtue'),
		'testimonial_label' => __('Testimonial', 'virtue'),
		'location_label' => __('Location - optional', 'virtue'),
		'position_label' => __('Position or Company - optional', 'virtue'),
		'link_label' => __('Link - optional', 'virtue'),
		'consent_label' => '',
		'image_label' => __('Image Upload - optional', 'virtue'),
		'submit_label' => __('Submit', 'virtue'),
		'math_error' => __('Check your math.', 'virtue'),
		'spam_error' => __('Your post appears to be spam, if this is incorrect please contact the site admnistator.', 'virtue'),
		'name_error' => __('Please enter your name.', 'virtue'),
		'consent_error' => __('Please check box above.', 'virtue'),
		'content_error' => __('Please add testimonial content.', 'virtue'),
		'error_message' => __('Sorry, an error occured.', 'virtue'),
		'login_message' => __('You must be logged in to submit an testimonial.', 'virtue'),
		'success_message' => __('Thank you for submitting your testimonial! It is now awaiting approval from the site admnistator. Thank you!', 'virtue'),
), $atts));
	global $kt_feedback_has_run, $kt_feedback_created;
	if ( 'true' == $consent_checkbox ) {
		if ( empty( $consent_label ) ) {
			if ( function_exists( 'the_privacy_policy_link' ) ) {
				$privacy_link = get_the_privacy_policy_link();
			}
			if( ! empty( $privacy_link ) ) {
				$consent_label = sprintf( __('Please check to consent to our %s.', 'virtue'), $privacy_link);
			} else {
				$consent_label = __('Please check to consent to our privacy policy.', 'virtue');
			}
		}
	}
	ob_start();
	if(isset($_POST['submitted']) && wp_verify_nonce( $_POST['post-title-nonce'], 'post-title' ) ) {
		$user_id = null;
		$spam_hook = apply_filters( 'kadence-testimonial-spam-check', true, $_POST );
		if( ! $spam_hook ) {
			$kt_feed_error = true; 
			$spamError = $spam_error;
		}
		$post_title = sanitize_text_field( $_POST['post-title']);
		$post_content = sanitize_textarea_field( $_POST['posttext'] );
		if ( $enable_math == true || $enable_math == 'true' ) {
 			if(empty($_POST['post-verify'])) { 
 				$kt_feed_error = true; 
 				$kad_captchaError = $math_error; 
 			}
 			if(md5($_POST['post-verify']) != $_POST['hval']) { 
 				$kt_feed_error = true; 
 				$kad_captchaError = $math_error;
 			}
 		}
 		if('true' == $consent_checkbox) {
 			if ( isset( $_POST['post-consent'] ) ) {
 				$gdpr_consent = sanitize_text_field( $_POST['post-consent'] );
 			} else {
 				$gdpr_consent = '';
 			}
			if( 'on' != $gdpr_consent ) {
 				$kt_feed_error = true;  
 				$consentError = $consent_error;
 			}
 		}
		if (empty($post_title)) {$kt_feed_error = true;  $nameError = $name_error;}
		if (empty($post_content)) {$kt_feed_error = true; $contentError = $content_error;}
 
		if ( ! isset( $kt_feed_error ) && $kt_feedback_has_run != 'yes'){
			$kt_feedback_has_run = 'yes';
 
			$post_id = wp_insert_post( array(
				'post_author'	=> $user_id,
				'post_title'	=> $post_title,
				'post_type'     => 'testimonial',
				'post_content'	=> $post_content,
				'post_status'	=> 'pending'
				) );
				if(isset($_POST['post-location'])) {	
					update_post_meta($post_id, '_kad_testimonial_location', sanitize_text_field( $_POST['post-location'] ));
					}	
				if(isset($_POST['post-company'])) {	
					update_post_meta($post_id, '_kad_testimonial_occupation', sanitize_text_field( $_POST['post-company'] ));
					}	
				if(isset($_POST['post-link'])) {	
					update_post_meta($post_id, '_kad_testimonial_link', sanitize_text_field( $_POST['post-link'] ));
					}
				if(isset($_FILES['post-img'])) {	
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
		
					$attachment_id = media_handle_upload('post-img', $post_id);
					if ( is_wp_error( $attachment_id ) ) {

						} else {
							set_post_thumbnail($post_id, $attachment_id);
						}
					unset($_FILES);
	       		}
	       	if(!empty($email)){
				$emailTo = $email;
			} else {
				$emailTo = get_option('admin_email');
			}
			$sitename = get_bloginfo('name');
			$subject = '['.$sitename . __(" Testimonial Post", "virtue").'] '. __("From ", "virtue"). $post_title;
			$body = __('Name', 'virtue').": $post_title \n\nComments: $post_content";
			$headers = '';

			wp_mail($emailTo, $subject, $body, $headers);		
	 		$kt_feedback_created = true;
		}
	}

		?>
<div id="kt-feedback-postbox" class="testimonial-form-container">
		<?php if(isset($kt_feedback_created) && $kt_feedback_created == true) { ?>
							<div class="thanks">
								<p><?php echo esc_html($success_message);?></p>
							</div>
		<?php } else { ?>
			<?php if(isset($kt_feed_error)) { ?>
				<p class="kt-error"><?php echo esc_html($error_message); ?><p>
			<?php } ?>
			<?php if($login && !is_user_logged_in()) { ?>
   					<p><?php echo esc_html($login_message);?></p> 
			<?php } else { ?>
		<div class="kt-feedback-inputarea">
			<form id="kad-feedback-new-post" name="new_post" method="post" enctype="multipart/form-data" action="<?php the_permalink(); ?>">
				<p><label><?php echo $name_label;?></label>
					<input type="text" class="full required requiredField" value="<?php if(isset($_POST['post-title'])) echo esc_attr($_POST['post-title']);?>" id="kt-feedback-post-title" name="post-title" />
					<?php if(isset($nameError)) { ?><label class="error"><?php echo esc_html($nameError);?></label><?php } ?>
				</p>
				<p><label><?php echo $testimonial_label;?></label>
					<textarea class="required requiredField" name="posttext" id="kt-feedback-post-text" cols="60" rows="10"><?php if(isset($_POST['posttext'])) echo esc_textarea($_POST['posttext']);?></textarea>
					<?php if(isset($contentError)) { ?><label class="error"><?php echo esc_html($contentError);?></label><?php } ?>
				</p>
				<?php if($location) {?>
				<p><label><?php echo $location_label;?></label>
					<input type="text" class="full" id="kt-feedback-post-location" value="<?php if(isset($_POST['post-location'])) echo esc_attr($_POST['post-location']);?>" name="post-location" />
				</p>
				<?php } 
				if($position) {?>
				<p><label><?php echo esc_html($position_label); ?></label>
					<input type="text" class="full" value="<?php if(isset($_POST['post-company'])) echo esc_attr($_POST['post-company']);?>" id="kt-feedback-post-company" name="post-company" />
				</p>
				<?php } 
				if($link) {?>
				<p><label><?php echo esc_html($link_label);?></label>
					<input type="text" class="full" id="kt-feedback-post-link" value="<?php if(isset($_POST['post-link'])) echo esc_attr($_POST['post-link']);?>" name="post-link" />
				</p>
				<?php } 
				if($image) {?>
				<p><label><?php echo esc_html($image_label);?></label>
					<input type="file" class="full kad_file_input" id="post-img"  multiple="false" value="<?php if(isset($_POST['post-img'])) echo esc_attr($_POST['post-img']);?>" name="post-img" />
				</p>
				<?php } 
				if ( $enable_math == true || $enable_math == 'true' ) {
					$one = rand(5, 50);
					$two = rand(1, 9);
					$result = md5($one + $two); ?>

				<p><label><?php echo $one.' + '.$two; ?></label>
					<input type="text" id="kt-feedback-post-verify" class="kad-quarter required requiredField" name="post-verify" />
				<?php if(isset($kad_captchaError)) { ?><label class="error"><?php echo esc_html($kad_captchaError);?></label><?php } ?>
				</p>
				<input type="hidden" name="hval" id="hval" value="<?php echo $result;?>" />
				<?php } ?>

				<?php if($consent_checkbox) {?>
				<p>
					<input type="checkbox" id="kt-feedback-post-consent" name="post-consent" />
					<label for="kt-feedback-post-consent" class="kt-consent-label"><?php echo wp_kses_post( $consent_label );?></label>
					<?php if(isset($consentError)) { ?><label class="error"><?php echo esc_html($consentError);?></label><?php } ?>
				</p>
				<?php }

					$spam_field = apply_filters( 'kadence-testimonial-spam-field', null );

					if( ! empty( $spam_field ) && is_array( $spam_field ) ) { ?>
						<p>	
							<?php if( isset( $spam_field['label'] ) && !empty( $spam_field['label'] ) ){
								echo wp_kses_post( $spam_field['label'] );
							}
							if( isset( $spam_field['input'] ) && !empty( $spam_field['input'] ) ){
								echo wp_kses_post( $spam_field['input'] );
							}
					
							if( isset( $spamError) ) { ?><label class="error"><?php echo esc_html( $spamError );?></label><?php } ?>
					</p>
					<?php }

				 wp_nonce_field('post-title', 'post-title-nonce'); ?>
				<input id="submit" type="submit" class="kad-btn kad-btn-primary" tabindex="3" value="<?php echo esc_html($submit_label); ?>" />					
				<input type="hidden" name="submitted" id="submitted" value="true" />
			</form>
		</div>
		<?php } }?>
 
</div>
<?php
	// Output the content.
	$output = ob_get_contents();
	ob_end_clean();
 
  	return $output;
}
 
// Add the shortcode to WordPress. 
add_shortcode('kad_testimonial_form', 'kad_testimonial_form');

