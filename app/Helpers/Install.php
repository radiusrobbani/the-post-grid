<?php


namespace RT\ThePostGrid\Helpers;


class Install {
	function activate() {
		self::insertDefaultData();
	}

	function deactivate() {

	}

	public static function insertDefaultData() {
		update_option( rtTPG()->options['installed_version'], RT_THE_POST_GRID_VERSION );
		if ( ! get_option( rtTPG()->options['settings'] ) ) {
			update_option( rtTPG()->options['settings'], rtTPG()->defaultSettings );
		}
	}
}