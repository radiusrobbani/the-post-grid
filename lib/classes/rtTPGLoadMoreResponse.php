<?php

if (!class_exists('rtTPGLoadMoreResponse')):

    class rtTPGLoadMoreResponse
    {
        private $l4toggleLoadMore;
        private $order = "DESC";

        function __construct() {
            add_action('wp_ajax_tpgLayoutAjaxAction', array($this, 'tpgLayoutAjaxAction'));
            add_action('wp_ajax_nopriv_tpgLayoutAjaxAction', array($this, 'tpgLayoutAjaxAction'));
        }

        function tpgLayoutAjaxAction() {

            $error = true;
            $msg = $data = null;
            $paged = $total_pares = 1;
            $args = array();
            if (rtTPG()->verifyNonce()) {
                $scID = intval($_REQUEST['scID']);
                if ($scID && !is_null(get_post($scID))) {
                    $scMeta = get_post_meta($scID);
                    $layout = (isset($scMeta['layout'][0]) ? $scMeta['layout'][0] : 'layout1');
                    if (!in_array($layout, array_keys(rtTPG()->rtTPGLayouts()))) {
                        $layout = 'layout1';
                    }
                    if ($layout == 'layout4') {
                        $this->l4toggleLoadMore = empty($_REQUEST['l4toggle']) ? true : false;
                    }
                    $isIsotope = preg_match('/isotope/', $layout);
                    $isCarousel = preg_match('/carousel/', $layout);
                    $isOffset = preg_match('/offset/', $layout);
                    $colStore = $dCol = (isset($scMeta['column'][0]) ? absint($scMeta['column'][0]) : 3);
                    $tCol = (isset($scMeta['tpg_tab_column'][0]) ? absint($scMeta['tpg_tab_column'][0]) : 2);
                    $mCol = (isset($scMeta['tpg_mobile_column'][0]) ? absint($scMeta['tpg_mobile_column'][0]) : 1);
                    if (!in_array($dCol, array_keys(rtTPG()->scColumns()))) {
                        $dCol = 3;
                    }
                    if (!in_array($tCol, array_keys(rtTPG()->scColumns()))) {
                        $tCol = 2;
                    }
                    if (!in_array($dCol, array_keys(rtTPG()->scColumns()))) {
                        $mCol = 1;
                    }
                    if ($isOffset) {
                        $dCol = ($dCol < 3 ? 2 : $dCol);
                        $tCol = ($tCol < 3 ? 2 : $tCol);
                        $mCol = ($mCol < 3 ? 1 : $mCol);
                    }
                    $arg = array();
                    $fImg = (!empty($scMeta['feature_image'][0]) ? true : false);
                    $fImgSize = (isset($scMeta['featured_image_size'][0]) ? $scMeta['featured_image_size'][0] : "medium");
                    $mediaSource = (isset($scMeta['media_source'][0]) ? $scMeta['media_source'][0] : "feature_image");
                    $arg['excerpt_type'] = (isset($scMeta['tgp_excerpt_type'][0]) ? $scMeta['tgp_excerpt_type'][0] : 'character');
                    $arg['title_limit_type'] = (isset($scMeta['tpg_title_limit_type'][0]) ? $scMeta['tpg_title_limit_type'][0] : 'character');
                    $arg['excerpt_limit'] = (isset($scMeta['excerpt_limit'][0]) ? absint($scMeta['excerpt_limit'][0]) : 0);
                    $arg['title_limit'] = (isset($scMeta['tpg_title_limit'][0]) ? absint($scMeta['tpg_title_limit'][0]) : 0);
                    $arg['excerpt_more_text'] = (isset($scMeta['tgp_excerpt_more_text'][0]) ? $scMeta['tgp_excerpt_more_text'][0] : null);
                    $arg['read_more_text'] = (!empty($scMeta['tgp_read_more_text'][0]) ? $scMeta['tgp_read_more_text'][0] : __('Read More',
                        'the-post-grid'));
                    $arg['show_all_text'] = (!empty($scMeta['tpg_show_all_text'][0]) ? $scMeta['tpg_show_all_text'][0] : __('Show all',
                        'the-post-grid'));
                    $arg['tpg_title_position'] = isset($scMeta['tpg_title_position'][0]) && !empty($scMeta['tpg_title_position'][0]) ? $scMeta['tpg_title_position'][0] : null;
                    $arg['btn_alignment_class'] = isset($scMeta['tpg_read_more_button_alignment'][0]) && !empty($scMeta['tpg_read_more_button_alignment'][0]) ? $scMeta['tpg_read_more_button_alignment'][0] : '';

                    /* Argument create */
                    $args = array();
                    $postType = (isset($scMeta['tpg_post_type'][0]) ? $scMeta['tpg_post_type'][0] : null);
                    if ($postType) {
                        $args['post_type'] = $postType;
                    }

                    // Common filter
                    /* post__in */
                    $post__in = (isset($scMeta['post__in'][0]) ? $scMeta['post__in'][0] : null);
                    if ($post__in) {
                        $post__in = explode(',', $post__in);
                        $args['post__in'] = $post__in;
                    }
                    /* post__not_in */
                    $post__not_in = (isset($scMeta['post__not_in'][0]) ? $scMeta['post__not_in'][0] : null);
                    if ($post__not_in) {
                        $post__not_in = explode(',', $post__not_in);
                        $args['post__not_in'] = $post__not_in;
                    }
                    $loaded_posts = !empty($_REQUEST['loaded_post_ids']) ? array_map('intval',
                        $_REQUEST['loaded_post_ids']) : array();
                    if (!empty($loaded_posts)) {
                        $args['post__not_in'] = is_array($post__not_in) && !empty($post__not_in) ? array_merge($post__not_in,
                            $loaded_posts) : $loaded_posts;
                    }

                    /* LIMIT */
                    $limit = ((empty($scMeta['limit'][0]) || $scMeta['limit'][0] === '-1') ? 10000000 : absint($scMeta['limit'][0]));
                    $args['posts_per_page'] = $limit;
                    $pagination = (!empty($scMeta['pagination'][0]) ? true : false);
                    if ($pagination) {
                        $posts_per_page = (isset($scMeta['posts_per_page'][0]) ? intval($scMeta['posts_per_page'][0]) : $limit);
                        if ($posts_per_page > $limit) {
                            $posts_per_page = $limit;
                        }
                        // Set 'posts_per_page' parameter
                        $args['posts_per_page'] = $posts_per_page;

                        $paged = (!empty($_REQUEST['paged'])) ? intval($_REQUEST['paged']) : 2;

                        $offset = $posts_per_page * ((int)$paged - 1);
                        $args['paged'] = $paged;

                        // Update posts_per_page
                        if (intval($args['posts_per_page']) > $limit - $offset) {
                            $args['posts_per_page'] = $limit - $offset;
                        }
                    }
                    if (!empty($loaded_posts)) {
                        $paged = 1;
                        unset($args['paged']);
                    }

                    // Advance Filter
                    $adv_filter = (isset($scMeta['post_filter']) ? $scMeta['post_filter'] : array());
                    $tpg_taxonomy = !empty($scMeta['tpg_taxonomy']) ? $scMeta['tpg_taxonomy'] : array();
                    $action_taxonomy = !empty($_REQUEST['taxonomy']) ? trim($_REQUEST['taxonomy']) : null;
                    $action_term = !empty($_REQUEST['term']) ? ($_REQUEST['term'] === 'all' ? $_REQUEST['term'] : absint($_REQUEST['term'])) : 0;
                    // Taxonomy
                    $taxQ = array();
                    if (in_array('tpg_taxonomy', $adv_filter) && !empty($tpg_taxonomy)) {
                        foreach ($tpg_taxonomy as $taxonomy) {
                            $terms = !empty($scMeta['term_' . $taxonomy]) ? $scMeta['term_' . $taxonomy] : array();
                            if (is_array($terms) && !empty($terms) && ($action_term === 'all' || !in_array($action_taxonomy, $tpg_taxonomy))) {
                                $operator = !empty($scMeta['term_operator_' . $taxonomy][0]) ? $scMeta['term_operator_' . $taxonomy][0] : "IN";
                                $taxQ[] = array(
                                    'taxonomy' => $taxonomy,
                                    'field'    => 'term_id',
                                    'terms'    => $terms,
                                    'operator' => $operator,
                                );
                            }
                        }

                    }
                    if ($action_taxonomy && $action_term && $action_term !== 'all') {
                        $taxQ[] = array(
                            'taxonomy' => $action_taxonomy,
                            'field'    => 'term_id',
                            'terms'    => array($action_term),
                            'operator' => "IN",
                        );
                    }

                    if (is_array($taxQ) && count($taxQ) >= 2) {
                        $taxQ['relation'] = !empty($scMeta['taxonomy_relation'][0]) ? $scMeta['taxonomy_relation'][0] : "AND";
                    }
                    if (!empty($taxQ)) {
                        $args['tax_query'] = $taxQ;
                    }

                    // Order
                    if (in_array('order', $adv_filter)) {
                        $order_by = (isset($scMeta['order_by'][0]) ? $scMeta['order_by'][0] : null);
                        $order = (isset($scMeta['order'][0]) ? $scMeta['order'][0] : null);
                        if ($order) {
                            $args['order'] = $order;
                        }
                        if ($order_by) {
                            $args['orderby'] = $order_by;
                            $meta_key = !empty($scMeta['tpg_meta_key'][0]) ? trim($scMeta['tpg_meta_key'][0]) : null;
                            if (in_array($order_by, array_keys(rtTPG()->rtMetaKeyType())) && $meta_key) {
                                $args['orderby'] = $order_by;
                                $args['meta_key'] = $meta_key;
                            }
                        }
                    }
                    // override shortcode filter
                    $action_order = !empty($_REQUEST['order']) ? trim($_REQUEST['order']) : null;
                    if ($action_order) {
                        $args['order'] = $action_order;
                    }
                    $action_order_by = !empty($_REQUEST['order_by']) ? trim($_REQUEST['order_by']) : null;
                    if ($action_order_by) {
                        $args['orderby'] = $action_order_by;
                        unset($args['meta_key']);
                        $meta_key = !empty($scMeta['tpg_meta_key'][0]) ? $scMeta['tpg_meta_key'][0] : null;
                        if (in_array($action_order_by, array_keys(rtTPG()->rtMetaKeyType())) && $meta_key) {
                            $args['orderby'] = $order_by;
                            $args['meta_key'] = $meta_key;
                        }
                    }
                    $this->order = !empty($args['order']) ? $args['order'] : "DESC";
                    if ($postType == "product" && !empty($args['orderby'])) {
                        switch ($args['orderby']) {
                            case 'price':
                                $args['orderby'] = 'meta_value_num';
                                $args['meta_key'] = '_price';
                                break;
                            case 'rating' :
                                // Sorting handled later though a hook
                                add_filter('posts_clauses', array($this, 'order_by_rating_post_clauses'));
                                break;
                        }
                    }


                    // Status
                    if (in_array('tpg_post_status', $adv_filter)) {
                        $post_status = (isset($scMeta['tpg_post_status']) && !empty($scMeta['tpg_post_status']) ? $scMeta['tpg_post_status'] : array());
                        if (!empty($post_status)) {
                            $args['post_status'] = $post_status;
                        } else {
                            $args['post_status'] = 'publish';
                        }
                    }
                    // Author
                    $author = (isset($scMeta['author']) ? $scMeta['author'] : array());
                    if (in_array('author', $adv_filter) && !empty($author)) {
                        $args['author__in'] = $author;
                    }
                    $action_author = !empty($_REQUEST['author']) ? absint($_REQUEST['author']) : null;
                    if ($action_author) {
                        $args['author__in'] = array($action_author);
                    }

                    // Search
                    $s = (isset($scMeta['s'][0]) ? $scMeta['s'][0] : array());
                    if (in_array('s', $adv_filter) && !empty($s)) {
                        $args['s'] = $s;
                    }
                    $sAction = (!empty($_REQUEST['search']) ? trim($_REQUEST['search']) : null);
                    if ($sAction) {
                        $args['s'] = $sAction;
                    }

                    // Date query
                    if (in_array('date_range', $adv_filter)) {
                        $startDate = (!empty($scMeta['date_range_start'][0]) ? $scMeta['date_range_start'][0] : null);
                        $endDate = (!empty($scMeta['date_range_end'][0]) ? $scMeta['date_range_end'][0] : null);
                        if ($startDate && $endDate) {
                            $args['date_query'] = array(
                                array(
                                    'after'     => $startDate,
                                    'before'    => $endDate,
                                    'inclusive' => true,
                                ),
                            );
                        }
                    }
                    if (!empty($_REQUEST['archive'])) {
                        $archive = $_REQUEST['archive'];
                        $archiveValue = !empty($_REQUEST['archive_value']) ? $_REQUEST['archive_value'] : null;
                        $settings = get_option(rtTPG()->options['settings']);
                        $oLayoutTag = !empty($settings['template_tag']) ? absint($settings['template_tag']) : null;
                        $oLayoutAuthor = !empty($settings['template_author']) ? $settings['template_author'] : null;
                        $oLayoutCategory = !empty($settings['template_category']) ? $settings['template_category'] : null;
                        $oLayoutSearch = !empty($settings['template_search']) ? $settings['template_search'] : null;
                        $dataArchive = null;
                        if ($archive) {
                            unset($args['post_type']);
                            unset($args['tax_query']);
                            unset($args['author__in']);
                            if ($oLayoutTag && $archive == "tag") {
                                if (!empty($archiveValue)) {
                                    $args['tag'] = $archiveValue;
                                }
                            } else if ($oLayoutCategory && $archive == "category") {
                                if (!empty($archiveValue)) {
                                    $args['category_name'] = $archiveValue;
                                }
                            } else if ($oLayoutAuthor && $archive == "author") {
                                if (!empty($archiveValue)) {
                                    $args['author'] = $archiveValue;
                                }
                            } else if ($oLayoutSearch && $archive == "search") {
                                $args['s'] = $archiveValue;
                            }
                            $args['posts_per_archive_page'] = $args['posts_per_page'];
                        }
                    }

                    // Validation
                    $dCol = $dCol == 5 ? '24' : round(12 / $dCol);
                    $tCol = $dCol == 5 ? '24' : round(12 / $tCol);
                    $mCol = $dCol == 5 ? '24' : round(12 / $mCol);
                    if ($isCarousel) {
                        $dCol = $tCol = $mCol = 12;
                    }
                    $arg['grid'] = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";
                    if ($layout == 'layout2' || $layout == 'layout3') {
                        $iCol = (isset($scMeta['tgp_layout2_image_column'][0]) ? absint($scMeta['tgp_layout2_image_column'][0]) : 4);
                        $iCol = $iCol > 12 ? 4 : $iCol;
                        $cCol = 12 - $iCol;
                        $arg['image_area'] = "rt-col-sm-{$iCol} rt-col-xs-12 ";
                        $arg['content_area'] = "rt-col-sm-{$cCol} rt-col-xs-12 ";
                    } else if ($layout == 'layout4') {
                        $arg['image_area'] = "rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ";
                        $arg['content_area'] = "rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ";
                    }
                    $gridType = !empty($scMeta['grid_style'][0]) ? $scMeta['grid_style'][0] : 'even';

                    $masonryG = null;
                    $arg_class = [];
                    $arg_class[] = "rt-grid-item";
                    if ($isOffset) {
                        $arg_class[] = "rt-offset-item";
                    } else {
                        if ($gridType == "even") {
                            $masonryG = " tpg-even";
                            $arg_class[] = 'even-grid-item';
                        } else if ($gridType == "masonry" && !$isIsotope && !$isCarousel) {
                            $masonryG = "tpg-masonry";
                            $arg_class[] = 'masonry-grid-item';
                        }
                    }

                    $preLoader = null;
                    if ($isIsotope) {
                        $arg_class[] = 'isotope-item';
                        $preLoader = 'tss-pre-loader';
                    }
                    if ($isCarousel) {
                        $arg_class[] = 'carousel-item';
                        $preLoader = 'tss-pre-loader';
                    }

                    $margin = !empty($scMeta['margin_option'][0]) ? $scMeta['margin_option'][0] : 'default';
                    if ($margin == 'no') {
                        $arg_class[] = 'no-margin';
                    }
                    if (!empty($scMeta['tpg_image_type'][0]) && $scMeta['tpg_image_type'][0] == 'circle') {
                        $arg_class[] = 'tpg-img-circle';
                    }
                    $arg['class'] = implode(" ", $arg_class);
                    $arg['anchorClass'] = $arg['link_target'] = null;
                    $link = !empty($scMeta['link_to_detail_page'][0]) ? $scMeta['link_to_detail_page'][0] : 'yes';
                    if ($link != 'yes') {
                        $arg['anchorClass'] = ' disabled';
                    }
                    $isSinglePopUp = false;
                    $linkType = !empty($scMeta['detail_page_link_type'][0]) ? $scMeta['detail_page_link_type'][0] : 'popup';
                    if ($link == 'yes') {
                        if ($linkType == 'popup') {
                            $popupType = !empty($scMeta['popup_type'][0]) ? $scMeta['popup_type'][0] : 'single';
                            if ($popupType == 'single') {
                                $arg['anchorClass'] .= ' tpg-single-popup';
                                $isSinglePopUp = true;
                            } else {
                                $arg['anchorClass'] .= ' tpg-multi-popup';
                            }
                        } else {
                            $arg['link_target'] = !empty($scMeta['link_target'][0]) ? " target='{$scMeta['link_target'][0]}'" : null;
                        }
                    }


                    $defaultImgId = (!empty($scMeta['default_preview_image'][0]) ? absint($scMeta['default_preview_image'][0]) : null);
                    $customImgSize = (!empty($scMeta['custom_image_size']) ? $scMeta['custom_image_size'] : array());

                    $arg['items'] = isset($scMeta['item_fields']) ? ($scMeta['item_fields'] ? $scMeta['item_fields'] : array()) : array();
                    if (in_array('cf', $arg['items'])) {
                        $arg['cf_group'] = array();
                        $arg['cf_group'] = get_post_meta($scID, 'cf_group');
                        $arg['format'] = array(
                            'hide_empty'       => get_post_meta($scID, 'cf_hide_empty_value', true),
                            'show_value'       => get_post_meta($scID, 'cf_show_only_value', true),
                            'hide_group_title' => get_post_meta($scID, 'cf_hide_group_title', true)
                        );
                    }

                    // Set readmore false if excerpt type = full content
                    if (isset($arg['excerpt_type']) && $arg['excerpt_type'] === 'full' && ($key = array_search('read_more', $arg['items'])) !== false) {
                        unset($arg['items'][$key]);
                    }
                    if (!empty($scMeta['ignore_sticky_posts'])) {
                        $args['ignore_sticky_posts'] = true;
                    } else {
                        $args['wp_tpg_is_home'] = true;
                    }
                    $teamQuery = new WP_Query(apply_filters('tpg_sc_query_args', $args));
                    // Start layout
                    if ($teamQuery->have_posts()) {
                        $l = $offLoop = 0;
                        $offsetBigHtml = $offsetSmallHtml = null;
                        while ($teamQuery->have_posts()) {
                            $teamQuery->the_post();

                            if ($colStore == $l) {
                                if ($this->l4toggleLoadMore) {
                                    $this->l4toggleLoadMore = false;
                                } else {
                                    $this->l4toggleLoadMore = true;
                                }
                                $l = 0;
                            }
                            $l++;

                            $pID = get_the_ID();
                            $arg['pID'] = $pID;
                            $arg['title'] = rtTPG()->get_the_title($pID, $arg);
                            $arg['pLink'] = get_permalink();
                            $arg['toggle'] = $this->l4toggleLoadMore;
                            $arg['author'] = '<a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>';
                            $cc = wp_count_comments($pID);
                            $arg['date'] = get_the_date();
                            $arg['excerpt'] = rtTPG()->get_the_excerpt($pID, $arg);
                            $arg['categories'] = get_the_term_list($pID, 'category', null, '<span class="rt-separator">,</span>');
                            $arg['tags'] = get_the_term_list($pID, 'post_tag', null, '<span class="rt-separator">,</span>');
                            if ($isIsotope) {
                                $isotope_filter = isset($scMeta['isotope_filter'][0]) ? $scMeta['isotope_filter'][0] : null;
                                $termAs = wp_get_post_terms($pID, $isotope_filter,
                                    array("fields" => "all"));
                                $isoFilter = [];
                                if (!empty($termAs)) {
                                    foreach ($termAs as $term) {
                                        $isoFilter[] = "iso_" . $term->term_id;
                                        $isoFilter[] = "rt-item-" . esc_attr($term->slug);
                                    }
                                }
                                $arg['isoFilter'] = !empty($isoFilter) ? implode(" ", $isoFilter) : '';
                            }
                            $deptClass = null;
                            if (!empty($deptAs)) {
                                foreach ($deptAs as $dept) {
                                    $deptClass .= " " . $dept->slug;
                                }
                            }
                            if (comments_open()) {
                                $arg['comment'] = "<a href='" . get_comments_link($pID) . "'>{$cc->total_comments} </a>";
                            } else {
                                $arg['comment'] = "{$cc->total_comments}";
                            }
                            $imgSrc = null;

                            if ($isOffset) {
                                if ($offLoop == 0) {
                                    $arg['imgSrc'] = !$fImg ? rtTPG()->getFeatureImageSrc($pID, $fImgSize,
                                        $mediaSource,
                                        $defaultImgId,
                                        $customImgSize) : null;
                                    $arg['offset'] = 'big';
                                    $offsetBigHtml = rtTPG()->render('layouts/' . $layout, $arg, true);
                                } else {
                                    $arg['offset'] = 'small';
                                    $arg['offsetCol'] = array($dCol, $tCol, $mCol);
                                    $arg['imgSrc'] = !$fImg ? rtTPG()->getFeatureImageSrc($pID, 'thumbnail',
                                        $mediaSource,
                                        $defaultImgId,
                                        $customImgSize) : null;
                                    $offsetSmallHtml .= rtTPG()->render('layouts/' . $layout, $arg, true);
                                }
                            } else {
                                $arg['imgSrc'] = !$fImg ? rtTPG()->getFeatureImageSrc($pID, $fImgSize, $mediaSource,
                                    $defaultImgId,
                                    $customImgSize) : null;
                                $data .= rtTPG()->render('layouts/' . $layout, $arg, true);
                            }
                            $offLoop++;
                        }
                        if ($isOffset) {
                            $oDCol = rtTPG()->get_offset_col($dCol);
                            $oTCol = rtTPG()->get_offset_col($tCol);
                            $oMCol = rtTPG()->get_offset_col($mCol);
                            if ($layout == "offset03" || $layout == "offset04") {
                                $oDCol['big'] = $oTCol['big'] = $oDCol['small'] = $oTCol['small'] = 6;
                                $oMCol['big'] = $oMCol['small'] = 12;
                            }
                            $data .= "<div class='rt-col-md-{$oDCol['big']} rt-col-sm-{$oTCol['big']} rt-col-xs-{$oMCol['big']}'><div class='rt-row'>{$offsetBigHtml}</div></div>";
                            $data .= "<div class='rt-col-md-{$oDCol['small']} rt-col-sm-{$oTCol['small']} rt-col-xs-{$oMCol['small']}'><div class='rt-row offset-small-wrap'>{$offsetSmallHtml}</div></div>";
                        }
                        if (!empty($data)) {
                            $error = false;
                        }

                    } else {
                        if ($paged == 1) {
                            $error = false;
                        }
                        $not_found_text = isset($scMeta['tgp_not_found_text'][0]) && !empty($scMeta['tgp_not_found_text'][0]) ? esc_attr($scMeta['tgp_not_found_text'][0]) : __('No post found', 'the-post-grid');
                        $data = $msg = apply_filters('tpg_not_found_text', $not_found_text, $args, $scMeta);
                    }
                    $total_pares = $teamQuery->max_num_pages;
                    wp_reset_postdata();

                } else {
                    $msg = apply_filters('tpg_shortcode_not_found_error_text', __('Shortcode Id not defined', 'the-post-grid'));
                }
            } else {
                $msg = apply_filters('tpg_session_error_text', __('Session error', 'the-post-grid'));
            }
            wp_send_json(apply_filters(
                    'tpg_load_more_response',
                    array(
                        'error'       => $error,
                        'msg'         => $msg,
                        'data'        => $data,
                        'paged'       => $paged,
                        'total_pages' => $total_pares,
                        'l4toggle'    => ($this->l4toggleLoadMore ? 1 : null),
                        'args'        => $args
                    ))
            );
        }

        public function order_by_rating_post_clauses($args) {
            global $wpdb;
            $args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";
            $args['where'] .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";
            $args['join'] .= "
			LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

            $args['orderby'] = "average_rating {$this->order}, $wpdb->posts.post_date {$this->order}";

            $args['groupby'] = "$wpdb->posts.ID";

            return $args;
        }

    }
endif;