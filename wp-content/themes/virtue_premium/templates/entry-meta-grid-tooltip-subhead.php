<div class="subhead color_gray">
	<?php do_action( 'virtue_before_post_meta_tooltip' ); ?>
    <span class="postauthortop" rel="tooltip" data-placement="top" data-original-title="<?php echo get_the_author() ?>">
        <i class="icon-user"></i>
    </span>
    <span class="kad-hidepostauthortop"> | </span>
    
    <?php $post_category = get_the_category($post->ID); if (!empty($post_category)) { ?> 
        <span class="postedintop" rel="tooltip" data-placement="top" data-original-title="<?php 
            foreach ($post_category as $category)  { 
                echo $category->name .'&nbsp;'; 
            } ?>"><i class="icon-folder"></i>
        </span>
    <?php }?>
    <?php if(comments_open() || (get_comments_number() != 0) ) { ?>
        <span class="kad-hidepostedin">|</span>
        <span class="postcommentscount" rel="tooltip" data-placement="top" data-original-title="<?php echo esc_attr(get_comments_number()); ?>">
          <i class="icon-bubbles"></i>
        </span>
    <?php }?>
    <span class="postdatetooltip">|</span>
     <span style="margin-left:3px;" class="postdatetooltip" rel="tooltip" data-placement="top" data-original-title="<?php echo get_the_date(); ?>">
      <i class="icon-calendar"></i>
    </span>
    <?php do_action( 'virtue_after_post_meta_tooltip' ); ?>
</div>   