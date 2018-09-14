<?php
if (!defined('ABSPATH')) exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$module = NewsletterStatistics::instance();
$controls = new NewsletterControls();

wp_enqueue_script('tnp-chart');

$email = $module->get_email($_GET['id']);

//$module->maybe_fix_sent_stats($email);
$module->update_stats($email);
       

$total_count = $module->get_total_count($email->id);
$open_count = $module->get_open_count($email->id);
$click_count = $module->get_click_count($email->id);

?>

<div class="wrap" id="tnp-wrap">
    <?php include NEWSLETTER_DIR . '/tnp-header.php' ?>
    <div id="tnp-heading">
        <h2><?php _e('Statistics of', 'newsletter') ?> "<?php echo htmlspecialchars($email->subject); ?>"</h2>
    </div>

    <div id="tnp-body" style="min-width: 500px">
        
        <?php if ($email->status == 'new') { ?>
        
        <div class="tnp-warning"><?php _e('No data, newsletter not sent yet.', 'newsletter')?></div>
        
        <?php } else { ?>

        <form action="" method="post">
            <?php $controls->init(); ?>

            <div class="row">

                <div class="col-md-6">
                    <!-- START Statistics -->
                    <div class="tnp-widget">

                        <h3>Subscribers Reached <a href="admin.php?page=newsletter_statistics_view_users&id=<?php echo $email->id ?>">Details</a> 
                            <a href="admin.php?page=newsletter_statistics_view_retarget&id=<?php echo $email->id ?>">Retarget</a></h3>
                        
                        <div class="inside">
                            <div class="row tnp-row-pie-charts">
                                <div class="col-md-6">
                                    <canvas id="tnp-rates1-chart"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="tnp-rates2-chart"></canvas>
                                </div>
                            </div>

                            <script type="text/javascript">
                               
                                var rates1 = {
                                    labels: [
                                        "Not opened",
                                        "Opened"
                                    ],
                                    datasets: [
                                        {
                                            data: [<?php echo $total_count - $open_count; ?>, <?php echo $open_count; ?>],
                                            backgroundColor: [
                                                "#E67E22",
                                                "#2980B9"
                                            ],
                                            hoverBackgroundColor: [
                                                "#E67E22",
                                                "#2980B9"
                                            ]
                                        }]};
                                        
                                var rates2 = {
                                    labels: [
                                        "Opened",
                                        "Clicked"
                                    ],
                                    datasets: [
                                        {
                                            data: [<?php echo $open_count-$click_count; ?>, <?php echo $click_count; ?>],
                                            backgroundColor: [
                                                "#2980B9",
                                                "#27AE60"
                                            ],
                                            hoverBackgroundColor: [
                                                "#2980B9",
                                                "#27AE60"
                                            ]
                                        }]};

                                jQuery(document).ready(function ($) {
                                    ctx1 = $('#tnp-rates1-chart').get(0).getContext("2d");
                                    ctx2 = $('#tnp-rates2-chart').get(0).getContext("2d");
                                    myPieChart1 = new Chart(ctx1, {type: 'pie', data: rates1});
                                    myPieChart2 = new Chart(ctx2, {type: 'pie', data: rates2});
                                });

                            </script>

                            <div class="row tnp-row-values">
                                <div class="col-md-6">
                                    <div class="tnp-data">
                                        <?php if ($email->status == 'sending' || $email->status == 'paused'): ?>
                                            <div class="tnp-data-title">Sent</div>
                                            <div class="tnp-data-value"><?php echo $email->sent; ?> of <?php echo $email->total; ?></div>
                                        <?php else: ?>
                                            <div class="tnp-data-title">Total Sent</div>
                                            <div class="tnp-data-value"><?php echo $email->sent; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <!--
                                    <div class="tnp-data">
                                        <div class="tnp-data-title">Interactions</div>
                                        <div class="tnp-data-value"><?php echo $open_count; ?> (<?php echo $module->percent($open_count, $total_count); ?>)</div>
                                    </div>
                                    -->

                                </div>
                                <div class="col-md-6">
                                    <div class="tnp-data">
                                        <div class="tnp-data-title">Opened</div>
                                        <div class="tnp-data-value"><?php echo $open_count; ?> (<?php echo $module->percent($open_count, $total_count); ?>)</div>
                                    </div>
                                    <div class="tnp-data">
                                        <div class="tnp-data-title">Clicked</div>
                                        <div class="tnp-data-value"><?php echo $click_count; ?> (<?php echo $module->percent($click_count, $total_count); ?>)</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="tnp-widget">
                        <h3>World Map</h3>
                        <div class="inside">
                            <a href="https://www.thenewsletterplugin.com/premium?utm_source=plugin&utm_medium=link&utm_content=worldmap&utm_campaign=newsletter-reports" target="_blank">
                                <img style="width: 100%" src="<?php echo plugins_url('newsletter') ?>/statistics/images/map.gif">
                            </a>
                        </div>
                    </div>
                </div>

            </div><!-- row -->


        </form>
        
        <?php } // if "new" ?>

    </div>
    <?php include NEWSLETTER_DIR . '/tnp-footer.php' ?>
</div>
