<?php
if (!defined('ABSPATH')) exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterStatistics::instance();
$controls = new NewsletterControls();
$emails = Newsletter::instance()->get_emails();

$types = $wpdb->get_results("select distinct type from " . NEWSLETTER_EMAILS_TABLE);
$type_options = array();
foreach ($types as $type) {
    if ($type->type == 'followup')
        continue;
    if ($type->type == 'message') {
        $type_options[$type->type] = 'Standard Newsletter';
    } else if ($type->type == 'feed') {
        $type_options[$type->type] = 'Feed by Mail';
    } else if (strpos($type->type, 'automated') === 0) {
        list($a, $id) = explode('_', $type->type);
        $type_options[$type->type] = 'Automated Channel ' . $id;
    } else {
        $type_options[$type->type] = $type->type;
    }
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Newsletters', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <table class="widefat">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th><?php _e('Subject', 'newsletter') ?></th>
                        <th>Type</th>
                        <th><?php _e('Status', 'newsletter') ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th><?php _e('Tracking', 'newsletter') ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($emails as &$email) { ?>
                        <?php if ($email->type != 'message' && $email->type != 'feed') continue; ?>
                        <tr>
                            <td><?php echo $email->id; ?></td>
                            <td><?php echo esc_html($email->subject); ?></td>
                            <td><?php echo esc_html($module->get_email_type_label($email)) ?></td>
                            <td><?php echo esc_html($module->get_email_status_label($email)) ?></td>
                            <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $email->sent . ' ' . __('of', 'newsletter') . ' ' . $email->total; ?></td>
                            <td><?php if ($email->status == 'sent' || $email->status == 'sending') echo $module->format_date($email->send_on); ?></td>
                            <td><?php echo $email->track == 1 ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a class="button-primary" href="<?php echo NewsletterStatistics::instance()->get_statistics_url($email->id); ?>">statistics</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>
</div>
