<?php echo rtTPG()->rtFieldGenerator( rtTPG()->rtTPGStyleFields(), true ); ?>

<div class="field-holder button-color-style-wrapper">
    <div class="field-label"><?php _e( 'Button Color', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo rtTPG()->rtFieldGenerator( rtTPG()->rtTPGStyleButtonColorFields(), true ); ?>
        </div>
    </div>
</div>

<div class="field-holder widget-heading-stle-wrapper">
    <div class="field-label"><?php _e( 'Widget Heading', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo rtTPG()->rtFieldGenerator( rtTPG()->rtTPGStyleHeading(), true ); ?>
        </div>
    </div>
</div>

<div class="field-holder full-area-style-wrapper">
    <div class="field-label"><?php _e( 'Full Area / Section', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo rtTPG()->rtFieldGenerator( rtTPG()->rtTPGStyleFullArea(), true ); ?>
        </div>
    </div>
</div>

<div class="field-holder content-style-wrapper">
    <div class="field-label"><?php _e( 'Content Wrap', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo rtTPG()->rtFieldGenerator( rtTPG()->rtTPGStyleContentWrap(), true ); ?>
        </div>
    </div>
</div>

<div class="field-holder category-style-wrapper">
    <div class="field-label"><?php _e( 'Category', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo rtTPG()->rtFieldGenerator( rtTPG()->rtTPGStyleCategory(), true ); ?>
        </div>
    </div>
</div>

<?php echo rtTPG()->rtSmartStyle( rtTPG()->extraStyle() ); ?>


