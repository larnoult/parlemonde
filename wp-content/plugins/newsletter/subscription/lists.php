<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

$current_language = $module->get_current_language();
$is_all_languages = $module->is_all_languages();
$is_multilanguage = $module->is_multilanguage();

$controls->add_language_warning();

if (!$controls->is_action()) {
    $controls->data = $module->get_options('lists', $current_language);
} else {
    if ($controls->is_action('save')) {
        $module->save_options($controls->data, 'lists', null, $current_language);
        $controls->add_message_saved();
    }
    if ($controls->is_action('unlink')) {
        $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . ((int) $controls->button_data) . "=0");
        $controls->add_message_done();
    }
}

for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    if (!isset($controls->data['list_' . $i . '_forced'])) {
        $controls->data['list_' . $i . '_forced'] = empty($module->options['preferences_' . $i]) ? 0 : 1;
    }
}


$status = array(0 => 'Disabled/Private use', 1 => 'Only on profile page', 2 => 'Even on subscription forms', '3' => 'Hidden');
?>
<script>
    jQuery(function () {
        jQuery(".tnp-notes").tooltip({
            content: function () {
                // That activates the HTML in the tooltip
                return this.title;
            }
        });
    });
</script>
<div class="wrap tnp-lists" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Lists', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>
            <p>
                <?php $controls->button_save(); ?>
            </p>
            <table class="widefat" style="width: auto">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php _e('Name', 'newsletter') ?></th>
                        <?php if ($is_all_languages) { ?>
                            <th><?php _e('Visibility', 'newsletter') ?></th>
                            <th><?php _e('Pre-checked', 'newsletter') ?></th>
                            <th><?php _e('Pre-assigned', 'newsletter') ?></th>
                            <?php if ($is_multilanguage) { ?>
                                <th><?php _e('Pre-assigned by language', 'newsletter') ?></th>
                            <?php } ?>
                        <?php } ?>
                        <th><?php _e('Subscribers', 'newsletter') ?></th>
                        <th>&nbsp;</th>

                        <th><?php _e('Notes', 'newsletter') ?></th>
                    </tr>
                </thead>
                <?php for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) { ?>
                <?php 
                if (!$is_all_languages && empty($controls->data['list_' . $i])) { 
                    continue;
                }
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php $controls->text('list_' . $i, 50); ?></td>
                        <?php if ($is_all_languages) { ?>
                            <td><?php $controls->select('list_' . $i . '_status', $status); ?></td>
                            <td><?php $controls->select('list_' . $i . '_checked', array(0 => 'No', 1 => 'Yes')); ?></td>
                            <td><?php $controls->select('list_' . $i . '_forced', array(0 => 'No', 1 => 'Yes')); ?></td>
                            <?php if ($is_multilanguage) { ?>
                                <td><?php $controls->languages('list_' . $i . '_languages'); ?></td>
                            <?php } ?>
                        <?php } ?>

                        <td><?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where list_" . $i . "=1 and status='C'"); ?></td>
                        <td><?php $controls->button_confirm('unlink', __('Unlink everyone', 'newsletter'), '', $i); ?></td>

                        <td>
                            <?php $notes = apply_filters('newsletter_lists_notes', array(), $i); ?>
                            <?php
                            $text = '';
                            foreach ($notes as $note) {
                                $text .= $note . '<br>';
                            }
                            if (!empty($text)) {
                                echo '<i class="fa fa-info-circle tnp-notes" title="', esc_attr($text), '"></i>';
                            }
                            ?> 

                        </td>
                    </tr>
                <?php } ?>
            </table>

            <p>
                <?php $controls->button_save(); ?>
            </p>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>