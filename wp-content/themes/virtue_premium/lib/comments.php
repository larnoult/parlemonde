<?php
/**
 * Use custom comment output.
 *
 * @package Virtue Theme
 */

/**
 * Walker Comment.
 *
 * @category class
 */
class Kadence_Walker_Comment extends Walker_Comment {
  function start_lvl(&$output, $depth = 0, $args = array()) {
    $GLOBALS['comment_depth'] = $depth + 1; ?>
    <ul <?php comment_class('media unstyled comment-' . get_comment_ID()); ?>>
    <?php
  }

  function end_lvl(&$output, $depth = 0, $args = array()) {
    $GLOBALS['comment_depth'] = $depth + 1;
    echo '</ul>';
  }

  function start_el(&$output, $comment, $depth = 0, $args = array(), $id = 0) {
    $depth++;
    $GLOBALS['comment_depth'] = $depth;
    $GLOBALS['comment'] = $comment;

    if (!empty($args['callback'])) {
      call_user_func($args['callback'], $comment, $args, $depth);
      return;
    }

    extract($args, EXTR_SKIP); ?>

  <li id="comment-<?php comment_ID(); ?>" <?php comment_class('media comment-' . get_comment_ID()); ?>>
    <?php echo get_avatar($comment, $size = '56'); ?>
    <div class="media-body">
      <div class="comment-header clearfix">
        <h5 class="media-heading"><?php echo get_comment_author_link(); ?></h5>
        <div class="comment-meta">
        <time datetime="<?php echo comment_date('c'); ?>">
          <?php printf(__('%1$s', 'virtue'), get_comment_date(),  get_comment_time()); ?>
        </time>
        |
        <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
        
        <?php edit_comment_link(__('| (Edit)', 'virtue'), '', ''); ?>
        </div>
      </div>

      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert">
          <?php _e('Your comment is awaiting moderation.', 'virtue'); ?>
        </div>
      <?php endif; ?>

      <?php comment_text(); ?>
      
  <?php
  }

  function end_el(&$output, $comment, $depth = 0, $args = array()) {
    if (!empty($args['end-callback'])) {
      call_user_func($args['end-callback'], $comment, $args, $depth);
      return;
    }
    echo "</div></li>\n";
  }
}

function kadence_get_avatar($avatar) {
  $avatar = str_replace("class='avatar", "class='avatar pull-left media-object", $avatar);
  return $avatar;
}
add_filter('get_avatar', 'kadence_get_avatar');


function kadence_custom_comments_before_feilds() {
   echo '<div class="row">';
}
add_action ('comment_form_before_fields', 'Kadence_custom_comments_before_feilds', 5);
function kadence_custom_comments_after_feilds() {
   echo '</div>';
}
add_action ('comment_form_after_fields', 'kadence_custom_comments_after_feilds', 5);
