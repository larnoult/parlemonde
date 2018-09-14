<script type="text/javascript">
	var options = {
		markerTypes: {
			imageCustom: '<?php echo MarkNewPosts_MarkerType::IMAGE_CUSTOM; ?>'
		},
		messages: {
			imageUrl: '<?php _e('Please specify image URL', self::TEXT_DOMAIN); ?>',
			postStaysNewDays: '<?php _e('Please specify correct count of days', self::TEXT_DOMAIN); ?>'
		}
	};
	MarkNewPostsAdminForm(jQuery, options);
</script>
<div class="mnp-options">
	<div class="mnp-title">
		<?php echo self::PLUGIN_NAME ?>
	</div>
	<div class="mnp-row mnp-divider">
		<div class="mnp-col">
			<?php _e('Marker placement', self::TEXT_DOMAIN); ?>
		</div>
		<div class="mnp-col">
			<select id="mnp-show-marker-placement" class="mnp-input">
				<?php
					$option = $this->options->marker_placement;
					$this->echo_option($option, MarkNewPosts_MarkerPlacement::TITLE_BEFORE, __('Before post title', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerPlacement::TITLE_AFTER, __('After post title', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerPlacement::TITLE_BOTH, __('Before and after post title', self::TEXT_DOMAIN));
				?>
			</select>
		</div>
	</div>
	<div class="mnp-row">
		<div class="mnp-col">
			<?php _e('Marker type', self::TEXT_DOMAIN) ?>
		</div>
		<div class="mnp-col">
			<select id="mnp-marker-type" class="mnp-input">
				<?php
					$option = $this->options->marker_type;
					$this->echo_option($option, MarkNewPosts_MarkerType::CIRCLE, __('Orange circle', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerType::TEXT, __('"New" text', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerType::FLAG, __('Flag', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerType::IMAGE_DEFAULT, __('Picture (default)', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerType::IMAGE_CUSTOM, __('Picture (custom)', self::TEXT_DOMAIN));
					$this->echo_option($option, MarkNewPosts_MarkerType::NONE, __('None', self::TEXT_DOMAIN));
				?>
			</select>
		</div>
	</div>
	<div id="mnp-image-row" class="mnp-row" style="display: none">
		<div class="mnp-col">
			<?php _e('Image URL', self::TEXT_DOMAIN) ?>
		</div>
		<div class="mnp-col">
			<input type="text" id="mnp-image-url" class="mnp-input"
				value="<?php echo $this->options->image_url; ?>">
		</div>
	</div>
	<div class="mnp-row mnp-divider">
		<div><?php _e('A post should be marked as read:', self::TEXT_DOMAIN); ?></div>
		<div class="mnp-radio">
			<?php
				$this->echo_mark_after_option(MarkNewPosts_MarkAfter::OPENING_POST, __('after viewing the post\'s page', self::TEXT_DOMAIN));
				$this->echo_mark_after_option(MarkNewPosts_MarkAfter::OPENING_LIST, __('after viewing the post on any page', self::TEXT_DOMAIN));
				$this->echo_mark_after_option(MarkNewPosts_MarkAfter::OPENING_BLOG, __('after opening any page of the blog', self::TEXT_DOMAIN));
			?>
		</div>
	</div>
	<div class="mnp-row mnp-divider">
		<input type="checkbox" id="mnp-post-stays-new" autocomplete="off"
			<?php if ($this->options->post_stays_new_days) { echo 'checked="checked"'; } ?>>
		<label for="mnp-post-stays-new"><?php
			$template = __('Published post stays marked as new only for %s days', self::TEXT_DOMAIN);
			$input = '<input type="text" id="mnp-post-stays-new-days" autocomplete="off" value="'
				. ($this->options->post_stays_new_days ? $this->options->post_stays_new_days : '') . '" />';
			echo sprintf($template, $input);
		?></label>
	</div>
	<div class="mnp-row">
		<input type="checkbox" id="mnp-all-new-for-new-visitor" autocomplete="off"
			<?php if ($this->options->all_new_for_new_visitor) { echo 'checked="checked"'; } ?>>
		<label for="mnp-all-new-for-new-visitor"><?php _e('Mark all existing posts as new to new visitors', self::TEXT_DOMAIN); ?></label>
	</div>
	<div class="mnp-row mnp-divider">
		<input type="checkbox" id="mnp-check-markup" autocomplete="off"
			<?php if ($this->options->check_markup) { echo 'checked="checked"'; } ?>>
		<label for="mnp-check-markup"><?php _e('Check page markup before displaying a marker', self::TEXT_DOMAIN); ?></label>
		<div class="mnp-note"><?php _e('Enable this option only if the plugin is exploding your blog\'s markup.', self::TEXT_DOMAIN); ?></div>
	</div>
	<div class="mnp-buttons-set mnp-divider">
		<button id="mnp-save-options-btn" class="mnp-button mnp-button-green">
			<?php _e('Save', self::TEXT_DOMAIN); ?>
		</button>
		<button id="mnp-reset-options-btn" class="mnp-button mnp-button-blue">
			<?php _e('Reset', self::TEXT_DOMAIN); ?>
		</button>
	</div>
	<div class="mnp-clearfix"></div>
	<div id="mnp-message" class="mnp-message"></div>
	<div class="mnp-clearfix"></div>
</div>