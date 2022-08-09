<?php
/**
 * Grid Hover Layout Template - 1
 *
 * @package RT_TPG
 */

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

$pID     = get_the_ID();
$excerpt = Fns::get_the_excerpt( $pID, $data );
$title   = Fns::get_the_title( $pID, $data );

if ( 'custom' !== $data['title_visibility_style'] ) {
	$title = get_the_title();
}

/**
 * Get post link markup
 * $link_start, $link_end, $readmore_link_start, $readmore_link_end
 */

$post_link = Fns::get_post_link( $pID, $data );

extract( $post_link );

//Grid Column:
$grid_column_desktop = '0' !== $data['grid_column'] ? $data['grid_column'] : '4';
$grid_column_tab     = '0' !== $data['grid_column_tablet'] ? $data['grid_column_tablet'] : '6';
$grid_column_mobile  = '0' !== $data['grid_column_mobile'] ? $data['grid_column_mobile'] : '12';
$col_class           = "rt-col-md-{$grid_column_desktop} rt-col-sm-{$grid_column_tab} rt-col-xs-{$grid_column_mobile}";

// Column Dynamic Class.
$column_classes = [];

$column_classes[] .= $data['hover_animation'];
$column_classes[] .= 'rt-grid-hover-item rt-grid-item';

if ( 'masonry' == $data['layout_style'] ) {
	$column_classes[] .= 'masonry-grid-item';
}

?>

<div class="<?php echo esc_attr( $col_class . ' ' . implode( ' ', $column_classes ) ); ?>" data-id="<?php echo esc_attr( $pID ); ?>">
	<div class="rt-holder tpg-post-holder">
		<div class="rt-detail rt-el-content-wrapper">
			<?php
			if ( 'show' == $data['show_thumb'] ) :
				$has_thumbnail = has_post_thumbnail() ? 'has-thumbnail' : 'has-no-thumbnail';
				?>
				<div class="rt-img-holder tpg-el-image-wrap <?php echo esc_attr( $has_thumbnail ); ?>">
					<?php Fns::get_post_thumbnail( $pID, $data, $link_start, $link_end ); ?>
				</div>
			<?php endif; ?>

			<div class="grid-hover-content">
				<?php
				if ( 'show' == $data['show_title'] ) {
					Fns::get_el_post_title( $data['title_tag'], $title, $link_start, $link_end, $data );
				}
				?>

				<?php if ( 'show' == $data['show_meta'] ) : ?>
					<div class="post-meta-tags rt-el-post-meta">
						<?php Fns::get_post_meta_html( $pID, $data ); ?>
					</div>
				<?php endif; ?>

				<?php if ( 'show' == $data['show_excerpt'] || 'show' == $data['show_acf'] ) : ?>
					<div class="tpg-excerpt tpg-el-excerpt">
						<?php if ( $excerpt && 'show' == $data['show_excerpt'] ) : ?>
							<div class="tpg-excerpt-inner">
								<?php echo wp_kses_post( $excerpt ); ?>
							</div>
						<?php endif; ?>
						<?php Fns::tpg_get_acf_data_elementor( $data, $pID ); ?>
					</div>
				<?php endif; ?>

				<?php
				if ( rtTPG()->hasPro() && 'show' === $data['show_social_share'] ) {
					echo wp_kses( Functions::rtShare( $pID ), Fns::allowedHtml() );
				}
				?>

				<?php if ( 'show' === $data['show_read_more'] ) : ?>
					<div class="post-footer">
						<div class="read-more">
							<?php
							echo wp_kses( $readmore_link_start, Fns::allowedHtml() );

							if ( 'yes' == $data['show_btn_icon'] && 'left' == $data['readmore_icon_position'] ) {
								\Elementor\Icons_Manager::render_icon(
									$data['readmore_btn_icon'],
									[
										'aria-hidden' => 'true',
										'class'       => 'left-icon',
									]
								);
							}

							echo esc_html( $data['read_more_label'] );

							if ( 'yes' == $data['show_btn_icon'] && 'right' == $data['readmore_icon_position'] ) {
								\Elementor\Icons_Manager::render_icon(
									$data['readmore_btn_icon'],
									[
										'aria-hidden' => 'true',
										'class'       => 'right-icon',
									]
								);
							}

							echo wp_kses( $readmore_link_end, Fns::allowedHtml() );
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
