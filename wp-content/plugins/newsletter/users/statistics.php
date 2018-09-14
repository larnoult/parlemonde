<?php
if (!defined('ABSPATH'))
    exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

wp_enqueue_script('tnp-chart');

$all_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE);
$options_profile = get_option('newsletter_profile');

$module = NewsletterUsers::instance();
$controls = new NewsletterControls();
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    google.charts.load("current", {packages: ['corechart', 'geochart', 'geomap']});

</script>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Subscriber statistics', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body" class="tnp-users-statistics">

        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tab-overview">Overview</a></li>
                <li><a href="#tabs-lists">Lists</a></li>
                <li><a href="#tabs-countries">World Map</a></li>
                <li><a href="#tabs-referrers">Referrer</a></li>
                <li><a href="#tabs-sources">Sources</a></li>
                <li><a href="#tabs-gender">Gender</a></li>
                <li><a href="#tabs-time">By time</a></li>
            </ul>

            <div id="tab-overview">

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th><?php _e('Status', 'newsletter') ?></th>
                            <th><?php _e('Total', 'newsletter') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e('Any', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Confirmed', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Not confirmed', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='S'"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Unsubscribed', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='U'"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Bounced', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='B'"); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>


            <div id="tabs-lists">

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><?php _e('List', 'newsletter') ?></th>
                            <th><?php _e('Total', 'newsletter') ?> (*)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $lists = $module->get_lists(); ?>
                        <?php foreach ($lists as $list) { ?>
                            <tr>
                                <td><?php echo $list->id ?></td>
                                <td><?php echo esc_html($list->name) ?></td>
                                <td>
                                    <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where list_" . $list->id . "=1 and status='C'"); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                (*) <?php _e('Confirmed', 'newsletter') ?>
            </div>


            <div id="tabs-countries">
                <?php
                if (!has_action('newsletter_users_statistics_countries')) {
                    include __DIR__ . '/statistics-countries.php';
                } else {
                    do_action('newsletter_users_statistics_countries', $controls);
                }
                ?>
            </div>


            <div id="tabs-referrers">
                <p>
                    <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscribers-statistics#referrer') ?>
                </p>
                <?php
                $list = $wpdb->get_results("select referrer, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed from " . NEWSLETTER_USERS_TABLE . " group by referrer order by confirmed desc");
                ?>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th><?php _e('Referrer', 'newsletter') ?></th>
                            <th><?php _e('Confirmed', 'newsletter') ?></th>
                            <th><?php _e('Not confirmed', 'newsletter') ?></th>
                            <th><?php _e('Unsubscribed', 'newsletter') ?></th>
                            <th><?php _e('Bounced', 'newsletter') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?php echo empty($row->referrer) ? '[not set]' : esc_html($row->referrer) ?></td>
                                <td><?php echo $row->confirmed; ?></td>
                                <td><?php echo $row->unconfirmed; ?></td>
                                <td><?php echo $row->unsubscribed; ?></td>
                                <td><?php echo $row->bounced; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>


            <div id="tabs-sources">
                <p>
                    <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscribers-statistics#source') ?>
                </p>
                <?php
                $list = $wpdb->get_results("select http_referer, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed from " . NEWSLETTER_USERS_TABLE . " group by http_referer order by count(*) desc limit 100");
                ?>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th><?php _e('Confirmed', 'newsletter') ?></th>
                            <th><?php _e('Not confirmed', 'newsletter') ?></th>
                            <th><?php _e('Unsubscribed', 'newsletter') ?></th>
                            <th><?php _e('Bounced', 'newsletter') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?php echo empty($row->http_referer) ? '[not set]' : $controls->print_truncated($row->http_referer, 120) ?></td>
                                <td><?php echo $row->confirmed; ?></td>
                                <td><?php echo $row->unconfirmed; ?></td>
                                <td><?php echo $row->unsubscribed; ?></td>
                                <td><?php echo $row->bounced; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>


            <div id="tabs-gender">


                <?php
                $male_count = $wpdb->get_row("select SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed from " . NEWSLETTER_USERS_TABLE . " where sex='m'");
                $female_count = $wpdb->get_row("select SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed from " . NEWSLETTER_USERS_TABLE . " where sex='f'");
                $none_count = $wpdb->get_row("select SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed from " . NEWSLETTER_USERS_TABLE . " where sex='n'");
                ?>
                
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Gender', 'newsletter')?></th>
                            <th><?php _e('Confirmed', 'newsletter') ?></th>
                            <th><?php _e('Not confirmed', 'newsletter') ?></th>
                            <th><?php _e('Unsubscribed', 'newsletter') ?></th>
                            <th><?php _e('Bounced', 'newsletter') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                                <td><?php _e('Female', 'newsletter')?></td>
                                <td><?php echo $female_count->confirmed; ?></td>
                                <td><?php echo $female_count->unconfirmed; ?></td>
                                <td><?php echo $female_count->unsubscribed; ?></td>
                                <td><?php echo $female_count->bounced; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Male', 'newsletter')?></td>
                                <td><?php echo $male_count->confirmed; ?></td>
                                <td><?php echo $male_count->unconfirmed; ?></td>
                                <td><?php echo $male_count->unsubscribed; ?></td>
                                <td><?php echo $male_count->bounced; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Not specified', 'newsletter')?></td>
                                <td><?php echo $none_count->confirmed; ?></td>
                                <td><?php echo $none_count->unconfirmed; ?></td>
                                <td><?php echo $none_count->unsubscribed; ?></td>
                                <td><?php echo $none_count->bounced; ?></td>
                            </tr>
                    </tbody>
                </table>


            </div>


            <div id="tabs-time">

                <?php
                if (!has_action('newsletter_users_statistics_time')) {
                    include __DIR__ . '/statistics-time.php';
                } else {
                    do_action('newsletter_users_statistics_time', $controls);
                }
                ?>

            </div>

            <?php
            if (isset($panels['user_statistics'])) {
                foreach ($panels['user_statistics'] as $panel) {
                    call_user_func($panel['callback'], $id, $controls);
                }
            }
            ?>
        </div>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>



