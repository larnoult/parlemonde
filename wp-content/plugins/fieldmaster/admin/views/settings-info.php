<?php

// extract args
extract( $args );

?>
<div class="wrap about-wrap fieldmaster-wrap">

	<h1><?php _e("Welcome to FieldMaster",'fieldmaster'); ?> <?php echo $version; ?></h1>
	<div class="about-text"><?php printf(__("Thank you for updating! FieldMaster %s is bigger and better than ever before. We hope you like it.", 'fieldmaster'), $version); ?></div>
	<div class="fieldmaster-icon logo">
		<i class="fieldmaster-sprite-logo"></i>
	</div>

	<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $tab_slug => $tab_title ): ?>
			<a class="nav-tab<?php if( $active == $tab_slug ): ?> nav-tab-active<?php endif; ?>" href="<?php echo admin_url("edit.php?post_type=fm-field-group&page=fieldmaster-settings-info&tab={$tab_slug}"); ?>"><?php echo $tab_title; ?></a>
		<?php endforeach; ?>
	</h2>

<?php if( $active == 'new' ): ?>

	<h2 class="about-headline-callout"><?php _e("A smoother custom field experience", 'fieldmaster'); ?></h2>

	<div class="feature-section fieldmaster-three-col">
		<div>
			<h3><?php _e("Improved Usability", 'fieldmaster'); ?></h3>
			<p><?php _e("Including the popular Select2 library has improved both usability and speed across a number of field types including post object, page link, taxonomy and select.", 'fieldmaster'); ?></p>
		</div>
		<div>
			<h3><?php _e("Improved Design", 'fieldmaster'); ?></h3>
			<p><?php _e("Many fields have undergone a visual refresh to make FieldMaster look better than ever! Noticeable changes are seen on the gallery, relationship and oEmbed (new) fields!", 'fieldmaster'); ?></p>
		</div>
		<div>
			<h3><?php _e("Improved Data", 'fieldmaster'); ?></h3>
			<p><?php _e("Redesigning the data architecture has allowed sub fields to live independently from their parents. This allows you to drag and drop fields in and out of parent fields!", 'fieldmaster'); ?></p>
		</div>
	</div>

	<hr />

	<h2 class="about-headline-callout"><?php _e("Goodbye Profiteering. Hello Freedom", 'fieldmaster'); ?></h2>

	<div class="feature-section fieldmaster-three-col">

		<div>
			<h3><?php _e("Introducing FieldMaster, Completely Free Forever", 'fieldmaster'); ?></h3>
			<p><?php _e("FieldMaster is free, and always will be.", 'fieldmaster'); ?></p>
			<p><?php printf(__('FieldMaster is a liberation project, and we are committed to maintaining and supporting it for free, forever.', 'fieldmaster'), esc_url('https://goldhat.ca/fieldmaster')); ?></p>
		</div>

	</div>

	<hr />

	<h2 class="about-headline-callout"><?php _e("Under the Hood", 'fieldmaster'); ?></h2>

	<div class="feature-section fieldmaster-three-col">

		<div>
			<h4><?php _e("Smarter field settings", 'fieldmaster'); ?></h4>
			<p><?php _e("FieldMaster now saves its field settings as individual post objects", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("More AJAX", 'fieldmaster'); ?></h4>
			<p><?php _e("More fields use AJAX powered search to speed up page loading", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("Local JSON", 'fieldmaster'); ?></h4>
			<p><?php _e("New auto export to JSON feature improves speed", 'fieldmaster'); ?></p>
		</div>

		<br />

		<div>
			<h4><?php _e("Better version control", 'fieldmaster'); ?></h4>
			<p><?php _e("New auto export to JSON feature allows field settings to be version controlled", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("Swapped XML for JSON", 'fieldmaster'); ?></h4>
			<p><?php _e("Import / Export now uses JSON in favour of XML", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("New Forms", 'fieldmaster'); ?></h4>
			<p><?php _e("Fields can now be mapped to comments, widgets and all user forms!", 'fieldmaster'); ?></p>
		</div>

		<br />

		<div>
			<h4><?php _e("New Field", 'fieldmaster'); ?></h4>
			<p><?php _e("A new field for embedding content has been added", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("New Gallery", 'fieldmaster'); ?></h4>
			<p><?php _e("The gallery field has undergone a much needed facelift", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("New Settings", 'fieldmaster'); ?></h4>
			<p><?php _e("Field group settings have been added for label placement and instruction placement", 'fieldmaster'); ?></p>
		</div>

		<br />

		<div>
			<h4><?php _e("Better Front End Forms", 'fieldmaster'); ?></h4>
			<p><?php _e("fieldmaster_form() can now create a new post on submission", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("Better Validation", 'fieldmaster'); ?></h4>
			<p><?php _e("Form validation is now done via PHP + AJAX in favour of only JS", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("Relationship Field", 'fieldmaster'); ?></h4>
			<p><?php _e("New Relationship field setting for 'Filters' (Search, Post Type, Taxonomy)", 'fieldmaster'); ?></p>
		</div>

		<br />

		<div>
			<h4><?php _e("Moving Fields", 'fieldmaster'); ?></h4>
			<p><?php _e("New field group functionality allows you to move a field between groups & parents", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("Page Link", 'fieldmaster'); ?></h4>
			<p><?php _e("New archives group in page_link field selection", 'fieldmaster'); ?></p>
		</div>

		<div>
			<h4><?php _e("Better Options Pages", 'fieldmaster'); ?></h4>
			<p><?php _e("New functions for options page allow creation of both parent and child menu pages", 'fieldmaster'); ?></p>
		</div>

	</div>



<?php elseif( $active == 'changelog' ): ?>

	<p class="about-description"><?php printf(__("We think you'll love the changes in %s.", 'fieldmaster'), $version); ?></p>

	<?php

	$items = file_get_contents( fieldmaster_get_path('readme.txt') );
	$items = explode('= ' . $version . ' =', $items);

	$items = end( $items );
	$items = current( explode("\n\n", $items) );
	$items = array_filter( array_map('trim', explode("*", $items)) );

	?>
	<ul class="changelog">
	<?php foreach( $items as $item ):

		$item = explode('http', $item);

		?>
		<li><?php echo $item[0]; ?><?php if( isset($item[1]) ): ?><a href="http<?php echo $item[1]; ?>" target="_blank">[...]</a><?php endif; ?></li>
	<?php endforeach; ?>
	</ul>

<?php endif; ?>

</div>
