<?php


namespace RT\ThePostGrid\Controllers\Admin;


class PostTypeController {
	private $version;

	public function __construct() {
		$this->version = defined('WP_DEBUG') && WP_DEBUG ? time() : RT_THE_POST_GRID_VERSION;
		add_action( 'init', [ &$this, 'register_post_types' ], 1 );
		add_action( 'admin_init', [ &$this, 'the_post_grid_remove_all_meta_box' ], 9999 );
	}

	public function the_post_grid_remove_all_meta_box() {
		if ( is_admin() && apply_filters( 'rttpg_remove_all_extra_metabox_from_shordcode', true ) ) {
			add_filter( "get_user_option_meta-box-order_" . rtTPG()->post_type, [
				&$this,
				'remove_all_meta_boxes_tgp_sc'
			] );
		}
	}

	public function register_post_types() {
		// Create the post grid post type
		$labels = array(
			'name'               => __( 'The Post Grid', 'the-post-grid' ),
			'singular_name'      => __( 'The Post Grid', 'the-post-grid' ),
			'add_new'            => __( 'Add New Grid', 'the-post-grid' ),
			'all_items'          => __( 'All Grids', 'the-post-grid' ),
			'add_new_item'       => __( 'Add New Post Grid', 'the-post-grid' ),
			'edit_item'          => __( 'Edit Post Grid', 'the-post-grid' ),
			'new_item'           => __( 'New Post Grid', 'the-post-grid' ),
			'view_item'          => __( 'View Post Grid', 'the-post-grid' ),
			'search_items'       => __( 'Search Post Grids', 'the-post-grid' ),
			'not_found'          => __( 'No Post Grids found', 'the-post-grid' ),
			'not_found_in_trash' => __( 'No Post Grids found in Trash', 'the-post-grid' ),
		);

		register_post_type( rtTPG()->post_type, array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'_builtin'        => false,
			'capability_type' => 'page',
			'hierarchical'    => true,
			'menu_icon'       => rtTPG()->get_assets_uri('images/icon-16x16.png'),
			'rewrite'         => false,
			'query_var'       => rtTPG()->post_type,
			'supports'        => array(
				'title',
			),
			'show_in_menu'    => true,
			'menu_position'   => 20,
		) );

		// register scripts
		$scripts   = array();
		$styles    = array();
		$scripts[] = array(
			'handle' => 'rt-image-load-js',
			'src'    => rtTPG()->get_assets_uri("vendor/isotope/imagesloaded.pkgd.min.js"),
			'deps'   => array( 'jquery' ),
			'footer' => true
		);
		$scripts[] = array(
			'handle' => 'rt-isotope-js',
			'src'    => rtTPG()->get_assets_uri("vendor/isotope/isotope.pkgd.min.js"),
			'deps'   => array( 'jquery' ),
			'footer' => true
		);

		$scripts[] = array(
			'handle' => 'rt-tpg',
			'src'    => rtTPG()->get_assets_uri('js/rttpg.js'),
			'deps'   => array( 'jquery' ),
			'footer' => true
		);

		// register acf styles
		$styles['rt-fontawsome'] = rtTPG()->get_assets_uri('vendor/font-awesome/css/font-awesome.min.css');
		$styles['rt-tpg']        = rtTPG()->get_assets_uri('css/thepostgrid.css');
		$styles['rt-tpg-rtl']    = rtTPG()->get_assets_uri('css/thepostgrid-rtl.css');

		if ( is_admin() ) {

			$scripts[]                      = array(
				'handle' => 'rt-select2',
				'src'    => rtTPG()->get_assets_uri('vendor/select2/select2.min.js'),
				'deps'   => array( 'jquery' ),
				'footer' => false
			);
			$scripts[]                      = array(
				'handle' => 'rt-tpg-admin',
				'src'    => rtTPG()->get_assets_uri('js/admin.js'),
				'deps'   => array( 'jquery' ),
				'footer' => true
			);
			$scripts[]                      = array(
				'handle' => 'rt-tpg-admin-preview',
				'src'    => rtTPG()->get_assets_uri('js/admin-preview.js'),
				'deps'   => array( 'jquery' ),
				'footer' => true
			);
			$styles['rt-select2']           = rtTPG()->get_assets_uri('vendor/select2/select2.min.css');
			$styles['rt-tpg-admin']         = rtTPG()->get_assets_uri('css/admin.css');
			$styles['rt-tpg-admin-preview'] = rtTPG()->get_assets_uri('css/admin-preview.css');
		}

		foreach ( $scripts as $script ) {
			wp_register_script( $script['handle'], $script['src'], $script['deps'], isset( $script['version'] ) ? $script['version'] : $this->version, $script['footer'] );
		}

		foreach ( $styles as $k => $v ) {
			wp_register_style( $k, $v, false, isset( $script['version'] ) ? $script['version'] : $this->version );
		}
	}


	/**
	 * @return void|array
	 */
	public function remove_all_meta_boxes_tgp_sc() {
		global $wp_meta_boxes;
		if ( isset( $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_meta'] ) && $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_sc_preview_meta'] && $wp_meta_boxes[ rtTPG()->post_type ]['side']['low']['rt_plugin_sc_pro_information'] ) {
			$publishBox   = $wp_meta_boxes[ rtTPG()->post_type ]['side']['core']['submitdiv'];
			$scBox        = $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_meta'];
			$scBoxPreview = $wp_meta_boxes[ rtTPG()->post_type ]['normal']['high']['rttpg_sc_preview_meta'];
			$docBox       = $wp_meta_boxes[ rtTPG()->post_type ]['side']['low']['rt_plugin_sc_pro_information'];

			$wp_meta_boxes[ rtTPG()->post_type ] = array(
				'side'     => array(
					'core'    => array( 'submitdiv' => $publishBox ),
					'default' => [
						'rt_plugin_sc_pro_information' => $docBox
					]
				),
				'normal'   => array( 'high' => array( 'submitdiv' => $scBox ) ),
				'advanced' => array( 'high' => array( 'postexcerpt' => $scBoxPreview ) )
			);

			return array();
		}
	}

}