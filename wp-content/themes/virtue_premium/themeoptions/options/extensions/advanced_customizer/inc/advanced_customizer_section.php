<?php


    /**
     * Customizer section representing widget area (sidebar).
     *
     * @package    WordPress
     * @subpackage Customize
     * @since      4.1.0
     * @see        WP_Customize_Section
     */
    class Redux_Advanced_Customizer_Section extends Redux_Customizer_Section {

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
                'panel',
                'type'
            ) );
            $array['content']        = $this->get_content();
            $array['active']         = $this->active();
            $array['instanceNumber'] = $this->instance_number;

            if ( $this->panel ) {
                /* translators: &#9656; is the unicode right-pointing triangle, and %s is the section title in the Customizer */
                $array['customizeAction'] = sprintf( __( 'Customizing &#9656; %s', 'redux-framework' ), esc_html( $this->manager->get_panel( $this->panel )->title ) );
            } else {
                $array['customizeAction'] = __( 'Customizing', 'redux-framework' );
            }

            // BEGIN Redux Additions
            $array['width'] = isset( $this->section['customizer_width'] ) ? $this->section['customizer_width'] : '';
            $array['icon']  = ( isset( $this->section['icon'] ) && ! empty( $this->section['icon'] ) ) ? $this->section['icon'] : 'hide';
            // EMD Redux Additions

            return $array;
        }

        /**
         * An Underscore (JS) template for rendering this section.
         * Class variables for this section class are available in the `data` JS object;
         * export custom variables by overriding {@see WP_Customize_Section::json()}.
         *
         * @see   WP_Customize_Section::print_template()
         * @since 4.3.0
         */
        protected function render_template() {
            ?>
            <li id="accordion-section-{{ data.id }}" class="redux-section accordion-section control-section control-section-{{ data.type }}" data-width="{{ data.width }}">
                <h3 class="accordion-section-title" tabindex="0">
                    <# if ( data.icon ) { #><i class="{{ data.icon }}"></i> <# } #>{{ data.title }}
                    <span class="screen-reader-text"><?php _e( 'Press return or enter to open', 'redux-framework' ); ?></span>
                </h3>
                <ul class="accordion-section-content redux-main">
                    <li class="customize-section-description-container">
                        <div class="customize-section-title">
                            <button class="customize-section-back" tabindex="-1">
                                <span class="screen-reader-text"><?php _e( 'Back', 'redux-framework' ); ?></span>
                            </button>
                            <h3>
							<span class="customize-action">
								{{{ data.customizeAction }}}
							</span> {{ data.title }}
                            </h3>
                        </div>
                        <# if ( data.description ) { #>
                            <p class="description customize-section-description">{{{ data.description }}}</p>
                            <# } #>
                    </li>
                </ul>
            </li>
            <?php
        }

        /**
         * Render the section, and the controls that have been added to it.
         *
         * @since 3.4.0
         */
        protected function render_fallback() {
            $classes = 'accordion-section redux-section control-section control-section-' . $this->type;
            ?>
            <li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>" data-width="<?php echo isset( $this->section['customizer_width'] ) ? $this->section['customizer_width'] : '' ;?>">
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
                    <span class="screen-reader-text"><?php _e( 'Press return or enter to expand', 'redux-framework' ); ?></span>
                </h3>
                <ul class="accordion-section-content redux-main">
                    <?php if ( ! empty( $this->description ) ) : ?>
                        <li class="customize-section-description-container">
                            <p class="description customize-section-description legacy"><?php echo $this->description; ?></p>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php
        }
    }