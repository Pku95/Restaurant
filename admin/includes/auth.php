<?php
/**
 * Staff-area guard. Include this at the top of every admin page.
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

function admin_is_logged_in()
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/** Call at the top of a protected page. */
function admin_require_login()
{
    if (!admin_is_logged_in()) {
        redirect('login.php');
    }
}

/** Accepts a password attempt, whichever storage mode is configured. */
function admin_verify_password($password)
{
    $password = (string) $password;

    if (ADMIN_PASSWORD_HASH !== '') {
        return password_verify($password, ADMIN_PASSWORD_HASH);
    }

    return ADMIN_PASSWORD !== '' && hash_equals(ADMIN_PASSWORD, $password);
}

function admin_login($password)
{
    if (!admin_verify_password($password)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    return true;
}

function admin_logout()
{
    unset($_SESSION['admin_logged_in']);
}
