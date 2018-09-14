<?php
/**
 * @author ThimPress
 * @version 2.0.6
 */
ob_start();
 do_action('get_footer');
  get_template_part('templates/footer'); 
echo '</body>
</html>';

$footer = ob_get_clean();

// Find signal we added before
preg_match( '/(<!-- LEARN-PRESS-REMOVE-UNWANTED-PARTS -->)/', $footer, $matches );

// Split by our signal
$footer_parts = preg_split( '/(<!-- LEARN-PRESS-REMOVE-UNWANTED-PARTS -->)/', $footer );

// Output our footer with unwanted sections has removed
echo $footer_parts[1];

// No output anything after closing tag </html>
ob_start();