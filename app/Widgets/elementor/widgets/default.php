<?php
/**
 * Default Widget Class
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Default Widget Class
 */
class RtElementorWidget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'the-post-grid';
	}

	public function get_title() {
		return __( 'The Post Grid <br> (Deprecated)', 'the-post-grid' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid tpg-grid-icon';
	}

	public function get_categories() {
		return [ RT_THE_POST_GRID_PLUGIN_SLUG . '-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'The Post Grid (Deprecated)', 'the-post-grid' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_grid_id',
			[
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'id'      => 'style',
				'label'   => esc_html__( 'Post Grid', 'the-post-grid' ),
				'options' => Fns::getAllTPGShortCodeList(),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( isset( $settings['post_grid_id'] ) && ! empty( $settings['post_grid_id'] ) && $id = $settings['post_grid_id'] ) {
			echo do_shortcode( '[the-post-grid id="' . absint( $id ) . '"]' );
		} else {
			echo esc_html__( 'Please select a post grid', 'the-post-grid' );
		}
	}
}
