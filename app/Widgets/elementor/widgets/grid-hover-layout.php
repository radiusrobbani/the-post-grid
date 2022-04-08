<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

use RT\ThePostGrid\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TPGGridHoverLayout extends Custom_Widget_Base {

	/**
	 * GridLayout constructor.
	 *
	 * @param  array  $data
	 * @param  null   $args
	 *
	 * @throws \Exception
	 */

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->prefix   = 'grid_hover';
		$this->tpg_name = esc_html__( 'TPG - Grid Hover Layout', 'the-post-grid' );
		$this->tpg_base = 'tpg-grid-hover-layout';
		$this->tpg_icon = 'eicon-image-rollover tpg-grid-icon'; //.tpg-grid-icon class for just style
	}

	protected function register_controls() {
		/**
		 * Content Tab
		 * ===========
		 */

		//Query
		rtTPGElementorHelper::query( $this );

		//Layout
		rtTPGElementorHelper::grid_layouts( $this );

		//Filter  Settings
		rtTPGElementorHelper::filter_settings( $this );

		//Pagination Settings
		rtTPGElementorHelper::pagination_settings( $this );

		//Links
		rtTPGElementorHelper::links( $this );

		/**
		 * Settings Tab
		 * =============
		 */

		//Field Selection
		rtTPGElementorHelper::field_selection( $this );

		//Section Title Settings
		rtTPGElementorHelper::section_title_settings( $this );

		//Title Settings
		rtTPGElementorHelper::post_title_settings( $this );

		//Thumbnail Settings
		rtTPGElementorHelper::post_thumbnail_settings( $this );

		//Excerpt Settings
		rtTPGElementorHelper::post_excerpt_settings( $this );

		//Meta Settings
		rtTPGElementorHelper::post_meta_settings( $this );

		//Readmore Settings
		rtTPGElementorHelper::post_readmore_settings( $this );

		//Advanced Custom Field ACF Settings
		rtTPGElementorHelper::tpg_acf_settings( $this );

		/**
		 * Style Tab
		 * ==========
		 */

		//Section Title Style
		rtTPGElementorHelper::sectionTitle( $this );

		// Title Style
		rtTPGElementorHelper::titleStyle( $this );

		//Thumbnail Style
		rtTPGElementorHelper::thumbnailStyle( $this );

		// Content Style
		rtTPGElementorHelper::contentStyle( $this );

		// Meta Info Style
		rtTPGElementorHelper::metaInfoStyle( $this );

		//Pagination - Loadmore Style
		rtTPGElementorHelper::readmoreStyle( $this );

		//Pagination - Loadmore Style
		rtTPGElementorHelper::paginationStyle( $this );

		//Box Style
		rtTPGElementorHelper::frontEndFilter( $this );

		//Box Style
		rtTPGElementorHelper::socialShareStyle( $this );

		//ACF Style
		rtTPGElementorHelper::tpg_acf_style( $this );

		//Box Style
		rtTPGElementorHelper::articlBoxSettings( $this );

		//Promotions Style
		rtTPGElementorHelper::promotions( $this );
	}

	protected function render() {
		$data    = $this->get_settings();
		$_prefix = $this->prefix;
		if ( ! rtTPG()->hasPro() && ! in_array( $data[ $_prefix . '_layout' ], [ 'grid_hover-layout1', 'grid_hover-layout2', 'grid_hover-layout3' ] ) ) {
			$data[ $_prefix . '_layout' ] = 'grid_hover-layout1';
		}

		if ( rtTPG()->hasPro() && ( 'popup' == $data['post_link_type'] || 'multi_popup' == $data['post_link_type'] ) ) {
			wp_enqueue_style( 'rt-scrollbar' );
			wp_enqueue_style( 'rt-magnific-popup' );
			wp_enqueue_script( 'rt-scrollbar' );
			wp_enqueue_script( 'rt-magnific-popup' );
			add_action( 'wp_footer', [ $this, 'get_modal_markup' ] );
		}

		//Query
		$query_args     = rtTPGElementorQuery::post_query( $data, $_prefix );
		$query          = new WP_Query( $query_args );
		$rand           = mt_rand();
		$layoutID       = "rt-tpg-container-" . $rand;
		$posts_per_page = $data['display_per_page'] ? $data['display_per_page'] : $data['post_limit'];


		//TODO: Get Post Data for render post
		$post_data = $this->get_render_data_set( $data, $query->max_num_pages, $posts_per_page );

		//Post type render
		$post_types = Fns::get_post_types();
		foreach ( $post_types as $post_type => $label ) {
			$_taxonomies = get_object_taxonomies( $post_type, 'object' );
			if ( empty( $_taxonomies ) ) {
				continue;
			}
			$post_data[ $data['post_type'] . '_taxonomy' ] = $data[ $data['post_type'] . '_taxonomy' ];
			$post_data[ $data['post_type'] . '_tags' ]     = $data[ $data['post_type'] . '_tags' ];
		}
		$template_path = $this->tpg_template_path( $post_data );
		$_layout       = $data[ $_prefix . '_layout' ];
		?>

        <div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper <?php echo esc_attr( $_layout . '-main' ); ?>"
             id="<?php echo esc_attr( $layoutID ); ?>"
             data-layout="<?php echo esc_attr( $data[ $_prefix . '_layout' ] ); ?>"
             data-sc-id="elementor"
             data-el-settings='<?php echo Fns::is_filter_enable( $data ) ? htmlspecialchars( wp_json_encode( $post_data ) ) : ''; ?>'
             data-el-query='<?php echo Fns::is_filter_enable( $data ) ? htmlspecialchars( wp_json_encode( $query_args ) ) : ''; ?>'
             data-el-path='<?php echo Fns::is_filter_enable( $data ) ? esc_attr( $template_path ) : ''; ?>'
        >
			<?php
			$wrapper_class = [];
			if ( in_array( $_layout, [ 'grid_hover-layout6', 'grid_hover-layout7', 'grid_hover-layout8', 'grid_hover-layout9', 'grid_hover-layout10', 'grid_hover-layout11', 'grid_hover-layout5-2', 'grid_hover-layout6-2', 'grid_hover-layout7-2', 'grid_hover-layout9-2', ] ) ) {
				$wrapper_class[] = 'grid_hover-layout5';
			}
			$wrapper_class[] = str_replace('-2', null, $_layout);
			$wrapper_class[] = 'tpg-even grid-behaviour';
			$wrapper_class[] = $_prefix . '_layout_wrapper';

			//section title settings
			$is_carousel = '';
			if ( rtTPG()->hasPro() && 'carousel' == $data['filter_btn_style'] && 'button' == $data['filter_type'] ) {
				$is_carousel = 'carousel';
			}

			echo "<div class='tpg-header-wrapper {$is_carousel}'>";
			$this->get_section_title( $data );
			echo $this->get_frontend_filter_markup( $data );
			echo "</div>";
			?>

            <div data-title="Loading ..." class="rt-row rt-content-loader <?php echo esc_attr( implode( ' ', $wrapper_class ) ) ?>">
				<?php
				if ( $query->have_posts() ) {
					$pCount = 1;
					while ( $query->have_posts() ) {
						$query->the_post();
						set_query_var( 'tpg_post_count', $pCount );
						set_query_var( 'tpg_total_posts', $query->post_count );
						$this->tpg_template( $post_data );
						$pCount ++;
					}
				} else {
					if ( $data['no_posts_found_text'] ) {
						printf( "<div class='no_posts_found_text'>%s</div>", esc_html( $data['no_posts_found_text'] ) );
					} else {
						printf( "<div class='no_posts_found_text'>%s</div>", esc_html__( 'No post found', 'the-post-grid-pro' ) );
					}
				}
				wp_reset_postdata();
				?>
            </div>

			<?php echo $this->get_pagination_markup( $query, $data ); ?>
        </div>
		<?php
	}

}