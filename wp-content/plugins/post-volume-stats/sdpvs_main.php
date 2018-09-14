<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsMainContent {

	public function sdpvs_page_content() {
		$start_date = "";
		$end_date = "";

		// create an instance of the required classes
		$sdpvs_info = new sdpvsInfo();
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_pie = new sdpvsPieChart();

//		$years = $sdpvs_info -> sdpvs_how_many_years_of_posts();

		$options = get_option('sdpvs_year_option');
		$selected = absint($options['year_number']);
		if(isset($options['start_date'])){
			$start_date = filter_var ( $options['start_date'], FILTER_SANITIZE_STRING);
		}
		if(isset($options['end_date'])){
			$end_date = filter_var ( $options['end_date'], FILTER_SANITIZE_STRING);
		}

		$authoroptions = get_option('sdpvs_author_option');
		$author = absint($authoroptions['author_number']);

		$genoptions = get_option('sdpvs_general_settings');
		$authoroff = filter_var ( $genoptions['authoroff'], FILTER_SANITIZE_STRING);
		$customoff = filter_var ( $genoptions['customoff'], FILTER_SANITIZE_STRING);
		$customvalue = filter_var ( $genoptions['customvalue'], FILTER_SANITIZE_STRING);

		if("" != $author){
			$user = get_user_by( 'id', $author );
			$extradesc = "for $user->display_name";
		}else{
			$extradesc = "";
		}

		// Plugin Description
		if (0 < $selected) {
			echo '<p class="sdpvs">' . sprintf(__('These are the stats for %s %s for the selected year: <strong>%d</strong>. Click a bar of the "Years" or "Authors" bar charts to change to that year or author, or click the selected year/author (red) to view the stats for all years/authors.', 'post-volume-stats'), get_bloginfo('name'), $extradesc, $selected) . '</p>';
		}elseif($start_date){
			echo '<p class="sdpvs">' . sprintf(__('These are the stats for %s %s for the range: <strong>%s to %s</strong>. Click a bar of the "Years" or "Authors" bar charts to change to that year or author, or click the selected year/author (red) to view the stats for all years/authors.', 'post-volume-stats'), get_bloginfo('name'), $extradesc, $start_date, $end_date) . '</p>';
		} else {
			echo '<p class="sdpvs">' . sprintf(__('These are the <strong>all-time</strong> stats %s for %s. Click a bar of the "Years" bar chart to change to that year. Click a bar of "Authors" to change the stats to that author-only.', 'post-volume-stats'), $extradesc, get_bloginfo('name')) . '</p>';
		}


		echo "<hr>";

		if("one" != $authoroff){
			// posts per author bar chart
			echo "<div class='sdpvs_col'>";
			$sdpvs_bar -> sdpvs_draw_bar_chart_svg('author', $selected, $author, 'n', '', '', $start_date, $end_date);
			echo "</div>";
		}

		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// posts per month bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('month', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// posts per day of the month bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('dayofmonth', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// posts per day of the week bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('dayofweek', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// posts per hour of the day bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('hour', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// words per post bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('words', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// days between post bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('interval', $selected, $author, 'n', '', '', $start_date, $end_date);
		echo "</div>";

		// posts per category pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg('category', $selected, $author, 'n', 'n', $start_date, $end_date);
		echo "</div>";

		// posts per tag pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg('tag', $selected, $author, 'n', 'n', $start_date, $end_date);
		echo "</div>";

		if( "yes" == $customoff and "_all_taxonomies" == $customvalue ){
			// Custom Taxonomies
			$args = array(
				'public'   => true,
				'_builtin' => false
			);
			$all_taxes = get_taxonomies( $args );
			$count_taxes = count( $all_taxes );
			if( 1 < $count_taxes ){
				foreach ( $all_taxes as $taxonomy ) {
					if("category" != $taxonomy and "post_tag" != $taxonomy){
						$tax_labels = get_taxonomy($taxonomy);
						// posts per $tax_labels->name pie chart
						echo "<div class='sdpvs_col'>";
						echo $sdpvs_pie -> sdpvs_draw_pie_svg($tax_labels->name, $selected, $author, 'n', 'n', $start_date, $end_date);
						echo "</div>";
					}
				}
			}
		}elseif( "yes" == $customoff and "" != $customvalue ){
			// posts per custom taxonomy pie chart
			echo "<div class='sdpvs_col'>";
			echo $sdpvs_pie -> sdpvs_draw_pie_svg($customvalue, $selected, $author, 'n', 'n', $start_date, $end_date);
			echo "</div>";
		}
		return;
	}

}
?>
