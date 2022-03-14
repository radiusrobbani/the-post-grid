<?php


namespace RT\ThePostGrid\Controllers;


if ( ! defined( 'WPINC' ) ) {
	die;
}

use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! class_exists( 'ElementorController' ) ):

	class ElementorController {

		public $el_cat_id;
		private $version;

		function __construct() {
			$this->version   = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RT_THE_POST_GRID_VERSION;
			$this->el_cat_id = RT_THE_POST_GRID_PLUGIN_SLUG . '-elements';

			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
				add_action( 'elementor/elements/categories_registered', [ $this, 'widget_category' ] );
				//add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_style' ] );
				add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'el_editor_script' ] );
				add_action( 'wp_enqueue_scripts', [ $this, 'tpg_el_scripts' ] );
				add_action( "elementor/frontend/after_enqueue_scripts", [ $this, 'tpg_frontend_scripts' ] );
				add_filter( 'elementor/editor/localize_settings', [ $this, 'promotePremiumWidgets' ] );
			}

			add_action( "wp_head", [ $this, 'set_primary_color' ] );
		}

		public function set_primary_color() {
			echo "<style>:root{--tpg-primary-color: #4C6FFF;--tpg-secondary-color:#4262e5;--tpg-primary-light:#c4d0ff }</style>";
		}

		public function tpg_frontend_scripts() {
			//wp_enqueue_script( 'imagesloaded' );
			//wp_enqueue_script( 'isotope' );
			//wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'tpg-el-script', rtTPG()->get_assets_uri( 'js/el-frontend.js' ), [ 'jquery' ], $this->version, true );
		}

		public function tpg_el_scripts() {
			$settings = get_option( rtTPG()->options['settings'] );
			wp_enqueue_style( 'rt-fontawsome' );
			wp_enqueue_style( 'rt-tpg-common' );
			wp_enqueue_style( 'tgp-elementor-style', rtTPG()->get_assets_uri( 'css/tpg-elementor.css' ), [], $this->version );

			if ( is_rtl() ) {
				wp_enqueue_style( 'rt-tpg-common-rtl' );
				wp_enqueue_style( 'rt-tpg-rtl' );
			}

			wp_enqueue_script( 'rt-swiper' );
			wp_enqueue_script( 'rt-tpg' );
			//Custom CSS From Settings
			$css = isset( $settings['custom_css'] ) ? stripslashes( $settings['custom_css'] ) : null;
			if ( $css ) {
				wp_add_inline_style( 'rt-tpg', $css );
			}

			$ajaxurl = '';
			if ( in_array( 'sitepress-multilingual-cms/sitepress.php', get_option( 'active_plugins' ) ) ) {
				$ajaxurl .= admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE );
			} else {
				$ajaxurl .= admin_url( 'admin-ajax.php' );
			}
			$variables = [
				'nonceID' => rtTPG()->nonceId(),
				'nonce'   => wp_create_nonce( rtTPG()->nonceText() ),
				'ajaxurl' => $ajaxurl,
			];
			wp_localize_script( 'rt-tpg', 'rttpg', $variables );
		}

		public function el_editor_script() {
			wp_enqueue_script( 'tgp-el-editor-scripts', rtTPG()->get_assets_uri( 'js/tpg-el-editor.js' ), [ 'jquery' ], $this->version, true );
			wp_enqueue_style( 'tgp-el-editor-style', rtTPG()->get_assets_uri( 'css/tpg-el-editor.css' ), [], $this->version );
		}

		public function editor_style() {
			$css = "";
			wp_add_inline_style( 'elementor-editor', $css );
		}

		public function init_widgets() {
			require_once( RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/base.php' );
			require_once( RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/rtTPGElementorHelper.php' );

			// dir_name => class_name
			$widgets = [
				'grid-layout'       => 'TPGGridLayout',
				'list-layout'       => 'TPGListLayout',
				'grid-hover-layout' => 'TPGGridHoverLayout',
				'slider-layout'     => 'TPGSliderLayout',
				'default'           => 'RtElementorWidget',
			];

			foreach ( $widgets as $file_name => $class ) {
				if ( ! rtTPG()->hasPro() && 'slider-layout' == $file_name ) {
					continue;
				}
				$template_name = 'the-post-grid/elementor/' . $file_name . '.php';
				if ( file_exists( STYLESHEETPATH . $template_name ) ) {
					$file = STYLESHEETPATH . $template_name;
				} elseif ( file_exists( TEMPLATEPATH . $template_name ) ) {
					$file = TEMPLATEPATH . $template_name;
				} else {
					$file = RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/widgets/' . $file_name . '.php';
				}
				require_once $file;

				Plugin::instance()->widgets_manager->register( new $class );
			}
		}

		public function widget_category($elements_manager) {
//			$register_categories = [
//				$this->el_cat_id => [ 'title' => __( 'The Post Grid', 'the-post-grid' ) ],
//			];

			//$register_categories = apply_filters( 'rtcl_elementor_widgets_category_lists', $register_categories );

//			foreach ( $register_categories as $id => $category ) {
//				Plugin::$instance->elements_manager->add_category( $id, $category );
//			}

			$elements_manager->add_category(
				$this->el_cat_id,
				[
					'title' => esc_html__( 'The Post Grid', 'plugin-name' ),
					'icon' => 'fa fa-plug',
				]
			);


		}


		public function promotePremiumWidgets( $config ) {
			if ( rtTPG()->hasPro() ) {
				return $config;
			}

			if ( ! isset( $config['promotionWidgets'] ) || ! is_array( $config['promotionWidgets'] ) ) {
				$config['promotionWidgets'] = [];
			}

			$category = RT_THE_POST_GRID_PLUGIN_SLUG . '-elements';

			$pro_widgets = [
				[
					'name'        => 'tpg-slider-layout',
					'title'       => __( 'TPG - Slider Layout', 'testimonial-slider-showcase' ),
					'description' => __( 'TPG - Slider Layout', 'testimonial-slider-showcase' ),
					'icon'        => 'eicon-post-slider tpg-grid-icon tss-promotional-element',
					'categories'  => '[ "the-post-grid-elements" ]',
				],
			];

			$config['promotionWidgets'] = array_merge( $config['promotionWidgets'], $pro_widgets );

			return $config;
		}


	}

endif;