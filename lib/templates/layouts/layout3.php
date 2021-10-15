<?php
$html = $htmlDetail = $catHtml = $metaHtml = null;

if(in_array('categories', $items) && $categories) {
    $catHtml .= "<span class='categories-links'>";
    if ($metaIcon) {
        $catHtml .= "<i class='fas fa-folder-open'></i>";
    }
    $catHtml .= "{$categories}</span>";
}
if(in_array('post_date', $items) && $date) {
    $metaHtml .= "<span class='date-meta'>";
    if ($metaIcon) {
        $metaHtml .= "<i class='far fa-calendar-alt'></i>";
    }
    $metaHtml .= "{$date}</span>";
}
if(in_array('author', $items)) {
    $metaHtml .= "<span class='author'>";
    if ($metaIcon) {
        $metaHtml .= "<i class='fa fa-user'></i>";
    }
    $metaHtml .= "{$author}</span>";
}
if (empty($category_position)) {
    $metaHtml .= $catHtml;
}
if(in_array('tags', $items) && $tags) {
    $metaHtml .= "<span class='post-tags-links'>";
    if ($metaIcon) {
        $metaHtml .= "<i class='fa fa-tags'></i>";
    }
    $metaHtml .= "{$tags}</span>";
}
if(in_array('comment_count', $items)) {
    $metaHtml .= '<span class="comment-count">';
    if ($metaIcon) {
        $metaHtml .= '<i class="fas fa-comments"></i>';
    }
    $metaHtml .= $comment.'</span>';
}

$html .= sprintf('<div class="%s" data-id="%d">', esc_attr(implode(" ", [$grid, $class])), $pID);
    $html .= '<div class="rt-holder">';
        $html .= '<div class="rt-row">';
			if($imgSrc) {
            $html .= "<div class='{$image_area}'>";
                $html .= '<div class="rt-img-holder">';
                    $html .= "<a data-id='{$pID}' class='{$anchorClass} rounded' href='{$pLink}'{$link_target}>{$imgSrc}</a>";
                    if (!empty($category_position) && $category_position != 'above_title') {
                        $html .= sprintf('<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml);
                    }
                $html .= '</div>';
            $html .= '</div>';
			} else {
				$content_area = "rt-col-xs-12";
			}
            $html .= "<div class='{$content_area}'>";
                    if (!empty($metaHtml) && $metaPosition == 'above_title') {
                        $htmlDetail .="<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
                    }
                    if ($category_position == 'above_title') {
                        $htmlDetail .= sprintf('<div class="cat-above-title %s">%s</div>', $category_style, $catHtml);
                    }
                    if(in_array('title', $items)){
                        $htmlDetail .= sprintf('<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag,$pID,$anchorClass,$pLink,$link_target,$title);
                    }

                    if(!empty($metaHtml) && (empty($metaPosition) || $metaPosition == 'above_excerpt')){
                        $htmlDetail .="<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
                    }

                    if(in_array('excerpt', $items)) {
                        $htmlDetail .= "<div class='post-content'><p>{$excerpt}</p></div>";
                    }

                    if (!empty($metaHtml) && $metaPosition == 'below_excerpt') {
                        $htmlDetail .="<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
                    }

                    if(in_array('read_more', $items)){
                        $htmlDetail .= "<span class='read-more {$btn_alignment_class}'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a></span>";
                    }
                if(!empty($htmlDetail)){
                    $html .= "<div class='rt-detail'>$htmlDetail</div>";
                }
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</div>';
$html .='</div>';

echo $html;