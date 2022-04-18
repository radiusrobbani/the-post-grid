<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

require_once( RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/rtTPGElementorQuery.php' );

class rtTPGElementorHelper {


	/**
	 *  Post Query Settings
	 *
	 * @param $ref
	 */
	public static function query( $ref ) {
		$post_types = Fns::get_post_types();

		$taxonomies = get_taxonomies( [], 'objects' );

		do_action( 'rt_tpg_el_query_build', $ref );
		$ref->start_controls_section(
			'rt_post_query',
			[
				'label' => esc_html__( 'Query Build', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'post_type',
			[
				'label'   => esc_html__( 'Post Source', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $post_types,
				'default' => 'post',
			]
		);

		//TODO: Common Filter

		$ref->add_control(
			'common_filters_heading',
			[
				'label'     => __( 'Common Filters:', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		$ref->add_control(
			'post_id',
			[
				'label'       => __( 'Include only', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the post IDs separated by comma for include', 'the-post-grid' ),
				'placeholder' => "Eg. 10, 15, 17",
			]
		);

		$ref->add_control(
			'exclude',
			[
				'label'       => __( 'Exclude', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the post IDs separated by comma for exclude', 'the-post-grid' ),
				'placeholder' => "Eg. 12, 13",
			]
		);

		$ref->add_control(
			'post_limit',
			[
				'label'       => __( 'Limit', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'The number of posts to show. Enter -1 to show all found posts.', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'offset',
			[
				'label'       => __( 'Offset', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Post offset', 'the-post-grid' ),
				'description' => __( 'Number of posts to skip. The offset parameter is ignored when post limit => -1 is used.', 'the-post-grid' ),
			]
		);

		//TODO: Advance Filter

		$ref->add_control(
			'advanced_filters_heading',
			[
				'label'     => __( 'Advanced Filters:', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
			]
		);

		foreach ( $taxonomies as $taxonomy => $object ) {
			if ( ! isset( $object->object_type[0] ) || ! in_array( $object->object_type[0], array_keys( $post_types ) )
			     || in_array( $taxonomy, Custom_Widget_Base::get_excluded_taxonomy() )
			) {
				continue;
			}
			$ref->add_control(
				$taxonomy . '_ids',
				[
					'label'       => __( "By ", 'the-post-grid' ) . $object->label,
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $ref->tpg_get_categories_by_id( $taxonomy ),
					'condition'   => [
						'post_type' => $object->object_type,
					],
				]
			);
		}

		$ref->add_control(
			'author',
			[
				'label'       => __( 'By Author', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Fns::rt_get_users(),
			]
		);

		$ref->add_control(
			'post_keyword',
			[
				'label'       => __( 'By Keyword', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Search by keyword', 'the-post-grid' ),
				'description' => __( 'Search by post title or content keyword', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'relation',
			[
				'label'   => __( 'Taxonomies Relation', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'OR',
				'options' => [
					'OR'     => __( 'OR', 'the-post-grid' ),
					'AND'    => __( 'AND', 'the-post-grid' ),
					'NOT IN' => __( 'NOT IN', 'the-post-grid' ),
				],
			]
		);


		$ref->add_control(
			'date_range',
			[
				'label'          => __( 'Date Range (Start to End)', 'plugin-domain' ) . $ref->pro_label,
				'type'           => \Elementor\Controls_Manager::DATE_TIME,
				'placeholder'    => "Choose date...",
				'description'    => __( "NB: Enter DEL button for delete date range", "the-post-grid" ),
				'classes'        => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'picker_options' => [
					'enableTime' => false,
					'mode'       => "range",
					'dateFormat' => "M j, Y",
				],
			]
		);


		$orderby_opt = [
			'date'          => __( 'Date', 'the-post-grid' ),
			'ID'            => __( 'Order by post ID', 'the-post-grid' ),
			'author'        => __( 'Author', 'the-post-grid' ),
			'title'         => __( 'Title', 'the-post-grid' ),
			'modified'      => __( 'Last modified date', 'the-post-grid' ),
			'parent'        => __( 'Post parent ID', 'the-post-grid' ),
			'comment_count' => __( 'Number of comments', 'the-post-grid' ),
			'menu_order'    => __( 'Menu order', 'the-post-grid' ),

		];
		if ( rtTPG()->hasPro() ) {
			$prderby_pro_opt = [
				'rand' => __( 'Random order', 'the-post-grid' ),
			];
			$orderby_opt     = array_merge( $orderby_opt, $prderby_pro_opt );
		}

		$ref->add_control(
			'orderby',
			[
				'label'       => __( 'Order by', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $orderby_opt,
				'default'     => 'date',
				'description' => $ref->get_pro_message( 'Random Order.' ),
			]
		);

		$ref->add_control(
			'order',
			[
				'label'     => __( 'Sort order', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'ASC'  => __( 'ASC', 'the-post-grid' ),
					'DESC' => __( 'DESC', 'the-post-grid' ),
				],
				'default'   => 'DESC',
				'condition' => [
					'orderby!' => 'menu_order',
				],
			]
		);

		$ref->add_control(
			'post_status',
			[
				'label'   => esc_html__( 'Post Status', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'options' => Options::rtTPGPostStatus(),
				'default' => 'publish',
			]
		);


		$ref->add_control(
			'ignore_sticky_posts',
			[
				'label'        => __( 'Ignore sticky posts at the top', 'plugin-domain' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'your-plugin' ),
				'label_off'    => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'disabled'     => true,
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'no_posts_found_text',
			[
				'label'       => __( 'No post found Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'No posts found.', 'the-post-grid' ),
				'placeholder' => __( 'Enter No post found', 'the-post-grid' ),
				'separator'   => 'before',
			]
		);


		$ref->end_controls_section();
	}


	/**
	 *  Builder Post Query Settings
	 *
	 * @param $ref
	 */
	public static function query_builder( $ref, $layout_type = '' ) {
		$post_types = Fns::get_post_types();

		$taxonomies = get_object_taxonomies( 'post', 'object' );

		do_action( 'rt_tpg_el_query_build', $ref );
		$ref->start_controls_section(
			'rt_post_query',
			[
				'label' => esc_html__( 'Query Build', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'post_limit',
			[
				'label'       => __( 'Posts per page', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'The number of posts to show. Enter -1 to show all found posts.', 'the-post-grid' ),
			]
		);


		if ( 'single' == $layout_type ) {
			$get_all_taxonomy = [];
			foreach ( $taxonomies as $taxonomy => $object ) {
				if ( ! isset( $object->object_type[0] ) || ! in_array( $object->object_type[0], array_keys( $post_types ) )
				     || in_array( $taxonomy, Custom_Widget_Base::get_excluded_taxonomy() )
				) {
					continue;
				}
				$get_all_taxonomy[ $object->name ] = $object->label;
			}

			$ref->add_control(
				'taxonomy_lists',
				[
					'label'   => __( 'Select a Taxonomy for relation', 'the-post-grid' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'category',
					'options' => $get_all_taxonomy,
				]
			);

			$orderby_opt = [
				'date'          => __( 'Date', 'the-post-grid' ),
				'ID'            => __( 'Order by post ID', 'the-post-grid' ),
				'author'        => __( 'Author', 'the-post-grid' ),
				'title'         => __( 'Title', 'the-post-grid' ),
				'modified'      => __( 'Last modified date', 'the-post-grid' ),
				'parent'        => __( 'Post parent ID', 'the-post-grid' ),
				'comment_count' => __( 'Number of comments', 'the-post-grid' ),
				'menu_order'    => __( 'Menu order', 'the-post-grid' ),
				'rand'          => __( 'Random order', 'the-post-grid' ),

			];

			$ref->add_control(
				'orderby',
				[
					'label'   => __( 'Order by', 'the-post-grid' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $orderby_opt,
					'default' => 'date',
				]
			);

			$ref->add_control(
				'order',
				[
					'label'     => __( 'Sort order', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => [
						'ASC'  => __( 'ASC', 'the-post-grid' ),
						'DESC' => __( 'DESC', 'the-post-grid' ),
					],
					'default'   => 'DESC',
					'condition' => [
						'orderby!' => 'menu_order',
					],
				]
			);
		} else {
			$ref->add_control(
				'post_id',
				[
					'label'       => __( 'Include only', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'Enter the post IDs separated by comma for include', 'the-post-grid' ),
					'placeholder' => "Eg. 10, 15, 17",
				]
			);

			$ref->add_control(
				'exclude',
				[
					'label'       => __( 'Exclude', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'Enter the post IDs separated by comma for exclude', 'the-post-grid' ),
					'placeholder' => "Eg. 12, 13",
				]
			);

			$ref->add_control(
				'offset',
				[
					'label'       => __( 'Offset', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Enter Post offset', 'the-post-grid' ),
					'description' => __( 'Number of posts to skip. The offset parameter is ignored when post limit => -1 is used.', 'the-post-grid' ),
				]
			);
		}
		$ref->end_controls_section();
	}

	/**
	 * Grid Layout Settings
	 *
	 * @param $ref
	 */
	public static function grid_layouts( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			$prefix . '_layout_settings',
			[
				'label' => __( 'Layout', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( 'grid' === $prefix ) {
			$layout_class   = 'grid-layout';
			$layout_options = [
				$prefix . '-layout1'   => [
					'title' => __( 'Layout 1', 'the-post-grid' ),
				],
				$prefix . '-layout3'   => [
					'title' => __( 'Layout 2', 'the-post-grid' ),
				],
				$prefix . '-layout4'   => [
					'title' => __( 'Layout 3', 'the-post-grid' ),
				],
				$prefix . '-layout2'   => [
					'title' => __( 'Layout 4', 'the-post-grid' ),
				],
				$prefix . '-layout5'   => [
					'title' => __( 'Layout 5', 'the-post-grid' ),
				],
				$prefix . '-layout5-2' => [
					'title' => __( 'Layout 6', 'the-post-grid' ),
				],
				$prefix . '-layout6'   => [
					'title' => __( 'Layout 7', 'the-post-grid' ),
				],
				$prefix . '-layout6-2' => [
					'title' => __( 'Layout 8', 'the-post-grid' ),
				],
				$prefix . '-layout7'   => [
					'title' => __( 'Gallery', 'the-post-grid' ),
				],
			];
		}

		if ( 'grid_hover' === $prefix ) {
			$layout_class   = 'grid-hover-layout';
			$layout_options = [
				$prefix . '-layout1'   => [
					'title' => __( 'Layout 1', 'the-post-grid' ),
				],
				$prefix . '-layout2'   => [
					'title' => __( 'Layout 2', 'the-post-grid' ),
				],
				$prefix . '-layout3'   => [
					'title' => __( 'Layout 3', 'the-post-grid' ),
				],
				$prefix . '-layout4'   => [
					'title' => __( 'Layout 4', 'the-post-grid' ),
				],
				$prefix . '-layout4-2' => [
					'title' => __( 'Layout 5', 'the-post-grid' ),
				],
				$prefix . '-layout5'   => [
					'title' => __( 'Layout 6', 'the-post-grid' ),
				],
				$prefix . '-layout5-2' => [
					'title' => __( 'Layout 7', 'the-post-grid' ),
				],
				$prefix . '-layout6'   => [
					'title' => __( 'Layout 8', 'the-post-grid' ),
				],
				$prefix . '-layout6-2' => [
					'title' => __( 'Layout 9', 'the-post-grid' ),
				],
				$prefix . '-layout7'   => [
					'title' => __( 'Layout 10', 'the-post-grid' ),
				],
				$prefix . '-layout7-2' => [
					'title' => __( 'Layout 11', 'the-post-grid' ),
				],
				$prefix . '-layout8'   => [
					'title' => __( 'Layout 12', 'the-post-grid' ),
				],
				$prefix . '-layout9'   => [
					'title' => __( 'Layout 13', 'the-post-grid' ),
				],
				$prefix . '-layout9-2' => [
					'title' => __( 'Layout 14', 'the-post-grid' ),
				],
				$prefix . '-layout10'  => [
					'title' => __( 'Layout 15', 'the-post-grid' ),
				],
				$prefix . '-layout11'  => [
					'title' => __( 'Layout 16', 'the-post-grid' ),
				],
			];
		}

		if ( 'slider' === $prefix ) {
			$layout_class   = 'slider-layout';
			$layout_options = [
				$prefix . '-layout1'  => [
					'title' => __( 'Layout 1', 'the-post-grid' ),
				],
				$prefix . '-layout2'  => [
					'title' => __( 'Layout 2', 'the-post-grid' ),
				],
				$prefix . '-layout3'  => [
					'title' => __( 'Layout 3', 'the-post-grid' ),
				],
				$prefix . '-layout4'  => [
					'title' => __( 'Layout 4', 'the-post-grid' ),
				],
				$prefix . '-layout5'  => [
					'title' => __( 'Layout 5', 'the-post-grid' ),
				],
				$prefix . '-layout6'  => [
					'title' => __( 'Layout 6', 'the-post-grid' ),
				],
				$prefix . '-layout7'  => [
					'title' => __( 'Layout 7', 'the-post-grid' ),
				],
				$prefix . '-layout8'  => [
					'title' => __( 'Layout 8', 'the-post-grid' ),
				],
				$prefix . '-layout9'  => [
					'title' => __( 'Layout 9', 'the-post-grid' ),
				],
				$prefix . '-layout10' => [
					'title' => __( 'Layout 10', 'the-post-grid' ),
				],
				$prefix . '-layout11' => [
					'title' => __( 'Layout 11', 'the-post-grid' ),
				],
				$prefix . '-layout12' => [
					'title' => __( 'Layout 12', 'the-post-grid' ),
				],
				$prefix . '-layout13' => [
					'title' => __( 'Layout 13', 'the-post-grid' ),
				],
			];

			if ( 'single' === $layout_type ) {
				$layout_options = array_slice( $layout_options, 0, 9 );
			}
		}

		$ref->add_control(
			$prefix . '_layout',
			[
				'label'          => __( 'Choose Layout', 'the-post-grid' ),
				'type'           => \Elementor\Controls_Manager::CHOOSE,
				'label_block'    => true,
				'options'        => $layout_options,
				'toggle'         => false,
				'default'        => $prefix . '-layout1',
				'style_transfer' => true,
				'classes'        => 'tpg-image-select ' . $layout_class . ' ' . $ref->is_post_layout,
			]
		);

		$ref->add_control(
			'layout_options_heading',
			[
				'label'   => __( 'Layout Options:', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);


		$column_options = [
			'0'  => __( 'Default from layout', 'the-post-grid' ),
			'12' => __( '1 Columns', 'the-post-grid' ),
			'6'  => __( '2 Columns', 'the-post-grid' ),
			'4'  => __( '3 Columns', 'the-post-grid' ),
			'3'  => __( '4 Columns', 'the-post-grid' ),
		];

		if ( 'grid' === $prefix ) {
			$grid_column_condition = [
				'grid_layout!' => [ 'grid-layout5', 'grid-layout5-2', 'grid-layout6', 'grid-layout6-2' ],
			];
		}

		if ( 'grid_hover' === $prefix ) {
			$grid_column_condition = [
				'grid_hover_layout!' => [ 'grid_hover-layout8' ],
			];
		}

		if ( 'slider' === $prefix ) {
			$column_options        = [
				'0' => __( 'Default from layout', 'the-post-grid' ),
				'1' => __( '1 Columns', 'the-post-grid' ),
				'2' => __( '2 Columns', 'the-post-grid' ),
				'3' => __( '3 Columns', 'the-post-grid' ),
				'4' => __( '4 Columns', 'the-post-grid' ),
				'5' => __( '5 Columns', 'the-post-grid' ),
				'6' => __( '6 Columns', 'the-post-grid' ),
			];
			$grid_column_condition = [
				'slider_layout!' => [ 'slider-layout10', 'slider-layout11', 'slider-layout13' ],
			];
		}

		$ref->add_responsive_control(
			$prefix . '_column',
			[
				'label'          => esc_html__( 'Column', 'the-post-grid' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => $column_options,
				'default'        => '0',
				'tablet_default' => '0',
				'mobile_default' => '0',
				'description'    => __( 'Choose Column for layout.', 'the-post-grid' ),
				'condition'      => $grid_column_condition,
			]
		);

		if ( 'single' === $layout_type ) {
			$ref->add_control(
				'enable_related_slider',
				[
					'label'        => __( 'Enable Slider', 'the-post-grid' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'the-post-grid' ),
					'label_off'    => __( 'Hide', 'the-post-grid' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);

			$ref->add_responsive_control(
				'slider_gap_2',
				[
					'label'      => __( 'Grid Gap', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						],
					],
					'selectors'  => [
						'body {{WRAPPER}} .tpg-el-main-wrapper .rt-slider-item'    => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}}; padding-bottom: calc({{SIZE}}{{UNIT}} * 2)',
						'body {{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'enable_related_slider!' => 'yes',
					],
				]
			);
		}

		$ref->add_responsive_control(
			$prefix . '_offset_col_width',
			[
				'label'      => __( 'Offset Column Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 30,
						'max'  => 70,
						'step' => 1,
					],
					'%'  => [
						'min'  => 30,
						'max'  => 70,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .offset-left'  => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .tpg-el-main-wrapper .offset-right' => 'width: calc( 100% - {{SIZE}}%);',
				],
				'condition'  => [
					$prefix . '_layout' => [
						'grid-layout5',
						'grid-layout5-2',
						'grid-layout6',
						'grid-layout6-2',
						'grid_hover-layout4',
						'grid_hover-layout4-2',
						'grid_hover-layout5',
						'grid_hover-layout5-2',
						'grid_hover-layout6',
						'grid_hover-layout6-2',
						'grid_hover-layout7',
						'grid_hover-layout7-2',
						'grid_hover-layout9',
						'grid_hover-layout9-2',
					],
				],
			]
		);


		if ( 'grid' === $prefix ) {
			$layout_style_opt = [
				'tpg-even'        => __( 'Grid', 'the-post-grid' ),
				'tpg-full-height' => __( 'Grid Equal Height', 'the-post-grid' ),
			];
			if ( rtTPG()->hasPro() ) {
				$layout_style_new_opt = [
					'masonry' => __( 'Masonry', 'the-post-grid' ),
				];
				$layout_style_opt     = array_merge( $layout_style_opt, $layout_style_new_opt );
			}

			$ref->add_control(
				$prefix . '_layout_style',
				[
					'label'       => __( 'Layout Style', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => 'tpg-full-height',
					'options'     => $layout_style_opt,
					'description' => __( 'If you use card border then equal height will work. ', 'the-post-grid' ) . $ref->get_pro_message( "masonry layout" ),
					'classes'     => rtTPG()->hasPro() ? '' : 'tpg-should-hide-field',
					'condition'   => [
						$prefix . '_layout!' => [ 'grid-layout2', 'grid-layout5', 'grid-layout5-2', 'grid-layout6', 'grid-layout6-2', 'grid-layout7', 'grid-layout7-2' ],
					],
				]
			);
		}

		if ( $prefix !== 'slider' ) {
			$layout_align_css = [
				'{{WRAPPER}} .rt-tpg-container .grid-layout2 .rt-holder .post-right-content' => 'justify-content: {{VALUE}};',
			];

			if ( $prefix === 'grid_hover' ) {
				$layout_align_css = [
					'{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content' => 'justify-content: {{VALUE}};',
				];
			}

			//Grid layout
			$ref->add_control(
				$prefix . '_layout_alignment',
				[
					'label'     => __( 'Vertical Align', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => [
						''              => __( 'Default', 'the-post-grid' ),
						'flex-start'    => __( 'Start', 'the-post-grid' ),
						'center'        => __( 'Center', 'the-post-grid' ),
						'flex-end'      => __( 'End', 'the-post-grid' ),
						'space-around'  => __( 'Space Around', 'the-post-grid' ),
						'space-between' => __( 'Space Between', 'the-post-grid' ),
					],
					'condition' => [
						$prefix . '_layout!' => [
							'grid-layout1',
							'grid-layout3',
							'grid-layout4',
							'grid-layout5',
							'grid-layout5-2',
							'grid-layout6',
							'grid-layout6-2',
							'grid-layout7',
							'grid_hover-layout2',
							'grid_hover-layout4',
							'grid_hover-layout4-2',
						],
					],
					'selectors' => $layout_align_css,
				]
			);
		}

		if ( $prefix === 'slider' ) {
			//Grid layout
			$ref->add_control(
				$prefix . '_layout_alignment_2',
				[
					'label'     => __( 'Vertical Align', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => [
						''              => __( 'Default', 'the-post-grid' ),
						'flex-start'    => __( 'Start', 'the-post-grid' ),
						'center'        => __( 'Center', 'the-post-grid' ),
						'flex-end'      => __( 'End', 'the-post-grid' ),
						'space-around'  => __( 'Space Around', 'the-post-grid' ),
						'space-between' => __( 'Space Between', 'the-post-grid' ),
					],
					'condition' => [
						$prefix . '_layout!' => [ 'slider-layout1', 'slider-layout2', 'slider-layout3', 'slider-layout13', 'grid-layout7' ],
					],
					'selectors' => [
						'{{WRAPPER}} .tpg-el-main-wrapper .grid-behaviour .rt-holder .rt-el-content-wrapper .gallery-content' => 'justify-content: {{VALUE}};height:100%;',
						'{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content'                    => 'justify-content: {{VALUE}};',
					],
				]
			);
		}

		$ref->add_responsive_control(
			'full_wrapper_align',
			[
				'label'        => esc_html__( 'Text Align', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'tpg-wrapper-align-',
				'toggle'       => true,
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],

			]
		);


		$ref->end_controls_section();
	}


	/**
	 * Front-end Filter Settings
	 *
	 * @param $ref
	 */
	public static function filter_settings( $ref ) {
		$prefix = $ref->prefix;

		if ( ! rtTPG()->hasPro() ) {
			return;
		}
		$ref->start_controls_section(
			$prefix . '_filter_settings',
			[
				'label' => __( 'Filter', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'show_taxonomy_filter',
			[
				'label'        => __( 'Taxonomy Filter', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_author_filter',
			[
				'label'        => __( 'Author filter', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_order_by',
			[
				'label'        => __( 'Order By Filter', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_sort_order',
			[
				'label'        => __( 'Sort Order Filter', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		$ref->add_control(
			'show_search',
			[
				'label'        => __( 'Search filter', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'hide',
			]
		);

		//TODO: Filter Settings
		//======================================================

		$front_end_filter_condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'show_taxonomy_filter',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_author_filter',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_order_by',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_sort_order',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_search',
					'operator' => '==',
					'value'    => 'show',
				],
			],
		];


		$ref->add_control(
			'filter_type',
			[
				'label'        => __( 'Filter Type', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'dropdown',
				'options'      => [
					'dropdown' => __( 'Dropdown', 'the-post-grid' ),
					'button'   => __( 'Button', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'tpg-filter-type-',
				'conditions'   => $front_end_filter_condition,
				'separator'    => 'before',
			]
		);

		$ref->add_control(
			'filter_btn_style',
			[
				'label'       => __( 'Filter Style', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default'  => __( 'Default', 'the-post-grid' ),
					'carousel' => __( 'Collapsable', 'the-post-grid' ),
				],
				'condition'   => [
					'filter_type' => 'button',
				],
				'conditions'  => $front_end_filter_condition,
				'description' => __( 'If you use collapsable then only category section show on the filter', 'the-post-grid' ),
			]
		);

		$ref->add_responsive_control(
			'filter_btn_item_per_page',
			[
				'label'          => __( 'Button Item Per Slider', 'the-post-grid' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'options'        => [
					'auto' => __( 'Auto', 'the-post-grid' ),
					'2'    => __( '2', 'the-post-grid' ),
					'3'    => __( '3', 'the-post-grid' ),
					'4'    => __( '4', 'the-post-grid' ),
					'5'    => __( '5', 'the-post-grid' ),
					'6'    => __( '6', 'the-post-grid' ),
					'7'    => __( '7', 'the-post-grid' ),
					'8'    => __( '8', 'the-post-grid' ),
					'9'    => __( '9', 'the-post-grid' ),
					'10'   => __( '10', 'the-post-grid' ),
					'11'   => __( '11', 'the-post-grid' ),
					'12'   => __( '12', 'the-post-grid' ),
				],
				'default'        => 'auto',
				'tablet_default' => 'auto',
				'mobile_default' => 'auto',
				'condition'      => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'carousel',
				],
				'conditions'     => $front_end_filter_condition,
				'description'    => __( 'If you use carousel then only category section show on the filter', 'the-post-grid' ),
			]
		);


		$post_types = Fns::get_post_types();
		foreach ( $post_types as $post_type => $label ) {
			$_taxonomies = get_object_taxonomies( $post_type, 'object' );
			if ( empty( $_taxonomies ) ) {
				continue;
			}
			$taxonomies_list = [];
			foreach ( $_taxonomies as $tax ) {
				if ( in_array( $tax->name, [ 'post_format', 'elementor_library_type', 'product_visibility', 'product_shipping_class' ] ) ) {
					continue;
				}
				$taxonomies_list[ $tax->name ] = $tax->label;
			}

			if ( 'post' === $post_type ) {
				$default_cat = 'category';
			} elseif ( 'product' === $post_type ) {
				$default_cat = 'product_cat';
			} elseif ( 'download' === $post_type ) {
				$default_cat = 'download_category';
			} elseif ( 'docs' === $post_type ) {
				$default_cat = 'doc_category';
			} elseif ( 'lp_course' === $post_type ) {
				$default_cat = 'course_category';
			} else {
				$taxonomie_keys = array_keys( $_taxonomies );
				$filter_cat     = array_filter(
					$taxonomie_keys,
					function ( $item ) {
						return strpos( $item, 'cat' ) !== false;
					}
				);

				if ( is_array( $filter_cat ) && ! empty( $filter_cat ) ) {
					$default_cat = array_shift( $filter_cat );
				}
			}

			$ref->add_control(
				$post_type . '_filter_taxonomy',
				[
					'label'       => __( 'Choose Taxonomy', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => $default_cat,
					'options'     => $taxonomies_list,
					'condition'   => [
						'post_type'            => $post_type,
						'show_taxonomy_filter' => 'show',
					],
					'description' => __( 'Select a taxonomy for showing in filter', 'the-post-grid' ),
				]
			);

			foreach ( $_taxonomies as $tax ) {
				if ( in_array( $tax->name, [ 'post_format', 'elementor_library_type', 'product_visibility', 'product_shipping_class' ] ) ) {
					continue;
				}

				$term_first = [ '0' => __( '--Select--', 'the-post-grid' ) ];
				$term_lists = get_terms(
					[
						'taxonomy'   => $tax->name, //Custom taxonomy name
						'hide_empty' => true,
						'fields'     => "id=>name",
					]
				);

				$term_lists = $term_first + $term_lists;

				$ref->add_control(
					$tax->name . '_default_terms',
					[
						'label'     => __( 'Default ', 'the-post-grid' ) . $tax->label,
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => '0',
						'options'   => $term_lists,
						'condition' => [
							$post_type . '_filter_taxonomy' => $tax->name,
							'post_type'                     => $post_type,
							'show_taxonomy_filter'          => 'show',
						],
					]
				);
			}
		}

		$front_end_filter_tax_condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'show_taxonomy_filter',
					'operator' => '==',
					'value'    => 'show',
				],
				[
					'name'     => 'show_author_filter',
					'operator' => '==',
					'value'    => 'show',
				],
			],
		];

		$ref->add_control(
			'filter_post_count',
			[
				'label'      => __( 'Filter Post Count', 'the-post-grid' ),
				'type'       => \Elementor\Controls_Manager::SELECT,
				'default'    => 'no',
				'options'    => [
					'yes' => __( 'Yes', 'the-post-grid' ),
					'no'  => __( 'No', 'the-post-grid' ),
				],
				'conditions' => $front_end_filter_tax_condition,
			]
		);


		$ref->add_control(
			'tgp_filter_taxonomy_hierarchical',
			[
				'label'        => __( 'Tax Hierarchical', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'conditions'   => $front_end_filter_tax_condition,
				'condition'    => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_control(
			'tpg_hide_all_button',
			[
				'label'        => __( 'Hide Show all button', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'conditions'   => $front_end_filter_tax_condition,
				'condition'    => [
					'filter_type' => 'button',
				],
			]
		);

		$ref->add_control(
			'tax_filter_all_text',
			[
				'label'       => __( 'All Taxonomy Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter All Category Text Here..', 'the-post-grid' ),
				'conditions'  => $front_end_filter_tax_condition,
			]
		);
		$ref->add_control(
			'author_filter_all_text',
			[
				'label'       => __( 'All Users Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter All Users Text Here..', 'the-post-grid' ),
				'condition'   => [
					'show_author_filter' => 'show',
					'filter_btn_style'   => 'default',
				],
			]
		);


		$ref->end_controls_section();
	}


	/**
	 * List Layout Settings
	 *
	 * @param $ref
	 */
	public static function list_layouts( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'list_layout_settings',
			[
				'label' => esc_html__( 'Layout', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'list_layout',
			[
				'label'          => __( 'Choose Layout', 'the-post-grid' ),
				'type'           => \Elementor\Controls_Manager::CHOOSE,
				'label_block'    => true,
				'options'        => [
					'list-layout1'   => [
						'title' => __( 'Layout 1', 'the-post-grid' ),
					],
					'list-layout2'   => [
						'title' => __( 'Layout 2', 'the-post-grid' ),
					],
					'list-layout2-2' => [
						'title' => __( 'Layout 3', 'the-post-grid' ),
					],
					'list-layout3'   => [
						'title' => __( 'Layout 4', 'the-post-grid' ),
					],
					'list-layout3-2' => [
						'title' => __( 'Layout 5', 'the-post-grid' ),
					],
					'list-layout4'   => [
						'title' => __( 'Layout 6', 'the-post-grid' ),
					],
					'list-layout5'   => [
						'title' => __( 'Layout 7', 'the-post-grid' ),
					],
				],
				'toggle'         => false,
				'default'        => 'list-layout1',
				'style_transfer' => true,
				'classes'        => 'tpg-image-select list-layout ' . $ref->is_post_layout,
			]
		);

		$ref->add_control(
			'layout_options_heading2',
			[
				'label'   => __( 'Layout Options:', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$ref->add_responsive_control(
			'list_column',
			[
				'label'          => esc_html__( 'Column', 'the-post-grid' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => [
					'0'  => __( 'Default from layout', 'the-post-grid' ),
					'12' => __( '1 Columns', 'the-post-grid' ),
					'6'  => __( '2 Columns', 'the-post-grid' ),
					'4'  => __( '3 Columns', 'the-post-grid' ),
					'3'  => __( '4 Columns', 'the-post-grid' ),
				],
				'default'        => '0',
				'tablet_default' => '0',
				'mobile_default' => '0',
				'description'    => __( 'Choose Column for layout', 'the-post-grid' ),
				'condition'      => [
					'list_layout!' => [ 'list-layout2', 'list-layout2-2', 'list-layout4' ],
				],
			]
		);


		$ref->add_responsive_control(
			'list_layout_alignment',
			[
				'label'     => __( 'Vertical Alignment', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					''              => __( 'Default', 'the-post-grid' ),
					'flex-start'    => __( 'Start', 'the-post-grid' ),
					'center'        => __( 'Center', 'the-post-grid' ),
					'flex-end'      => __( 'End', 'the-post-grid' ),
					'space-around'  => __( 'Space Around', 'the-post-grid' ),
					'space-between' => __( 'Space Between', 'the-post-grid' ),
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .list-behaviour .rt-holder .rt-el-content-wrapper' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'list_layout!' => [ 'list-layout2', 'list-layout2-2' ],
				],
			]
		);

		$ref->add_responsive_control(
			'list_left_side_width',
			[
				'label'      => __( 'Offset Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 700,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .list-layout-wrapper .offset-left'  => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .rt-tpg-container .list-layout-wrapper .offset-right' => 'width: calc( 100% - {{SIZE}}%);',
				],
				'condition'  => [
					'list_layout' => [ 'list-layout2', 'list-layout3', 'list-layout2-2', 'list-layout3-2' ],
				],
			]
		);


		$layout_style_opt = [
			'tpg-even' => __( ucwords( $ref->prefix ) . ' Default', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$layout_style_new_opt = [
				'masonry' => __( 'Masonry', 'the-post-grid' ),
			];
			$layout_style_opt     = array_merge( $layout_style_opt, $layout_style_new_opt );
		}

		$ref->add_control(
			'list_layout_style',
			[
				'label'       => __( 'Layout Style', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'tpg-even',
				'options'     => $layout_style_opt,
				'description' => $ref->get_pro_message( "masonry layout" ),
				'condition'   => [
					'list_layout'  => [ 'list-layout1', 'list-layout5' ],
					'list_column!' => [ '0', '12' ],
				],
			]
		);

		$ref->add_responsive_control(
			'full_wrapper_align',
			[
				'label'        => esc_html__( 'Text Align', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'tpg-wrapper-align-',
				'toggle'       => true,
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Pagination and Load more style tab
	 *
	 * @param        $ref
	 * @param  bool  $is_print
	 */
	public static function pagination_settings( $ref, $layout_type = '' ) {
		$ref->start_controls_section(
			'pagination_settings',
			[
				'label' => __( 'Pagination', 'the-post-grid' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$ref->add_control(
			'show_pagination',
			[
				'label'        => __( 'Show Pagination', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'default',
				'render_type'  => 'template',
				'prefix_class' => 'pagination-visibility-',
			]
		);


		if ( 'archive' !== $layout_type ) {
			$ref->add_control(
				'display_per_page',
				[
					'label'       => __( 'Display Per Page', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'default'     => 6,
					'description' => __( 'Enter how may posts will display per page', 'the-post-grid' ),
					'condition'   => [
						'show_pagination' => 'show',
					],
				]
			);
		}


		$default_pagination = 'pagination';
		if ( 'archive' == $layout_type ) {
			$pagination_type    = [];
			$default_pagination = 'pagination_ajax';
		} else {
			$pagination_type = [
				'pagination' => __( 'Default Pagination', 'the-post-grid' ),
			];
		}

		if ( rtTPG()->hasPro() ) {
			$pagination_type_pro = [
				'pagination_ajax' => __( 'Ajax Pagination ( Only for Grid )', 'the-post-grid' ),
				'load_more'       => __( 'Load More - On Click', 'the-post-grid' ),
				'load_on_scroll'  => __( 'Load More - On Scroll', 'the-post-grid' ),
			];
			$pagination_type     = array_merge( $pagination_type, $pagination_type_pro );
		}

		$ref->add_control(
			'pagination_type',
			[
				'label'       => __( 'Pagination Type', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => $default_pagination,
				'options'     => $pagination_type,
				'description' => $ref->get_pro_message( 'loadmore and ajax pagination' ),
				'condition'   => [
					'show_pagination' => 'show',
				],
			]
		);

		$ref->add_control(
			'ajax_pagination_type',
			[
				'label'        => __( 'Enable Ajax Next Previous', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'condition'    => [
					'pagination_type' => 'pagination_ajax',
					'show_pagination' => 'show',
				],
				'prefix_class' => 'ajax-pagination-type-next-prev-',
			]
		);


		$ref->add_control(
			'load_more_button_text',
			[
				'label'     => __( 'Button Text', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'the-post-grid' ),
				'condition' => [
					'pagination_type' => 'load_more',
					'show_pagination' => 'show',
				],
			]
		);


		$ref->end_controls_section();
	}

	/**
	 * Get Field Selections
	 *
	 * @param $ref
	 */

	public static function field_selection( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'field_selection_settings',
			[
				'label' => esc_html__( 'Field Selection', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$ref->add_control(
			'show_section_title',
			[
				'label'        => __( 'Show Section Title', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'section-title-visibility-',
			]
		);

		$ref->add_control(
			'show_title',
			[
				'label'        => __( 'Post Title', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'title-visibility-',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],

			]
		);

		$ref->add_control(
			'show_thumb',
			[
				'label'        => __( 'Post Thumbnail', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'thumbnail-visibility-',
			]
		);

		$ref->add_control(
			'show_excerpt',
			[
				'label'        => __( 'Post Excerpt', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'excerpt-visibility-',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_meta',
			[
				'label'        => __( 'Post Meta', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'meta-visibility-',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_date',
			[
				'label'        => __( 'Post Date', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'classes'      => 'tpg-padding-left',
				'prefix_class' => 'date-visibility-',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_category',
			[
				'label'        => __( 'Post Categories', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'classes'      => 'tpg-padding-left',
				'prefix_class' => 'category-visibility-',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_author',
			[
				'label'        => __( 'Post Author', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'classes'      => 'tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_tags',
			[
				'label'        => __( 'Post Tags', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'classes'      => 'tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_comment_count',
			[
				'label'        => __( 'Post Comment Count', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'classes'      => 'tpg-padding-left',
				'condition'    => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_post_count',
			[
				'label'        => __( 'Post View Count', 'the-post-grid' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'classes'      => rtTPG()->hasPro() ? 'tpg-padding-left' : 'the-post-grid-field-hide tpg-padding-left',

				'condition' => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_read_more',
			[
				'label'        => __( 'Read More Button', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => false,
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_social_share',
			[
				'label'        => __( 'Social Share', 'the-post-grid' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'default',
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'condition'    => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'show_woocommerce_rating',
			[
				'label'        => __( 'Rating (WooCommerce)', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'default',
				'condition'    => [
					'post_type' => [ 'product', 'download' ],
				],
			]
		);

		$cf = Fns::checkWhichCustomMetaPluginIsInstalled();
		if ( $cf ) {
			$ref->add_control(
				'show_acf',
				[
					'label'        => __( 'Advanced Custom Field', 'the-post-grid' ) . $ref->pro_label,
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'the-post-grid' ),
					'label_off'    => __( 'Hide', 'the-post-grid' ),
					'return_value' => 'show',
					'default'      => false,
					'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
					'condition'    => [
						$prefix . '_layout!' => [ 'grid-layout7' ],
					],
				]
			);

			$ref->add_control(
				'cf_group',
				[
					'label'       => __( 'Choose Advanced Custom Field (ACF)', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => Fns::get_groups_by_post_type( 'all' ),
					'condition'   => [
						'show_acf' => 'show',
					],
				]
			);
		}

		$ref->end_controls_section();
	}


	/**
	 * Section Title Settings
	 *
	 * @param $ref
	 */

	public static function section_title_settings( $ref, $layout_type = '' ) {
		$default = $layout_type == 'single' ? 'Related Posts' : 'Section Title';
		$ref->start_controls_section(
			'section_title_settings',
			[
				'label'     => esc_html__( 'Section Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_section_title' => 'show',
				],
			]
		);


		$ref->add_control(
			'section_title_style',
			[
				'label'        => __( 'Section Title Style', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'style1',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'style1'  => __( 'Style 1', 'the-post-grid' ),
					'style2'  => __( 'Style 2', 'the-post-grid' ),
					'style3'  => __( 'Style 3', 'the-post-grid' ),
				],
				'prefix_class' => 'section-title-style-',
				'render_type'  => 'template',
				'condition'    => [
					'show_section_title' => 'show',
				],
			]
		);

		if ( 'single' === $layout_type ) {
			$ref->add_control(
				'section_title_source',
				[
					'label'   => esc_html__( 'Title source', 'the-post-grid' ),
					'type'    => \Elementor\Controls_Manager::HIDDEN,
					'default' => 'custom_title',
				]
			);
		} else {
			$ref->add_control(
				'section_title_source',
				[
					'label'     => __( 'Title Source', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => 'custom_title',
					'options'   => [
						'page_title'   => __( 'Page Title', 'the-post-grid' ),
						'custom_title' => __( 'Custom Title', 'the-post-grid' ),
					],
					'condition' => [
						'show_section_title' => 'show',
					],
				]
			);
		}

		$ref->add_control(
			'section_title_text',
			[
				'label'       => __( 'Title', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your title here', 'the-post-grid' ),
				'default'     => __( $default, 'the-post-grid' ),
				'label_block' => true,
				'condition'   => [
					'section_title_source' => 'custom_title',
					'show_section_title'   => 'show',
				],
			]
		);


		$ref->add_control(
			'section_title_tag',
			[
				'label'     => __( 'Title Tag', 'the-post-grid' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => [
					'h1' => __( 'H1', 'the-post-grid' ),
					'h2' => __( 'H2', 'the-post-grid' ),
					'h3' => __( 'H3', 'the-post-grid' ),
					'h4' => __( 'H4', 'the-post-grid' ),
					'h5' => __( 'H5', 'the-post-grid' ),
					'h6' => __( 'H6', 'the-post-grid' ),
				],
				'condition' => [
					'show_section_title' => 'show',
				],
			]
		);

		$ref->add_control(
			'title_prefix',
			[
				'label'       => __( 'Title Prefix Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Title prefix text', 'the-post-grid' ),
				'condition'   => [
					'section_title_source' => 'page_title',
				],
			]
		);

		$ref->add_control(
			'title_suffix',
			[
				'label'       => __( 'Title Suffix Text', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Title suffix text', 'the-post-grid' ),
				'condition'   => [
					'section_title_source' => 'page_title',
				],
			]
		);

		if ( 'archive' == $layout_type ) {
			$ref->add_control(
				'show_cat_desc',
				[
					'label'        => __( 'Show Archive Description', 'the-post-grid' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'the-post-grid' ),
					'label_off'    => __( 'Hide', 'the-post-grid' ),
					'return_value' => 'yes',
					'default'      => false,
				]
			);
		}

		$ref->end_controls_section();
	}


	/**
	 * Thumbnail Settings
	 *
	 * @param $ref
	 */

	public static function post_thumbnail_settings( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'post_thumbnail_settings',
			[
				'label'     => esc_html__( 'Post Thumbnail', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_thumb' => 'show',
				],
			]
		);


		$ref->add_control(
			'media_source',
			[
				'label'   => __( 'Media Source', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'feature_image',
				'options' => [
					'feature_image' => __( 'Feature Image', 'the-post-grid' ),
					'first_image'   => __( 'First Image from content', 'the-post-grid' ),
				],
			]
		);

		$thumb_exclude = '';
		if ( ! rtTPG()->hasPro() ) {
			$thumb_exclude = 'custom';
		}


		//Default Image
		$ref->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'exclude'   => [ $thumb_exclude ],
				'default'   => 'medium_large',
				'label'     => $ref->get_pro_message( "custom dimension." ),
				'condition' => [
					'media_source' => 'feature_image',
				],
			]
		);

		$ref->add_control(
			'img_crop_style',
			[
				'label'     => __( 'Image Crop Style', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'hard',
				'options'   => [
					'soft' => __( 'Soft Crop', 'the-post-grid' ),
					'hard' => __( 'Hard Crop', 'the-post-grid' ),
				],
				'condition' => [
					'image_size'   => 'custom',
					'media_source' => 'feature_image',
				],
			]
		);


		$thumb_condition = [
			'media_source' => 'feature_image',
			'grid_layout'  => [ 'grid-layout5', 'grid-layout5-2', 'grid-layout6', 'grid-layout6-2' ],
		];

		if ( $ref->prefix === 'list' ) {
			$thumb_condition = [
				'media_source' => 'feature_image',
				'list_layout'  => [ 'list-layout2', 'list-layout3', 'list-layout2-2', 'list-layout3-2' ],
			];
		}

		if ( $ref->prefix === 'grid_hover' ) {
			$thumb_condition = [
				'media_source'      => 'feature_image',
				'grid_hover_layout' => [
					'grid_hover-layout4',
					'grid_hover-layout4-2',
					'grid_hover-layout5',
					'grid_hover-layout5-2',
					'grid_hover-layout6',
					'grid_hover-layout6-2',
					'grid_hover-layout7',
					'grid_hover-layout7-2',
					'grid_hover-layout9',
					'grid_hover-layout9-2',
				],
			];
		}
		if ( $ref->prefix === 'slider' ) {
			$thumb_condition = [
				'media_source'  => 'feature_image',
				'slider_layout' => [ 'slider-layout10' ],
			];
		}

		//Offset Image
		$ref->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image_offset',
				'exclude'   => [ 'custom' ],
				'default'   => 'medium_large',
				'condition' => $thumb_condition,
				'classes'   => 'tpg-offset-thumb-size',
			]
		);

		if ( 'list' == $prefix ) {
			$ref->add_responsive_control(
				'list_image_side_width',
				[
					'label'      => __( 'List Image Width', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range'      => [
						'px' => [
							'min'  => 0,
							'max'  => 700,
							'step' => 5,
						],
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .rt-tpg-container .list-layout-wrapper [class*="rt-col"]:not(.offset-left) .rt-holder .tpg-el-image-wrap' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'list_layout!' => [ 'list-layout4' ],
					],
				]
			);
		}

		if ( rtTPG()->hasPro() ) {
			$ref->add_responsive_control(
				'image_height',
				[
					'label'      => __( 'Image Height', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range'      => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader > :not(.offset-right) .tpg-el-image-wrap'                => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader > :not(.offset-right) .tpg-el-image-wrap img'            => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper.slider-layout11-main .rt-grid-hover-item .rt-holder .rt-el-content-wrapper' => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper.slider-layout12-main .rt-grid-hover-item .rt-holder .rt-el-content-wrapper' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$ref->add_responsive_control(
				'offset_image_height',
				[
					'label'      => __( 'Offset Image Height', 'the-post-grid' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range'      => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						],
					],
					'condition'  => $thumb_condition,
					'selectors'  => [
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader .offset-right .tpg-el-image-wrap'     => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-content-loader .offset-right .tpg-el-image-wrap img' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$ref->add_control(
			'hover_animation',
			[
				'label'        => __( 'Image Hover Animation', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'        => __( 'Default', 'the-post-grid' ),
					'img_zoom_in'    => __( 'Zoom In', 'the-post-grid' ),
					'img_zoom_out'   => __( 'Zoom Out', 'the-post-grid' ),
					'slide_to_right' => __( 'Slide to Right', 'the-post-grid' ),
					'slide_to_left'  => __( 'Slide to Left', 'the-post-grid' ),
					'img_no_effect'  => __( 'None', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'img_hover_animation_',
			]
		);

		$ref->add_control(
			'is_thumb_lightbox',
			[
				'label'   => __( 'Light Box', 'the-post-grid' ) . $ref->pro_label,
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'the-post-grid' ),
					'show'    => __( 'Show', 'the-post-grid' ),
					'hide'    => __( 'Hide', 'the-post-grid' ),
				],
				'classes' => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'is_default_img',
			[
				'label'        => __( 'Enable Default Image', 'the-post-grid' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);


		$ref->add_control(
			'default_image',
			[
				'label'     => __( 'Default Image', 'the-post-grid' ) . $ref->pro_label,
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'is_default_img' => 'yes',
				],
				'classes'   => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Post Title Settings
	 *
	 * @param $ref
	 */

	public static function post_title_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_title_settings',
			[
				'label'     => esc_html__( 'Post Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_title'         => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_control(
			'title_tag',
			[
				'label'   => __( 'Title Tag', 'the-post-grid' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => __( 'H1', 'the-post-grid' ),
					'h2' => __( 'H2', 'the-post-grid' ),
					'h3' => __( 'H3', 'the-post-grid' ),
					'h4' => __( 'H4', 'the-post-grid' ),
					'h5' => __( 'H5', 'the-post-grid' ),
					'h6' => __( 'H6', 'the-post-grid' ),
				],
			]
		);

		$ref->add_control(
			'title_visibility_style',
			[
				'label'        => __( 'Title Visibility Style', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'    => __( 'Default', 'the-post-grid' ),
					'one-line'   => __( 'Show in 1 line', 'the-post-grid' ),
					'two-line'   => __( 'Show in 2 line', 'the-post-grid' ),
					'three-line' => __( 'Show in 3 line', 'the-post-grid' ),
					'custom'     => __( 'Custom', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'title-',
			]
		);

		$ref->add_control(
			'title_limit',
			[
				'label'     => __( 'Title Length', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'step'      => 1,
				'classes'   => 'tpg-padding-left',
				'condition' => [
					'title_visibility_style' => 'custom',
				],
			]
		);

		$ref->add_control(
			'title_limit_type',
			[
				'label'      => __( 'Title Crop by', 'the-post-grid' ),
				'type'       => \Elementor\Controls_Manager::SELECT,
				'default'    => 'word',
				'options'    => [
					'word'      => __( 'Words', 'the-post-grid' ),
					'character' => __( 'Characters', 'the-post-grid' ),
				],
				'classes'    => 'tpg-padding-left',
				'conditions' => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'title_limit',
							'operator' => '>',
							'value'    => 0,
						],
						[
							'name'     => 'title_visibility_style',
							'operator' => '==',
							'value'    => 'custom',
						],
					],

				],
			]
		);

		$title_position = [
			'default' => __( 'Default', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$title_position_pro = [
				'above_image' => __( 'Above Image', 'the-post-grid' ),
				'below_image' => __( 'Below Image', 'the-post-grid' ),
			];
			$title_position     = array_merge( $title_position, $title_position_pro );
		}

		$ref->add_control(
			'title_position',
			[
				'label'        => __( 'Title Position', 'the-post-grid' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'title_position_',
				'render_type'  => 'template',
				'classes'      => rtTPG()->hasPro() ? '' : 'tpg-should-hide-field',
				'options'      => $title_position,
				'description'  => $ref->get_pro_message( 'more position (above/below image)' ),
				'condition'    => [
					$prefix . '_layout' => [
						'grid-layout1',
						'grid-layout2',
						'grid-layout3',
						'grid-layout4',
						'slider-layout1',
						'slider-layout2',
						'slider-layout3',
					],
				],
			]
		);

		$ref->add_control(
			'title_hover_underline',
			[
				'label'        => __( 'Title Hover Underline', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'title_hover_border_',
				'render_type'  => 'template',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'enable'  => __( 'Enable', 'the-post-grid' ),
					'disable' => __( 'Disable', 'the-post-grid' ),
				],
			]
		);


		$ref->end_controls_section();
	}


	/**
	 * Post Excerpt Settings
	 *
	 * @param $ref
	 */

	public static function post_excerpt_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_excerpt_settings',
			[
				'label'     => esc_html__( 'Excerpt', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_excerpt'       => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$excerpt_type = [
			'character' => __( 'Character', 'the-post-grid' ),
			'word'      => __( 'Word', 'the-post-grid' ),
		];


		if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
			$excerpt_type['full'] = __( 'Full Content', 'the-post-grid' );
		}

		$ref->add_control(
			'excerpt_type',
			[
				'label'   => __( 'Excerpt Type', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'character',
				'options' => $excerpt_type,
			]
		);

		$default_excerpt_limit = 100;
		if ( 'grid' == $prefix ) {
			$default_excerpt_limit = 200;
		}

		$ref->add_control(
			'excerpt_limit',
			[
				'label'     => __( 'Excerpt Limit', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'step'      => 1,
				'default'   => $default_excerpt_limit,
				'condition' => [
					'excerpt_type' => [ 'character', 'word' ],
				],
			]
		);

		$ref->add_control(
			'excerpt_more_text',
			[
				'label'     => __( 'Expansion Indicator', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '...',
				'condition' => [
					'excerpt_type' => [ 'character', 'word' ],
				],
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Post Meta Settings
	 *
	 * @param $ref
	 */

	public static function post_meta_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_meta_settings',
			[
				'label'      => esc_html__( 'Meta Data', 'the-post-grid' ),
				'tab'        => Controls_Manager::TAB_SETTINGS,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_date',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_category',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_author',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_tags',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_comment_count',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_post_count',
							'operator' => '==',
							'value'    => 'show',
						],
					],
				],
				'condition'  => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$meta_position = [
			'default' => __( 'Default', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$meta_position_pro = [
				'above_title'   => __( 'Above Title', 'the-post-grid' ),
				'below_title'   => __( 'Below Title', 'the-post-grid' ),
				'above_excerpt' => __( 'Above excerpt', 'the-post-grid' ),
				'below_excerpt' => __( 'Below excerpt', 'the-post-grid' ),
			];
			$meta_position     = array_merge( $meta_position, $meta_position_pro );
		}

		$ref->add_control(
			'meta_position',
			[
				'label'        => __( 'Meta Position', 'the-post-grid' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'prefix_class' => 'meta_position_',
				'render_type'  => 'template',
				'options'      => $meta_position,
				'classes'      => rtTPG()->hasPro() ? '' : 'tpg-should-hide-field',
			]
		);

		$ref->add_control(
			'show_meta_icon',
			[
				'label'        => __( 'Show Meta Icon', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'meta_separator',
			[
				'label'   => __( 'Meta Separator', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default - None', 'the-post-grid' ),
					'.'       => __( 'Dot ( . )', 'the-post-grid' ),
					'/'       => __( 'Single Slash ( / )', 'the-post-grid' ),
					'//'      => __( 'Double Slash ( // )', 'the-post-grid' ),
					'-'       => __( 'Hyphen ( - )', 'the-post-grid' ),
					'|'       => __( 'Vertical Pipe ( | )', 'the-post-grid' ),
				],
			]
		);


		$ref->add_control(
			'meta_popover_toggle',
			[
				'label'        => __( 'Change Meta Icon', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'the-post-grid' ),
				'label_on'     => __( 'Custom', 'the-post-grid' ),
				'return_value' => 'yes',
				'condition'    => [
					'show_meta_icon' => 'yes',
				],
			]
		);

		$ref->start_popover();

		$ref->add_control(
			'user_icon',
			[
				'label'     => __( 'Author Icon', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-user',
					'library' => 'solid',
				],
				'condition' => [
					'show_author_image!' => 'show',
				],
			]
		);

		$ref->add_control(
			'cat_icon',
			[
				'label'     => __( 'Category Icon', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-folder-open',
					'library' => 'solid',
				],
				'condition' => [
					'show_category' => 'show',
				],
			]
		);

		$ref->add_control(
			'date_icon',
			[
				'label'     => __( 'Date Icon', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'far fa-calendar-alt',
					'library' => 'solid',
				],
				'condition' => [
					'show_date' => 'show',
				],
			]
		);

		$ref->add_control(
			'tag_icon',
			[
				'label'     => __( 'Tags Icon', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fa fa-tags',
					'library' => 'solid',
				],
				'condition' => [
					'show_tags' => 'show',
				],
			]
		);

		$ref->add_control(
			'comment_icon',
			[
				'label'     => __( 'Comment Icon', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-comments',
					'library' => 'solid',
				],
				'condition' => [
					'show_comment_count' => 'show',
				],
			]
		);

		$ref->add_control(
			'post_count_icon',
			[
				'label'     => __( 'Post Count Icon', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-eye',
					'library' => 'solid',
				],
				'condition' => [
					'show_post_count' => 'show',
				],
			]
		);

		$ref->end_popover();


		/**
		 * TODO: Author Style
		 * ********************
		 */

		$ref->add_control(
			'meta_author_divider',
			[
				'type'      => \Elementor\Controls_Manager::DIVIDER,
				'condition' => [
					'show_author!' => '',
				],
			]
		);

		$ref->add_control(
			'meta_author_heading',
			[
				'label'     => __( 'Author Setting:', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'show_author!' => '',
				],
			]
		);

		$ref->add_control(
			'author_prefix',
			[
				'label'       => __( 'Author Prefix', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'By', 'the-post-grid' ),
				'condition'   => [
					'show_author!' => '',
				],
			]
		);

		$ref->add_control(
			'author_icon_visibility',
			[
				'label'        => __( 'Author Icon Visibility', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'hide'    => __( 'Hide', 'the-post-grid' ),
					'show'    => __( 'Show', 'the-post-grid' ),
				],
				'condition'    => [
					'show_author!' => '',
				],
				'prefix_class' => 'tpg-is-author-icon-',
			]
		);

		$ref->add_control(
			'show_author_image',
			[
				'label'        => __( 'Author Image / Icon', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Image', 'the-post-grid' ),
				'label_off'    => __( 'Icon', 'the-post-grid' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'prefix_class' => 'author-image-visibility-',
				'condition'    => [
					'show_author!'       => '',
					'author_icon_visibility!' => 'hide',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_responsive_control(
			'author_icon_width',
			[
				'label'      => __( 'Author Image Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags span img' => 'width: {{SIZE}}{{UNIT}} !important;max-width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'show_author!'       => '',
					'show_author_image!' => '',
				],
			]
		);

		/**
		 * TODO: Category Style
		 * ********************
		 */

		$ref->add_control(
			'category_heading',
			[
				'label'      => __( 'Category and Tags Setting:', 'the-post-grid' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'classes'    => 'tpg-control-type-heading',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_category',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_tags',
							'operator' => '==',
							'value'    => 'show',
						],
					],
				],
			]
		);


		$ref->add_control(
			'category_position',
			[
				'label'        => __( 'Category Position', 'the-post-grid' ) . $ref->pro_label,
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'      => __( 'Default', 'the-post-grid' ),
					'above_title'  => __( 'Above Title', 'the-post-grid' ),
					'with_meta'    => __( 'With Meta', 'the-post-grid' ),
					'top_left'     => __( 'Over image (Top Left)', 'the-post-grid' ),
					'top_right'    => __( 'Over image (Top Right)', 'the-post-grid' ),
					'bottom_left'  => __( 'Over image (Bottom Left)', 'the-post-grid' ),
					'bottom_right' => __( 'Over image (Bottom Right)', 'the-post-grid' ),
					'image_center' => __( 'Over image (Center)', 'the-post-grid' ),
				],
				'condition'    => [
					'show_category' => 'show',
				],
				'render_type'  => 'template',
				'divider'      => 'before',
				'prefix_class' => 'tpg-category-position-',
				'classes'      => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'category_style',
			[
				'label'     => __( 'Category Style', 'the-post-grid' ) . $ref->pro_label,
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'style1',
				'options'   => [
					'style1' => __( 'Style 1', 'the-post-grid' ),
					'style2' => __( 'Style 2', 'the-post-grid' ),
					'style3' => __( 'Style 3', 'the-post-grid' ),
				],
				'classes'   => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
				'condition' => [
					'category_position!' => 'default',
				],
			]
		);

		if ( rtTPG()->hasPro() ) {
			$ref->add_control(
				'show_cat_icon',
				[
					'label'        => __( 'Show Over Image Category Icon', 'the-post-grid' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'the-post-grid' ),
					'label_off'    => __( 'Hide', 'the-post-grid' ),
					'return_value' => 'yes',
					'default'      => false,
					'condition'    => [
						'category_position!' => 'default',
					],
				]
			);
		}

		$post_types = Fns::get_post_types();
		foreach ( $post_types as $post_type => $label ) {
			$_taxonomies = get_object_taxonomies( $post_type, 'object' );
			if ( empty( $_taxonomies ) ) {
				continue;
			}
			$term_options = [];
			foreach ( $_taxonomies as $tax ) {
				if ( 'post_format' == $tax->name ) {
					continue;
				}
				$term_options[ $tax->name ] = $tax->label;
			}

			if ( 'post' === $post_type ) {
				$default_cat = 'category';
				$default_tag = 'post_tag';
			} elseif ( 'product' === $post_type ) {
				$default_cat = 'product_cat';
				$default_tag = 'product_tag';
			} elseif ( 'download' === $post_type ) {
				$default_cat = 'download_category';
				$default_tag = 'download_tag';
			} elseif ( 'docs' === $post_type ) {
				$default_cat = 'doc_category';
				$default_tag = 'doc_tag';
			} elseif ( 'lp_course' === $post_type ) {
				$default_cat = 'course_category';
				$default_tag = 'course_tag';
			} else {
				$taxonomie_keys = array_keys( $_taxonomies );
				$filter_cat     = array_filter(
					$taxonomie_keys,
					function ( $item ) {
						return strpos( $item, 'cat' ) !== false;
					}
				);
				$filter_tag     = array_filter(
					$taxonomie_keys,
					function ( $item ) {
						return strpos( $item, 'tag' ) !== false;
					}
				);

				if ( is_array( $filter_cat ) && ! empty( $filter_cat ) ) {
					$default_cat = array_shift( $filter_cat );
				}
				if ( is_array( $filter_tag ) && ! empty( $filter_tag ) ) {
					$default_tag = array_shift( $filter_tag );
				}
			}

			$ref->add_control(
				$post_type . '_taxonomy',
				[
					'label'       => __( 'Category Source', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => $default_cat,
					'options'     => $term_options,
					'condition'   => [
						'show_category' => 'show',
						'post_type'     => $post_type,
					],
					'description' => __( 'Select which taxonomy should sit in the place of categories. Default: Category' ),
				]
			);

			$ref->add_control(
				$post_type . '_tags',
				[
					'label'       => __( 'Tags Source', 'the-post-grid' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => $default_tag,
					'options'     => $term_options,
					'condition'   => [
						'show_category' => 'show',
						'post_type'     => $post_type,
					],
					'description' => __( 'Select which taxonomy should sit in the place of tags. Default: Tags' ),
				]
			);
		}

		$ref->add_control(
			'comment_count_heading',
			[
				'label'     => __( 'Comment Count ', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'show_comment_count' => 'show',
				],
			]
		);

		$ref->add_control(
			'show_comment_count_label',
			[
				'label'        => __( 'Show Comment Label', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_comment_count' => 'show',
				],
			]
		);

		$ref->add_control(
			'comment_count_label_singular',
			[
				'label'       => __( 'Comment Label Singular', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Comment', 'the-post-grid' ),
				'placeholder' => __( 'Type your title here', 'the-post-grid' ),
				'condition'   => [
					'show_comment_count'       => 'show',
					'show_comment_count_label' => 'yes',
				],
			]
		);

		$ref->add_control(
			'comment_count_label_plural',
			[
				'label'       => __( 'Comment Label Plural', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Comments', 'the-post-grid' ),
				'placeholder' => __( 'Type your title here', 'the-post-grid' ),
				'condition'   => [
					'show_comment_count'       => 'show',
					'show_comment_count_label' => 'yes',
				],
			]
		);

		$ref->add_control(
			'meta_ordering_heading',
			[
				'label'   => __( 'Meta Ordering', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'repeater_hidden',
			[
				'type' => \Elementor\Controls_Manager::HIDDEN,
			]
		);

		$ref->add_control(
			'meta_ordering',
			[
				'label'       => esc_html__( 'Meta Ordering (Drag and Drop)', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'meta_title' => esc_html__( 'Author', 'the-post-grid' ),
						'meta_name'  => 'author',
					],
					[
						'meta_title' => esc_html__( 'Date', 'the-post-grid' ),
						'meta_name'  => 'date',
					],
					[
						'meta_title' => esc_html__( 'Category', 'the-post-grid' ),
						'meta_name'  => 'category',
					],
					[
						'meta_title' => esc_html__( 'Tags', 'the-post-grid' ),
						'meta_name'  => 'tags',
					],
					[
						'meta_title' => esc_html__( 'Comment Count', 'the-post-grid' ),
						'meta_name'  => 'comment_count',
					],
					[
						'meta_title' => esc_html__( 'Post Count', 'the-post-grid' ),
						'meta_name'  => 'post_count',
					],
					[
						'meta_title' => esc_html__( 'Post Like', 'the-post-grid' ),
						'meta_name'  => 'post_like',
					],
				],
				'classes'     => 'tpg-item-order-repeater',
				'title_field' => '{{{ meta_title }}}',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Read More Settings
	 *
	 * @param $ref
	 */

	public static function post_readmore_settings( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_readmore_settings',
			[
				'label'     => esc_html__( 'Read More', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_read_more'     => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);


		$ref->add_control(
			'readmore_btn_style',
			[
				'label'        => __( 'Button Style', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default-style',
				'options'      => [
					'default-style' => __( 'Default from style', 'the-post-grid' ),
					'only-text'     => __( 'Only Text Button', 'the-post-grid' ),
				],
				'prefix_class' => 'readmore-btn-',
			]
		);

		$ref->add_control(
			'read_more_label',
			[
				'label'       => __( 'Read More Label', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Read More', 'the-post-grid' ),
				'placeholder' => __( 'Type Read More Label here', 'the-post-grid' ),
			]
		);


		$ref->add_control(
			'show_btn_icon',
			[
				'label'        => __( 'Show Button Icon', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
			]
		);

		$ref->add_control(
			'readmore_btn_icon',
			[
				'label'     => __( 'Choose Icon', 'text-domain' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-angle-right',
					'library' => 'solid',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->end_controls_section();
	}


	/**
	 *  Advanced Custom Field ACF Settings
	 *
	 * @param $ref
	 */

	public static function tpg_acf_settings( $ref ) {
		$prefix = $ref->prefix;
		$cf     = Fns::checkWhichCustomMetaPluginIsInstalled();

		if ( ! $cf || ! rtTPG()->hasPro() ) {
			return;
		}

		$ref->start_controls_section(
			'tgp_acf_settings',
			[
				'label'     => esc_html__( 'ACF Settings', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => [
					'show_acf'           => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		self::get_tpg_acf_settings( $ref );

		$ref->end_controls_section();
	}

	public static function get_tpg_acf_settings( $ref ) {
		$ref->add_control(
			'cf_hide_empty_value',
			[
				'label'        => __( 'Hide field with empty value?', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'No', 'the-post-grid' ),
				'label_off'    => __( 'Yes', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'cf_group!' => '',
				],
			]
		);

		$ref->add_control(
			'cf_hide_group_title',
			[
				'label'        => __( 'Show group title?', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'No', 'the-post-grid' ),
				'label_off'    => __( 'Yes', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'cf_group!' => '',
				],
			]
		);

		$ref->add_control(
			'cf_show_only_value',
			[
				'label'        => __( 'Show label?', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'No', 'the-post-grid' ),
				'label_off'    => __( 'Yes', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'cf_group!' => '',
				],
			]
		);
	}


	/**
	 * Links Settings
	 *
	 * @param $ref
	 */
	public static function links( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'tpg_links_settings',
			[
				'label'     => esc_html__( 'Links', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],

			]
		);

		$link_type = [
			'default' => __( 'Link to details page', 'the-post-grid' ),
		];
		if ( rtTPG()->hasPro() ) {
			$link_type['popup']       = __( 'Single Popup', 'the-post-grid' );
			$link_type['multi_popup'] = __( 'Multi Popup', 'the-post-grid' );
		}
		$link_type['none'] = __( 'No Link', 'the-post-grid' );

		$ref->add_control(
			'post_link_type',
			[
				'label'       => __( 'Post link type', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => $link_type,
				'description' => $ref->get_pro_message( 'popup options' ),
			]
		);

		$ref->add_control(
			'link_target',
			[
				'label'     => __( 'Link Target', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '_self',
				'options'   => [
					'_self'  => __( 'Same Window', 'the-post-grid' ),
					'_blank' => __( 'New Window', 'the-post-grid' ),
				],
				'condition' => [
					'post_link_type' => 'default',
				],
			]
		);

		$ref->add_control(
			'is_thumb_linked',
			[
				'label'        => __( 'Thumbnail Link', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Promotions
	 *
	 * @param $ref
	 */
	public static function promotions( $ref ) {
		if ( rtTPG()->hasPro() ) {
			return;
		}
		$pro_url = "//www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/";

		$ref->start_controls_section(
			'tpg_pro_alert',
			[
				'label' => sprintf(
					'<span style="color: #f54">%s</span>',
					__( 'Go Premium for More Features', 'the-post-grid' )
				),
			]
		);

		$ref->add_control(
			'tpg_control_get_pro',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div class="elementor-nerd-box"><div class="elementor-nerd-box-title" style="margin-top: 0; margin-bottom: 20px;">Unlock more possibilities</div><div class="elementor-nerd-box-message"><span class="pro-feature" style="font-size: 13px;"> Get the <a href="'
				          . $pro_url
				          . '" target="_blank" style="color: #f54">Pro version</a> for more stunning layouts and customization options.</span></div><a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-button-go-pro" href="'
				          . $pro_url . '" target="_blank">Get Pro</a></div>',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Section Title Style
	 *
	 * @param $ref
	 */
	public static function sectionTitle( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'tpg_section_title_style',
			[
				'label'     => esc_html__( 'Section Title', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_section_title' => 'show',
				],
			]
		);

		$section_title_condition = [];
		if ( 'archive' !== $layout_type ) {
			$section_title_condition = [
				'filter_btn_style!' => 'carousel',
			];
		}

		$ref->add_responsive_control(
			'section_title_alignment',
			[
				'label'        => __( 'Alignment', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'render_type'  => 'template',
				'prefix_class' => 'section-title-align-',
				'condition'    => $section_title_condition,
			]
		);


		$ref->add_responsive_control(
			'section_title_margin',
			[
				'label'              => __( 'Margin Y axis', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'vertical', //horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		if ( 'slider' === $prefix ) {
			$ref->add_responsive_control(
				'section_title_padding',
				[
					'label'              => __( 'Padding', 'the-post-grid' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => [ 'px' ],
					'allowed_dimensions' => 'all', //horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
					'default'            => [
						'top'      => '',
						'right'    => '',
						'bottom'   => '',
						'left'     => '',
						'isLinked' => false,
					],
					'selectors'          => [
						'{{WRAPPER}} .slider-layout-main .tpg-widget-heading-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'section_title_typography',
				'label'    => __( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading',
			]
		);

		$ref->add_control(
			'section_title_color',
			[
				'label'     => __( 'Title Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'section_title_bg_color',
			[
				'label'     => __( 'Title Background Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading, {{WRAPPER}} .tpg-widget-heading-wrapper.heading-style3 .tpg-widget-heading'                => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::after, {{WRAPPER}} .tpg-widget-heading-wrapper.heading-style2 .tpg-widget-heading::before' => 'border-color: {{VALUE}} transparent',
				],
				'condition' => [
					'section_title_style' => [ 'style2', 'style3' ],
				],
			]
		);


		$ref->add_control(
			'section_title_dot_color',
			[
				'label'     => __( 'Dot Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'section_title_style' => 'style1',
				],
			]
		);

		$ref->add_control(
			'section_title_line_color',
			[
				'label'     => __( 'Line / Border Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper.heading-style1 .tpg-widget-heading-line'                                                                                                                                                                                                                                                                      => 'border-color: {{VALUE}}',
					//'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper'                                                                                                                                         => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper:not(.carousel) .tpg-widget-heading-wrapper,{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide.selected'                                                                                       => 'color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover, {{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide:hover'                                                                                             => 'color: {{VALUE}}',
					'{{WRAPPER}}.section-title-style-style2 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before, {{WRAPPER}}.section-title-style-style3 .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide::before'                                                                                         => 'border-bottom-color: {{VALUE}}',

				],
				'condition' => [
					'section_title_style!' => 'default',
				],
			]
		);

		$ref->add_control(
			'prefix_text_color',
			[
				'label'     => __( 'Prefix Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading .prefix-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'section_title_source' => 'page_title',
				],
			]
		);
		$ref->add_control(
			'suffix_text_color',
			[
				'label'     => __( 'Suffix Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-widget-heading-wrapper .tpg-widget-heading .suffix-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'section_title_source' => 'page_title',
				],
			]
		);


		if ( 'archive' == $layout_type ) {
			$ref->add_control(
				'cat_tag_description_heading',
				[
					'label'     => __( 'Category / Tag Description', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'classes'   => 'tpg-control-type-heading',
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name'      => 'taxonomy_des_typography',
					'label'     => __( 'Description Typography', 'the-post-grid' ),
					'selector'  => '{{WRAPPER}} .tpg-category-description',
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_responsive_control(
				'taxonomy_des_alignment',
				[
					'label'     => __( 'Alignment', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => [
						'left'   => [
							'title' => __( 'Left', 'the-post-grid' ),
							'icon'  => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'the-post-grid' ),
							'icon'  => 'eicon-text-align-center',
						],
						'right'  => [
							'title' => __( 'Right', 'the-post-grid' ),
							'icon'  => 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tpg-category-description' => 'text-align: {{VALUE}}',
					],
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_control(
				'taxonomy_des_color',
				[
					'label'     => __( 'Title Color', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tpg-category-description' => 'color: {{VALUE}}',
					],
					'condition' => [
						'show_cat_desc' => 'yes',
					],
				]
			);

			$ref->add_responsive_control(
				'taxonomy_des_dimension',
				[
					'label'      => __( 'Padding', 'the-post-grid' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px' ],
					'selectors'  => [
						'{{WRAPPER}} .tpg-category-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'show_cat_desc' => 'yes',
					],
				]
			);
		}

		$ref->end_controls_section();
	}


	/**
	 * Thumbnail Style Tab
	 *
	 * @param $ref
	 */
	public static function thumbnailStyle( $ref ) {
		$prefix = $ref->prefix;
		// Thumbnail style
		//========================================================
		$ref->start_controls_section(
			'thumbnail_style',
			[
				'label'     => __( 'Thumbnail', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumb' => 'show',
				],
			]
		);

		$ref->add_responsive_control(
			'img_border_radius',
			[
				'label'              => __( 'Border Radius', 'the-post-grid' ) . $ref->pro_label,
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all', //horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => true,
				],
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap, {{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap img, {{WRAPPER}} .rt-grid-hover-item .grid-hover-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'description'        => $ref->get_pro_message( "image radius." ),
				'classes'            => rtTPG()->hasPro() ? '' : 'the-post-grid-field-hide',
			]
		);

		$ref->add_control(
			'image_width',
			[
				'label'     => __( 'Image Width (Optional)', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'inherit',
				'options'   => [
					'inherit' => __( 'Default', 'the-post-grid' ),
					'100%'    => __( '100%', 'the-post-grid' ),
					'auto'    => __( 'Auto', 'the-post-grid' ),
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap img' => 'width: {{VALUE}};',
				],
			]
		);

		$ref->add_responsive_control(
			'thumbnail_spacing',
			[
				'label'      => __( 'Thumbnail Margin', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		//Overlay Style Heading

		$ref->add_control(
			'thumb_overlay_style_heading',
			[
				'label'   => __( 'Overlay Style:', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		//TODO: Tab normal
		$ref->start_controls_tabs(
			'grid_hover_style_tabs'
		);

		$ref->start_controls_tab(
			'grid_hover_style_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'grid_hover_overlay_color',
				'label'          => __( 'Overlay BG', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content:before, {{WRAPPER}} .tpg-el-main-wrapper .tpg-el-image-wrap .overlay',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay Background Type', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_bg',
			[
				'label'     => __( 'Light Box Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_color',
			[
				'label'     => __( 'Light Box Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->end_controls_tab();

		//TODO: Tab Hover
		$ref->start_controls_tab(
			'grid_hover_style_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'grid_hover_overlay_color_hover',
				'label'          => __( 'Overlay BG - Hover', 'the-post-grid' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .rt-tpg-container .rt-grid-hover-item .rt-holder .grid-hover-content:after, {{WRAPPER}} .tpg-el-main-wrapper .rt-holder:hover .tpg-el-image-wrap .overlay',
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay Background Type - Hover', 'the-post-grid' ),
					],
					'color'      => [
						'label' => 'Background Color',
					],
					'color_b'    => [
						'label' => 'Background Color 2',
					],
				],
			]
		);


		$ref->add_control(
			'thumb_lightbox_bg_hover',
			[
				'label'     => __( 'Light Box Background - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->add_control(
			'thumb_lightbox_color_hover',
			[
				'label'     => __( 'Light Box Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .rt-img-holder .tpg-zoom .fa' => 'color: {{VALUE}}',
				],
				'condition' => [
					'is_thumb_lightbox' => 'show',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->add_control(
			'hr_for_overlay',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$overlay_type_opt = [
			'always'              => __( 'Show Always', 'the-post-grid' ),
			'fadein-on-hover'     => __( 'FadeIn on hover', 'the-post-grid' ),
			'fadeout-on-hover'    => __( 'FadeOut on hover', 'the-post-grid' ),
			'slidein-on-hover'    => __( 'SlideIn on hover', 'the-post-grid' ),
			'slideout-on-hover'   => __( 'SlideOut on hover', 'the-post-grid' ),
			'zoomin-on-hover'     => __( 'ZoomIn on hover', 'the-post-grid' ),
			'zoomout-on-hover'    => __( 'ZoomOut on hover', 'the-post-grid' ),
			'zoominall-on-hover'  => __( 'ZoomIn Content on hover', 'the-post-grid' ),
			'zoomoutall-on-hover' => __( 'ZoomOut Content on hover', 'the-post-grid' ),
		];

		if ( $ref->prefix == 'grid_hover' || $ref->prefix == 'slider' ) {
			$overlay_type_opt2 = [
				'flipin-on-hover'  => __( 'FlipIn on hover', 'the-post-grid' ),
				'flipout-on-hover' => __( 'FlipOut on hover', 'the-post-grid' ),
			];
			$overlay_type_opt  = array_merge( $overlay_type_opt, $overlay_type_opt2 );
		}

		$ref->add_control(
			'grid_hover_overlay_type',
			[
				'label'        => __( 'Overlay Type', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'always',
				'options'      => $overlay_type_opt,
				'description'  => __( 'If you don\'t choose overlay background then it will work only for some selected layout ', 'the-post-grid' ),
				'prefix_class' => 'grid-hover-overlay-type-',
			]
		);

		$overlay_height_condition = [
			'grid_hover_layout!' => [ 'grid_hover-layout3' ],
		];
		if ( $ref->prefix === 'slider' ) {
			$overlay_height_condition = [
				'slider_layout!' => [ '' ],
			];
		}
		$ref->add_control(
			'grid_hover_overlay_height',
			[
				'label'        => __( 'Overlay Height', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'full'    => __( '100%', 'the-post-grid' ),
					'auto'    => __( 'Auto', 'the-post-grid' ),
				],
				'condition'    => $overlay_height_condition,
				'prefix_class' => 'grid-hover-overlay-height-',
			]
		);

		$ref->add_control(
			'on_hover_overlay',
			[
				'label'        => __( 'Overlay Height on hover', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'full'    => __( '100%', 'the-post-grid' ),
					'auto'    => __( 'Auto', 'the-post-grid' ),
				],
				'condition'    => $overlay_height_condition,
				'prefix_class' => 'hover-overlay-height-',
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Post Title Style
	 *
	 * @param $ref
	 */
	public static function titleStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'title_style',
			[
				'label'     => __( 'Post Title', 'the-post-grid' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title'         => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .tpg-el-main-wrapper .entry-title-wrapper .entry-title',
			]
		);

		//Offset Title
		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'        => 'title_offset_typography',
				'label'       => esc_html__( 'Offset Typography', 'the-post-grid' ),
				'selector'    => '{{WRAPPER}} .tpg-el-main-wrapper .offset-left .entry-title-wrapper .entry-title',
				'description' => __( 'You can overwrite offset title font style', 'the-post-grid' ),
				'condition'   => [
					$prefix . '_layout' => [
						'grid-layout5',
						'grid-layout5-2',
						'grid-layout6',
						'grid-layout6-2',
						'list-layout2',
						'list-layout3',
						'list-layout2-2',
						'list-layout3-2',
						'grid_hover-layout4',
						'grid_hover-layout4-2',
						'grid_hover-layout5',
						'grid_hover-layout5-2',
						'grid_hover-layout6',
						'grid_hover-layout6-2',
						'grid_hover-layout7',
						'grid_hover-layout7-2',
						'grid_hover-layout9',
						'grid_hover-layout9-2',
					],
				],
			]
		);

		$ref->add_control(
			'title_border_visibility',
			[
				'label'        => __( 'Title Border Bottom', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'show'    => __( 'Show', 'the-post-grid' ),
					'hide'    => __( 'Hide', 'the-post-grid' ),
				],
				'prefix_class' => 'tpg-title-border-',
				'condition'    => [
					$prefix . '_layout' => 'grid_hover-layout3',
				],
			]
		);

		$ref->add_responsive_control(
			'title_spacing',
			[
				'label'              => __( 'Title Margin', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .entry-title-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$ref->add_responsive_control(
			'title_padding',
			[
				'label'              => __( 'Title Padding', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .entry-title-wrapper .entry-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'all',
			]
		);

		$ref->add_responsive_control(
			'title_alignment',
			[
				'label'        => __( 'Alignment', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'    => [
						'title' => __( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'title-alignment-',
				'toggle'       => true,
				'selectors'    => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		//TODO: Start Title Style Tba
		$ref->start_controls_tabs(
			'title_style_tabs'
		);

		$ref->start_controls_tab(
			'title_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);
		//TODO: Normal Tab
		$ref->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_bg_color',
			[
				'label'     => __( 'Title Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_border_color',
			[
				'label'     => __( 'Title Separator Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder .entry-title-wrapper .entry-title::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					$prefix . '_layout'        => 'grid_hover-layout3',
					'title_border_visibility!' => 'hide',
				],
			]
		);

		$ref->add_control(
			'title_hover_border_color',
			[
				'label'     => __( 'Title Hover Border Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--tpg-primary-color: {{VALUE}}',
				],
				'condition' => [
					'title_hover_underline' => 'enable',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'title_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		//TODO: Hover Tab
		$ref->add_control(
			'title_hover_color',
			[
				'label'     => __( 'Title Color on Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder .entry-title:hover, {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder .entry-title a:hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'title_bg_color_hover',
			[
				'label'     => __( 'Title Background on hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .entry-title:hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'title_box_hover_tab',
			[
				'label' => __( 'Box Hover', 'the-post-grid' ),
			]
		);

		//TODO: Box Hover Tab
		$ref->add_control(
			'title_color_box_hover',
			[
				'label'     => __( 'Title color on boxhover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title, {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_bg_color_box_hover',
			[
				'label'     => __( 'Title Background on boxhover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .entry-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'title_border_color_hover',
			[
				'label'     => __( 'Title Separator color - boxhover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .entry-title-wrapper .entry-title::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					$prefix . '_layout'        => 'grid_hover-layout3',
					'title_border_visibility!' => 'hide',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}

	/**
	 * Content Style Tab
	 *
	 * @param $ref
	 */
	public static function contentStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'excerpt_style',
			[
				'label'     => __( 'Excerpt / Content', 'the-post-grid' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt'       => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner',
			]
		);

		$ref->add_responsive_control(
			'excerpt_spacing',
			[
				'label'              => __( 'Excerpt Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$ref->add_responsive_control(
			'content_alignment',
			[
				'label'     => __( 'Alignment', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner' => 'text-align: {{VALUE}}',
				],
			]
		);

		//TODO: Start Content Tab

		$ref->start_controls_tabs(
			'excerpt_style_tabs'
		);

		$ref->start_controls_tab(
			'excerpt_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		//TODO: Normal Tab
		$ref->add_control(
			'excerpt_color',
			[
				'label'     => __( 'Excerpt color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-el-excerpt .tpg-excerpt-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'excerpt_hover_tab',
			[
				'label' => __( 'Box Hover', 'the-post-grid' ),
			]
		);

		//TODO: Hover Tab
		$ref->add_control(
			'excerpt_hover_color',
			[
				'label'     => __( 'Excerpt color on hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-el-excerpt .tpg-excerpt-inner' => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}

	/**
	 * Post Meta Style Tab
	 *
	 * @param $ref
	 */
	public static function metaInfoStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'post_meta_style',
			[
				'label'     => __( 'Post Meta', 'the-post-grid' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta'          => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'post_meta_typography',
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-el-post-meta',
			]
		);

		$ref->add_responsive_control(
			'meta_spacing',
			[
				'label'              => __( 'Meta Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-holder .rt-el-post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$ref->add_control(
			'separator_cat_heading',
			[
				'label'     => __( 'Separate Category', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'category_position!' => 'default',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'separator_cat_typography',
				'selector'  => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .tpg-separate-category',
				'condition' => [
					'category_position!' => 'default',
				],
			]
		);

		$ref->add_control(
			'category_margin_bottom',
			[
				'label'     => __( 'Category Margin Bottom', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 50,
				'step'      => 1,
				'condition' => [
					'category_position' => 'above_title',
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.above_title' => 'margin-bottom: {{VALUE}}px;',
				],
			]
		);


		$ref->add_responsive_control(
			'category_radius',
			[
				'label'      => __( 'Category Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links'         => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'category_position!' => 'default',
					'category_style!'    => 'style3',
				],
			]
		);


		/**
		 * TODO: Tags Style
		 * ********************
		 */


		//TODO: Start Content Tab

		$ref->start_controls_tabs(
			'meta_info_style_tabs'
		);

		$ref->start_controls_tab(
			'meta_info_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		//TODO: Normal Tab

		$ref->add_control(
			'meta_info_color',
			[
				'label'     => __( 'Meta Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags span' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'meta_link_color',
			[
				'label'     => __( 'Meta Link Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'meta_separator_color',
			[
				'label'     => __( 'Separator Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .separator' => 'color: {{VALUE}}',
				],
				'condition' => [
					'meta_separator!' => 'default',
				],
			]
		);

		$ref->add_control(
			'meta_icon_color',
			[
				'label'     => __( 'Icon Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags i' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'separate_category_color',
			[
				'label'     => __( 'Category Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a'        => 'color: {{VALUE}}',
				],
			]
		);
		$ref->add_control(
			'separate_category_bg',
			[
				'label'     => __( 'Category Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links'               => 'background-color: {{VALUE}};padding: 3px 8px 1px;',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a'       => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category:not(.style1) .categories-links a:after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a'                           => 'background-color: {{VALUE}}',
				],
			]
		);
		$ref->add_control(
			'separate_category_icon_color',
			[
				'label'     => __( 'Category Icon Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links i'        => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_cat_icon' => 'yes',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'meta_info_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		//TODO: Hover Tab


		$ref->add_control(
			'meta_link_colo_hover',
			[
				'label'     => __( 'Meta Link Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder .post-meta-tags a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'separate_category_color_hover',
			[
				'label'     => __( 'Category Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links a:hover' => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a:hover'        => 'color: {{VALUE}} !important',
				],
			]
		);

		$ref->add_control(
			'separate_category_bg_hover',
			[
				'label'     => __( 'Category Background - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category.style1 .categories-links:hover'                => 'background-color: {{VALUE}};padding: 3px 8px;',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links:not(.style1) a:hover'        => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-separate-category .categories-links:not(.style1) a:hover::after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .post-meta-tags .categories-links a:hover'                            => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'meta_info_box_hover_tab',
			[
				'label' => __( 'Box Hover', 'the-post-grid' ),
			]
		);

		//TODO: Box Hover Tab


		$ref->add_control(
			'meta_link_colo_box_hover',
			[
				'label'     => __( 'Meta Color - Box Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags *' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'separate_category_color_box_hover',
			[
				'label'     => __( 'Category Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links a'        => 'color: {{VALUE}}',
				],
			]
		);
		$ref->add_control(
			'separate_category_bg_box_hover',
			[
				'label'     => __( 'Category Background - Box Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category.style1 .categories-links'                => 'background-color: {{VALUE}};padding: 3px 8px;',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category:not(.style1) .categories-links a'        => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category:not(.style1) .categories-links a::after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links a'                            => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'separate_category_icon_color_box_hover',
			[
				'label'     => __( 'Category Icon Color - Box Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .tpg-separate-category .categories-links i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover .post-meta-tags .categories-links i'        => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_cat_icon' => 'yes',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 * Read More style
	 *
	 * @param $ref
	 */
	public static function readmoreStyle( $ref ) {
		$prefix = $ref->prefix;

		$ref->start_controls_section(
			'readmore_button_style',
			[
				'label'     => __( 'Read More', 'the-post-grid' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_read_more'     => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a',
			]
		);


		$ref->add_responsive_control(
			'readmore_spacing',
			[
				'label'              => __( 'Button Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_padding',
			[
				'label'      => __( 'Button Padding', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);


		$ref->add_responsive_control(
			'readmore_btn_alignment',
			[
				'label'     => __( 'Button Alignment', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more' => 'text-align:{{VALUE}}',
				],
				'toggle'    => true,
			]
		);

		$ref->add_control(
			'readmore_icon_position',
			[
				'label'     => __( 'Icon Position', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'  => __( 'Left', 'the-post-grid' ),
					'right' => __( 'Right', 'the-post-grid' ),
				],
				'separator' => 'before',
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_size',
			[
				'label'      => __( 'Icon Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_y_position',
			[
				'label'      => __( 'Icon Vertical Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 20,
						'max'  => 20,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'transform: translateY( {{SIZE}}{{UNIT}} );',
				],
				'condition'  => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		//TODO: Button style Tabs
		$ref->start_controls_tabs(
			'readmore_style_tabs'
		);

		$ref->start_controls_tab(
			'readmore_style_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'readmore_text_color',
			[
				'label'     => __( 'Text Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_color',
			[
				'label'     => __( 'Icon Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_bg',
			[
				'label'     => __( 'Background Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_margin',
			[
				'label'              => __( 'Icon Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'horizontal',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'border_radius',
			[
				'label'              => __( 'Border Radius', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'readmore_border',
				'label'          => __( 'Button Border', 'the-post-grid' ),
				'selector'       => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#D4D4D4',
					],
				],
				'condition'      => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->end_controls_tab();

		//TODO: Hover Tab

		$ref->start_controls_tab(
			'readmore_style_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'readmore_text_color_hover',
			[
				'label'     => __( 'Text Color hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_color_hover',
			[
				'label'     => __( 'Icon Color Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_bg_hover',
			[
				'label'     => __( 'Background Color hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_responsive_control(
			'readmore_icon_margin_hover',
			[
				'label'              => __( 'Icon Spacing - Hover', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'horizontal',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'border_radius_hover',
			[
				'label'              => __( 'Border Radius - Hover', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'readmore_border_hover',
				'label'          => __( 'Button Border - Hover', 'the-post-grid' ),
				'selector'       => 'body {{WRAPPER}} .rt-tpg-container .tpg-post-holder .rt-detail .read-more a:hover',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#7a64f2',
					],
				],
				'condition'      => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->end_controls_tab();


		//TODO: Box Hover Tab
		$ref->start_controls_tab(
			'readmore_style_box_hover_tab',
			[
				'label' => __( 'Box Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'readmore_text_color_box_hover',
			[
				'label'     => __( 'Text Color - BoxHover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'readmore_icon_color_box_hover',
			[
				'label'     => __( 'Icon Color - BoxHover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_btn_icon' => 'yes',
				],
			]
		);

		$ref->add_control(
			'readmore_bg_box_hover',
			[
				'label'     => __( 'Background Color - BoxHover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'readmore_border_box_hover',
				'label'          => __( 'Button Border - Box Hover', 'the-post-grid' ),
				'selector'       => '{{WRAPPER}} .rt-tpg-container .tpg-post-holder:hover .rt-detail .read-more a',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#D4D4D4',
					],
				],
				'condition'      => [
					'readmore_btn_style' => 'default-style',
				],
			]
		);


		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 * Pagination and Load more style tab
	 *
	 * @param $ref
	 */
	public static function paginationStyle( $ref ) {
		$ref->start_controls_section(
			'pagination_loadmore_style',
			[
				'label'     => __( 'Pagination / Load More', 'the-post-grid' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'show',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .rt-pagination .pagination-list > li > a, {{WRAPPER}} .rt-pagination .pagination-list > li > span',

			]
		);

		$ref->add_responsive_control(
			'pagination_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rt-pagination-wrap' => 'justify-content: {{VALUE}};',
				],
				'default'   => 'center',
				'toggle'    => true,
			]
		);

		$ref->add_responsive_control(
			'pagination_spacing',
			[
				'label'              => __( 'Button Vertical Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-pagination-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		$ref->add_responsive_control(
			'pagination_padding',
			[
				'label'              => __( 'Button Padding', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a, {{WRAPPER}} .rt-pagination .pagination-list > li > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'          => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		$ref->add_responsive_control(
			'pagination_border_radius',
			[
				'label'      => __( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li:first-child > a, {{WRAPPER}} .rt-pagination .pagination-list > li:first-child > span' => 'border-bottom-left-radius: {{SIZE}}{{UNIT}}; border-top-left-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-pagination .pagination-list > li:last-child > a, {{WRAPPER}} .rt-pagination .pagination-list > li:last-child > span'   => 'border-bottom-right-radius: {{SIZE}}{{UNIT}}; border-top-right-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);

		//Button style Tabs
		$ref->start_controls_tabs(
			'pagination_style_tabs',
			[
				'condition' => [
					'pagination_type!' => 'load_on_scroll',
				],
			]
		);


		//TODO: Normal Tab
		$ref->start_controls_tab(
			'pagination_style_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'pagination_color',
			[
				'label'     => __( 'Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:not(:hover), {{WRAPPER}} .rt-pagination .pagination-list > li > span:not(:hover)' => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a:not(:hover)'            => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'pagination_bg',
			[
				'label'     => __( 'Background Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:not(:hover), {{WRAPPER}} .rt-pagination .pagination-list > li > span:not(:hover)' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a:not(:hover)'            => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'background-color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'pagination_border_color',
			[
				'label'     => __( 'Border Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:not(:hover), {{WRAPPER}} .rt-pagination .pagination-list > li > span:not(:hover)' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a:not(:hover)'            => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn'                                                                    => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		//TODO: Hover Tab
		$ref->start_controls_tab(
			'pagination_style_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'pagination_color_hover',
			[
				'label'     => __( 'Color - hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:hover, {{WRAPPER}} .rt-pagination .pagination-list > li > span:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a:hover'      => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover'                                                  => 'color: {{VALUE}}',
				],
			]
		);


		$ref->add_control(
			'pagination_bg_hover',
			[
				'label'     => __( 'Background Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:hover, {{WRAPPER}} .rt-pagination .pagination-list > li > span:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a:hover'      => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover'                                                  => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'pagination_border_color_hover',
			[
				'label'     => __( 'Border Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > li > a:hover, {{WRAPPER}} .rt-pagination .pagination-list > li > span:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li > a:hover'      => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-loadmore-btn:hover'                                                  => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();


		//TODO: Active Tab
		$ref->start_controls_tab(
			'pagination_style_active_tab',
			[
				'label' => __( 'Active', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'pagination_color_active',
			[
				'label'     => __( 'Color - Active', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > .active > a, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:hover, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:hover, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:focus, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:focus'                                                     => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a' => 'color: {{VALUE}}',
				],
			]
		);


		$ref->add_control(
			'pagination_bg_active',
			[
				'label'     => __( 'Background Color - Active', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > .active > a, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:hover, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:hover, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:focus, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:focus'                                                     => 'background-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'pagination_border_color_active',
			[
				'label'     => __( 'Border Color - Active', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-pagination .pagination-list > .active > a, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:hover, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:hover, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > a:focus, 
					{{WRAPPER}} .rt-pagination .pagination-list > .active > span:focus'                                                     => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} .rt-tpg-container .rt-pagination-wrap .rt-page-numbers .paginationjs .paginationjs-pages ul li.active > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 * Front-End Filter style
	 *
	 * @param $ref
	 */
	public static function frontEndFilter( $ref ) {
		if ( ! rtTPG()->hasPro() ) {
			return;
		}
		$ref->start_controls_section(
			'front_end_filter_style',
			[
				'label'      => esc_html__( 'Front-End Filter', 'the-post-grid' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_taxonomy_filter',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_author_filter',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_order_by',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_sort_order',
							'operator' => '==',
							'value'    => 'show',
						],
						[
							'name'     => 'show_search',
							'operator' => '==',
							'value'    => 'show',
						],
					],
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'front_filter_typography',
				'label'    => __( 'Filter Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap, {{WRAPPER}} .tpg-header-wrapper.carousel .rt-filter-item-wrap.swiper-wrapper .swiper-slide',
			]
		);

		$ref->add_responsive_control(
			'filter_text_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'default',
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .rt-layout-filter-container .rt-filter-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$ref->add_control(
			'filter_v_alignment',
			[
				'label'        => esc_html__( 'Vertical Alignment', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Top', 'the-post-grid' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'condition'    => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'default',
				],
				'prefix_class' => 'tpg-filter-alignment-',
				'toggle'       => true,
			]
		);

		$ref->add_responsive_control(
			'filter_button_width',
			[
				'label'      => __( 'Filter Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-header-wrapper.carousel .rt-layout-filter-container' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'carousel',
				],
			]
		);


		$ref->add_control(
			'border_style',
			[
				'label'        => __( 'Filter Border', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'disable',
				'options'      => [
					'disable' => __( 'Disable', 'the-post-grid' ),
					'enable'  => __( 'Enable', 'the-post-grid' ),
				],
				'condition'    => [
					'filter_type'          => 'button',
					'filter_btn_style'     => 'carousel',
					'section_title_style!' => [ 'style2', 'style3' ],
				],
				'prefix_class' => 'filter-button-border-',
			]
		);

		$ref->add_control(
			'filter_next_prev_btn',
			[
				'label'        => __( 'Next/Prev Button', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'visible',
				'options'      => [
					'visible' => __( 'Visible', 'the-post-grid' ),
					'hidden'  => __( 'Hidden', 'the-post-grid' ),
				],
				'condition'    => [
					'filter_type'      => 'button',
					'filter_btn_style' => 'carousel',
				],
				'prefix_class' => 'filter-nex-prev-btn-',
			]
		);

		$ref->add_control(
			'filter_h_alignment',
			[
				'label'        => esc_html__( 'Vertical Alignment', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'          => [
						'title' => esc_html__( 'Top', 'the-post-grid' ),
						'icon'  => 'eicon-justify-start-h',
					],
					'center'        => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-justify-center-h',
					],
					'right'         => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html__( 'Space Between', 'the-post-grid' ),
						'icon'  => 'eicon-justify-space-between-h',
					],
				],
				'condition'    => [
					'filter_type!' => 'button',
				],
				'prefix_class' => 'tpg-filter-h-alignment-',
				'toggle'       => true,
			]
		);

		$ref->add_responsive_control(
			'filter_btn_radius',
			[
				'label'      => __( 'Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap'      => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input'      => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'filter_btn_style' => 'default',
				],
			]
		);


		//TODO: Start Tab
		$ref->start_controls_tabs(
			'frontend_filter_style_tabs'
		);

		$ref->start_controls_tab(
			'frontend_filter_style_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'filter_color',
			[
				'label'     => __( 'Filter Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item'                            => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap'                                                                                            => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action .rt-sort-order-action-arrow > span:before, {{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action .rt-sort-order-action-arrow > span:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input'                                                                                                                    => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_bg_color',
			[
				'label'     => __( 'Filter Background Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item'                    => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action'    => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_border_color',
			[
				'label'     => __( 'Filter Border Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item'                    => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action'    => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input'                         => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.filter-button-border-enable .tpg-header-wrapper.carousel .rt-layout-filter-container'     => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_search_bg',
			[
				'label'     => __( 'Search Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_search'      => 'show',
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color_heading',
			[
				'label'     => __( 'Sub Menu Options', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_bg_color',
			[
				'label'     => __( 'Submenu Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color',
			[
				'label'     => __( 'Submenu Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_border_bottom',
			[
				'label'     => __( 'Submenu Border', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'filter_nav_color',
			[
				'label'     => __( 'Filter Nav Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_bg',
			[
				'label'     => __( 'Filter Nav Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_border',
			[
				'label'     => __( 'Filter Nav Border', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->end_controls_tab();

		//TODO: Start Tab Hover
		$ref->start_controls_tab(
			'frontend_filter_style_hover_tab',
			[
				'label' => __( 'Hover / Active', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'filter_color_hover',
			[
				'label'     => __( 'Filter Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover'                         => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover'                                                                                                  => 'color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action:hover .rt-sort-order-action-arrow > span:before, {{WRAPPER}} .rt-filter-item-wrap.rt-sort-order-action:hover .rt-sort-order-action-arrow > span:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_bg_color_hover',
			[
				'label'     => __( 'Filter Background Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover'                                                                          => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover'                                                                             => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_border_color_hover',
			[
				'label'     => __( 'Filter Border Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item.selected, {{WRAPPER}} .rt-filter-item-wrap.rt-filter-button-wrap span.rt-filter-button-item:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap:hover'                                                                          => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-sort-order-action:hover'                                                                             => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input:hover'                                                                                                  => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.filter-button-border-enable .tpg-header-wrapper.carousel .rt-layout-filter-container:hover'                                                                              => 'border-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'filter_search_bg_hover',
			[
				'label'     => __( 'Search Background - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-filter-item-wrap.rt-search-filter-wrap input.rt-search-input:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_search'      => 'show',
					'filter_btn_style' => 'default',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color_heading_hover',
			[
				'label'     => __( 'Sub Menu Options - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_bg_color_hover',
			[
				'label'     => __( 'Submenu Background - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_color_hover',
			[
				'label'     => __( 'Submenu Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'sub_menu_border_bottom_hover',
			[
				'label'     => __( 'Submenu Border - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-layout-filter-container .rt-filter-wrap .rt-filter-item-wrap.rt-filter-dropdown-wrap .rt-filter-dropdown .rt-filter-dropdown-item:hover' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'filter_type' => 'dropdown',
				],
			]
		);

		$ref->add_control(
			'filter_nav_color_hover',
			[
				'label'     => __( 'Filter Nav Color - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_bg_hover',
			[
				'label'     => __( 'Filter Nav Background - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);

		$ref->add_control(
			'filter_nav_border_hover',
			[
				'label'     => __( 'Filter Nav Border - Hover', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'filter_btn_style'     => 'carousel',
					'filter_next_prev_btn' => 'visible',
				],
			]
		);


		$ref->end_controls_tab();

		$ref->end_controls_tabs();
		//TODO: End Tab


		$ref->end_controls_section();
	}


	/**
	 * Social Share control
	 *
	 * @param $ref
	 */
	public static function socialShareStyle( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'social_share_style',
			[
				'label'     => esc_html__( 'Social Share Style', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_share'  => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		self::get_social_share_control( $ref );

		$ref->end_controls_section();
	}

	/**
	 * Get Social Share
	 *
	 * @param $ref
	 * @param $prefix
	 */
	public static function get_social_share_control( $ref ) {
		$ref->add_control(
			'social_icon_style',
			[
				'label'       => __( 'Icon Color Style', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default'         => __( 'Default (Brand Color)', 'the-post-grid' ),
					'different_color' => __( 'Different Color for each', 'the-post-grid' ),
					'custom'          => __( 'Custom color', 'the-post-grid' ),
				],
				'description' => __( 'Select Custom for your own customize', 'the-post-grid' ),
			]
		);

		$settings = get_option( rtTPG()->options['settings'] );
		$ssList   = ! empty( $settings['social_share_items'] ) ? $settings['social_share_items'] : [];

		$ref->add_responsive_control(
			'social_icon_margin',
			[
				'label'              => __( 'Icon Margin', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-social-share a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'social_wrapper_margin',
			[
				'label'              => __( 'Icon Wrapper Spacing', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'vertical', //horizontal, vertical, [ 'top', 'right', 'bottom', 'left' ]
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-social-share' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_responsive_control(
			'social_icon_radius',
			[
				'label'              => __( 'Border Radius', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'all',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => true,
				],
				'selectors'          => [
					'{{WRAPPER}} .rt-tpg-social-share i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$ref->add_control(
			'icon_width_height',
			[
				'label'       => __( 'Icon Dimension', 'the-post-grid' ),
				'type'        => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'default'     => [
					'width'  => '',
					'height' => '',
				],
				'selectors'   => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'width:{{width}}px; height:{{height}}px; line-height:{{height}}px; text-align:center',
				],
				'description' => __( 'Just write number. Don\'t use (px or em).', 'the-post-grid' ),
				'classes'     => 'should-show-title',
			]
		);

		$ref->add_responsive_control(
			'icon_font_size',
			[
				'label'      => __( 'Icon Font Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 12,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		//TODO: Start Social Share Tabs Tab
		$ref->start_controls_tabs(
			'social_share_style_tabs'
		);

		$ref->start_controls_tab(
			'social_share_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);
		//TODO: Normal Tab


		$ref->add_control(
			'social_icon_color',
			[
				'label'     => __( 'Social Icon color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'social_icon_style' => 'custom',
				],
			]
		);

		$ref->add_control(
			'social_icon_bg_color',
			[
				'label'     => __( 'Social Icon Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'social_icon_style' => 'custom',
				],
			]
		);


		foreach ( $ssList as $ss ) {
			$ref->add_control(
				$ss . '_social_icon_color',
				[
					'label'     => ucwords( $ss ) . __( ' color', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ' i' => 'color: {{VALUE}}',
					],
					'condition' => [
						'social_icon_style' => 'different_color',
					],
				]
			);

			$ref->add_control(
				$ss . '_social_icon_bg_color',
				[
					'label'     => __( ucwords( $ss ) . ' Background', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ' i' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'social_icon_style' => 'different_color',
					],
				]
			);
		}


		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'social_icon_border',
				'label'    => __( 'Icon Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-social-share a i',
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'socia_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		//TODO: Hover Tab

		$ref->add_control(
			'social_icon_color_hover',
			[
				'label'     => __( 'Icon color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'social_icon_style' => 'custom',
				],
			]
		);

		$ref->add_control(
			'social_icon_bg_color_hover',
			[
				'label'     => __( 'Social Icon Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-social-share a:hover i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'social_icon_style' => 'custom',
				],
			]
		);

		foreach ( $ssList as $ss ) {
			$ref->add_control(
				$ss . '_social_icon_color_hover',
				[
					'label'     => ucwords( $ss ) . __( ' color - Hover', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ':hover i' => 'color: {{VALUE}}',
					],
					'condition' => [
						'social_icon_style' => 'different_color',
					],
				]
			);

			$ref->add_control(
				$ss . '_social_icon_bg_color_hover',
				[
					'label'     => __( ucwords( $ss ) . ' Background - Hover', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-social-share a.' . $ss . ':hover i' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'social_icon_style' => 'different_color',
					],
				]
			);
		}

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'social_icon_border_hover',
				'label'    => __( 'Icon Border - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-social-share a:hover i',
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();
	}

	/**
	 * Box Settings
	 *
	 * @param $ref
	 */
	public static function articlBoxSettings( $ref ) {
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'article_box_settings',
			[
				'label' => esc_html__( 'Card', 'the-post-grid' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		if ( 'slider' !== $prefix ) {
			$ref->add_responsive_control(
				'box_margin',
				[
					'label'       => __( 'Card Gap', 'the-post-grid' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => [ 'px' ],
					'render_type' => 'template',
					'selectors'   => [
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-row [class*="rt-col"]'              => 'padding-left: {{LEFT}}{{UNIT}} !important; padding-right: {{RIGHT}}{{UNIT}} !important; padding-bottom: calc(2 * {{BOTTOM}}{{UNIT}}) !important;',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-row'                                => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}}',
						'{{WRAPPER}} .tpg-el-main-wrapper .rt-row .rt-row'                        => 'margin-bottom: -{{RIGHT}}{{UNIT}}',
						'{{WRAPPER}} .rt-tpg-container .grid_hover-layout8 .display-grid-wrapper' => 'grid-gap: {{TOP}}{{UNIT}};margin-bottom: {{TOP}}{{UNIT}}',
					],
					'condition'   => [
						$prefix . '_layout!' => [
							'grid-layout2',
							'grid-layout5',
							'grid-layout5-2',
							'grid-layout6',
							'grid-layout6-2',
							'list-layout4',
						],
					],
				]
			);
		}


		if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
			$ref->add_responsive_control(
				'content_box_padding',
				[
					'label'              => __( 'Content Padding', 'the-post-grid' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => [ 'px' ],
					'allowed_dimensions' => 'all',
					'selectors'          => [
						'body {{WRAPPER}} .rt-tpg-container .rt-el-content-wrapper'                                  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						'body {{WRAPPER}} .rt-tpg-container .rt-el-content-wrapper-flex .post-right-content'         => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						'body {{WRAPPER}} .tpg-el-main-wrapper .rt-holder .rt-el-content-wrapper .tpg-el-image-wrap' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}};',
					],
					'condition'          => [
						$prefix . '_layout!' => [
							'grid-layout5',
							'grid-layout5-2',
							'grid-layout6',
							'grid-layout6-2',
							'grid-layout7',
							'list-layout1',
							'list-layout2',
							'list-layout2-2',
							'list-layout3',
							'list-layout3-2',
							'list-layout4',
							'list-layout5',
						],
					],
				]
			);


			$ref->add_responsive_control(
				'content_box_padding_offset',
				[
					'label'              => __( 'Content Padding', 'the-post-grid' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => [ 'px' ],
					'allowed_dimensions' => 'all',
					'selectors'          => [
						'body {{WRAPPER}} .tpg-el-main-wrapper .offset-left .tpg-post-holder .offset-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						'{{WRAPPER}} .rt-tpg-container .list-layout4 .post-right-content'                     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					],
					'condition'          => [
						$prefix . '_layout' => [
							'grid-layout5',
							'grid-layout5-2',
							'list-layout4',
						],
					],
				]
			);
		}

		$ref->add_responsive_control(
			'content_box_padding_2',
			[
				'label'              => __( 'Content Padding', 'the-post-grid' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'all',
				'selectors'          => [
					'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition'          => [
					$prefix . '_layout' => [ 'slider-layout13' ],
				],
			]
		);

		$ref->add_responsive_control(
			'box_radius',
			[
				'label'      => __( 'Card Border Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder'                       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
					'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',
				],
				'condition'  => [
					$prefix . '_layout!' => [
						'list-layout2',
						'list-layout2-2',
						'list-layout3',
						'list-layout3-2',
						'list-layout4',
						'list-layout4-2',
						'list-layout5',
						'list-layout5-2',
						'slider-layout11',
						'slider-layout12',
						'slider-layout13',
					],
				],
			]
		);

		if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
			$ref->add_control(
				'is_box_border',
				[
					'label'        => __( 'Enable Border & Box Shadow', 'the-post-grid' ),
					'type'         => \Elementor\Controls_Manager::SELECT,
					'default'      => 'enable',
					'options'      => [
						'enable'  => __( 'Enable', 'the-post-grid' ),
						'disable' => __( 'Disable', 'the-post-grid' ),
					],
					'prefix_class' => 'tpg-el-box-border-',
					'condition'    => [
						$prefix . '_layout!' => [
							'slider-layout11',
							'slider-layout12',
							'slider-layout13',
						],
					],
				]
			);
		}

		if ( 'grid_hover' !== $prefix ) {
			//TODO: Start Tab
			$ref->start_controls_tabs(
				'box_style_tabs'
			);

			//TODO: Normal Tab
			$ref->start_controls_tab(
				'box_style_normal_tab',
				[
					'label' => __( 'Normal', 'the-post-grid' ),
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background',
					'label'          => __( 'Background', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder',
					'condition'      => [
						$prefix . '_layout!' => [ 'slider-layout13' ],
					],
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background2',
					'label'          => __( 'Background', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition'      => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);

			if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
				$ref->add_control(
					'box_border',
					[
						'label'     => __( 'Border Color', 'the-post-grid' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder' => 'border: 1px solid {{VALUE}}',
						],
						'condition' => [
							'is_box_border'      => 'enable',
							$prefix . '_layout!' => [
								'slider-layout11',
								'slider-layout12',
								'slider-layout13',
							],
						],
					]
				);


				$ref->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'box_box_shadow',
						'label'     => __( 'Box Shadow', 'the-post-grid' ),
						'selector'  => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder',
						'condition' => [
							'is_box_border'      => 'enable',
							$prefix . '_layout!' => [
								'slider-layout11',
								'slider-layout12',
								'slider-layout13',
							],
						],
					]
				);
			}

			$ref->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'box_box_shadow2',
					'label'     => __( 'Box Shadow', 'the-post-grid' ),
					'selector'  => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition' => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);


			$ref->end_controls_tab();


			//TODO: Hover Tab
			$ref->start_controls_tab(
				'box_style_hover_tab',
				[
					'label' => __( 'Hover', 'the-post-grid' ),
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background_hover',
					'label'          => __( 'Background - Hover', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background - Hover', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover',
					'condition'      => [
						$prefix . '_layout!' => [ 'slider-layout13' ],
					],
				]
			);

			$ref->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name'           => 'box_background_hover2',
					'label'          => __( 'Background - Hover', 'the-post-grid' ),
					'fields_options' => [
						'background' => [
							'label' => esc_html__( 'Card Background - Hover', 'the-post-grid' ),
						],
					],
					'types'          => [ 'classic', 'gradient' ],
					'selector'       => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition'      => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);

			if ( in_array( $prefix, [ 'grid', 'list' ] ) ) {
				$ref->add_control(
					'box_border_hover',
					[
						'label'     => __( 'Border Color - Hover', 'the-post-grid' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover' => 'border: 1px solid {{VALUE}}',
						],
						'condition' => [
							'is_box_border'      => 'enable',
							$prefix . '_layout!' => [
								'slider-layout11',
								'slider-layout12',
								'slider-layout13',
							],
						],
					]
				);

				$ref->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'      => 'box_box_shadow_hover',
						'label'     => __( 'Box Shadow - Hover', 'the-post-grid' ),
						'selector'  => 'body {{WRAPPER}} .tpg-el-main-wrapper .tpg-post-holder:hover',
						'condition' => [
							'is_box_border'      => 'enable',
							$prefix . '_layout!' => [
								'slider-layout11',
								'slider-layout12',
								'slider-layout13',
							],
						],
					]
				);
			}


			$ref->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'box_box_shadow_hover2',
					'label'     => __( 'Box Shadow - Hover', 'the-post-grid' ),
					'selector'  => 'body {{WRAPPER}} .rt-tpg-container .slider-layout13 .rt-holder .post-content',
					'condition' => [
						$prefix . '_layout' => [ 'slider-layout13' ],
					],
				]
			);


			$ref->end_controls_tab();

			$ref->end_controls_tabs();
			//TODO: End Tab

		}

		$ref->end_controls_section();
	}


	/**
	 * Slider Settings
	 *
	 * @param $ref
	 */

	public static function slider_settings( $ref, $layout_type = '' ) {
		$slider_condition = '';
		if ( 'single' === $layout_type ) {
			$slider_condition = [
				'enable_related_slider!' => '',
			];
		}
		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'slider_settings',
			[
				'label'     => esc_html__( 'Slider', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => $slider_condition,
			]
		);

		$ref->add_responsive_control(
			'slider_gap',
			[
				'label'      => __( 'Slider Gap', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'body {{WRAPPER}} .tpg-el-main-wrapper .rt-slider-item'                     => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
					'body {{WRAPPER}} .tpg-el-main-wrapper .rt-swiper-holder'                   => 'margin-left: calc(-{{SIZE}}{{UNIT}} - 5px);margin-right: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .slider-column.swiper-slide .rt-slider-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					$prefix . '_layout!' => [ 'slider-layout10', 'slider-layout11', 'slider-layout12', 'slider-layout13' ],
				],
			]
		);


		$ref->add_control(
			'arrows',
			[
				'label'        => __( 'Arrow', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					$prefix . '_layout!' => [ 'slider-layout13', 'slider-layout11', 'slider-layout12' ],
				],
			]
		);


		$ref->add_control(
			'arrow_position',
			[
				'label'        => __( 'Arrow Position', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'    => __( 'Default', 'the-post-grid' ),
					'top-right'  => __( 'Top Right', 'the-post-grid' ),
					'top-left'   => __( 'Top Left', 'the-post-grid' ),
					'show-hover' => __( 'Center (Show on hover)', 'the-post-grid' ),
				],
				'condition'    => [
					'arrows'             => 'yes',
					$prefix . '_layout!' => [ 'slider-layout13', 'slider-layout11', 'slider-layout12' ],
				],
				'prefix_class' => 'slider-arrow-position-',
			]
		);

		$ref->add_control(
			'dots',
			[
				'label'        => __( 'Dots', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'slider-dot-enable-',
				'render_type'  => 'template',
				'condition'    => [
					$prefix . '_layout!' => [ 'slider-layout13', 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		$ref->add_control(
			'dots_style',
			[
				'label'        => __( 'Dots Style', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default'    => __( 'Default', 'the-post-grid' ),
					'background' => __( 'With Background', 'the-post-grid' ),
				],
				'condition'    => [
					'dots'               => 'yes',
					$prefix . '_layout!' => [ 'slider-layout13', 'slider-layout11', 'slider-layout12' ],
				],
				'prefix_class' => 'slider-dots-style-',
			]
		);

		$ref->add_control(
			'infinite',
			[
				'label'        => __( 'Infinite', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
			]
		);

		$ref->add_control(
			'stopOnHover',
			[
				'label'        => __( 'Stop On Hover', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$ref->add_control(
			'autoplaySpeed',
			[
				'label'     => __( 'Autoplay Speed', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1000,
				'max'       => 10000,
				'step'      => 500,
				'default'   => 3000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$ref->add_control(
			'autoHeight',
			[
				'label'        => __( 'Auto Height', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'condition'    => [
					'enable_2_rows!' => 'yes',
				],
			]
		);

		$ref->add_control(
			'lazyLoad',
			[
				'label'        => __( 'lazy Load', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the-post-grid' ),
				'label_off'    => __( 'No', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'prefix_class' => 'is-lazy-load-',
			]
		);

		$ref->add_control(
			'speed',
			[
				'label'   => __( 'Speed', 'the-post-grid' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 100,
				'default' => 500,
			]
		);

		$ref->add_control(
			'enable_2_rows',
			[
				'label'        => __( 'Enable 2 Rows', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the-post-grid' ),
				'label_off'    => __( 'Hide', 'the-post-grid' ),
				'return_value' => 'yes',
				'default'      => false,
				'prefix_class' => 'enable-two-rows-',
				'render_type'  => 'template',
				'description'  => __( 'If you use 2 rows then you have to put an even number for post limit' ),
				'condition'    => [
					$prefix . '_layout!' => [ 'slider-layout13', 'slider-layout11', 'slider-layout12', 'slider-layout10' ],
				],
			]
		);

		$ref->add_control(
			'carousel_overflow',
			[
				'label'        => __( 'Slider Overflow', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'hidden',
				'options'      => [
					'hidden' => __( 'Hidden', 'the-post-grid' ),
					'none'   => __( 'None', 'the-post-grid' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'is-carousel-overflow-',
				'condition'    => [
					'lazyLoad!' => 'yes',
				],
			]
		);

		$ref->add_control(
			'slider_direction',
			[
				'label'        => __( 'Direction', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'ltr',
				'options'      => [
					'ltr' => __( 'LTR', 'the-post-grid' ),
					'rtl' => __( 'RTL', 'the-post-grid' ),
				],
				'prefix_class' => 'slider-direction-',
				'render_type'  => 'template',
			]
		);

		$ref->end_controls_section();
	}

	/**
	 * Slider Style
	 *
	 * @param $ref
	 */

	public static function slider_style( $ref, $layout_type = '' ) {
		$prefix = $ref->prefix;
		if ( 'single' === $layout_type ) {
			$slider_condition = [
				'enable_related_slider!' => '',
				$prefix . '_layout!'     => [ 'slider-layout11', 'slider-layout12' ],
			];
		} else {
			$slider_condition = [
				$prefix . '_layout!' => [ 'slider-layout11', 'slider-layout12' ],
			];
		}

		$ref->start_controls_section(
			'slider_style',
			[
				'label'     => esc_html__( 'Slider', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $slider_condition,
			]
		);

		$ref->add_control(
			'arrow_style_heading',
			[
				'label'     => __( 'Arrow Style', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_font_size',
			[
				'label'      => __( 'Arrow Font Size', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Arrow Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_width',
			[
				'label'      => __( 'Arrow Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_height',
			[
				'label'      => __( 'Arrow Height', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'height: {{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_x_position',
			[
				'label'      => __( 'Arrow X Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 300,
						'max'  => 300,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn.swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn.swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.slider-arrow-position-top-right .swiper-navigation' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.slider-arrow-position-top-left .swiper-navigation' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows'         => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'arrow_y_position',
			[
				'label'      => __( 'Arrow Y Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => - 150,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn'                                                                  => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.slider-arrow-position-top-right .swiper-navigation, {{WRAPPER}}.slider-arrow-position-top-left .swiper-navigation' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'arrows' => 'yes',
				],
			]
		);

		//TODO: Arrow Tabs Start
		$ref->start_controls_tabs(
			'arrow_style_tabs',
			[
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);

		$ref->start_controls_tab(
			'arrow_style_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'arrow_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Icon Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'arrow_arrow_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrow_box_shadow',
				'label'    => __( 'Box Shadow', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn',
			]
		);

		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'arrow_style_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'arrow_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Icon Color - Hover', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'arrow_bg_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Arrow Background - Hover', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrow_box_shadow_hover',
				'label'    => __( 'Box Shadow - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'border_hover',
				'label'    => esc_html__( 'Border - Hover', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .swiper-navigation .slider-btn:hover',
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();
		//TODO: Arrow Tabs End


		//TODO: Dots style Start

		$ref->add_control(
			'dot_style_heading',
			[
				'label'     => __( 'Dots Style', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'classes'   => 'tpg-control-type-heading',
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_control(
			'dots_text_align',
			[
				'label'        => __( 'Dots Alignment', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'       => true,
				'condition'    => [
					'dots' => 'yes',
				],
				'prefix_class' => 'slider-dots-align-',
			]
		);


		$ref->add_responsive_control(
			'dot_wrapper_radius',
			[
				'label'      => __( 'Dots Wrapper Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.slider-dots-style-background .tpg-el-main-wrapper .swiper-pagination' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'dots_style' => 'background',
					'dots'       => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_border_radius',
			[
				'label'      => __( 'Dots Radius', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_width',
			[
				'label'      => __( 'Dots Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet'                                 => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'width: calc({{SIZE}}{{UNIT}} + 15px);',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_height',
			[
				'label'      => __( 'Dots Height', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);


		$ref->add_responsive_control(
			'dots_margin',
			[
				'label'      => __( 'Dots Margin', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_responsive_control(
			'dots_position',
			[
				'label'      => __( 'Dots Y Position', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 150,
						'max'  => 150,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}} !important ;',
				],
				'condition'  => [
					'dots' => 'yes',
				],
			]
		);


		//TODO: Dots Tab Start
		$ref->start_controls_tabs(
			'dots_style_tabs',
			[
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->start_controls_tab(
			'dots_style_normal_tab',
			[
				'label' => __( 'Normal', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'dots_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Dots Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_control(
			'dots_border_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Dots Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->add_control(
			'dots_wrap_bg',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Dots Wrapper Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}}.slider-dots-style-background .tpg-el-main-wrapper .swiper-pagination' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots'       => 'yes',
					'dots_style' => 'background',
				],
			]
		);


		$ref->end_controls_tab();

		$ref->start_controls_tab(
			'dots_style_hover_tab',
			[
				'label' => __( 'Hover', 'the-post-grid' ),
			]
		);

		$ref->add_control(
			'dots_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Dots Color - Hover', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$ref->end_controls_tab();

		$ref->end_controls_tabs();

		$ref->end_controls_section();
	}


	/**
	 *  Link Style
	 *
	 * @param $ref
	 */

	public static function linkStyle( $ref ) {
		$ref->start_controls_section(
			'linkStyle',
			[
				'label'     => esc_html__( 'Link Style', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_link_type' => [ 'popup', 'multi_popup' ],
				],
			]
		);

		$ref->add_control(
			'popup_head_bg',
			[
				'label'     => __( 'Header Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body .rt-popup-wrap .rt-popup-navigation-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'popup_head_txt_color',
			[
				'label'     => __( 'Header Text Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body #rt-popup-wrap .rt-popup-singlePage-counter' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'popup_title_color',
			[
				'label'     => __( 'Popup Title Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content .rt-md-content-holder > .md-header .entry-title' => 'color: {{VALUE}}',
					'body .rt-popup-content .rt-tpg-container h1.entry-title'          => 'color: {{VALUE}}',
				],

			]
		);


		$ref->add_control(
			'popup_meta_color',
			[
				'label'     => __( 'Popup Meta Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content .rt-md-content-holder > .md-header .post-meta-user *' => 'color: {{VALUE}}',
					'body .rt-popup-content .rt-tpg-container .post-meta-user *'            => 'color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'popup_content_color',
			[
				'label'     => __( 'Popup Content Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content .rt-md-content *'                       => 'color: {{VALUE}}',
					'body .rt-popup-content .rt-tpg-container .tpg-content *' => 'color: {{VALUE}}',
				],

			]
		);

		$ref->add_control(
			'popup_bg',
			[
				'label'     => __( 'Popup Background', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'body .md-content, body #rt-popup-wrap .rt-popup-content' => 'background-color: {{VALUE}}',
				],

			]
		);


		$ref->end_controls_section();
	}


	/**
	 *  Slider thumb Settings for layout- 11, 12
	 *
	 * @param $ref
	 */

	public static function slider_thumb_style( $ref ) {
		$ref->start_controls_section(
			'slider_thumb_style',
			[
				'label'     => esc_html__( 'Slider', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_layout' => [ 'slider-layout11', 'slider-layout12' ],
				],
			]
		);

		//TODO: Crative slider style:
		$ref->add_control(
			'scroll_bar_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Scroll Foreground Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .swiper-thumb-pagination .swiper-pagination-progressbar-fill'                                                => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper .post-thumbnail-wrap .p-thumbnail::before' => 'background-color: {{VALUE}}',
				],
			]
		);


		$ref->add_control(
			'scroll_bar_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Scroll Background Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-thumb-main-wrapper .swiper-pagination-progressbar'                 => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper::before' => 'background-color: {{VALUE}};opacity:1;',
				],
			]
		);

		$ref->add_control(
			'thumb_font_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Thumb Font Color', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .p-content *' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .swiper-thumb-wrapper .swiper-wrapper .p-content *' => 'color: {{VALUE}}',
				],
			]
		);

		$ref->add_control(
			'slider_thumb_bg',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Thumb Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .p-thumbnail img' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'slider_layout' => [ 'slider-layout11' ],
				],
			]
		);
		$ref->add_control(
			'slider_thumb_bg_active',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Thumb Active/Hover Background', 'the-post-grid' ),
				'selectors' => [
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .swiper-slide:hover .p-thumbnail img'         => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .swiper-slide-thumb-active .p-thumbnail img'  => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout11 .swiper-thumb-wrapper .swiper-wrapper .post-thumbnail-wrap::before'                 => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper .swiper-thumb-wrapper .post-thumbnail-wrap .p-thumbnail' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'thumb_wrapper_bg',
				'label'          => __( 'Thumb Wrapper Background', 'the-post-grid' ),
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Thumb Wrapper Background', 'the-post-grid' ),
					],
				],
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .tpg-el-main-wrapper .slider-thumb-main-wrapper, {{WRAPPER}} .tpg-el-main-wrapper .slider-layout12 .slider-thumb-main-wrapper',
				'exclude'        => [ 'image' ],
			]
		);

		$ref->end_controls_section();
	}


	/**
	 * Advanced Custom Field ACF Style
	 *
	 * @param $ref
	 */

	public static function tpg_acf_style( $ref ) {
		$cf = Fns::checkWhichCustomMetaPluginIsInstalled();
		if ( ! $cf || ! rtTPG()->hasPro() ) {
			return;
		}

		$prefix = $ref->prefix;
		$ref->start_controls_section(
			'tgp_acf_style',
			[
				'label'     => esc_html__( 'Advanced Custom Field', 'the-post-grid' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_acf'           => 'show',
					$prefix . '_layout!' => [ 'grid-layout7' ],
				],
			]
		);

		self::get_tpg_acf_style( $ref );

		$ref->end_controls_section();
	}

	public static function get_tpg_acf_style( $ref, $hover_control = true ) {
		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'acf_group_title_typography',
				'label'    => __( 'Group Title Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-cf-group-title',
			]
		);

		$ref->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'acf_typography',
				'label'    => __( 'Group Title Typography', 'the-post-grid' ),
				'selector' => '{{WRAPPER}} .rt-tpg-container .tpg-cf-fields',
			]
		);

		$ref->add_control(
			'acf_label_style',
			[
				'label'        => __( 'Label Style', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'inline',
				'options'      => [
					'default' => __( 'Default', 'the-post-grid' ),
					'inline'  => __( 'Inline', 'the-post-grid' ),
					'block'   => __( 'Block', 'the-post-grid' ),
				],
				'condition'    => [
					'cf_show_only_value' => 'yes',
				],
				'render_type'  => 'template',
				'prefix_class' => 'act-label-style-',
			]
		);

		$ref->add_responsive_control(
			'acf_label_width',
			[
				'label'      => __( 'Label Min Width', 'the-post-grid' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'condition'  => [
					'acf_label_style' => 'default',
				],
				'selectors'  => [
					'{{WRAPPER}} .tgp-cf-field-label' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$ref->add_control(
			'acf_alignment',
			[
				'label'        => esc_html__( 'Text Align', 'the-post-grid' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'tpg-acf-align-',
				'toggle'       => true,
				'condition'    => [
					'grid_layout!' => [ 'grid-layout7' ],
				],

			]
		);


		if ( $hover_control ) {
			//Start Tab
			$ref->start_controls_tabs(
				'acf_style_tabs'
			);

			//Normal Tab
			$ref->start_controls_tab(
				'acf_style_normal_tab',
				[
					'label' => __( 'Normal', 'the-post-grid' ),
				]
			);
		}
		$ref->add_control(
			'acf_group_title_color',
			[
				'label'     => __( 'Group Title Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acf-custom-field-wrap .tpg-cf-group-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'cf_hide_group_title' => 'yes',
				],
			]
		);

		$ref->add_control(
			'acf_label_color',
			[
				'label'     => __( 'Label Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acf-custom-field-wrap .tgp-cf-field-label' => 'color: {{VALUE}}',
				],
				'condition' => [
					'cf_show_only_value' => 'yes',
				],
			]
		);

		$ref->add_control(
			'acf_value_color',
			[
				'label'     => __( 'Value Color', 'the-post-grid' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acf-custom-field-wrap .tgp-cf-field-value' => 'color: {{VALUE}}',
				],
			]
		);

		if ( $hover_control ) {
			$ref->end_controls_tab();


			//Hover Tab
			$ref->start_controls_tab(
				'acf_style_hover_tab',
				[
					'label' => __( 'Hover', 'the-post-grid' ),
				]
			);

			$ref->add_control(
				'acf_group_title_color_hover',
				[
					'label'     => __( 'Group Title Color - Hover', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .tpg-cf-group-title' => 'color: {{VALUE}}',
					],
					'condition' => [
						'cf_hide_group_title' => 'yes',
					],
				]
			);

			$ref->add_control(
				'acf_label_color_hover',
				[
					'label'     => __( 'Label Color - Hover', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .tgp-cf-field-label' => 'color: {{VALUE}}',
					],
					'condition' => [
						'cf_show_only_value' => 'yes',
					],
				]
			);

			$ref->add_control(
				'acf_value_color_hover',
				[
					'label'     => __( 'Value Color - Hover', 'the-post-grid' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rt-tpg-container .rt-holder:hover .tgp-cf-field-value' => 'color: {{VALUE}}',
					],
				]
			);

			$ref->end_controls_tab();

			$ref->end_controls_tabs();
			//End Tab
		}
	}


	//End the class
}