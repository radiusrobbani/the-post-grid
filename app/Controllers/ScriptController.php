<?php


namespace RT\ThePostGrid\Controllers;


class ScriptController {

	private $version;

	public function __construct() {
		$this->version = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RT_THE_POST_GRID_VERSION;
		add_action( 'wp_head', [ &$this, 'header_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue' ] );
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init() {
		// register scripts
		$scripts   = [];
		$styles    = [];
		$scripts[] = [
			'handle' => 'rt-image-load-js',
			'src'    => rtTPG()->get_assets_uri( "vendor/isotope/imagesloaded.pkgd.min.js" ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];
		$scripts[] = [
			'handle' => 'rt-isotope-js',
			'src'    => rtTPG()->get_assets_uri( "vendor/isotope/isotope.pkgd.min.js" ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];

		$scripts[] = [
			'handle' => 'rt-tpg',
			'src'    => rtTPG()->get_assets_uri( 'js/rttpg.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];

		// register acf styles
		$styles['rt-fontawsome']    = rtTPG()->get_assets_uri( 'vendor/font-awesome/css/font-awesome.min.css' );
		$styles['rt-tpg']           = rtTPG()->tpg_can_be_rtl( 'css/thepostgrid' );
		$styles['rt-tpg-common']    = rtTPG()->tpg_can_be_rtl( 'css/rt-tpg-common' );
		$styles['rt-tpg-elementor'] = rtTPG()->tpg_can_be_rtl( 'css/tpg-elementor' );

		if ( is_admin() ) {
			$scripts[]                      = [
				'handle' => 'rt-select2',
				'src'    => rtTPG()->get_assets_uri( 'vendor/select2/select2.min.js' ),
				'deps'   => [ 'jquery' ],
				'footer' => false,
			];
			$scripts[]                      = [
				'handle' => 'rt-tpg-admin',
				'src'    => rtTPG()->get_assets_uri( 'js/admin.js' ),
				'deps'   => [ 'jquery' ],
				'footer' => true,
			];
			$scripts[]                      = [
				'handle' => 'rt-tpg-admin-preview',
				'src'    => rtTPG()->get_assets_uri( 'js/admin-preview.js' ),
				'deps'   => [ 'jquery' ],
				'footer' => true,
			];
			$styles['rt-select2']           = rtTPG()->get_assets_uri( 'vendor/select2/select2.min.css' );
			$styles['rt-tpg-admin']         = rtTPG()->get_assets_uri( 'css/admin.css' );
			$styles['rt-tpg-admin-preview'] = rtTPG()->get_assets_uri( 'css/admin-preview.css' );
		}

		foreach ( $scripts as $script ) {
			wp_register_script( $script['handle'], $script['src'], $script['deps'], isset( $script['version'] ) ? $script['version'] : $this->version, $script['footer'] );
		}

		foreach ( $styles as $k => $v ) {
			wp_register_style( $k, $v, false, isset( $script['version'] ) ? $script['version'] : $this->version );
		}
	}

	public function enqueue() {
		$settings = get_option( rtTPG()->options['settings'] );

		if ( ! isset( $settings['tpg_load_script'] ) ) {
			wp_enqueue_style( 'rt-tpg-common' );
			wp_enqueue_style( 'rt-tpg' );
		}
		$scriptBefore = isset( $settings['script_before_item_load'] ) ? stripslashes( $settings['script_before_item_load'] ) : null;
		$scriptAfter  = isset( $settings['script_after_item_load'] ) ? stripslashes( $settings['script_after_item_load'] ) : null;
		$scriptLoaded = isset( $settings['script_loaded'] ) ? stripslashes( $settings['script_loaded'] ) : null;
		$script       = "(function($){
				$('.rt-tpg-container').on('tpg_item_before_load', function(){{$scriptBefore}});
				$('.rt-tpg-container').on('tpg_item_after_load', function(){{$scriptAfter}});
				$('.rt-tpg-container').on('tpg_loaded', function(){{$scriptLoaded}});
			})(jQuery);";
		wp_add_inline_script( 'rt-tpg', $script );
	}

	/**
	 * Header Scripts
	 */
	public function header_scripts() {
		echo "<style>:root{--tpg-primary-color: #0d6efd;--tpg-secondary-color:#0654c4;--tpg-primary-light:#c4d0ff }</style>";
	}

}