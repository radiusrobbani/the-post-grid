/**
 * BLOCK: bootstrap-alert
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
// import './scss/style.scss';
// import './scss/editor.scss';

const {__} = wp.i18n; // Import __() from wp.i18n
const {registerBlockType} = wp.blocks; // Import registerBlockType() from wp.blocks
const {SelectControl, PanelBody, PanelRow, TextControl} = wp.components;
const {RichText, InspectorControls} = wp.editor;

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType('rttpg/post-grid-pro', {
    title: __('The Post Grid Pro', "the-post-grid-pro"),
    icon: 'grid-view',
    category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
    keywords: [
        __('Post Grid pro', "the-post-grid-pro"),
        __('The Post Grid pro', "the-post-grid-pro"),
        __('the-post-grid-pro', "the-post-grid-pro"),
    ],
    attributes: {
        gridId: {
            type: 'number',
            default: 0,
        }
    },

    /**
     * The edit function describes the structure of your block in the context of the editor.
     * This represents what the editor will render when the block is used.
     *
     * The "edit" property must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     */
    edit: function (props) {
        let {attributes: {gridId}, setAttributes} = props;
        let gridTitle = "";
        let options = [{value: 0, label: __("Select one", "the-post-grid-pro")}];
        if (rttpgGB.short_codes) {
            for (const [id, title] of Object.entries(rttpgGB.short_codes)) {
                options.push({
                    value: id,
                    label: title
                });
                if (gridId && Number(id) === gridId) {
                    gridTitle = title;
                }
            }
        }
        return (
            [
                <InspectorControls>
                    This text will show when the box is selected
                    <PanelRow>
                        <SelectControl
                            label={__('Select a grid:')}
                            options={options}
                            value={gridId}
                            onChange={(val) => setAttributes({gridId: Number(val)})}
                        />
                    </PanelRow>
                </InspectorControls>
                ,
                <div className={props.className}>
                    {!gridId ? (<p>Please select a shortcode from block settings</p>) : (
                        <div><span><img src={rttpgGB.icon}/></span> <span> {__('The Post Grid pro', "the-post-grid-pro")} ( {gridTitle} )</span></div>
                    )}
                </div>
            ]
        );
    },

    /**
     * The save function defines the way in which the different attributes should be combined
     * into the final markup, which is then serialized by Gutenberg into post_content.
     *
     * The "save" property must be specified and must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     */
    save: function ({attributes: {gridId}}) {
        return null;
    },
});