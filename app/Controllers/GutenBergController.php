<?php
/**
 * Elementor Controller class.
 *
 * @package RT_TPG
 */

namespace RT\ThePostGrid\Controllers;

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor Controller class.
 */
class GutenBergController {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );

		if ( function_exists( 'register_block_type' ) ) {
			register_block_type(
				'rttpg/post-grid',
				[ 'render_callback' => [ $this, 'render_shortcode' ] ]
			);
		}
	}

	/**
	 * Render
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function render_shortcode( $atts ) {
		if ( ! isset( $atts['gridId'] ) && empty( isset( $atts['gridId'] ) ) ) {
			return;
		};

		return do_shortcode( '[the-post-grid id="' . absint( $atts['gridId'] ) . '"]' );
	}

	/**
	 * Block assets
	 *
	 * @return void
	 */
	public function block_assets() {
		wp_enqueue_style( 'wp-blocks' );
	}

	/**
	 * Block editor assets
	 *
	 * @return void
	 */
	public function block_editor_assets() {
		// Scripts.
		wp_enqueue_script(
			'rt-tpg-cgb-block-js',
			rtTPG()->get_assets_uri( 'js/post-grid-blocks.js' ),
			[ 'wp-blocks', 'wp-i18n', 'wp-element' ],
			( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RT_THE_POST_GRID_VERSION,
			true
		);

		wp_localize_script(
			'rt-tpg-cgb-block-js',
			'rttpgGB',
			[
				'short_codes' => Fns::getAllTPGShortCodeList(),
				'icon'        => rtTPG()->get_assets_uri( 'images/icon-16x16.png' ),
			]
		);

		wp_enqueue_style( 'wp-edit-blocks' );
	}
}
