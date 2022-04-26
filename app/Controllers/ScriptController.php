<?php


namespace RT\ThePostGrid\Controllers;


class ScriptController {

	public function __construct() {
		add_action( 'wp_head', [ &$this, 'header_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue' ] );
	}

	public function enqueue() {
		$settings = get_option( rtTPG()->options['settings'] );

		if ( ! isset( $settings['tpg_load_script'] )) {
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

		$settings = get_option( rtTPG()->options['settings'] );
		if ( isset( $settings['tpg_load_script'] ) ) {
			?>
            <style>

                .tpg-shortcode-main-wrapper.loading .rt-content-loader {
                    opacity: 0;
                }

                .tpg-shortcode-main-wrapper.loading::before {
                    content: "Loading...";
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    z-index: 999;
                    display: flex;
                    justify-content: center;
                    padding-top: 100px;
                    transition: 0.4s;
                    animation: tpgFadeInOut .8s ease-in-out infinite;
                }

                @-webkit-keyframes tpgFadeInOut {
                    0%{opacity:1}
                    100%{opacity:0}

                }
            </style>

            <script>

                jQuery(window).load(function () {
                    setTimeout(function () {
                        // jQuery('.tpg-shortcode-main-wrapper').removeClass('loading');
                        jQuery('.tpg-shortcode-main-wrapper .rt-content-loader').css({'opacity': '1'});
                        jQuery('.tpg-shortcode-main-wrapper').removeClass('loading');
                    }, 500);
                });
            </script>

			<?php
		}
	}

}