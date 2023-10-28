<?php

namespace verifywoo\inc\app;

use verifywoo\core\DB;
use verifywoo\core\Mail;
use verifywoo\core\Router;
use verifywoo\core\Token;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Account {
    public static function woocommerce_save_account_details_errors($errors, &$new_user_data) {
        $notice_errors = wc_get_notices()['error'] ?? null;
        if ($notice_errors) return $errors;

        $user_id = $new_user_data->ID;
        $new_email = $new_user_data->user_email;
        $user = get_user_by('ID', $new_user_data->ID);
        $new_user_data->user_email = $user->user_email;

        if (!$user) {
            $errors->add('user_error', 'There was in issue finding your account information.');
            return $errors;
        }

        if ($user->user_email === $new_email) {
            return $errors;
        }

        $exists = DB::get_row('SELECT email from ' . DB::table(PLUGIN_PREFIX) . ' where email = %s', $new_email);

        if ($exists) {
            $errors->add('user_exists_error', __('A user with that email already exists.', 'verifywoo'));
            return $errors;
        }

        $token = Token::create();
        $verifywoo_table = DB::table(PLUGIN_PREFIX);
        $updated = DB::update($verifywoo_table, [
            'token' => $token,
            'token_exp' => Token::set_exp(),
            'email_change' => $new_email,
        ], [
            'user_id' => $user_id,
        ], ['%s', '%d', '%s'], ['%d']);

        if (!$updated) {
            $errors->add('verification_data_error', __('There was in issue creating your verification data. Please try again. If the problem persists contact your site administrator.', 'verifywoo'));
            return $errors;
        }

        $mail = Mail::send_token($new_email, $token);

        if (!$mail) {
            $errors->add(__('There was an issue sending your veification link. Please resend the verification link to the email you provided. Your old email will remain active until your new email is verified. <a href="' . Router::get_page_permalink('email-verification') . '">Resend Link</a>', 'verifywoo'), 'error');
            return $errors;
        }

        wc_add_notice(__('Please check the email you provided to verify your new email. Your old email will remain active until you verify your new email.', 'verifywoo'), 'success');

        return $errors;
    }
}
