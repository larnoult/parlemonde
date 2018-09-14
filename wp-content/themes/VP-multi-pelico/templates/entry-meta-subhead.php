<div class="subhead">
    <span class="postauthortop author vcard">
        <i class="icon-user2"></i> <?php global $virtue_premium; if(!empty($virtue_premium['post_by_text'])) {$authorbytext = $virtue_premium['post_by_text'];} else {$authorbytext = __('by', 'virtue');} echo $authorbytext; ?>  <span itemprop="author"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="fn" rel="author"><?php echo get_the_author() ?></a></span> |
    </span>
    <?php $post_category = get_the_category(); if ( $post_category==true ) { 
    if(!empty($virtue_premium['post_incat_text'])) {$incattext = $virtue_premium['post_incat_text'];} else {$incattext = __('posted in:', 'virtue');}
    ?>  <span class="postedintop"><i class="icon-drawer"></i> <?php echo $incattext;?> <?php the_category(', ') ?> </span>
    <span class="kad-hidepostedin">|</span><?php }?>
	<span> 
	 <?php if( false != get_the_term_list( $post->ID, 'schooltheme' ) )
		{echo "<i class=\"icon-tag\"></i> Les classes nous parlent : " ;
	the_terms(0, 'schooltheme');
	echo " |";
}
	?>  
	</span>
    <span class="postcommentscount">
    <a href="<?php the_permalink();?>#virtue_comments"><i class="icon-bubbles"></i> <?php comments_number( '0', '1', '%' ); ?></a>
    </span>
</div>