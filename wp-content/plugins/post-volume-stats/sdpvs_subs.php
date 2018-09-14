<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsSubPages {

	public function sdpvs_combined_page_content($type = "") {

		// create an instance of the required classes
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_pie = new sdpvsPieChart();
		$sdpvs_lists = new sdpvsTextLists();

		$year = get_option('sdpvs_year_option');
		$searchyear = absint($year['year_number']);
		$authoroptions = get_option('sdpvs_author_option');
		$searchauthor = absint($authoroptions['author_number']);
		$genoptions = get_option('sdpvs_general_settings');
		$authoroff = filter_var ( $genoptions['authoroff'], FILTER_SANITIZE_STRING);
		
		$selected = "";
		
		if ("category" == $type) {
			$typetitle = "Category";
			$typetitleplural = "Categories";
		} elseif ("tag" == $type) {
			$typetitle = "Tag";
			$typetitleplural = "Tags";
		}else{
			$tax_labels = get_taxonomy($type);
			$typetitle = $tax_labels->labels->singular_name;
			$typetitleplural = $tax_labels->label;
		}

		echo '<h1 class="sdpvs">' . sprintf(esc_html__('Post Volume Stats: %s', 'post-volume-stats'), $typetitleplural) . '</h1>';

		// Plugin Description
		if (0 < $searchyear) {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the "%1$s" stats for %2$s for the selected year: %3$d. Click a bar of the "Authors" and/or "Years" bar charts to filter the stats, click a red bar to remove the filter.', 'post-volume-stats'), $typetitle, get_bloginfo('name'), $searchyear) . '</p>';
		} else {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the all-time "%1$s" stats for %2$s. Click a bar of the "Authors" and/or "Years" bar charts to filter the stats, click a red bar to remove the filter.', 'post-volume-stats'), $typetitle, get_bloginfo('name')) . '</p>';
		}

		echo "<hr>";
		
		echo "<div style='display: inline-block; width: 250px; vertical-align: top;'>";
		
		if("one" != $authoroff){
			// posts per author bar chart
			echo "<div class='sdpvs_col'>";
			$sdpvs_bar -> sdpvs_draw_bar_chart_svg('author', $selected, $searchauthor, 'y');
			echo "</div>";
		}
		
		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year', $searchyear, $searchauthor, 'y');
		echo "</div>";
		
		echo "<div style='display: block; width: 250px; vertical-align: top;' id='sdpvs_listselect'>";
		echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($type, $searchyear, $searchauthor, '','','subpage', '');
		echo "</div>";
		
		echo "</div>";

		// Get both methods from AJAX call.
		echo "<div style='display: inline-block; vertical-align: top;' id='sdpvs_ajax_lists'>";

		echo "</div>";

		return;
	}

	public function update_ajax_lists($type, $matches) {

		// create an instance of the required classes
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_lists = new sdpvsTextLists();

		$year = get_option('sdpvs_year_option');
		$searchyear = absint($year['year_number']);
		$authoroptions = get_option('sdpvs_author_option');
		$searchauthor = absint($authoroptions['author_number']);
		$genoptions = get_option('sdpvs_general_settings');
		$rainbow = filter_var ( $genoptions['rainbow'], FILTER_SANITIZE_STRING);

		$color = $sdpvs_lists -> sdpvs_color_list();
		
		// Big Line Graph
		echo "<div style='display: block; width: 750px; vertical-align: top;' id='sdpvs_listgraph'>";
		echo $sdpvs_bar -> sdpvs_comparison_line_graph($type, $matches, $searchauthor, $color);
		echo "</div>";

		echo "<div style='display: inline-block; width: 500px; vertical-align: top;' id='sdpvs_listsource'>";
		echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($type, $searchyear, $searchauthor, '', '', 'public', $matches, $color);
		echo "</div>";

		echo "<div style='display: inline-block; width: 250px; vertical-align: top;' id='sdpvs_listpublic'>";
		echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($type, $searchyear, $searchauthor, '', '', 'buttons', $matches);
		echo "</div>";

		

		return;

	}

}
?>
