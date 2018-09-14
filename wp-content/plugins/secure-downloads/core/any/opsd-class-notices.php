<?php /**
 * @version 1.0
 * @description Notices Class
 * @category Show system Notices
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

/** Showing our system notices in admin panel */
class OPSD_Notices {
    
	
    function __construct() {
    
		// Hooks for showing notices only at specific admin pages
        add_action( 'opsd_hook_opsd_page_header',		array( $this, 'show_system_notices' ) );	
		add_action( 'opsd_settings_after_header',	array( $this, 'show_system_notices' ) );
    }    
	
	
	/** Check  and show some system  messages 
	 * 
	 * @param array $page_arr					 array( 'page' => $this->in_page() ) ||  array( 'page' => $this->in_page(), 'subpage' => 'emails_settings' )
	 */
	public function show_system_notices( $page_arr ) {

		if ( ! in_array( $page_arr, array( 'opsd-files', 'opsd', 'opsd-settings' ) ) ) 
			return false;
		
		///////////////////////////////////////////////////////////
		$notice_id = 'opsd_system_notice_nginix';
		///////////////////////////////////////////////////////////
		if (	   ( isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) 
				&& ( stristr( $_SERVER[ 'SERVER_SOFTWARE' ], 'nginx' ) !== false ) 				
				&& ( ! opsd_section_is_dismissed( $notice_id ) )
			// || true
		) {
			// Rules for NGINX
			$opsd_upload = opsd_upload();		
			$upload_path = $opsd_upload->get_protected_dir();
			if ( isset( $_SERVER[ 'DOCUMENT_ROOT' ] ) )
				$upload_path = str_replace( $_SERVER[ 'DOCUMENT_ROOT' ], '', $upload_path ); // replace document root because nginx uses path from document root			
			$nx_rules = "location " . $upload_path . " {\n  deny all;  \n  return 403;\n}";

			// echo '<div class="error notice is-dismissible dlm-notice" id="nginx_rules" data-nonce="' . wp_create_nonce( 'opsd_dismiss_notice-nginx_rules' ) . '">';
			?><div  id="<?php echo $notice_id; ?>" 
					class="opsd_system_notice opsd_is_dismissible opsd_is_hideable updated error" 
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_opsdnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"
				><?php 
   			
			opsd_x_dismiss_button();
			
			echo '<strong>' . __( 'Warning!', 'secure-downloads' ) . '</strong> ';
			printf( __( 'Your download files are not protected with .htaccess file, because your server is running on NGINX. To protect these files, you must add the following rules to your nginx config to disable direct file access: %s', 'secure-downloads' )
						, '<br/><pre style="line-height: 1.55em;pre-wrap;"><code>' . $nx_rules . '</code></pre>' );			
			?></div><?php
		}
		///////////////////////////////////////////////////////////
		
		
		///////////////////////////////////////////////////////////
		$notice_id = 'opsd_system_notice_free_instead_paid';
		///////////////////////////////////////////////////////////
		if (	    opsd_is_updated_paid_to_free()
				&& ( ! opsd_section_is_dismissed( $notice_id ) )
			// || true 
		) {

			?><div  id="<?php echo $notice_id; ?>" 
					class="opsd_system_notice opsd_is_dismissible opsd_is_hideable updated notice-warning"
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_opsdnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"
				><?php 
			
			opsd_x_dismiss_button();
			
			echo '<strong>' . __( 'Warning!', 'secure-downloads' ) . '</strong> ';
			printf( __( 'Probabaly you updated your paid version of Secure Downloads by free version or update process failed. You can request the new update of your paid version at %1sthis page%2s.', 'secure-downloads' )
					, '<a href="http://oplugins.com/plugins/secure-downloads/request-update/" target="_blank">', '</a>' );
			
			?></div><?php
		}       
		///////////////////////////////////////////////////////////
		
			
		
		///////////////////////////////////////////////////////////
		$notice_id = 'opsd-panel-get-started';
		///////////////////////////////////////////////////////////
		
		if ( ! opsd_section_is_dismissed( $notice_id ) ) {
			?>
			<style type="text/css" media="screen">
				/* OPSD Welcome Panel */                
				.opsd-panel .welcome-panel {
					background: linear-gradient(to top, #F5F5F5, #FAFAFA) repeat scroll 0 0 #F5F5F5;
					border-color: #DFDFDF;
					position: relative;
					overflow: auto;
					margin: 5px 0 20px;
					padding: 23px 10px 12px;
					border-width: 1px;
					border-style: solid;
					border-radius: 3px;
					font-size: 13px;
					line-height: 2.1em;
				}
				.opsd-panel .welcome-panel h3 {
					margin: 0;
					font-size: 21px;
					font-weight: 400;
					line-height: 1.2;
				}
				.opsd-panel .welcome-panel h4 {
					margin: 1.33em 0 0;
					font-size: 13px;
					font-weight: 600;
				}
				.opsd-panel .welcome-panel a{
					color:#21759B;
				}
				.opsd-panel .welcome-panel .about-description {
					font-size: 16px;
					margin: 0;
				}
				.opsd-panel .welcome-panel .welcome-panel-close {
					position: absolute;
					top: 5px;
					right: 10px;
					padding: 8px 3px;
					font-size: 13px;
					text-decoration: none;
					line-height: 1;
				}
				.opsd-panel .welcome-panel .welcome-panel-close:before {
					content: ' ';
					position: absolute;
					left: -12px;
					width: 10px;
					height: 100%;
					background: url('../wp-admin/images/xit.gif') 0 7% no-repeat;
				}
				.opsd-panel .welcome-panel .welcome-panel-close:hover:before {
					background-position: 100% 7%;
				}
				.opsd-panel .welcome-panel .button.button-hero {
					margin: 15px 0 3px;
				}
				.opsd-panel .welcome-panel-content {
					margin-left: 13px;
					max-width: 1500px;
				}
				.opsd-panel .welcome-panel .welcome-panel-column-container {
					clear: both;
					overflow: hidden;
					position: relative;
				}
				.opsd-panel .welcome-panel .welcome-panel-column {
					width: 32%;
					min-width: 200px;
					float: left;
				}
				.ie8 .opsd-panel .welcome-panel .welcome-panel-column {
					min-width: 230px;
				}
				.opsd-panel .welcome-panel .welcome-panel-column:first-child {
					width: 36%;
				}
				.opsd-panel .welcome-panel-column p {
					margin-top: 7px;
				}
				.opsd-panel .welcome-panel .welcome-icon {
					background: none;    
					display: block;
					padding: 2px 0 8px 2px;    
				}
				.opsd-panel .welcome-panel .welcome-add-page {
					background-position: 0 2px;
				}
				.opsd-panel .welcome-panel .welcome-edit-page {
					background-position: 0 -90px;
				}
				.opsd-panel .welcome-panel .welcome-learn-more {
					background-position: 0 -136px;
				}
				.opsd-panel .welcome-panel .welcome-comments {
					background-position: 0 -182px;
				}
				.opsd-panel .welcome-panel .welcome-view-site {
					background-position: 0 -274px;
				}
				.opsd-panel .welcome-panel .welcome-widgets-menus {
					background-position: 1px -229px;
					line-height: 14px;
				}
				.opsd-panel .welcome-panel .welcome-write-blog {
					background-position: 0 -44px;
				}
				.opsd-panel .welcome-panel .welcome-panel-column ul {
					margin: 0.8em 1em 1em 0;
				}
				.opsd-panel .welcome-panel .welcome-panel-column li {
					line-height: 1.7em;
					list-style-type: none;
					margin:0;
					padding:0;
				}
				@media screen and (max-width: 870px) {
					.opsd-panel .welcome-panel .welcome-panel-column,
					.opsd-panel .welcome-panel .welcome-panel-column:first-child {
						display: block;
						float: none;
						width: 100%;
					}
					.opsd-panel .welcome-panel .welcome-panel-column li {
						display: inline-block;
						margin-right: 13px;
					}
					.opsd-panel .welcome-panel .welcome-panel-column ul {
						margin: 0.4em 0 0;
					}
					.opsd-panel .welcome-panel .welcome-icon {
						padding-left: 25px;
					}
				}
			</style>                
			<div	id="<?php echo $notice_id ?>" 
					class="opsd-panel opsd_is_dismissible opsd_is_hideable "
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_opsdnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"		 

				 ><div class="welcome-panel"><?php 

			opsd_x_dismiss_button( '&times;', array( 'style' => 'font-size:1.5em;margin-top:-0.8em;' ) ); 
			
			?>
			<div class="welcome-panel-content">
				<p class="about-description"><?php _e( 'We&#8217;ve assembled some links to get you started:', 'secure-downloads'); ?></p>
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4><?php _e( 'Upload your files to secure direcory', 'secure-downloads'); ?>:</h4>
						<ul>                          
							<li><div class="welcome-icon"><?php 
									printf( __( 'Open %s menu page', 'secure-downloads')
											, '<a href="' . admin_url( 'admin.php?page=opsd-files' ) . '">' 
												. '<strong>' . 'Secure Downloads > ' . __( 'Files', 'secure-downloads' ) . '</strong>'
										. '</a>'
									 );
								?></div></li>                            
							<li><div class="welcome-icon"><?php
								echo '1. ' . sprintf( __( 'Click on %s"Add New"%s button and upload your files.', 'secure-downloads' ), '<strong>', '</strong>' );
							?></div></li>	
							<li><div class="welcome-icon"><?php
								echo '2. ' . sprintf( __( 'Enter Title, Version Number and Description at %s"Attachment details"%s section.', 'secure-downloads' ), '<strong>', '</strong>' );
							?></div></li>	
							<li><div class="welcome-icon"><?php
								echo '3. ' . sprintf( __( 'Select one or multiple files, click insert button and Save changes.', 'secure-downloads' ) );

							?></div></li>	
						</ul>
					</div>
					<div class="welcome-panel-column">
						<h4><?php _e( 'Next Steps', 'secure-downloads'); ?>.</h4>
						<ul>
							<li><div class="welcome-icon"><?php
									printf( __( 'Open %s menu page and %s send predefined email with secure link %s to your customer or simply %s generate secure link %s', 'secure-downloads')
											, '<a href="' . admin_url( 'admin.php?page=opsd' ) . '">' 
												. '<strong>' . __( 'Secure Links', 'secure-downloads' ) . '</strong>'
										. '</a>'
										, '<strong>', '</strong>'
										, '<strong>', '</strong>.'
									 );
							?></div></li>	
							<li><div class="welcome-icon"><?php
								printf( __( 'Configure different options in %sSettings%s.' , 'secure-downloads'),
									'<a href="' . esc_url( opsd_get_settings_url() ) . '">', '</a>' );
							?></div></li>                            
							<li><div class="welcome-icon"><?php
								printf( __( 'Configure your predefined %sEmail Templates%s.', 'secure-downloads'),
									'<a href="' . esc_url(  opsd_get_settings_url() . '&tab=email' ) . '">', '</a>' );
							?></div></li>
						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h4><?php _e( 'Tips', 'secure-downloads'); ?></h4>
						<ul>
							<li><div class="welcome-icon"><?php
									printf( __( 'You can easy reorder files list at %s page.', 'secure-downloads')
											, '<a href="' . admin_url( 'admin.php?page=opsd-files&tab=files-sortable' ) . '">' 
												. '<strong>' . __( 'Sortable List', 'secure-downloads' ) . '</strong>'
										. '</a>'
									 );
							?></div></li>	
							<li><div class="welcome-icon"><?php
								echo '<strong>' . __( 'Note', 'secure-downloads' ) . '</strong>. ' 
									.  __( 'You can use line with simple text without CSV separators for definition of sections in file list.', 'secure-downloads')
									;
							?></div></li>	

							<li><div class="welcome-icon"><?php
								printf( __( 'Still having questions? Contact %sSupport%s.', 'secure-downloads'),
									'<a href="http://oplugins.com/support/" target="_blank">',
									'</a>' );
							?></div></li>
							<li><div class="welcome-icon"><?php
								printf( __( 'Do you require new feature? Send your %ssuggestion%s to us.', 'secure-downloads'),
									'<a href="mailto:newfeature@oplugins.com?Subject=Secure%20Downloads" target="_blank">',
									'</a>' );
							?></div></li>
						</ul>
					</div>
				</div>
				<div class="welcome-icon welcome-widgets-menus" style="text-align:right;font-style:italic;"><?php
					printf( __( 'Need even more functionality? Check %s higher versions %s', 'secure-downloads'),
							'<a href="http://oplugins.com/plugins/secure-downloads/#premium" target="_blank">',
							'</a>' 
						); ?>
				</div>
			</div> 
			<?php

        
			?></div></div><?php
		}
		
	}
	
}
 
new OPSD_Notices();																// Run