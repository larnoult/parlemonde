<?php
if (!defined('ABSPATH')) exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$options_profile = get_option('newsletter_profile');

if ($controls->is_action('import')) {

    $mode = $controls->data['mode'];

    // TODO: to be removed, it's not safe
    @set_time_limit(0);

    $results = '';
    
    if (is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $lines = file($_FILES['csv_file']['tmp_name']);
    } else {
        $csv = stripslashes($controls->data['csv']);
        $lines = explode("\n", $csv);
    }

    // Set the selected preferences inside the
    if (!is_array($controls->data['preferences']))
        $controls->data['preferences'] = array();

//    if ($options['followup'] == 'activate') {
//        $subscriber['followup'] = 1;
//    }

    $error_count = 0;
    $added_count = 0;
    $updated_count = 0;
    $skipped_count = 0;
    
    foreach ($lines as &$line) {
        // Parse the CSV line
        $line = trim($line);
        if ($line == '') {
            continue;
        }
        if ($line[0] == '#' || $line[0] == ';') {
            continue;
        }
        $separator = $controls->data['separator'];
        if ($separator == 'tab') {
            $separator = "\t";
        }
        $data = explode($separator, $line);

        // Builds a subscriber data structure
        $email = $newsletter->normalize_email($data[0]);
        if (empty($email)) {
            continue;
        }
        
        if (!$newsletter->is_email($email)) {
            $results .= '[INVALID EMAIL] ' . $line . "\n";
            $error_count++;
            continue;
        }

        $subscriber = $module->get_user($email, ARRAY_A);
        if ($subscriber == null) {
            $subscriber = array();
            $subscriber['email'] = $email;
            if (isset($data[1])) {
                $subscriber['name'] = $module->normalize_name($data[1]);
            }
            if (isset($data[2])) {
                $subscriber['surname'] = $module->normalize_name($data[2]);
            }
            if (isset($data[3])) {
                $subscriber['sex'] = $module->normalize_sex($data[3]);
            }
            $subscriber['status'] = $controls->data['import_as'];
            foreach ($controls->data['preferences'] as $i) {
                $subscriber['list_' . $i] = 1;
            }
            $module->save_user($subscriber);
            $results .= '[ADDED] ' . $line . "\n";
            $added_count++;
        } else {
            if ($mode == 'skip') {
                $results .= '[SKIPPED] ' . $line . "\n";
                $skipped_count++;
                continue;
            }

            if ($mode == 'overwrite') {
                $subscriber['name'] = $module->normalize_name($data[1]);
                $subscriber['surname'] = $module->normalize_name($data[2]);
                if (isset($data[3])) {
                    $subscriber['sex'] = $module->normalize_sex($data[3]);
                }
                if (isset($controls->data['override_status'])) {
                    $subscriber['status'] = $controls->data['import_as'];
                }

                // Prepare the preference to zero
                for ($i = 1; $i < NEWSLETTER_LIST_MAX; $i++) {
                    $subscriber['list_' . $i] = 0;
                }

                foreach ($controls->data['preferences'] as $i) {
                    $subscriber['list_' . $i] = 1;
                }
            }

            if ($mode == 'update') {
                $subscriber['name'] = $module->normalize_name($data[1]);
                $subscriber['surname'] = $module->normalize_name($data[2]);
                if (isset($data[3])) {
                    $subscriber['sex'] = $module->normalize_sex($data[3]);
                }
                if (isset($controls->data['override_status'])) {
                    $subscriber['status'] = $controls->data['import_as'];
                }
                foreach ($controls->data['preferences'] as $i) {
                    $subscriber['list_' . $i] = 1;
                }
            }

            NewsletterUsers::instance()->save_user($subscriber);

            $results .= '[UPDATED] ' . $line . "\n";
            $updated_count++;
        }
    }
    if ($error_count) {
        $controls->errors = "Import completed but with errors.";
    }
    $controls->messages = "Import completed: $error_count errors, $added_count added, $updated_count updated, $skipped_count skipped.";
}
?>

<div class="wrap" id="tnp-wrap">
    
    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
    
        <h2><?php _e('Import', 'newsletter') ?></h2>
        <p>
            The import and export functions <strong>ARE NOT for backup</strong>. If you want to backup you should consider to backup the
            wp_newsletter* tables. Please, read on bottom of this page the data format to use and other important notes.</p>
        
    </div>
    
    <div id="tnp-body" class="tnp-users tnp-users-import">

    <?php if (!empty($results)) { ?>

        <h3>Results</h3>

        <textarea wrap="off" style="width: 100%; height: 150px; font-size: 11px; font-family: monospace"><?php echo esc_html($results) ?></textarea>

    <?php } ?>

        <form method="post" enctype="multipart/form-data">

        <?php $controls->init(); ?>

        <table class="form-table">

            <tr>
                <th><?php _e('Import Subscribers As', 'newsletter') ?></th>
                <td>
                    <?php $controls->select('import_as', array('C' => __('Confirmed', 'newsletter'), 'S' => __('Not confirmed', 'newsletter'))); ?>
                    <br>
                    <?php $controls->checkbox('override_status', __('Override status of existing users', 'newsletter')) ?>
                </td>
            </tr>

            <tr>
                <th><?php _e('Import mode', 'newsletter') ?></th>
                <td>
                    <?php $controls->select('mode', array('update' => 'Update', 'overwrite' => 'Overwrite', 'skip' => 'Skip')); ?>
                    if email is already present
                    <p class="description">
                        <strong>Update</strong>: <?php _e('user data will be updated, existing preferences will be left untouched and new ones will be added.', 'newsletter') ?><br />
                        <strong>Overwrite</strong>: <?php _e('user data will be overwritten with new informations (like name and preferences).', 'newsletter') ?><br />
                        <strong>Skip</strong>: <?php _e('user data will be left untouched if already present.', 'newsletter') ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th><?php _e('Lists', 'newsletter') ?></th>
                <td>
                    <?php $controls->preferences_group('preferences', true); ?>
                    <div class="hints">
                        Every new imported or updated subscriber will be associate with selected preferences above.
                    </div>
                </td>
            </tr>

            <tr>
                <th><?php _e('Field separator', 'newsletter') ?></th>
                <td>
                    <?php $controls->select('separator', array(';' => 'Semicolon', ',' => 'Comma', 'tab' => 'Tabulation')); ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?php _e('CSV file', 'newsletter') ?>
            <div class="tnp-tip">
                <span class="tip-button">Tip</span>
                <span class="tip-content">
                    Upload a CSV file, see format description <a href="#import_format">here</a>.
                </span>
            </div>
            </th>
            <td>
                <input type="file" name="csv_file" />
            </td>
            </tr>
            <tr>
                <th>CSV text
            <div class="tnp-tip">
                <span class="tip-button">Tip</span>
                <span class="tip-content">
                    Simply paste CVS text here.
                </span>
            </div>
            </th>
            <td>
                <textarea name="options[csv]" wrap="off" style="width: 100%; height: 200px; font-size: 11px; font-family: monospace"><?php echo $controls->data['csv']; ?></textarea>
            </td>
            </tr>
            <tr>
                <th>&nbsp;</th><td><?php $controls->button('import', 'Import'); ?></td>
            </tr>
            <tr>
                <th>
                    <a name="import_format"></a>
                    Data format<br>and other notes
                    <div class="tnp-tip">
                <span class="tip-button">Tip</span>
                <span class="tip-content">Consider to split up your input list if you get errors, blank pages or partially imported lists: it can be a time/resource limit
            of your provider. It's safe to import the same list a second time, no duplications will occur.</span>
                </th>
                <td>
        <p>
            Import list format is:
        <p><strong>email</strong><i>[separator]</i><strong>first name</strong><i>[separator]</i><strong>last name</strong><i>[separator]</i><strong>gender</strong><i>[new line]</i></p>
            Example:
        <p style="border: 1px solid #bfbfbf">
            email1@example.com;first name 1;last name 1;m<br />
            email2@example.com;first name 2;last name 2;f
        </p>
        <p>
            where [separator] must be selected from the available ones. Empty lines and lines starting with "#" will be skipped. There is
            no separator escaping mechanism, so be sure that field values do not contain the selected separator. The only required field is the email
            all other fields are options. Gender must be "m" or "f".
        </p>
                </td>
            </tr>
        </table>
            
    </form>

</div>
    
    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
