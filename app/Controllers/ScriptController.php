<?php


namespace RT\ThePostGrid\Controllers;


class ScriptController {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue' ] );
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

}