<?php


namespace RT\ThePostGrid\Controllers;


use Elementor\Plugin;
use RT\ThePostGrid\Widgets\ElementorWidget;

class ElementorController {
	function __construct() {
		if ( did_action( 'elementor/loaded' ) ) {
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'init' ) );
		}
	}

	function init() {
		// Register widget
		Plugin::instance()->widgets_manager->register_widget_type( new ElementorWidget() );
	}
}