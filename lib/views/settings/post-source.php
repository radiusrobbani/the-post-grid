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
        <?php
            $html = null;
            $pt = get_post_meta($post->ID, 'tpg_post_type', true);
            $advFilters = rtTPG()->rtTPAdvanceFilters();
            echo $html;
        ?>
    </div>
</div>

<?php
echo rtTPG()->rtFieldGenerator(rtTPG()->stickySettings(), true);
?>
