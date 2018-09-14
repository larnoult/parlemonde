<?php

// No direct access
defined( 'ABSPATH' ) or die( 'Access Denied' );
    
?>
<div class="error">
    <p><?php echo cssJSToolbox::_( 'CSS & JavaScript Toolbox plugin requires PHP %s to function properly. Please contact support your hosting to upgrade PHP', CJTPlugin::ENV_PHP_MIN_VERSION ) ?></p>
</div>
