<?php
defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

if ($controls->is_action('save')) {

    if (!is_email($controls->data['email'])) {
        $controls->errors = __('Wrong email address.', 'newsletter');
    }

    if (empty($controls->errors)) {
        $controls->data['status'] = 'C';
        $controls->data['sex'] = 'n';

        $user = $module->save_user($controls->data);
        if ($user === false) {
            $controls->errors = __('This subscriber already exists.', 'newsletter');
        } else {
            echo '<script>';
            echo 'location.href="' . $module->get_admin_page_url('edit') . '&id=' . $user->id . '"';
            echo '</script>';
            return;
        }
    }
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('New Subscriber', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body" class="tnp-users tnp-users-new">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <table class="form-table">
                <tr>
                    <th><?php _e('Email', 'newsletter')?></th>
                    <td>
                        <?php $controls->text_email('email', 60); ?>
                        <?php $controls->button('save', '&raquo;'); ?>

                    </td>
                </tr>
            </table>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
