<?php
	class MarkNewPosts_MarkerPlacement {
		const TITLE_BEFORE = 0;
		const TITLE_AFTER = 1;
		const TITLE_BOTH = 2;
	}

	class MarkNewPosts_MarkerType {
		const NONE = 0;
		const CIRCLE = 1;
		const TEXT = 2;
		const IMAGE_DEFAULT = 3;
		const IMAGE_CUSTOM = 4;
		const FLAG = 5;
	}

	class MarkNewPosts_MarkAfter {
		const OPENING_POST = 0; // post gets marked only after opening it
		const OPENING_LIST = 1; // post gets marked after being displayed in posts list
		const OPENING_BLOG = 2; // all posts get marked after opening any blog page
	}

	class MarkNewPosts_Options {
		public $marker_placement = MarkNewPosts_MarkerPlacement::TITLE_BEFORE;
		public $marker_type = MarkNewPosts_MarkerType::CIRCLE;
		public $image_url;
		public $mark_after = MarkNewPosts_MarkAfter::OPENING_LIST;
		public $post_stays_new_days = 0;
		public $all_new_for_new_visitor = false;
		public $check_markup = false;
	}
?>