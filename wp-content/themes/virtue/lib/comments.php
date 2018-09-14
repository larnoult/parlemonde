<?php

class Virtue_Walker_Comment extends Walker_Comment {

    protected function html5_comment( $comment, $depth, $args ) {
        $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
        $child_class = $this->has_children ? 'parent' : '';
?>
        <<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'media comment-' . get_comment_ID() . ' ' . $child_class, $comment ); ?>>
        <?php if ( 0 != $args['avatar_size'] ) {
    		echo get_avatar( $comment, $args['avatar_size'] );
    		} ?>
    		<div class="media-body">
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            	<header class="comment-header clearfix">
					<h5 class="media-heading comment-author"><span class="fn"><?php echo get_comment_author_link( $comment ); ?></span></h5>
					<div class="comment-meta comment-metadata">
						<time datetime="<?php echo comment_date( 'c' ); ?>"><a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>">
							<?php printf('%1$s', get_comment_date(),  get_comment_time()); ?></a>
						</time>
						|
						<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	        
						<?php edit_comment_link( __('| (Edit)', 'virtue' ), '', ''); ?>
					</div>
				</header>
				<?php if ($comment->comment_approved == '0') : ?>
				<div class="alert">
					<?php esc_html_e( 'Your comment is awaiting moderation.', 'virtue' ); ?>
				</div>
				<?php endif; ?>
				<div class="comment-content">
                    <?php comment_text(); ?>
                </div><!-- .comment-content -->
            </article><!-- .comment-body -->
<?php
    }
    public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
        if ( !empty( $args['end-callback'] ) ) {
            ob_start();
            call_user_func( $args['end-callback'], $comment, $args, $depth );
            $output .= ob_get_clean();
            return;
        }
        if ( 'div' == $args['style'] )
            $output .= "</div></div><!-- #comment-## -->\n";
        else
            $output .= "</div></li><!-- #comment-## -->\n";
    }
	
}

function virtue_get_avatar( $avatar ) {
	$avatar = str_replace( "class='avatar", "class='avatar pull-left media-object", $avatar );
	return $avatar;
}
add_filter( 'get_avatar', 'virtue_get_avatar' );

function virtue_custom_comments_before_feilds() {
	echo '<div class="row">';
}
add_action ('comment_form_before_fields', 'virtue_custom_comments_before_feilds', 5);

function virtue_custom_comments_after_feilds() {
	echo '</div>';
}
add_action ('comment_form_after_fields', 'virtue_custom_comments_after_feilds', 5);