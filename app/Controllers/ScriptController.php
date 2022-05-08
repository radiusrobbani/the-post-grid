<?php


namespace RT\ThePostGrid\Controllers;


class ScriptController {

	public function __construct() {
		add_action( 'wp_head', [ &$this, 'header_scripts' ] );
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

	/**
	 * Header Scripts
	 */
	public function header_scripts() {
		echo "<style>:root{--tpg-primary-color: #0d6efd;--tpg-secondary-color:#0654c4;--tpg-primary-light:#c4d0ff }</style>";

		$settings = get_option( rtTPG()->options['settings'] );


		if ( isset( $settings['tpg_load_script'] ) ) {
			$loadingContent = isset( $settings['tpg_enable_preloader'] ) ? 'Loading...' : '';
			?>
            <style>

                .tpg-shortcode-main-wrapper.loading .rt-content-loader {
                    opacity: 0;
                }

                .tpg-shortcode-main-wrapper.loading::before {
                    content: "<?php echo esc_attr($loadingContent) ?>";
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
                    0% {
                        opacity: 1
                    }
                    100% {
                        opacity: 0
                    }

                }
            </style>

            <script>

                window.addEventListener('load', () => {
                    setTimeout(function () {
                        var tpgContainer = document.querySelectorAll('.tpg-shortcode-main-wrapper');
                        var tpgContainerLoader = document.querySelectorAll('.tpg-shortcode-main-wrapper .rt-content-loader');

                        tpgContainerLoader.forEach(function (elm) {
                            elm.style.opactiy = 1
                        })

                        tpgContainer.forEach(function (elm) {
                            elm.classList.remove('loading');
                        })
                    }, 500);
                });

            </script>

			<?php
		}
	}

}