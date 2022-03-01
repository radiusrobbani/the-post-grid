<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class rtTPGElementorQuery {

	public static function post_query( $data, $prefix = '' ) {
		$args = [
			'post_type'   => [ $data['post_type'] ],
			'post_status' => $data['post_status'],
		];

		$excluded_ids = null;

		if ( $data['exclude'] ) {
			$excluded_ids         = explode( ',', $data['exclude'] );
			$excluded_ids         = array_map( 'trim', $excluded_ids );
			$args['post__not_in'] = $excluded_ids;
		}

		if ( $data['post_id'] ) {
			$post_ids = explode( ',', $data['post_id'] );
			$post_ids = array_map( 'trim', $post_ids );

			$args['post__in'] = $post_ids;

			if ( $excluded_ids != null && is_array( $excluded_ids ) ) {
				$args['post__in'] = array_diff( $post_ids, $excluded_ids );
			}
		}

		if ( $prefix !== 'slider' && 'pagination' !== $data['pagination_type'] ) {
			$args['offset'] = $data['offset'] ? $data['offset'] : 0;
		} elseif ( $prefix === 'slider' && $data['offset'] ) {
			$args['offset'] = $data['offset'];
		}

		if ( $prefix !== 'slider' && 'show' === $data['show_pagination'] ) {
			$_paged        = is_front_page() ? "page" : "paged";
			$args['paged'] = get_query_var( $_paged ) ? absint( get_query_var( $_paged ) ) : 1;
		}

		if ( rtTPG()->hasPro() && 'yes' == $data['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = 1;
		}
		if ( $data['orderby'] ) {
			$args['orderby'] = $data['orderby'];
		}

		if ( $data['order'] ) {
			$args['order'] = $data['order'];
		}

		if ( $data['author'] ) {
			$args['author__in'] = $data['author'];
		}

		if ( $data['date_range'] ) {
			if ( strpos( $data['date_range'], 'to' ) ) {
				$date_range         = explode( 'to', $data['date_range'] );
				$args['date_query'] = [
					[
						'after'     => trim( $date_range[0] ),
						'before'    => trim( $date_range[1] ),
						'inclusive' => true,
					],
				];
			}
		}

		$_taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );

		foreach ( $_taxonomies as $index => $object ) {
			if ( in_array( $object->name, Custom_Widget_Base::get_excluded_taxonomy() ) ) {
				continue;
			}

			if ( $prefix !== 'slider' && rtTPG()->hasPro() && 'show' === $data['show_taxonomy_filter'] ) {
				if ( ( $data[ $data['post_type'] . '_filter_taxonomy' ] == $object->name ) && $data[ $object->name . '_default_terms' ] !== '0' ) {
					$args['tax_query'][] = [
						'taxonomy' => $data[ $data['post_type'] . '_filter_taxonomy' ],
						'field'    => 'term_id',
						'terms'    => $data[ $object->name . '_default_terms' ],
					];
				}
			} else {
				$setting_key = $object->name . '_ids';
				if ( ! empty( $data[ $setting_key ] ) ) {
					$args['tax_query'][] = [
						'taxonomy' => $object->name,
						'field'    => 'term_id',
						'terms'    => $data[ $setting_key ],
					];
				}
			}
		}

		if ( ! empty( $args['tax_query'] ) && $data['relation'] ) {
			$args['tax_query']['relation'] = $data['relation'];
		}

		if ( $data['post_keyword'] ) {
			$args['s'] = $data['post_keyword'];
		}

		if ( $prefix !== 'slider' ) {
			if ( $data['post_limit'] ) {
				if ( 'show' !== $data['show_pagination'] ) {
					$args['posts_per_page'] = $data['post_limit'];
				} else {
					$tempArgs                   = $args;
					$tempArgs['posts_per_page'] = $data['post_limit'];
					$tempArgs['paged']          = 1;
					$tempArgs['fields']         = 'ids';
					$tempQ                      = new WP_Query( $tempArgs );
					if ( ! empty( $tempQ->posts ) ) {
						$args['post__in']       = $tempQ->posts;
						$args['posts_per_page'] = $data['post_limit'];
					}
				}
			} else {
				$_posts_per_page = 9;
				if ( 'grid' === $prefix ) {
					if ( $data['grid_layout'] == 'grid-layout5' ) {
						$_posts_per_page = 5;
					} elseif ( in_array( $data['grid_layout'], [ 'grid-layout6', 'grid-layout6-2' ] ) ) {
						$_posts_per_page = 3;
					} elseif ( in_array( $data['grid_layout'], [ 'grid-layout5', 'grid-layout5-2' ] ) ) {
						$_posts_per_page = 5;
					}
				} elseif ( 'list' === $prefix ) {
					if ( in_array( $data['list_layout'], [ 'list-layout2', 'list-layout2-2' ] ) ) {
						$_posts_per_page = 9;
					} elseif ( in_array( $data['list_layout'], [ 'list-layout3', 'list-layout3-2' ] ) ) {
						$_posts_per_page = 5;
					}
				} elseif ( 'grid_hover' === $prefix ) {
					if ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout4', 'grid_hover-layout4-2' ] ) ) {
						$_posts_per_page = 7;
					} elseif ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout5', 'grid_hover-layout5-2' ] ) ) {
						$_posts_per_page = 3;
					} elseif ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout6', 'grid_hover-layout6-2', 'grid_hover-layout9', 'grid_hover-layout9-2', 'grid_hover-layout10', 'grid_hover-layout11' ] ) ) {
						$_posts_per_page = 4;
					} elseif ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout7', 'grid_hover-layout7-2', 'grid_hover-layout8' ] ) ) {
						$_posts_per_page = 5;
					} elseif ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout6', 'grid_hover-layout6-2' ] ) ) {
						$_posts_per_page = 4;
					}
				}

				$args['posts_per_page'] = $_posts_per_page;
			}

			if ( 'show' === $data['show_pagination'] && $data['display_per_page'] ) {
				$args['posts_per_page'] = $data['display_per_page'];
			}
		} else {
			$slider_per_page = $data['post_limit'];
			if ( $data['slider_layout'] == 'slider-layout10' ) {
				$slider_reminder = ( $data['post_limit'] % 5 );
				if ( $slider_reminder ) {
					$slider_per_page = ( $data['post_limit'] - $slider_reminder + 5 );
				}
			}
			$args['posts_per_page'] = $slider_per_page;
		}

		return $args;
	}

}