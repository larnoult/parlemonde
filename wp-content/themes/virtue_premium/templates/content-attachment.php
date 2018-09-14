<?php 
/* Attachment Page Content */
?>
<div id="content" class="container">
    <div id="post-<?php the_ID(); ?>" class="row single-article kt-attachment-page">
        <div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
            <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <a href="<?php echo wp_get_attachment_url(get_the_ID());?>">
                    <?php echo wp_get_attachment_image( get_the_ID(), 'full' ); ?>
                </a>
                <header>
                    <a href="<?php echo wp_get_attachment_url(get_the_ID());?>">
                        <h1 class="entry-title" itemprop="name headline"><?php the_title(); ?></h1>
                    </a>
                    <?php get_template_part('templates/entry', 'meta-subhead'); ?>
                </header>
                <div class="entry-content clearfix" itemprop="description articleBody">
                    <?php the_content(); ?>
                </div>
                <footer class="single-footer">
                    <meta itemprop="dateModified" content="<?php echo esc_attr(get_the_modified_date()); ?>">
                </footer>
                <?php comments_template('/templates/comments.php'); ?>
            </article>
            <?php endwhile; ?>
        </div>
