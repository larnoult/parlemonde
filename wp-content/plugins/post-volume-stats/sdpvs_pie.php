<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsPieChart extends sdpvsArrays {
	private $number_of_categories = 0;
	private $total_category_posts = 0;
	private $number_of_tags = 0;
	private $total_tag_posts = 0;
	private $newx;
	private $newy;

	/*
	 * COUNT NUMBER OF POSTS PER CATEGORY IN TOTAL, some posts might have multiple cats
	 */
	private function sdpvs_count_post_taxonomies($year = "", $author = "") {
		$this -> number_of_taxonomies = "";
		$this -> total_taxonomy_posts = "";
		$c = 0;
		while (array_key_exists($c, $this->tax_type_array)) {
			if (0 < $this -> tax_type_array[$c]['volume']) {
				$this -> number_of_taxonomies++;
				$this -> total_taxonomy_posts += absint ( $this -> tax_type_array[$c]['volume'] );
			}
			$c++;
		}
		return;
	}

	/*
	 * ADD THE ANGLE TO THE EXISTING CATEGORY ARRAY
	 */
	private function sdpvs_add_to_taxonomy_array($type = "", $year = "", $author = "", $start_date = "", $end_date = "") {
		$this -> tax_type_array = "";
		parent::sdpvs_post_taxonomy_type_volumes($type, $year,$author, $start_date, $end_date);
		$this -> sdpvs_count_post_taxonomies($year,$author);
		$c = 0;
		while (array_key_exists($c, $this->tax_type_array)) {
			if (0 < $this -> tax_type_array[$c]['volume']) {
				$this -> tax_type_array[$c]['angle'] = ( absint($this -> tax_type_array[$c]['volume']) / $this -> total_taxonomy_posts) * 360;
			}
			$c++;
		}
		return;
	}

	/**
	 * DISPLAY CATEGORY DATA IN A PIE CHART
	 */
	public function sdpvs_draw_pie_svg($type = "", $year = "", $author = "", $subpage = "", $public = "", $start_date = "", $end_date = "") {
		$testangle_orig = 0;
		$radius = 100;
		$prev_angle = 0;
		$remaining = 0;
		$this -> newx = 0;
		$this -> newy = 0;
		$this -> tax_type_array = array();

		$genoptions = get_option('sdpvs_general_settings');
		$exportcsv = filter_var ( $genoptions['exportcsv'], FILTER_SANITIZE_STRING);

		if ("category" == $type) {
			$this -> sdpvs_add_to_taxonomy_array($type,$year,$author, $start_date, $end_date);
			$pie_array = $this -> tax_type_array;
			$total_volume = $this -> total_taxonomy_posts;
			$number_of_containers = $this -> number_of_taxonomies;
			$pie_svg = '<h2>' . esc_html__("Categories", 'post-volume-stats') . '</h2>';
			$link_part = "category_name";
		} elseif ("tag" == $type) {
			$wp_type_name = "post_tag";
			$this -> sdpvs_add_to_taxonomy_array($wp_type_name,$year,$author, $start_date, $end_date);
			$total_volume = $this -> total_taxonomy_posts;
			$pie_array = $this -> tax_type_array;
			$number_of_containers = $this -> number_of_taxonomies;
			$pie_svg = '<h2>' . esc_html__("Tags", 'post-volume-stats') . '</h2>';
			$link_part = $type;
		}else{
			$this -> sdpvs_add_to_taxonomy_array($type,$year,$author, $start_date, $end_date);
			$total_volume = $this -> total_taxonomy_posts;
			$pie_array = $this -> tax_type_array;
			$number_of_containers = $this -> number_of_taxonomies;
			$tax_labels = get_taxonomy($type);
			$pie_svg = '<h2>' . esc_html__($tax_labels->label, 'post-volume-stats') . '</h2>';
			$link_part = $type;
		}
		if ("year" != $type and "y" == $public) {
			if ( isset($searchyear) and 0 < $searchyear) {
				$pie_svg .= '<h3>' . sprintf(esc_html__('%d', 'post-volume-stats'), $searchyear) . '</h3>';
			} else {
				$pie_svg .= '<h3>' . esc_html__('All-time', 'post-volume-stats') . '</h3>';
			}
		}

		$pie_svg .= "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\" class=\"sdpvs_pie\"><circle cx=\"$radius\" cy=\"$radius\" r=\"$radius\" stroke=\"black\" stroke-width=\"1\" />\n";

		$c = 0;
		while (array_key_exists($c, $pie_array)) {
			if (0 < $pie_array[$c]['volume']) {
				if (0 < $pie_array[$c]['angle']) {
					$prev_angle = $testangle_orig;
					$testangle_orig += $pie_array[$c]['angle'];
					$quadrant = $this -> sdpvs_specify_starting_quadrant($testangle_orig);
					$testangle = $this -> sdpvs_specify_testangle($testangle_orig);
					$large = $this -> sdpvs_is_it_a_large_angle($testangle_orig, $prev_angle);
					$startingline = $this -> sdpvs_draw_starting_line($prev_angle, $this -> newx, $this -> newy);
					$this -> sdpvs_get_absolute_coordinates_from_angle($quadrant, $radius, $testangle);

					// Change the hue instead
					if(0==$c){
						$largest_angle = $pie_array[$c]['angle'];
					}

					$hue = 240 - intval($pie_array[$c]['angle']*240 / $largest_angle);
					$color = "hsl($hue, 70%, 65%)";

					$display_angle_as = sprintf("%.1f", $pie_array[$c]['angle']);

					if("y"==$public){
						$item_id = $pie_array[$c]['id'];
						if ("category" == $type) {
							$link = get_category_link($item_id);
						}elseif("tag" == $type){
							$link = get_tag_link($item_id);
						}
					}else{
						$link = admin_url("edit.php?$link_part=" . $pie_array[$c]['slug']);
					}
					if (360 == $pie_array[$c]['angle']) {
						// If only one category exists make sure there is a green solid circle
						$pie_svg .= "<a href='$link' xlink:title=\"{$pie_array[$c]['name']}, {$pie_array[$c]['volume']} posts\"><circle class=\"sdpvs_segment\" cx='100' cy='100' r='100' fill='red'/></a>\n";
					} else {
						$pie_svg .= "  <a href='$link' xlink:title=\"{$pie_array[$c]['name']}, {$pie_array[$c]['volume']} posts\"><path id=\"{$pie_array[$c]['name']}\" class=\"sdpvs_segment\" d=\"M$radius,$radius $startingline A$radius,$radius 0 $large,1 $this->newx,$this->newy z\" fill=\"$color\" stroke=\"black\" stroke-width=\"1\"  /></a>\n";
					}
				}
			}
			$c++;
		}
		$pie_svg .= "</svg>\n";

		if ("n" == $subpage and "y"!=$public) {
			$pie_svg .= "<p>";
			$pie_svg .= "<form class='sdpvs_form' action='' method='POST'><input type='hidden' name='whichdata' value='$type'><input type='submit' class='button-primary sdpvs_load_content' value='Show Data'></form>";
			$pie_svg .= "</p>";
			$pie_svg .= "<p>";
			$pie_svg .= "<form class='sdpvs_compare' action='' method='POST'><input type='hidden' name='comparedata' value='$type'><input type='submit' class='button-primary sdpvs_load_content' value='Compare Years'></form>";
			$pie_svg .= "</p>";
				if("yes"==$exportcsv){
					$sdpvs_csv_download_url = admin_url("/download-csv/$type.csv");
					$pie_svg .= "<p>";
					$pie_svg .= "<form class='sdpvs_export' action=\"$sdpvs_csv_download_url\" method='POST'><input type='submit' class='button-primary' value='Export Compare Years CSV'></form>";
					$pie_svg .= "</p>";
				}

		}

		return $pie_svg;
	}

	/**
	 * WHICH QUADRANT OF THE CIRCLE ARE WE IN ?
	 */
	private function sdpvs_specify_starting_quadrant($testangle_orig) {
		if (270 < $testangle_orig) {
			$quadrant = 4;
		} elseif (180 < $testangle_orig) {
			$quadrant = 3;
		} elseif (90 < $testangle_orig) {
			$quadrant = 2;
		} else {
			$quadrant = 1;
		}
		return $quadrant;
	}

	/**
	 * MAKE AN ACUTE ANGLE
	 */
	private function sdpvs_specify_testangle($testangle_orig) {
		if (270 < $testangle_orig) {
			$testangle = $testangle_orig - 270;
		} elseif (180 < $testangle_orig) {
			$testangle = $testangle_orig - 180;
		} elseif (90 < $testangle_orig) {
			$testangle = $testangle_orig - 90;
		} else {
			$testangle = $testangle_orig;
		}
		return $testangle;
	}

	/**
	 * IS THE ANGLE MORE THAN 180 DEGREES ?
	 */
	private function sdpvs_is_it_a_large_angle($testangle_orig, $prev_angle) {
		if (180 < $testangle_orig - $prev_angle) {
			$large = 1;
		} else {
			$large = 0;
		}
		return $large;
	}

	/**
	 * THIS GRABS THE INFO FROM THE PREVIOUS POINT AND STARTS OFF THE PIE SEGMENT
	 */
	private function sdpvs_draw_starting_line($prev_angle, $newx, $newy) {
		if (0 < $prev_angle) {
			$startingline = "L$newx,$newy";
		} else {
			$startingline = "V0";
		}
		return $startingline;
	}

	/**
	 * GET NEW X CO-ORDINATES
	 */
	private function sdpvs_get_absolute_coordinates_from_angle($quadrant, $radius, $testangle) {
		if (1 == $quadrant) {
			$this -> newx = $radius + ($radius * sin(deg2rad($testangle)));
			$this -> newy = $radius - ($radius * cos(deg2rad($testangle)));
		} elseif (2 == $quadrant) {
			$this -> newx = $radius + ($radius * cos(deg2rad($testangle)));
			$this -> newy = $radius + ($radius * sin(deg2rad($testangle)));
		} elseif (3 == $quadrant) {
			$this -> newx = $radius - ($radius * sin(deg2rad($testangle)));
			$this -> newy = $radius + ($radius * cos(deg2rad($testangle)));
		} elseif (4 == $quadrant) {
			$this -> newx = $radius - ($radius * cos(deg2rad($testangle)));
			$this -> newy = $radius - ($radius * sin(deg2rad($testangle)));
		}
		return;
	}

}
?>
