<?php
/*
 * View Own Post and Media Only WordPress plugin options page
 *
 * @Author: Vladimir Garagulya
 * @URL: http://shinephp.com
 * @package EditPostExpire
 *
 */


?>
<div class="wrap">
  <div class="icon32" id="icon-options-general"><br/></div>
  <h2><?php esc_html_e('View Own Post and Media Only - Options', 'vopmo'); ?></h2>
  <hr/>
  
  <form method="post" action="options-general.php?page=view-own-posts-media-only.php" >   
    <table>
      <tr>
        <td><label for="select_uploaded_to_this_post"><?php esc_html_e('Select "Uploaded to this post" by default:', 'vopmo'); ?></label></td>
        <td><input type="checkbox" name="select_uploaded_to_this_post" id="select_uploaded_to_this_post" value="1" <?php echo $this->lib->checked_html($select_uploaded_to_this_post); ?>/> </td>
		<td><div style="marging: 2px; padding: 5px; border: 1px #CCCCCC solid;"><img src="<?php echo VOPMO_PLUGIN_URL . 'images/uploaded-to-this-post.png'; ?>" width="303" height="128" /></div></td>
      </tr>
      <tr>
        <td><label for="hide_attachments_type_menu"><?php esc_html_e('Hide Attachments Type menu:', 'vopmo'); ?></label></td>
        <td><input type="checkbox" name="hide_attachments_type_menu" id="hide_attachments_type_menu" value="1" <?php echo $this->lib->checked_html($hide_attachments_type_menu); ?>/> </td>
		<td><div style="marging: 2px; padding: 5px; border: 1px #CCCCCC solid;"><img src="<?php echo VOPMO_PLUGIN_URL . 'images/hide-attachments-type-menu.png'; ?>" width="259" height="118" /></div></td>
      </tr>
      <tr><td colspan="3"></tr>
      <tr>
        <td><label for="hide_other_posts_comments"><?php esc_html_e('Hide comments from others posts:', 'vopmo'); ?></label></td>
        <td><input type="checkbox" name="hide_other_posts_comments" id="hide_other_posts_comments" value="1" <?php echo $this->lib->checked_html($hide_other_posts_comments); ?>/> </td>
        <td></td>
      </tr>
      <tr><td colspan="3"></tr>
<?php
	if (count($post_types)>0) {
?>
	  <tr>
		  <td>
			  <?php esc_html_e('Exclude Custom Post Types:', 'vopmo'); ?>
		  </td>
		  <td colspan="2">
<?php	  
		foreach($post_types as $post_type) {
			if (in_array($post_type, $exclude_custom_post_types)) {
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
?>
			  <input type="checkbox" name="exclude_custom_post_types[]" id="<?php echo $post_type;?>" 
					 value="<?php echo $post_type;?>" <?php echo $checked; ?> /> <label for="<?php echo $post_type;?>"><?php echo $post_type;?></label><br />
<?php	  
		}
?>
		  </td>
	  </tr>
<?php		  
	}
?>
    </table>
    <?php wp_nonce_field('view-own-posts-media-only'); ?>   
    <p class="submit">
      <input type="submit" class="button-primary" name="view_own_posts_media_only_update" value="<?php _e('Update', 'vopmo') ?>" />
    </p>  

  </form>  
</div>


Credit by <a href="http://shinephp.com">ShinePHP.com</a>

