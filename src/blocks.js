
//  Import CSS.
import './scss/style.scss';
import './scss/editor.scss';

const {__} = wp.i18n; // Import __() from wp.i18n
const {registerBlockType} = wp.blocks; // Import registerBlockType() from wp.blocks
const {SelectControl, PanelRow} = wp.components;
const {InspectorControls} = wp.editor;


registerBlockType('rttpg/post-grid', {
    title: __('The Post Grid', "the-post-grid"),
    icon: 'grid-view',
    category: 'common',
    keywords: [
        __('Post Grid', "the-post-grid"),
        __('The Post Grid', "the-post-grid"),
        __('the-post-grid', "the-post-grid"),
    ],
    attributes: {
        gridId: {
            type: 'number',
            default: 0,
        }
    },

    edit: function (props) {
        let {attributes: {gridId}, setAttributes} = props;
        let gridTitle = "";
        let options = [{value: 0, label: __("Select one", "the-post-grid")}];
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
                        <div><span><img src={rttpgGB.icon}/></span> <span>{__('The Post Grid', "the-post-grid")} ( {gridTitle} )</span></div>
                    )}
                </div>
            ]
        );
    },
    save: function ({attributes: {gridId}}) {
        return null;
    },
});