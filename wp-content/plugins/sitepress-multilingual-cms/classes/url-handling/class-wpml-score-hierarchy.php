<?php

class WPML_Score_Hierarchy {

	private $data = array();
	private $slugs = array();

	/**
	 * WPML_Score_Hierarchy constructor.
	 *
	 * @param object[] $data_set
	 * @param string[] $slugs
	 */
	public function __construct( $data_set, $slugs ) {
		$this->data  = $data_set;
		$this->slugs = $slugs;
	}

	/**
	 * Array of best matched post_ids. Better matches have a lower index!
	 *
	 * @return int[]
	 */
	public function get_possible_ids_ordered() {
		$pages_with_name = $this->data;
		$slugs           = $this->slugs;
		$parent_slugs    = array_slice( $slugs, 0, - 1 );

		foreach ( $this->data as $key => $page ) {
			if ( $page->parent_name ) {
				$slug_pos     = array_keys( $slugs, $page->post_name, true );
				$par_slug_pos = array_keys( $slugs, $page->parent_name, true );
				if ( (bool) $par_slug_pos !== false
				     && (bool) $slug_pos !== false
				) {
					$remove = true;
					foreach ( $slug_pos as $child_slug_pos ) {
						if ( in_array( $child_slug_pos - 1, $par_slug_pos ) ) {
							$remove = false;
							break;
						}
					}
					if ( $remove === true ) {
						unset( $pages_with_name[ $key ] );
					}
				}
			}
		}
		$related_ids  = array();
		$matching_ids = array();
		foreach ( $pages_with_name as $key => $page ) {
			$correct_slug = end( $slugs );
			if ( $page->post_name === $correct_slug ) {
				if ( $this->is_matching( $page ) ) {
					$matching_ids[] = (int) $page->ID;
				} else {
					$related_ids[ $page->ID ] = $this->calculate_score( $parent_slugs, $pages_with_name, $page );
				}
			}
		}

		arsort( $related_ids );
		$related_ids = array_keys( $related_ids );

		return array(
			'matching_ids' => $matching_ids,
			'related_ids'  => $related_ids,
		);
	}

	/**
	 * @param string[] $parent_slugs
	 * @param object[] $pages_with_name
	 * @param object   $page
	 *
	 * @return int
	 */
	private function calculate_score( $parent_slugs, $pages_with_name, $page ) {
		$parent_positions = array_keys( $parent_slugs, $page->parent_name, true );
		$new_score        = (bool) $parent_positions === true ? max( $parent_positions ) + 1 : ( $page->post_parent ? - 1 : 0 );
		if ( $page->post_parent ) {
			foreach ( $pages_with_name as $parent ) {
				if ( $parent->ID == $page->post_parent ) {
					$new_score += $this->calculate_score( array_slice( $parent_slugs, 0, count( $parent_slugs ) - 1 ), $pages_with_name, $parent );
					break;
				}
			}
		}

		return $new_score;
	}

	private function is_matching( $page ) {
		$revert_slugs = array_reverse( $this->slugs );
		$current_page = $page;

		foreach ( $revert_slugs as $slug ) {
			if ( $slug !== $current_page->post_name ) {
				return false;
			}

			foreach ( $this->data as $page ) {
				if ( $page->ID === $current_page->post_parent ) {
					$current_page = $page;
					break;
				}
			}
		}

		return true;
	}
}