<?php
if (!defined('ABSPATH')) exit;

$email_id = (int) $_GET['id'];
$module = NewsletterStatistics::instance();

$email = $module->get_email($email_id);
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php' ?>

    <div id="tnp-heading">
        <h2>Reached subscribers for "<?php echo esc_html($email->subject); ?>"</h2>
    </div>

    <div id="tnp-body" style="min-width: 500px">

        <p><a href="admin.php?page=newsletter_statistics_view&id=<?php echo $email->id ?>" class="button-primary">Back to the dashboard</a></p>

        <a href="https://www.thenewsletterplugin.com/premium?utm_source=plugin&utm_medium=link&utm_content=audience&utm_campaign=newsletter-reports" target="_blank">
            <img src="<?php echo plugins_url('newsletter') ?>/statistics/images/users.png">
        </a>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php' ?>

</div>
