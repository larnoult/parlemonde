<?php
defined('ABSPATH') || exit;

if (version_compare(phpversion(), '5.3', '<')) {
    return;
}

/**
 * Newsletter widget version 2.0: it'll replace the old version left for compatibility.
 */
class NewsletterWidget extends WP_Widget {

    function __construct() {
        parent::__construct(false, $name = 'Newsletter', array('description' => 'Newsletter widget to add subscription forms on sidebars'), array('width' => '350px'));
    }

    static function get_widget_form($instance) {

        $field_wrapper_tag = 'div';
        if (!isset($instance['nl']) || !is_array($instance['nl']))
            $instance['nl'] = array();

        $instance = array_merge(array('lists_layout' => '',
            'lists_empty_label' => '',
            'lists_field_label' => ''), $instance);

        $options_profile = get_option('newsletter_profile');
        $form = '';

        $form .= '<div class="tnp tnp-widget">';
        $form .= NewsletterSubscription::instance()->get_subscription_form('widget', null, array(
            'list' => implode(',', $instance['nl']),
            'lists_field_layout' => $instance['lists_layout'],
            'lists_field_empty_label' => $instance['lists_empty_label'],
            'lists_field_label' => $instance['lists_field_label'],
        ));
        $form .= "</div>\n";

        return $form;
    }

    function widget($args, $instance) {
        
        $newsletter = Newsletter::instance();

        extract($args);

        if (empty($instance))
            $instance = array();
        $instance = array_merge(array('text' => '', 'title' => ''), $instance);

        echo $before_widget;

        // Filters are used for WPML
        if (!empty($instance['title'])) {
            $title = apply_filters('widget_title', $instance['title'], $instance);
            echo $before_title . $title . $after_title;
        }

        $buffer = apply_filters('widget_text', $instance['text'], $instance);
        $options = get_option('newsletter');
        $options_profile = get_option('newsletter_profile');

        if (stripos($instance['text'], '<form') === false) {

            $form = NewsletterWidget::get_widget_form($instance);

            // Canot user directly the replace, since the form is different on the widget...
            if (strpos($buffer, '{subscription_form}') !== false)
                $buffer = str_replace('{subscription_form}', $form, $buffer);
            else {
                if (strpos($buffer, '{subscription_form_') !== false) {
                    // TODO: Optimize with a method to replace only the custom forms
                    $buffer = $newsletter->replace($buffer);
                } else {
                    $buffer .= $form;
                }
            }
        } else {
            $buffer = str_ireplace('<form', '<form method="post" action="' . esc_attr($newsletter->get_subscribe_url())  . '" onsubmit="return newsletter_check(this)"', $buffer);
            $buffer = str_ireplace('</form>', '<input type="hidden" name="nr" value="widget"/></form>', $buffer);
        }

        // That replace all the remaining tags
        $buffer = $newsletter->replace($buffer);

        echo $buffer;
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        if (!is_array($instance)) {
            $instance = array();
        }
        $instance = array_merge(array('title' => '', 'text' => '', 'lists_layout' => '', 'lists_empty_label' => '', 'lists_field_label' => ''), $instance);
        $options_profile = get_option('newsletter_profile');
        if (!isset($instance['nl']) || !is_array($instance['nl']))
            $instance['nl'] = array();
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title')?>:
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
            </label>

            <label for="<?php echo $this->get_field_id('text'); ?>">
                Introduction:
                <textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_html($instance['text']); ?></textarea>
            </label>

            <label>
                Show lists as:
                <select name="<?php echo $this->get_field_name('lists_layout'); ?>" id="<?php echo $this->get_field_id('lists_layout'); ?>" style="width: 100%">
                    <option value="">Checkboxes</option>
                    <option value="dropdown" <?php echo $instance['lists_layout'] == 'dropdown' ? 'selected' : '' ?>>Dropdown</option>
                </select>
            </label>
            <br>
            <label for="<?php echo $this->get_field_id('lists_empty_label'); ?>">
                First dropdown entry label:
                <input class="widefat" id="<?php echo $this->get_field_id('lists_empty_label'); ?>" name="<?php echo $this->get_field_name('lists_empty_label'); ?>" type="text" value="<?php echo esc_attr($instance['lists_empty_label']); ?>" />
            </label>
            <br>
            <label for="<?php echo $this->get_field_id('lists_field_label'); ?>">
                Lists field label:
                <input class="widefat" id="<?php echo $this->get_field_id('lists_field_label'); ?>" name="<?php echo $this->get_field_name('lists_field_label'); ?>" type="text" value="<?php echo esc_attr($instance['lists_field_label']); ?>" />
            </label>

            <br><br>
            <?php _e('Automatically subscribe to', 'newsletter') ?>
            <br>
            <?php
            $lists = Newsletter::instance()->get_lists_public();
            foreach ($lists as $list) {
                ?>
                <label for="nl<?php echo $list->id ?>">
                    <input type="checkbox" value="<?php echo $list->id ?>" name="<?php echo $this->get_field_name('nl[]') ?>" <?php echo array_search($list->id, $instance['nl']) !== false ? 'checked' : '' ?>> <?php echo esc_html($list->name) ?>
                </label>
                <br>
            <?php } ?>


            <br>

        </p>

        <p>
            Use the tag {subscription_form} to place the subscription form within your personal text.
        </p>
        <?php
    }

}

add_action('widgets_init', function () {
    return register_widget("NewsletterWidget");
});
