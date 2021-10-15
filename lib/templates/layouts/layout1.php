<?php
$html = $htmlDetail = $metaHtml = $iTitle = $catHtml = $postMetaTop = $postMetaMid = null;

if(in_array('categories', $items) && $categories) {
    $catHtml .= "<span class='categories-links'>";
    if ($metaIcon) {
        $catHtml .= "<i class='fas fa-folder-open'></i>";
    }
    $catHtml .= "{$categories}</span>";
}
if(in_array('author', $items)){
    $postMetaTop .= "<span class='author'>";
    if ($metaIcon) {
        $postMetaTop .= "<i class='fa fa-user'></i>";
    }
    $postMetaTop .= "{$author}</span>";
}
if(in_array('post_date', $items) && $date){
    $postMetaTop .= "<span class='date'>";
    if ($metaIcon) {
        $postMetaTop .= "<i class='far fa-calendar-alt'></i>";
    }
    $postMetaTop .= "{$date}</span>";
}
if(empty($category_position)) {
    $postMetaTop .= $catHtml;
}
if(in_array('tags', $items) && $tags){
    $postMetaTop .= "<span class='post-tags-links'>";
    if ($metaIcon) {
        $postMetaTop .= "<i class='fa fa-tags'></i>";
    }
    $postMetaTop .= "{$tags}</span>";
}
if(in_array('comment_count', $items)){
    $postMetaTop .= '<span class="comment-count">';
    if ($metaIcon) {
        $postMetaTop .= '<i class="fas fa-comments"></i>';
    }
    $postMetaTop .= $comment.'</span>';
}
if(in_array('title', $items)) {
    $iTitle = sprintf('<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag,$pID,$anchorClass,$pLink,$link_target,$title);
}

$html .= sprintf('<div class="%s" data-id="%d">', esc_attr(implode(" ", [$grid, $class])), $pID);
    $html .= '<div class="rt-holder">';
            if($tpg_title_position == 'above') {
                if ($category_position == 'above_title') {
                    $html .= sprintf('<div class="cat-above-title %s">%s</div>', $category_style, $catHtml);
                }
                if (!empty($postMetaTop) && $metaPosition == 'above_title') {
                    $html .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
                }
                $html .= sprintf('<div class="rt-detail rt-with-title">%s</div>', $iTitle);
            }
            if($imgSrc) {
                $html .= '<div class="rt-img-holder">';
                $html .= sprintf('<a data-id="%s" class="%s" href="%s"%s>%s</a>', $pID, $anchorClass, $pLink, $link_target, $imgSrc);
                if (!empty($category_position) && $category_position != 'above_title') {
                    $html .= sprintf('<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml);
                }
                $html .= '</div>';
            }

            if ($category_position == 'above_title' && $tpg_title_position != 'above') {
                $htmlDetail .= sprintf('<div class="cat-above-title %s">%s</div>', $category_style, $catHtml);
            }

            if (!empty($postMetaTop) && $metaPosition == 'above_title' && $tpg_title_position != 'above') {
                $htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
            }
            if($tpg_title_position != 'above'){
                $htmlDetail .= $iTitle;
            }
            if(!empty($postMetaTop) && (empty($metaPosition) || $metaPosition == 'above_excerpt')){
                $htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
            }
            if(!empty($postMetaMid)){
                $htmlDetail .= "<div class='post-meta-tags'>{$postMetaMid}</div>";
            }
            if(in_array('excerpt', $items)){
                $htmlDetail .= "<div class='tpg-excerpt'>{$excerpt}</div>";
            }
            if (!empty($postMetaTop) && $metaPosition == 'below_excerpt') {
                $htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
            }
            $postMetaBottom = null;

            if(in_array('read_more', $items)) {
                $postMetaBottom .= "<span class='read-more'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a></span>";
            }
            if(!empty($postMetaBottom)){
                $htmlDetail .= "<div class='post-meta {$btn_alignment_class}'>$postMetaBottom</div>";
            }
        if(!empty($htmlDetail)){
            $html .="<div class='rt-detail'>$htmlDetail</div>";
        }
    $html .= '</div>';
$html .='</div>';

echo $html;