                      <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in kad_blog_fade_in kt_item_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
                            <?php if (has_post_thumbnail( $post->ID ) ) {
                              $image_url = wp_get_attachment_image_src( 
                              get_post_thumbnail_id( $post->ID ), 'full' ); 
                              $thumbnailURL = $image_url[0];
                              $image = aq_resize($thumbnailURL, 260, false);
                              if(empty($image)) { $image = $thumbnailURL; }
                              ?>
                                  <div class="imghoverclass img-margin-center">
                                    <a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
                                      <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" class="iconhover" style="display:block;">
                                    </a> 
                                  </div>
                              <?php $image = null; $thumbnailURL = null; }  ?>
                      <div class="postcontent">
                          <header>
                              <a href="<?php the_permalink() ?>"><h5 class="entry-title" itemprop="name headline"><?php the_title(); ?></h5></a>
                                <div class="subhead color_gray">
                                  <span class="postauthortop author vcard" rel="tooltip" data-placement="top" data-original-title="<?php echo get_the_author() ?>">
                                    <i class="icon-user"></i>
                                  </span>
                                  <span class="kad-hidepostauthortop"> | </span>
                                    <?php $post_category = get_the_category($post->ID); if (!empty($post_category)) { ?> 
                                    <span class="postedintop" rel="tooltip" data-placement="top" data-original-title="<?php 
                                      foreach ($post_category as $category)  { 
                                        echo $category->name .'&nbsp;'; 
                                      } ?>"><i class="icon-folder"></i></span>
                                                                       <span class="kad-hidepostedin">|</span>
                                                                       <?php }?>
                                <?php if(comments_open() || (get_comments_number() != 0) ) { ?>                        
                                <span class="postcommentscount" rel="tooltip" data-placement="top" data-original-title="<?php echo esc_attr(get_comments_number()); ?>">
                                  <i class="icon-bubbles"></i>
                                </span>
                                <span class="postdatetooltip">|</span>
                                <?php }?>
                                 <span style="margin-left:3px;" class="postdatetooltip updated" rel="tooltip" data-placement="top" data-original-title="<?php echo get_the_date(); ?>">
                                  <i class="icon-calendar"></i>
                                </span>
                              </div>   
                          </header>
                          <div class="entry-content" itemprop="articleBody">
                              <?php the_excerpt(); ?>
                          </div>
                          <footer>
                              <?php $tags = get_the_tags(); if ($tags) { ?> <span class="posttags color_gray"><i class="icon-tag"></i> <?php the_tags('', ', ', ''); ?> </span><?php } ?>
                          </footer>
                        </div><!-- Text size -->
              </div> <!-- Blog Item -->

              