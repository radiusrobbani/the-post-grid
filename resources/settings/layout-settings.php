<?php
/**
 * Settings: Layout Settings
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

echo Fns::rtFieldGenerator( Options::rtTPGLayoutSettingFields() );
echo '<div class="rd-responsive-column">';
echo Fns::rtFieldGenerator( Options::responsiveSettingsColumn() );
echo '</div>';
echo Fns::rtFieldGenerator( Options::layoutMiscSettings() );
