<?php

    /**
     * Customizer section representing widget area (sidebar).
     *
     * @package    WordPress
     * @subpackage Customize
     * @since      4.1.0
     * @see        WP_Customize_Section
     */
    class Redux_Advanced_Customizer_Panel extends Redux_Customizer_Panel {

        /**
         * Gather the parameters passed to client JavaScript via JSON.
         *
         * @since 4.1.0
         * @return array The array to be exported to the client as JSON.
         */
        public function json() {
            $array                   = wp_array_slice_assoc( (array) $this, array(
                'id',
                'title',
                'description',
                'priority',
                'type'
            ) );
            $array['content']        = $this->get_content();
            $array['active']         = $this->active();
            $array['instanceNumber'] = $this->instance_number;
            // BEGIN Redux Additions
            $array['width'] = isset( $this->section['customizer_width'] ) ? $this->section['customizer_width'] : '';
            $array['icon']  = ( isset( $this->section['icon'] ) && ! empty( $this->section['icon'] ) ) ? $this->section['icon'] : 'hide';
            // EMD Redux Additions
            return $array;
        }

        protected function render_fallback() {
            $classes = 'accordion-section redux-main redux-panel control-section control-panel control-panel-' . $this->type;
            ?>
            <li id="accordion-panel-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>" data-width="<?php echo isset( $this->section['customizer_width'] ) ? $this->section['customizer_width'] : '' ;?>">
                <h3 class="accordion-section-title" tabindex="0">
                    <?php if ( isset( $this->section['icon'] ) && ! empty( $this->section['icon'] ) ) : ?>
                        <i class="<?php echo $this->section['icon']; ?>"></i>
                    <?php endif; ?>
                    <?php echo wp_kses( $this->title, array(
                        'em'     => array(),
                        'i'      => array(),
                        'strong' => array(),
                        'span'   => array(
                            'class' => array(),
                            'style' => array(),
                        ),
                    ) ); ?>
                    <span class="screen-reader-text"><?php _e( 'Press return or enter to open this panel', 'redux-framework' ); ?></span>
                </h3>
                <ul class="accordion-sub-container control-panel-content">
                    <table class="form-table">
                        <tbody><?php $this->render_content(); ?></tbody>
                    </table>
                </ul>
            </li>
            <?php
        }

        /**
         * An Underscore (JS) template for rendering this panel's container.
         * Class variables for this panel class are available in the `data` JS object;
         * export custom variables by overriding {@see WP_Customize_Panel::json()}.
         *
         * @see   WP_Customize_Panel::print_template()
         * @since 4.3.0
         */
        protected function render_template() {
            ?>
            <li id="accordion-panel-{{ data.id }}" class="accordion-section redux-panel control-section control-panel control-panel-{{ data.type }}" data-width="{{ data.width }}">
                <h3 class="accordion-section-title" tabindex="0">
                    <# if ( data.icon ) { #><i class="{{ data.icon }}"></i> <# } #>{{ data.title }}
                    <span class="screen-reader-text"><?php _e( 'Press return or enter to open this panel', 'redux-framework' ); ?></span>
                </h3>
                <ul class="accordion-sub-container control-panel-content"></ul>
            </li>
            <?php
        }
    }