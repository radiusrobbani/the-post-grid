<?php

namespace RT\ThePostGrid\Controllers\Admin;


class UpgradeController {

	public static function check_plugin_version() {
		if ( version_compare( RT_TPG_PRO_VERSION, '5', '<' ) ) {
			add_action( 'admin_notices',
				function () {
					$class    = 'notice notice-error';
					$text     = esc_html__( 'The Post Grid Pro', 'the-post-grid' );
					$link_pro = 'https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/';

					printf( '<div class="%1$s"><p><a target="_blank" href="%2$s"><strong>The Post Grid Pro</strong></a> is not working, You need to update <strong>%3$s</strong> version to 5.0.0 or more to get the pro features.</p></div>',
						$class,
						$link_pro,
						$text );
				} );

			return false;
		}
		return true;
	}

}
