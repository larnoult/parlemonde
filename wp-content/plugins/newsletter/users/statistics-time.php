<?php
defined('ABSPATH') || exit;
?>

<div class="row">
    <div class="col-md-6">
        <h3><?php _e('Subscriptions by month (max 12 months)', 'newsletter') ?></h3>
        <?php
        $months = $wpdb->get_results("select count(*) as c, concat(year(created), '-', date_format(created, '%m')) as d from " . NEWSLETTER_USERS_TABLE . " where status='C' group by concat(year(created), '-', date_format(created, '%m')) order by d desc limit 12");
        ?>

        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Year and month', 'newsletter') ?></th>
                    <th><?php _e('Total', 'newsletter') ?></th>
                </tr>
            </thead>
            <?php foreach ($months as &$day) { ?>
                <tr>
                    <td><?php echo $day->d; ?></td>
                    <td><?php echo $day->c; ?></td>
                </tr>
            <?php } ?>
        </table>

    </div>

    <div class="col-md-6">

        <h3><?php _e('Subscriptions by day (max 90 days)', 'newsletter') ?></h3>
        <?php
        $list = $wpdb->get_results("select count(*) as c, date(created) as d from " . NEWSLETTER_USERS_TABLE . " where status='C' group by date(created) order by d desc limit 90");
        ?>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Date', 'newsletter') ?></th>
                    <th><?php _e('Total', 'newsletter') ?></th>
                </tr>
            </thead>
            <?php foreach ($list as $day) { ?>
                <tr>
                    <td><?php echo $day->d; ?></td>
                    <td><?php echo $day->c; ?></td>
                </tr>
            <?php } ?>
        </table>

    </div>

</div>

