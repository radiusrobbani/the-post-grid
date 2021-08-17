<?php
echo rtTPG()->rtFieldGenerator(rtTPG()->rtTPGPostType());
$sHtml = null;
$sHtml .= '<div class="field-holder">';
    $sHtml .= '<div class="field-label">Common filters</div>';
    $sHtml .= '<div class="field">';
        $sHtml .=rtTPG()->rtFieldGenerator(rtTPG()->rtTPGCommonFilterFields(), true);
    $sHtml .= '</div>';
$sHtml .= '</div>';

echo $sHtml;

?>

<div class='rt-tpg-filter-container'>
    <?php echo rtTPG()->rtFieldGenerator(rtTPG()->rtTPAdvanceFilters()); ?>
    <div class="rt-tpg-filter-holder">
        <h3 style="text-align: center">Advance filter options</h3>
        <?php
            $html = null;
            $pt = get_post_meta($post->ID, 'tpg_post_type', true);
            $advFilters = rtTPG()->rtTPAdvanceFilters();
            foreach($advFilters['post_filter']['options'] as $key => $filter){
                if($key == 'tpg_taxonomy'){
                    $html .= "<div class='rt-tpg-filter taxonomy tpg_taxonomy tpg-hidden'>";
                    if(isset($pt) && $pt){
                        $taxonomies = rtTPG()->rt_get_all_taxonomy_by_post_type($pt);
                        $taxA = get_post_meta($post->ID, 'tpg_taxonomy');
                            $post_filter = get_post_meta($post->ID, 'post_filter');
                            $html .= "<div class='taxonomy-field'>";
                            if(is_array($post_filter) && !empty($post_filter) && in_array('tpg_taxonomy', $post_filter) && !empty($taxonomies)) {
                                $html .= rtTPG()->rtFieldGenerator(
                                    array(
	                                    'tpg_taxonomy' => array(
                                            'type' => 'checkbox',
                                            'label' => 'Taxonomy',
                                            'id' => 'post-taxonomy',
                                            "multiple" => true,
                                            'options' => $taxonomies
                                        )
                                    )
                                );
                            }else{
                                $html .= '<div class="field-holder">No Taxonomy found</div>';
                            }
                            $html .= "</div>";
                            $html .= "<div class='rt-tpg-filter-item term-filter-item tpg-hidden'>";
                                $html .= '<div class="field-holder">';
                                    $html .= '<div class="field-label">Terms</div>';
                                    $html .= '<div class="field term-filter-holder">';
                                        if(is_array($taxA) && !empty($taxA)){
                                            foreach($taxA as $tax){

                                                $html .="<div class='term-filter-item-container {$tax}'>";
                                                    $html .= rtTPG()->rtFieldGenerator(
                                                        array(
	                                                        'term_'.$tax => array(
                                                                'type' => 'select',
                                                                'label' => ucfirst(str_replace('_', ' ', $tax)),
                                                                'class' => 'rt-select2 full',
                                                                'holderClass' => "term-filter-item {$tax}",
                                                                'value' => get_post_meta($post->ID, 'term_'.$tax),
                                                                "multiple" => true,
                                                                'options' => rtTPG()->rt_get_all_term_by_taxonomy($tax)
                                                            )
                                                        )
                                                    );
                                                    $html .= rtTPG()->rtFieldGenerator(
                                                        array(
	                                                        'term_operator_'.$tax => array(
                                                                'type' => 'select',
                                                                'label' => 'Operator',
                                                                'class' => 'rt-select2 full',
                                                                'holderClass' => "term-filter-item-operator {$tax}",
                                                                'value' => get_post_meta($post->ID, 'term_operator_'.$tax, true),
                                                                'options' => rtTPG()->rtTermOperators()
                                                            )
                                                        )
                                                    );
                                                $html .= "</div>";
                                            }
                                        }
                                    $html .= "</div>";
                                $html .= "</div>";

                                $html .= rtTPG()->rtFieldGenerator(
                                    array(
                                        'taxonomy_relation' => array(
                                            'type' => 'select',
                                            'label' => 'Relation',
                                            'class' => 'rt-select2',
                                            'holderClass' => "term-filter-item-relation ". (count($taxA) > 1 ? null : "hidden"),
                                            'value' => get_post_meta($post->ID, 'taxonomy_relation', true),
                                            'options' => rtTPG()->rtTermRelations()
                                        )
                                    )
                                );

                            $html .= "</div>";
                    }else{

                        $html .= "<div class='taxonomy-field'>";
                        $html .= "</div>";
                        $html .= "<div class='rt-tpg-filter-item'>";
                            $html .= '<div class="field-holder">';
                                $html .= '<div class="field-label">Terms</div>';
                                    $html .= '<div class="field term-filter-holder">';
                                    $html .= "</div>";
                                $html .= "</div>";
                            $html .= "</div>";
                            $html .= rtTPG()->rtFieldGenerator(
                                array(
                                        'taxonomy_relation' => array(
                                            'type' => 'select',
                                            'label' => 'Relation',
                                            'class' => 'rt-select2',
                                            'holderClass' => "term-filter-item-relation tpg-hidden",
                                            'default'   => 'OR',
                                            'options' => rtTPG()->rtTermRelations()
                                        )
                                )
                            );
                    }
                    $html .= "</div>";
                }else if($key == 'order'){
                    $html .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
                        $html .= "<div class='rt-tpg-filter-item'>";
                            $html .="<div class='field-holder'>";
                                $html .= "<div class='field-label'><label>Order Settings</label></div>";
                                    $html .="<div class='field'>";
                                    $html .= rtTPG()->rtFieldGenerator(
                                        array(
                                            'order_by' => array(
                                                'type' => 'select',
                                                'label' => 'Order by',
                                                'class' => 'rt-select2 filter-item',
                                                'value' => get_post_meta($post->ID, 'order_by', true),
                                                'options' => rtTPG()->rtPostOrderBy(false, true),
                                                'description' => __('If "Meta value", "Meta value Number" or "Meta value datetime" is chosen then meta key is required.', 'the-post-grid')
                                            )
                                        )
                                    );
                                    $html .= rtTPG()->rtFieldGenerator(
                                        array(
                                            'tpg_meta_key' => array(
                                                'type' => 'text',
                                                'label' => 'Meta key',
                                                'class' => 'rt-select2 filter-item',
                                                'holderClass' => 'tpg-hidden',
                                                'value' => get_post_meta($post->ID, 'tpg_meta_key', true)
                                            )
                                        )
                                    );
                                    $html .= rtTPG()->rtFieldGenerator(
                                        array(
                                            'order' => array(
                                                'type' => 'radio',
                                                'label' => 'Order',
                                                'class' => 'rt-select2 filter-item',
                                                'alignment' => 'vertical',
                                                'default' => 'DESC',
                                                'value' => get_post_meta($post->ID, 'order', true),
                                                'options' => rtTPG()->rtPostOrders()
                                            )
                                        )
                                    );
                                $html .="</div>";
                            $html .="</div>";
                        $html .= "</div>";
                    $html .= "</div>";
                }else if($key == 'author'){
                    $html .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
                        $html .= "<div class='rt-tpg-filter-item'>";
                            $html .= rtTPG()->rtFieldGenerator(
                                array(
                                    $key => array(
                                        'type' => 'select',
                                        'label' => 'Author',
                                        'class' => 'rt-select2 filter-item full',
                                        'value' => get_post_meta($post->ID, $key),
                                        "multiple" => true,
                                        'options' => rtTPG()->rt_get_users()
                                    )
                                )
                            );
                        $html .= "</div>";
                    $html .= "</div>";
                }else if($key == 'tpg_post_status'){
                    $html .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
                        $html .= "<div class='rt-tpg-filter-item'>";
                            $html .= rtTPG()->rtFieldGenerator(
                                array(
	                                $key => array(
                                        'type' => 'select',
                                        'label' => 'Status',
                                        'class' => 'rt-select2 filter-item full',
                                        'default' => array('publish'),
                                        'value' => get_post_meta($post->ID, $key),
                                        "multiple" => true,
                                        'options' => rtTPG()->rtTPGPostStatus()
                                    )
                                )
                            );
                        $html .= "</div>";
                    $html .= "</div>";
                }else if($key == 's'){
                    $html .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
                        $html .= "<div class='rt-tpg-filter-item'>";
                            $html .= rtTPG()->rtFieldGenerator(
                                array(
	                                $key => array(
                                    'type' => 'text',
                                    'label' => 'Search keyword',
                                    'class' => 'filter-item full',
                                    'value' => get_post_meta($post->ID, $key, true)
                                    )
                                )
                            );
                        $html .= "</div>";
                    $html .= "</div>";
                }else if($key == 'date_range'){
                    $range_start = get_post_meta($post->ID, 'date_range_start', true);
                    $range_end = get_post_meta($post->ID, 'date_range_end', true);
                    $range_value = [
                        'start' => $range_start,
                        'end' => $range_end
                    ];
                    $html .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
                        $html .= "<div class='rt-tpg-filter-item'>";
                            $html .= rtTPG()->rtFieldGenerator(
                                array(
	                                $key=> array(
                                    'type' => 'date_range',
                                    'label' => 'Date Range',
                                    'class' => 'filter-item full rt-date-range',
                                    'value' => $range_value,
                                    'description' => "Date format should be 'yyyy-mm-dd'",
                                    )
                                )
                            );
                        $html .= "</div>";
                    $html .= "</div>";
                }

            }
            echo $html;
        ?>
    </div>
</div>
