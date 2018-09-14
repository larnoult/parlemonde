<?php
/**
 * Comments Template
 *
 * @package Virtue Premium
 */

if ( post_password_required() ) {
	return;
}
echo '<div id="virtue_comments"></div>';
if ( have_comments() && ( comments_open() || get_comments_number() ) ) :
	?>
	<section id="comments">
		<h3>
			<?php
			$comments_number = get_comments_number();
			if ( '1' === $comments_number ) {
				printf( esc_html__( 'One Response', 'virtue' ), esc_html( $comments_number ) );
			} else {
				/* translators: %d comment count */
				printf( esc_html( _n( '%d Response', '%d Responses', $comments_number, 'virtue' ) ), esc_html( $comments_number ) );
			}
			?>
		</h3>

		<ol class="media-list">
			<?php wp_list_comments( array( 'walker' => new Kadence_Walker_Comment() ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav>
				<ul class="pager">
				<?php if ( get_previous_comments_link() ) : ?>
					<li class="previous"><?php previous_comments_link( __( '&larr; Older comments', 'virtue' ) ); ?></li>
				<?php endif; ?>
				<?php if ( get_next_comments_link() ) : ?>
					<li class="next"><?php next_comments_link( __( 'Newer comments &rarr;', 'virtue' ) ); ?></li>
				<?php endif; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php
		if ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
			global $virtue_premium;
			if ( isset( $virtue_premium['close_comments'] ) ) {
				$show_closed_comment = $virtue_premium['close_comments'];
			} else {
				$show_closed_comment = 1;
			}
			if ( 1 == $show_closed_comment ) {
				?>
				<div class="alert">
					<?php esc_html_e( 'Comments are closed.', 'virtue' ); ?>
				</div>
				<?php
			}
		endif;
		?>
	</section><!-- /#comments -->
<?php
endif;
if ( ! have_comments() && ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	global $virtue_premium;
	if ( isset( $virtue_premium['close_comments'] ) ) {
		$show_closed_comment = $virtue_premium['close_comments'];
	} else {
		$show_closed_comment = 1;
	}
	if ( 1 == $show_closed_comment ) {
		?>
		<section id="comments">
			<div class="alert">
				<?php esc_html_e( 'Comments are closed.', 'virtue' ); ?>
			</div>
		</section><!-- /#comments -->
		<?php
	}
endif;

if ( comments_open() ) :
	?>
	<section id="respond-container">
		<?php comment_form(); ?>
	</section><!-- /#respond -->
<?php
endif;
