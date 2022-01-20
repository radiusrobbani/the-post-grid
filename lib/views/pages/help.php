<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get Help
 */
?>
	<style>
        .rttpg-help-wrapper {
            width: 60%;
            margin: 0 auto;
        }
        .rttpg-help-wrapper .rt-document-box .rt-box-title {
            margin-bottom: 30px;
        }
        .rttpg-help-wrapper .rttpg-help-section {
            margin-top: 30px;
        }
        .rttpg-feature-list ul {
            display: flex;
            flex-wrap: wrap;
        }
        .rttpg-feature-list ul li {
            margin: 5px 10px 5px 0;
            width: calc(50% - 20px);
            flex: 0 0 calc(50% - 20px);
            font-size: 14px;
        }
        .rttpg-feature-list ul li i {
            color: var(--rt-primary-color);
        }
        .rttpg-pro-feature-content {
            display: flex;
        }
        .rttpg-pro-feature-content .rt-document-box + .rt-document-box {
            margin-left: 30px;
        }
        .rttpg-pro-feature-content .rt-document-box {
            flex: 0 0 calc(33.3333% - 60px);
            margin-top: 30px;
        }
        .rttpg-testimonials {
            display: flex;
            flex-wrap: wrap;
        }
        .rttpg-testimonials .rttpg-testimonial + .rttpg-testimonial  {
            margin-left: 30px;
        }
        .rttpg-testimonials .rttpg-testimonial  {
            flex: 0 0 calc(50% - 30px)
        }
        .rttpg-testimonial .client-info {
            display: flex;
            flex-wrap: wrap;
            font-size: 14px;
            align-items: center;
        }
        .rttpg-testimonial .client-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }
        .rttpg-testimonial .client-info .rttpg-star {
            color: var(--rt-primary-color);
        }
        .rttpg-testimonial .client-info .client-name {
            display: block;
            color: #000;
            font-size: 16px;
            font-weight: 600;
            margin: 8px 0 5px;
        }
	</style>
	<div class="rttpg-help-wrapper" >
		<div class="rttpg-help-section rt-document-box">
			<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
			<div class="rt-box-content">
				<h3 class="rt-box-title">Thank you for installing The Post Grid</h3>
				<iframe width="611" height="360" src="https://www.youtube.com/embed/_xZBDU4kgKk" title="The Post Grid" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
        <div class="rt-document-box">
            <div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div>
            <div class="rt-box-content rttpg-feature-list">
                <h3 class="rt-box-title">Pro Features</h3>
                <ul>
                    <li><i class="dashicons dashicons-saved"></i> Custom Post Type Supported.</li>
                    <li><i class="dashicons dashicons-saved"></i> AJAX Pagination (Load more and Load on Scrolling).</li>
                    <li><i class="dashicons dashicons-saved"></i> Advanced Post Filter.</li>
                    <li><i class="dashicons dashicons-saved"></i> Single or Multi Popup.</li>
                    <li><i class="dashicons dashicons-saved"></i> Custom Image Size.</li>
                    <li><i class="dashicons dashicons-saved"></i> Meta Position Control.</li>
                    <li><i class="dashicons dashicons-saved"></i> Social Share.</li>
                    <li><i class="dashicons dashicons-saved"></i> 62 Different Layouts.</li>
                    <li><i class="dashicons dashicons-saved"></i> Special Layout for WooCommerce.</li>
                    <li><i class="dashicons dashicons-saved"></i> Slider Layout.</li>
                    <li><i class="dashicons dashicons-saved"></i> Fields Selection.</li>
                    <li><i class="dashicons dashicons-saved"></i> All Text and Color control.</li>
                </ul>
                <a href="https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/" class="rt-admin-btn" target="_blank">More Features</a>';
            </div>
        </div>
        <div class="rt-document-box rt-update-pro-btn-wrap">
            <a href="https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/" target="_blank" class="rt-update-pro-btn">Update Pro To Get More Features</a>
        </div>
        <div class="rt-document-box">
            <div class="rt-box-icon"><i class="dashicons dashicons-thumbs-up"></i></div>
            <div class="rt-box-content">
                <h3 class="rt-box-title">Happy clients of the Post Grid</h3>
                <div class="rttpg-testimonials">
                    <div class="rttpg-testimonial">
                        <p>So much functionality in the free version. Thank you very much! Many plugins offer a crippled free version to push into going to their PRO. The guys here provide a free version that brings lots of value also. I needed a flexible grid solution to my website that has dozen of grids in different configurations and the plugin could do everything I needed. Very easy to use and support it fantastic. Highly Recomended!</p>
                        <div class="client-info">
                            <img src="<?php echo rtTPG()->assetsUrl; ?>images/admin/client1.jpeg">
                            <div>
                                <div class="rttpg-star">
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                </div>
                                <span class="client-name">Erez Speiser</span>
                            </div>
                        </div>
                    </div>
                    <div class="rttpg-testimonial">
                        <p>I am a photographer and I'm using the plugin for my website and i find it useful.</p>
                        <div class="client-info">
                            <img src="<?php echo rtTPG()->assetsUrl; ?>images/admin/client2.png">
                            <div>
                                <div class="rttpg-star">
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                </div>
                                <span class="client-name">Sergi Albir</span>
                                <span class="client-designation">Professional photographer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rttpg-pro-feature-content">
            <div class="rt-document-box">
                <div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
                <div class="rt-box-content">
                    <h3 class="rt-box-title">Documentation</h3>
                    <p>Get started by spending some time with the documentation we included step by step process with screenshots with video.</p>
                    <a href="https://www.radiustheme.com/docs/the-post-grid/" target="_blank" class="rt-admin-btn">Documentation</a>
                </div>
            </div>
            <div class="rt-document-box">
                <div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
                <div class="rt-box-content">
                    <h3 class="rt-box-title">Need Help?</h3>
                    <p>Stuck with something? Please create a
                        <a href="https://www.radiustheme.com/contact/">ticket here</a> or post on <a href="https://www.facebook.com/groups/234799147426640/">facebook group</a>. For emergency case join our <a href="https://www.radiustheme.com/">live chat</a>.</p>
                    <a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">Get Support</a>
                </div>
            </div>
            <div class="rt-document-box">
                <div class="rt-box-icon"><i class="dashicons dashicons-smiley"></i></div>
                <div class="rt-box-content">
                    <h3 class="rt-box-title">Happy Our Work?</h3>
                    <p>If you happy with <strong>The Post Grid</strong> plugin, please add a rating. It would be glad to us.</p>
                    <a href="https://wordpress.org/support/plugin/review-schema/reviews/?filter=5#new-post" class="rt-admin-btn" target="_blank">Add Rating</a>
                </div>
            </div>
        </div>
	</div>
<?php