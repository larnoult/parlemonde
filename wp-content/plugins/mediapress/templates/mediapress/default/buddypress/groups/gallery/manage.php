<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This template loads appropriate template file for the current Edit gallery or Edit media action
 *
 */

?>

<div class="mpp-menu mpp-menu-open  mpp-menu-horizontal mpp-gallery-admin-menu">
	<?php mpp_gallery_admin_menu( mpp_get_current_gallery(), mpp_get_current_edit_action() ); ?>
</div>
<hr/>

<?php
$template = '';
if ( mpp_is_gallery_add_media() ) {
	$template = 'gallery/manage/add-media.php';
} elseif ( mpp_is_gallery_edit_media() ) {
	$template = 'gallery/manage/edit-media.php';
} elseif ( mpp_is_gallery_reorder_media() ) {
	$template = 'gallery/manage/reorder-media.php';
} elseif ( mpp_is_gallery_settings() ) {
	$template = 'gallery/manage/settings.php';
} elseif ( mpp_is_gallery_delete() ) {
	$template = 'gallery/manage/delete.php';
}

$template = apply_filters( 'mpp_get_gallery_management_template', $template );

// load it.
if ( $template ) {
	mpp_get_template( $template );
}
unset( $template );// do not let the global litter unintentionally.
do_action( 'mpp_load_gallery_management_template' );
