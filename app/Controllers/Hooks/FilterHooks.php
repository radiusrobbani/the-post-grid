<?php

namespace RT\ThePostGrid\Controllers\Hooks;

class FilterHooks {
	public static function init() {
		add_filter( 'tpg_author_arg', [ __CLASS__, 'filter_author_args' ], 10 );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_row_meta' ], 10, 2 );
	}

	public static function filter_author_args( $args ) {
		$defaults = [ 'role__in' => [ 'administrator', 'editor', 'author' ] ];

		return wp_parse_args( $args, $defaults );
	}

	public static function plugin_row_meta( $links, $file ) {
		if ( $file == RT_THE_POST_GRID_PLUGIN_ACTIVE_FILE_NAME ) {
			$report_url         = 'https://www.radiustheme.com/contact/';
			$row_meta['issues'] = sprintf( '%2$s <a target="_blank" href="%1$s">%3$s</a>', esc_url( $report_url ), esc_html__( 'Facing issue?', 'the-post-grid' ), '<span style="color: red">' . esc_html__( 'Please open a support ticket.', 'the-post-grid' ) . '</span>' );

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

}
