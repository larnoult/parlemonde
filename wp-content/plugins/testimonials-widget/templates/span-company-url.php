<?php
global $tw_template_args;

$testimonial = $tw_template_args['testimonial'];
?>
<span class="company"><a href="<?php echo $testimonial['testimonial_url']; ?>" target="_blank" rel="nofollow"><?php echo $testimonial['testimonial_company']; ?></a></span>
