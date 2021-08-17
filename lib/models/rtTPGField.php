<?php

if ( ! class_exists( 'rtTPGField' ) ):
	class rtTPGField {
		private $type;
		private $name;
		private $value;
		private $default;
		private $label;
		private $id;
		private $class;
		private $holderClass;
		private $description;
		private $options;
		private $option;
		private $optionLabel;
		private $attr;
		private $multiple;
		private $alignment;
		private $placeholder;
		private $blank;

		function __construct() {
		}

		private function setArgument( $key, $attr ) {
			$this->type     = isset( $attr['type'] ) ? ( $attr['type'] ? $attr['type'] : 'text' ) : 'text';
			$this->multiple = isset( $attr['multiple'] ) ? ( $attr['multiple'] ? $attr['multiple'] : false ) : false;
			$this->name     = ! empty( $key ) ? $key : null;
			$id             = isset( $attr['id'] ) ? $attr['id'] : null;
			$this->id       = ! empty( $id ) ? $id : $this->name;
			$this->default  = isset( $attr['default'] ) ? $attr['default'] : null;
			$this->value    = isset( $attr['value'] ) ? ( $attr['value'] ? $attr['value'] : null ) : null;

			if ( ! $this->value ) {
				$post_id = get_the_ID();
				if ( ! rtTPG()->meta_exist( $post_id, $this->name ) ) {
					$this->value = $this->default;
				} else {
					if ( $this->multiple ) {
						$this->value = get_post_meta( $post_id, $this->name );
					} else {
						$this->value = get_post_meta( $post_id, $this->name, true );
					}
				}
			}

			$this->label       = isset( $attr['label'] ) ? ( $attr['label'] ? $attr['label'] : null ) : null;
			$this->class       = isset( $attr['class'] ) ? ( $attr['class'] ? $attr['class'] : null ) : null;
			$this->holderClass = isset( $attr['holderClass'] ) ? ( $attr['holderClass'] ? $attr['holderClass'] : null ) : null;
			$this->placeholder = isset( $attr['placeholder'] ) ? ( $attr['placeholder'] ? $attr['placeholder'] : null ) : null;
			$this->description = isset( $attr['description'] ) ? ( $attr['description'] ? $attr['description'] : null ) : null;
			$this->options     = isset( $attr['options'] ) ? ( $attr['options'] ? $attr['options'] : array() ) : array();
			$this->option      = isset( $attr['option'] ) ? ( $attr['option'] ? $attr['option'] : null ) : null;
			$this->optionLabel = isset( $attr['optionLabel'] ) ? ( $attr['optionLabel'] ? $attr['optionLabel'] : null ) : null;
			$this->attr        = isset( $attr['attr'] ) ? ( $attr['attr'] ? $attr['attr'] : null ) : null;
			$this->alignment   = isset( $attr['alignment'] ) ? ( $attr['alignment'] ? $attr['alignment'] : null ) : null;
			$this->blank       = ! empty( $attr['blank'] ) ? $attr['blank'] : null;

		}

		public function Field( $key, $attr = array() ) {
			$this->setArgument( $key, $attr );
			$holderId = $this->name . "_holder";

			if (!rtTPG()->hasPro()) {
			    $class = $this->holderClass;
            } else {
			    $class = str_replace('pro-field', '', $this->holderClass);
            }
			$html     = null;
			$html     .= "<div class='field-holder {$class}' id='{$holderId}'>";

			$holderClass = explode(' ', $this->holderClass);

			if ( $this->label ) {
				$html .= "<div class='field-label'>";
				$html .= "<label>{$this->label}</label>";
				if (in_array('pro-field', $holderClass) && ! rtTPG()->hasPro()) {
				    $html .= '<span class="rttpg-tooltip">[Pro]<span class="rttpg-tooltip-text">'.esc_html__('Premium Option', 'the-post-grid').'</span></span>';
                }
				$html .= "</div>";
			}
			$html .= "<div class='field'>";
			switch ( $this->type ) {
				case 'text':
					$html .= $this->text();
					break;

				case 'url':
					$html .= $this->url();
					break;

				case 'number':
					$html .= $this->number();
					break;

				case 'select':
					$html .= $this->select();
					break;

				case 'textarea':
					$html .= $this->textArea();
					break;

				case 'checkbox':
					$html .= $this->checkbox();
					break;

				case 'radio':
					$html .= $this->radioField();
					break;

				case 'date_range':
					$html .= $this->dateRange();
					break;

				case 'script':
					$html .= $this->script();
					break;

				case 'image':
					$html .= $this->image();
					break;

				case 'image_size':
					$html .= $this->imageSize();
					break;
			}
			if ( $this->description ) {
				$html .= "<p class='description'>{$this->description}</p>";
			}
			$html .= "</div>"; // field
			$html .= "</div>"; // field holder

			return $html;
		}

		private function text() {
            $holderClass = explode(' ', $this->holderClass);
            $atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
			$h = null;
			$h .= "<input {$atts}
                    type='text'
                    class='{$this->class}'
                    id='{$this->id}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";

			return $h;
		}

		private function script() {
			$type = "script";
			if ( $this->id == "custom-css" ) {
				$type = "css";
			}
			$h = null;
			$h .= '<div class="rt-script-wrapper" data-type="' . $type . '">';
			$h .= '<div class="rt-script-container">';
			$h .= "<div name='{$this->name}' id='ret-" . mt_rand() . "' class='rt-script'>";
			$h .= '</div>';
			$h .= '</div>';

			$h .= "<textarea
                        style='display: none;'
                        class='rt-script-textarea'
                        id='{$this->id}'
                        name='{$this->name}'
                        >{$this->value}</textarea>";
			$h .= '</div>';

			return $h;
		}

		private function url() {
			$h = null;
			$h .= "<input
                    type='url'
                    class='{$this->class}'
                    id='{$this->id}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";

			return $h;
		}

		private function number() {
            $holderClass = explode(' ', $this->holderClass);
            $atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
			$h = null;
			$h .= "<input {$atts}
                    type='number'
                    class='{$this->class}'
                    id='{$this->id}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";

			return $h;
		}

		private function select() {
            $holderClass = explode(' ', $this->holderClass);
            $atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
			$h = null;
			if ( $this->multiple ) {
				$this->attr  = " style='min-width:160px;'";
				$this->name  = $this->name . "[]";
				$this->attr  = $this->attr . " multiple='multiple'";
				$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : array() );
			} else {
				$this->value = array( $this->value );
			}

			$h .= "<select {$atts} name='{$this->name}' id='{$this->id}' class='{$this->class}' {$this->attr}>";
			if ( $this->blank ) {
				$h .= "<option value=''>{$this->blank}</option>";
			}
			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$slt = ( in_array( $key, $this->value ) ? "selected" : null );
					$h   .= "<option {$slt} value='{$key}'>{$value}</option>";
				}
			}
			$h .= "</select>";

			return $h;
		}

		private function textArea() {
            $holderClass = explode(' ', $this->holderClass);
            $atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
			$h = null;
			$h .= "<textarea {$atts}
                    class='{$this->class} rt-textarea'
                    id='{$this->id}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    >{$this->value}</textarea>";

			return $h;
		}

		private function image() {
            $holderClass = explode(' ', $this->holderClass);
			$h   = null;
			$h   .= "<div class='rt-image-holder'>";
			$h   .= "<input type='hidden' name='{$this->name}' value='{$this->value}' id='{$this->id}' class='hidden-image-id' />";
			$img = null;
			$c   = "hidden";
			if ( $id = absint( $this->value ) ) {
				$aImg = wp_get_attachment_image_src( $id, 'thumbnail' );
				$img  = "<img src='{$aImg[0]}' >";
				$c    = null;
			}

			$h .= "<div class='rt-image-preview'>{$img}<span class='dashicons dashicons-plus-alt rtAddImage'></span><span class='dashicons dashicons-trash rtRemoveImage {$c}'></span></div>";

			$h .= "</div>";

			return $h;
		}

		private function imageSize() {
			$width    = ( ! empty( $this->value[0] ) ? absint( $this->value[0] ) : null );
			$height   = ( ! empty( $this->value[1] ) ? absint( $this->value[1] ) : null );
			$cropV    = ( ! empty( $this->value[2] ) ? $this->value[2] : 'soft' );
			$h        = null;
			$h        .= "<div class='rt-image-size-holder'>";
			$h        .= "<div class='rt-image-size-width rt-image-size'>";
			$h        .= "<label>Width</label>";
			$h        .= "<input type='number' name='{$this->name}[]' value='{$width}' />";
			$h        .= "</div>";
			$h        .= "<div class='rt-image-size-height rt-image-size'>";
			$h        .= "<label>Height</label>";
			$h        .= "<input type='number' name='{$this->name}[]' value='{$height}' />";
			$h        .= "</div>";
			$h        .= "<div class='rt-image-size-crop rt-image-size'>";
			$h        .= "<label>Crop</label>";
			$h        .= "<select name='{$this->name}[]' class='rt-select2'>";
			$cropList = rtTPG()->imageCropType();
			foreach ( $cropList as $crop => $cropLabel ) {
				$cSl = ( $crop == $cropV ? "selected" : null );
				$h   .= "<option value='{$crop}' {$cSl}>{$cropLabel}</option>";
			}
			$h .= "</select>";
			$h .= "</div>";
			$h .= "</div>";

			return $h;
		}

		private function checkbox() {
            $holderClass = explode(' ', $this->holderClass);
            $atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
			$h = null;
			if ( $this->multiple ) {
				$this->name  = $this->name . "[]";
				$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : array() );
			}
			if ( $this->multiple ) {
				$h .= "<div class='checkbox-group {$this->alignment}' id='{$this->id}'>";
				if ( is_array( $this->options ) && ! empty( $this->options ) ) {
					foreach ( $this->options as $key => $value ) {
						$checked = ( in_array( $key, $this->value ) ? "checked" : null );
						$h       .= "<label for='{$this->id}-{$key}'>
                                <input {$atts} type='checkbox' id='{$this->id}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                                </label>";
					}
				}
				$h .= "</div>";
			} else {
				$checked = ( $this->value ? "checked" : null );
				$h       .= "<label><input type='checkbox' {$checked} id='{$this->id}' name='{$this->name}' value='1' />{$this->option}</label>";
			}

			return $h;
		}

		private function radioField() {
            $holderClass = explode(' ', $this->holderClass);
            $atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
			$h = null;
			$h .= "<div class='radio-group {$this->alignment}' id='{$this->id}'>";
			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$checked = ( $key == $this->value ? "checked" : null );
					if(empty($checked)) {
                        $checked = ( $key == $this->default ? "checked" : null );
                    }
					$h       .= "<label for='{$this->name}-{$key}'>
                            <input {$atts} type='radio' id='{$this->id}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                            </label>";
				}
			}
			$h .= "</div>";

			return $h;
		}

		private function dateRange() {
			$h          = null;
			$this->name = ( $this->name ? $this->name : "date-range-" . rand( 0, 1000 ) );
			$h          .= "<div class='date-range-container' id='{$this->id}'>";
			$h          .= "<div class='date-range-content start'><span>" . __( "Start", 'the-post-grid' ) . "</span><input
                            type='text'
                            class='date-range date-range-start {$this->class}'
                            id='{$this->id}-start'
                            value='{$this->value['start']}'
                            name='{$this->name}_start'
                            placeholder='{$this->placeholder}'
                            {$this->attr}
                            /></div>";
			$h          .= "<div class='date-range-content end'><span>" . __( "End", 'the-post-grid' ) . "</span><input
                            type='text'
                            class='date-range date-range-end {$this->class}'
                            id='{$this->id}-end'
                            value='{$this->value['end']}'
                            name='{$this->name}_end'
                            placeholder='{$this->placeholder}'
                            {$this->attr}
                            /></div>";
			$h          .= "</div>";

			return $h;
		}

	}
endif;