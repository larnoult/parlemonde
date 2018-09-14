<?php
/**
 * Footer Template
 *
 * @version 4.6.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 		/**
		* @hooked virtue_footer_markup - 10
		*/
		do_action( 'virtue_footer' );
		?>
		</div><!--Wrapper-->
		<?php wp_footer(); ?>
	</body>
</html>