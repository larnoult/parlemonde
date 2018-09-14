<?php
/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage Files Loading
 * @category Items
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//   L O A D   F I L E S
////////////////////////////////////////////////////////////////////////////////

require_once( OPSD_PLUGIN_DIR . '/core/any/class-css-js.php' );                 // Abstract. Loading CSS & JS files                 = Package: Any =
require_once( OPSD_PLUGIN_DIR . '/core/any/class-admin-settings-api.php' );     // Abstract. Settings API.        
require_once( OPSD_PLUGIN_DIR . '/core/any/class-admin-page-structure.php' );   // Abstract. Page Structure in Admin Panel    
require_once( OPSD_PLUGIN_DIR . '/core/any/class-admin-menu.php' );             // CLASS. Menus of plugin
require_once( OPSD_PLUGIN_DIR . '/core/any/admin-bs-ui.php' );                  // Functions. Toolbar BS UI Elements
if( is_admin() ) {
	require_once OPSD_PLUGIN_DIR . '/core/any/opsd-class-dismiss.php';			// Class - Dismiss                 
	require_once OPSD_PLUGIN_DIR . '/core/any/opsd-class-notices.php';			// Class - Notices                
}

// Functions	////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once( OPSD_PLUGIN_DIR . '/core/opsd-debug.php' );                       // Debug                                            = Package: OPSD =
require_once( OPSD_PLUGIN_DIR . '/core/opsd-core.php' );                        // Core 
require_once( OPSD_PLUGIN_DIR . '/core/opsd-translation.php' );                 // Translations 
        require_once( OPSD_PLUGIN_DIR . '/core/opsd-functions.php' );                   // Functions		
        require_once( OPSD_PLUGIN_DIR . '/core/opsd-products.php' );                    // Products Functions
        require_once( OPSD_PLUGIN_DIR . '/core/opsd-download.php' );                    // Download Functions
        require_once( OPSD_PLUGIN_DIR . '/core/opsd-upload.php' );						// Upload Functions
        require_once( OPSD_PLUGIN_DIR . '/core/opsd-emails.php' );                      // Emails
		
// JS & CSS		////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once( OPSD_PLUGIN_DIR . '/core/opsd-css.php' );                         // Load CSS
require_once( OPSD_PLUGIN_DIR . '/core/opsd-js.php' );                          // Load JavaScript and define JS Varibales

// Admin UI ////////////////////////////////////////////////////////////////////////////////
require_once( OPSD_PLUGIN_DIR . '/core/admin/opsd-toolbars.php' );              // Toolbar - BS UI Elements                
        require_once( OPSD_PLUGIN_DIR . '/core/admin/opsd-dashboard.php' );             // Dashboard Widget

// Admin Pages	////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once( OPSD_PLUGIN_DIR . '/core/admin/page-send.php' );							// Master page
require_once( OPSD_PLUGIN_DIR . '/core/admin/page-files-add.php' );						// Add New page
require_once( OPSD_PLUGIN_DIR . '/core/admin/page-files-sortable.php' );				// Sortable List page

//require_once( OPSD_PLUGIN_DIR . '/core/admin/exmpl-opsd-class-listing.php' );			// CLASS. item Listing Table						// 4.F.U.T.U.R.E
//require_once( OPSD_PLUGIN_DIR . '/core/admin/exmpl-page-with-toolbars-listing.php' );   // Template of page with  toolbars					// 4.F.U.T.U.R.E

require_once( OPSD_PLUGIN_DIR . '/core/admin/page-settings.php' );						// Settings page 
    require_once( OPSD_PLUGIN_DIR . '/core/admin/api-settings.php' );					// Settings API
    
require_once( OPSD_PLUGIN_DIR . '/core/admin/page-email-link-user.php' );				// Email - send email with download link to user
require_once( OPSD_PLUGIN_DIR . '/core/admin/page-email-download_notification.php' );	// Email - send email  about downloads happend    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once( OPSD_PLUGIN_DIR . '/core/any/activation.php' );
require_once( OPSD_PLUGIN_DIR . '/core/opsd-activation.php' );

make_opsd_action( 'opsd_loaded_php_files' );