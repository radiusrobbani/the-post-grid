<?php

$html = $htmlDetail =null;

$html .= sprintf('<div class="%s" data-id="%d">', esc_attr(implode(" ", [$grid, $class])), $pID);
    $html .= '<div class="rt-holder">';
        $html .= '<div class="rt-row">';
			if($imgSrc) {
				$html .= "<div class='{$image_area}'>";
				$html .= '<div class="rt-img-holder">';
				$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$imgSrc}</a>";
				$html .= '</div>';
				$html .= '</div>';
			}else{
				$content_area = "rt-col-xs-12";
			}
            $html .= "<div class='{$content_area}'>";

                    if(in_array('title', $items)){
                        $htmlDetail .= "<h3 class='entry-title'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$title}</a></h3>";
                    }

                    $metaHtml = null;
                    
                    if(in_array('post_date', $items) && $date){
                        $metaHtml .= "<span class='date-meta'><i class='far fa-calendar-alt'></i> {$date}</span>";
                    }
                    if(in_array('author', $items)){
                        $metaHtml .= "<span class='author'><i class='fa fa-user'></i>{$author}</span>";
                    }
                    if(in_array('categories', $items) && $categories){
                        $metaHtml .= "<span class='categories-links'><i class='fas fa-folder-open'></i>{$categories}</span>";
                    }
                    if(in_array('tags', $items) && $tags){
                        $metaHtml .= "<span class='post-tags-links'><i class='fa fa-tags'></i>{$tags}</span>";
                    }
                    if(in_array('comment_count', $items)){
                        $metaHtml .= '<span class="comment-count"><i class="fas fa-comments"></i> '. $comment.'</span>';
                    }
                    if(!empty($metaHtml)){
                        $htmlDetail .="<div class='post-meta-user'>$metaHtml</div>";
                    }

                    if(in_array('excerpt', $items)){
                        $htmlDetail .= "<div class='tpg-excerpt'>{$excerpt}</div>";
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