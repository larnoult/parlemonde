<?php

/*
 * TNP classes for internal API
 * 
 * Error reference
 * 404	Object not found
 * 403	Not allowed (when the API key is missing or wrong)
 * 400	Bad request, when the parameters are not correct or required parameters are missing
 * 
 */

/**
 * Main API functions
 *
 * @author roby
 */
class TNP {
    /*
     * The full process of subscription
     */

    public static function subscribe($params) {

//        error_reporting(E_ALL);

        $newsletter = Newsletter::instance();
        $subscription = NewsletterSubscription::instance();

        // Messages
        $options = get_option('newsletter', array());

        // Form field configuration
        $options_profile = get_option('newsletter_profile', array());

        $optin = (int) $options['noconfirmation']; // 0 - double, 1 - single

        $email = $newsletter->normalize_email(stripslashes($params['email']));

        // Should never reach this point without a valid email address
        if ($email == null) {
            return new WP_Error('-1', 'Email address not valid', array('status' => 400));
        }

        $user = $newsletter->get_user($email);

        if ($user != null) {

            $newsletter->logger->info('Subscription of an address with status ' . $user->status);

            // Bounced
            if ($user->status == 'B') {
                return new WP_Error('-1', 'Bounced address', array('status' => 400));
            }

            // If asked to put in confirmed status, do not check further
            if ($params['status'] != 'C' && $optin == 0) {

                // Already confirmed
                //if ($optin == 0 && $user->status == 'C') {
                if ($user->status == 'C') {

                    set_transient($user->id . '-' . $user->token, $params, 3600 * 48);
                    $subscription->set_updated($user);

                    // A second subscription always require confirmation otherwise anywan can change other users' data
                    $user->status = 'S';
                    $subscription->send_activation_email($user);

                    return $user;
                }
            }
        }

        if ($user != null) {
            $newsletter->logger->info("Email address subscribed but not confirmed");
            $user = array('id' => $user->id);
        } else {
            $newsletter->logger->info("New email address");
        }

        if ($optin) {
            $params['status'] = 'C';
        } else {
            $params['status'] = 'S';
        }
        
        // Lists
 
        if (!isset($params['lists']) || !is_array($params['lists'])) {
            $params['lists'] = array();
        }
        
        // Public lists: rebuild the array keeping only the valid lists
        $lists = $newsletter->get_lists_public();
        
        // Public list IDs
        $public_lists = array();
        foreach ($lists as $list) {
            $public_lists[] = $list->id;
        }
        
        // Keep only the public lists
        $params['lists'] = array_intersect($public_lists, $params['lists']);
        
        // Pre assigned lists
        $lists = $newsletter->get_lists();
        foreach ($lists as $list) {
            if ($list->forced) {
                $params['lists'][] =  $list->id;
            }
        }

        apply_filters('newsletter_api_subscribe', $params);
        
        $user = TNP::add_subscriber($params);

        if (is_wp_error($user)) {
            return ($user);
        }

        // Notification to admin (only for new confirmed subscriptions)
        if ($user->status == 'C') {
            do_action('newsletter_user_confirmed', $user);
            $subscription->notify_admin($user, 'Newsletter subscription');
            setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
        }

        if (empty($params['send_emails']) || !$params['send_emails']) {
            return $user;
        }

        $message_type = ($user->status == 'C') ? 'confirmed' : 'confirmation';
        $subscription->send_message($message_type, $user);
        
        return $user;
    }

    /*
     * The UNsubscription
     */

    public static function unsubscribe($params) {

        $newsletter = Newsletter::instance();
        $user = $newsletter->get_user($params['email']);

//        $newsletter->logger->debug($params);

        if (!$user) {
            return new WP_Error('-1', 'Email address not found', array('status' => 404));
        }

        if ($user->status == 'U') {
            return $user;
        }

        $user = $newsletter->set_user_status($user, 'U');

        if (empty(NewsletterSubscription::instance()->options['unsubscribed_disabled'])) {
            $newsletter->mail($user->email, $newsletter->replace(NewsletterSubscription::instance()->options['unsubscribed_subject'], $user), $newsletter->replace(NewsletterSubscription::instance()->options['unsubscribed_message'], $user));
        }
        NewsletterSubscription::instance()->notify_admin($user, 'Newsletter unsubscription');

        return $user;
    }

    /*
     * Adds a subscriber if not already in
     */

    public static function add_subscriber($params) {

        $newsletter = Newsletter::instance();

        $email = $newsletter->normalize_email(stripslashes($params['email']));

        if (!$email) {
            return new WP_Error('-1', 'Email address not valid', array('status' => 400));
        }

        $user = $newsletter->get_user($email);

        if ($user) {
            return new WP_Error('-1', 'Email address already exists', array('status' => 400));
        }

        $user = array('email' => $email);

        if (isset($params['name'])) {
            $user['name'] = $newsletter->normalize_name(stripslashes($params['name']));
        }

        if (isset($params['surname'])) {
            $user['surname'] = $newsletter->normalize_name(stripslashes($params['surname']));
        }

        if (!empty($params['gender'])) {
            $user['sex'] = $newsletter->normalize_sex($params['gender']);
        }

        if (isset($params['profile']) && is_array($params['profile'])) {
            foreach ($params['profile'] as $key => $value) {
                $user['profile_' . $key] = trim(stripslashes($value));
            }
        }

        // Lists (an arrayunder the key "lists")
        // Preferences (field names are nl[] and values the list number so special forms with radio button can work)
        if (isset($params['lists']) && is_array($params['lists'])) {
            foreach ($params['lists'] as $list_id) {
                $user['list_' . ((int)$list_id)] = 1;
            }
        }


        if (!empty($params['status'])) {
            $user['status'] = $params['status'];
        } else {
            $user['status'] = 'C';
        }

        $user['token'] = $newsletter->get_token();
        $user['updated'] = time();

        $user = $newsletter->save_user($user);

        return $user;
    }

    /*
     * Subscribers list
     */

    public static function subscribers($params) {

        global $wpdb;
        $newsletter = Newsletter::instance();

        $items_per_page = 20;
        $where = "";

        $query = "select name, email from " . NEWSLETTER_USERS_TABLE . ' ' . $where . " order by id desc";
        $query .= " limit 0," . $items_per_page;
        $list = $wpdb->get_results($query);

        return $list;
    }

    /*
     * Deletes a subscriber
     */

    public static function delete_subscriber($params) {

        global $wpdb;
        $newsletter = Newsletter::instance();

        $user = $newsletter->get_user($params['email']);

        if (!$user) {
            return new WP_Error('-1', 'Email address not found', array('status' => 404));
        }

        if ($wpdb->query($wpdb->prepare("delete from " . NEWSLETTER_USERS_TABLE . " where id=%d", (int) $user->id))) {
            return "OK";
        } else {
            $newsletter->logger->debug($wpdb->last_query);
            return new WP_Error('-1', $wpdb->last_error, array('status' => 400));
        }
    }

    /*
     * Newsletters list
     */

    public static function newsletters($params) {

        global $wpdb;

        $list = $wpdb->get_results("SELECT id, subject, created, status, total, sent, send_on FROM " . NEWSLETTER_EMAILS_TABLE . " ORDER BY id DESC LIMIT 10", OBJECT);

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }

        if (empty($list)) {
            return array();
        }

        return $list;
    }

}
