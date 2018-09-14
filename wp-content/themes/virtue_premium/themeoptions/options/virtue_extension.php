<?php
if ( class_exists( 'Redux' ) ) {
	$opt_name = 'virtue_premium';
    Redux::setExtensions( $opt_name, dirname( __FILE__ ) . '/extensions/' );
}
// Just keep from getting errors.

if (class_exists('WP_Customize_Control')) {

if ( ! class_exists( 'Redux_Advanced_Customizer_Control' ) ) {
        class Redux_Advanced_Customizer_Control extends WP_Customize_Control {

            public function render() {
                $this->redux_id = str_replace( 'customize-control-', '', 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) ) );
                $class          = 'customize-control redux-group-tab redux-field customize-control-' . $this->type;
                ?>
                <li id="<?php echo esc_attr( $this->redux_id ); ?>" class="<?php echo esc_attr( $class ); ?>">
                    <input type="hidden" data-id="<?php echo $this->id; ?>" class="redux-customizer-input"
                           id="customizer_control_id_<?php echo $this->redux_id; ?>" <?php echo $this->link() ?>
                           value=""/>
                    <?php $this->render_content(); ?>
                </li>
            <?php

            }

            public function render_content() {
                do_action( 'redux/advanced_customizer/control/render/' . $this->redux_id, $this );
            }


            public function label() {
                // The label has already been sanitized in the Fields class, no need to re-sanitize it.
                echo $this->label;

            }

            public function description() {
                if ( ! empty( $this->description ) ) {
                    // The description has already been sanitized in the Fields class, no need to re-sanitize it.
                    echo '<span class="description customize-control-description">' . $this->description . '</span>';
                }
            }

            public function title() {
                echo '<span class="customize-control-title">';
                $this->label();
                $this->description();
                echo '</span>';
            }

        }
    }
}
