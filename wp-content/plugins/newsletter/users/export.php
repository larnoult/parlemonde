<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$options_profile = get_option('newsletter_profile');
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Export', 'newsletter') ?></h2>
        <p>
            <strong>The import and export functions ARE NOT for backup</strong>. 
            If you want to backup you should consider to backup the <code><?php echo $wpdb->prefix ?>newsletter*</code> tables.
        </p>

    </div>
    
    <div id="tnp-body" class="tnp-users tnp-users-export">

        <form method="post" action="<?php echo admin_url('admin-ajax.php') ?>?action=newsletter_users_export">
            <?php $controls->init(); ?>
            <table class="form-table">
                <tr>
                    <th><?php _e('Field separator', 'newsletter') ?></th>

                    <td>
                        <?php $controls->select('separator', array(';' => 'Semicolon', ',' => 'Comma', 'tab' => 'Tabulation')); ?>
                        <p class="description">Try to change the separator if Excel does not recognize the columns.</p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('List', 'newsletter') ?></th>
                    <td>
                        <?php $controls->lists_select('list', __('All', 'newsletter')); ?>
                    </td>
                </tr>
            </table>
            <p>
                <?php $controls->button('export', __('Export', 'newsletter')); ?>
            </p>
        </form>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
