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

		$current_page = isset( $_GET["page"] ) ? $_GET["page"] : '';
		if ( 'rttpg_settings' == $current_page ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

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
		$styles['rt-tpg-common']    = rtTPG()->tpg_can_be_rtl( 'css/rt-tpg-common' );
		$styles['rt-tpg-elementor'] = rtTPG()->tpg_can_be_rtl( 'css/tpg-elementor' );
		$styles['rt-tpg']           = rtTPG()->tpg_can_be_rtl( 'css/thepostgrid' );

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
				'deps'   => [ 'jquery', 'wp-color-picker' ],
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
			wp_enqueue_style( 'rt-tpg-elementor' );
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
		$settings = get_option( rtTPG()->options['settings'] );
		?>
        <style>
            <?php if( isset( $settings['tpg_loader_color'] ) ) : ?>
            body #bottom-script-loader .rt-ball-clip-rotate {
                color: <?php echo esc_attr($settings['tpg_loader_color']) ?> !important;
            }

            <?php endif; ?>
        </style>
		<?php

		if ( isset( $settings['tpg_load_script'] ) ) : ?>
            <style>
                :root {
                    --tpg-primary-color: #0d6efd;
                    --tpg-secondary-color: #0654c4;
                    --tpg-primary-light: #c4d0ff
                }

                .rt-tpg-container .tpg-pre-loader {
                    position: relative;
                    overflow: hidden;
                }

                .rt-tpg-container .rt-loading-overlay {
                    opacity: 0;
                    visibility: hidden;
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1;
                    background-color: #fff;
                }

                .rt-tpg-container .rt-loading {
                    color: var(--tpg-primary-color);
                    position: absolute;
                    top: 40%;
                    left: 50%;
                    margin-left: -16px;
                    z-index: 2;
                    opacity: 0;
                    visibility: hidden;
                }

                .rt-tpg-container .tpg-pre-loader .rt-loading-overlay {
                    opacity: 0.8;
                    visibility: visible;
                }

                .rt-tpg-container .tpg-pre-loader .rt-loading {
                    opacity: 1;
                    visibility: visible;
                }


                #bottom-script-loader {
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    z-index: 20;
                    background: rgba(255, 255, 255, 0.95);
                }

                #bottom-script-loader .rt-ball-clip-rotate {
                    color: var(--tpg-primary-color);
                    position: absolute;
                    top: 80px;
                    left: 50%;
                    margin-left: -16px;
                    z-index: 2;
                }

                .tpg-el-main-wrapper.loading {
                    min-height: 300px;
                    transition: 0.4s;
                }

                .tpg-el-main-wrapper.loading::before {
                    width: 32px;
                    height: 32px;
                    display: inline-block;
                    float: none;
                    border: 2px solid currentColor;
                    background: transparent;
                    border-bottom-color: transparent;
                    border-radius: 100%;
                    -webkit-animation: ball-clip-rotate 0.75s linear infinite;
                    -moz-animation: ball-clip-rotate 0.75s linear infinite;
                    -o-animation: ball-clip-rotate 0.75s linear infinite;
                    animation: ball-clip-rotate 0.75s linear infinite;
                    left: 50%;
                    top: 50%;
                    position: absolute;
                    z-index: 9999999999;
                    color: red;
                }


                .tpg-el-main-wrapper .slider-main-wrapper {
                    opacity: 0;
                }

                .md-modal {
                    visibility: hidden;
                }

            </style>
		<?php endif;

	}

}