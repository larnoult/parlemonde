<?php
/**
 * @version 3.0.0
 */

// Changing to display the full conversation. 
?>
			<div class="wp-embed-footer">
				<a class="button acomment-reply bp-primary-action" href="<?php 
				echo bp_get_activity_thread_permalink();//the_embed_site_title() ?>"> Répondre !</a>


				<div class="wp-embed-meta">
					<?php
					/** This action is documented in wp-includes/theme-compat/embed-content.php */
					//do_action( 'embed_content_meta'); ?>
				</div>
			</div>
