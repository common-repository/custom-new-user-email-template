<?php

/**
 * Plugin Name: Custom New User Email Template
 * Plugin URI: https://wordpress.org/plugins/custom-new-user-email-template/
 * Description: This plugin allows you to customize the email sent on a new user registration.
 * Version: 1.0
 * Author: Logicrays
 * Author URI: http://logicrays.com/
 */
/**
 * Minimum version required
 *
 */
if (get_bloginfo('version') < 5.0) return;

include plugin_dir_path(__FILE__) . '/admin/includes.php';

//Loading style
add_action('admin_init', 'cnut_plugin_admin_styles');

function cnut_plugin_admin_styles()
{

    wp_register_style('cnutPluginStylesheet', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('cnutPluginStylesheet');
}

/**
 * Calling settings page
 */
add_action('admin_menu', 'cnut_plugin_menu');

function cnut_plugin_menu()
{

    add_options_page('Custom New User Notification Options', 'Registration Email', 'manage_options', 'custom-new-user-notification', 'cnut_plugin_options');
}

function cnut_get_email_info()
{

    $blogname      = get_option('blogname');
    $admin_email = get_option('admin_email');

    $email_info = [];

    $email_info['subject_user'] = get_option('cnut_user_mail_subject', '[' . $blogname . '] Your username and password info');
    $email_info['from_name_user'] = get_option('cnut_user_mail_sender_name', $blogname);
    $email_info['from_email_user'] = get_option('cnut_user_mail_sender_mail', $admin_email);
    $email_info['user_mail_content'] = get_option('cnut_user_mail_content', '<p>Username: [cnut-user-login]<br><br>To set your password, visit the following address:<br><br><a href="[cnut-reset-password-url]" data-wplink-url-error="true">[cnut-reset-password-url]</a><br></p>');

    $email_info['subject_admin'] = get_option('cnut_admin_mail_subject', '[' . get_option('blogname') . '] New User Registration');
    $email_info['from_name_admin'] = get_option('cnut_admin_mail_sender_name', $blogname);
    $email_info['from_email_admin'] = get_option('cnut_admin_mail_sender_mail', $admin_email);
    $email_info['admin_mail_content'] = get_option('cnut_admin_mail_content', '<p>New user registration on your site ' . $blogname . ':<br><br>Username: [cnut-user-login]<br><br>Email: [cnut-user-email]</p>');

    return $email_info;
}

/*
 * All the functions are in this file
 */

if (!function_exists('wp_new_user_notification')) {

    function wp_new_user_notification($user_id, $deprecated = null, $notify = '')
    {

        global $wpdb, $wp_hasher;

        // set email content type
        add_filter('wp_mail_content_type', 'cnut_mail_content_type');

        /// get user		
        $user = get_userdata($user_id);

        // Generate something random for a password reset key.		
        $key = wp_generate_password(20, false);

        // get email info		
        $email_info = cnut_get_email_info();
        //Shortcodes

        $shortcodes = array(
            "[cnut-display-name]",
            "[cnut-user-login]",
            "[cnut-user-email]",
            "[cnut-reset-password-url]",
            PHP_EOL,
        );

        $data = array(
            $user->display_name,
            $user->user_login,
            $user->user_email,
            network_site_url('wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode($user->user_login), 'login'),
            '<br/>',
        );

        if ($deprecated !== null) {

            _deprecated_argument(__FUNCTION__, '4.3.1');
        }

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
        // we want to reverse this for the plain text arena of emails.

        if ('user' !== $notify) {

            $switched_locale = switch_to_locale(get_locale());
            $message  = str_replace($shortcodes, $data, $email_info['admin_mail_content']);
            $subject  = str_replace($shortcodes, $data, $email_info['subject_admin']);

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            $headers .= 'From: ' . $email_info['from_name_admin'] . ' <' . $email_info['from_email_admin']  . '>  ' . "\r\n";

            mail(get_option('admin_email'), $subject, $message, $headers);

            if ($switched_locale) {
                restore_previous_locale();
            }
        }

        if ('admin' === $notify || (empty($deprecated) && empty($notify))) {

            return;
        }

        /** This action is documented in wp-login.php */

        do_action('retrieve_password_key', $user->user_login, $key);

        // Now insert the key, hashed, into the DB.

        if (empty($wp_hasher)) {

            require_once ABSPATH . WPINC . '/class-phpass.php';

            $wp_hasher = new PasswordHash(8, true);
        }

        $hashed = time() . ':' . $wp_hasher->HashPassword($key);

        $wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user->user_login));

        $switched_locale = switch_to_locale(get_user_locale($user));
        $message  = str_replace($shortcodes, $data, $email_info['user_mail_content']);
        $subject  = str_replace($shortcodes, $data, $email_info['subject_user']);

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $headers .= 'From: ' . $email_info['from_name_user'] . ' <' . $email_info['from_email_user']  . '>  ' . "\r\n";

        mail($user->user_email, $subject, $message, $headers);

        if ($switched_locale) {
            restore_previous_locale();
        }
    }
}

function cnut_mail_content_type($content_type)
{
    return 'text/html';
}

/**
 * Settings link
 */

function cnut_add_action_links($links)
{
    $mylinks = array(
        '<a href="' . admin_url('options-general.php?page=custom-new-user-notification') . '">Settings</a>',
    );

    return array_merge($links, $mylinks);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cnut_add_action_links');
