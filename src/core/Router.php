<?php

namespace VerifyWoo\Core;

use VerifyWoo\Core\Template;
use VerifyWoo\Core\Auth;

class Router {
    public static function verification_page() {
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'success':
                return Template::include('message', [
                    'type' => 'message',
                    'msg' => $_GET['msg'] ?? 'Success.'
                ]);
            case 'error':
                return Template::include('message', [
                    'type' => 'error',
                    'msg' => $_GET['msg'] ?? 'Error.'
                ]);
            case 'verify':
                $verified = Auth::verify_token();
                return Template::include('message', [
                    'type' => $verified ? 'message' : 'error',
                    'msg' => $verified ? 'Your email was successfully verified. You may now login.' : 'There was an issue verifying your token.'
                ]);
            case 'resend':
                return Template::include('resend');
            default:
                return Template::include('message', [
                    'type' => 'info',
                    'msg' => 'Nothing to do yet.'
                ]);
        }
    }
}
