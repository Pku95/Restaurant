<?php
/**
 * Small helpers shared by every page.
 */

// This file is meant to be included, never requested directly.
if (!defined('SITE_NAME')) {
    http_response_code(403);
    exit('This file cannot be loaded directly.');
}

/** Escapes a value for safe HTML output. */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/*
 * A few hosts disable the mbstring extension. Falling back to the byte-based
 * versions keeps the site working — the difference only matters for strings
 * written in non-Latin scripts, which this site does not validate.
 */
if (!function_exists('mb_strlen')) {
    function mb_strlen($string, $encoding = null)
    {
        return strlen((string) $string);
    }
}

/** 520 -> "৳ 520" */
function format_bdt($amount)
{
    return '৳ ' . number_format((float) $amount);
}

/** "2026-06-10" -> "10 Jun 2026" */
function format_date($iso)
{
    $timestamp = strtotime((string) $iso);
    return $timestamp ? date('j M Y', $timestamp) : (string) $iso;
}

/** "2026-06-10 14:03:00" -> "10 Jun 2026" */
function format_datetime($value)
{
    $timestamp = strtotime((string) $value);
    return $timestamp ? date('j M Y, g:i a', $timestamp) : (string) $value;
}

/** "19:30" -> "7:30 PM" */
function format_time($value)
{
    if (!preg_match('/^(\d{1,2}):(\d{2})$/', (string) $value, $matches)) {
        return (string) $value;
    }
    $hour = (int) $matches[1];
    $minute = $matches[2];
    if ($hour > 23) {
        return (string) $value;
    }
    $hour12 = ($hour % 12 === 0) ? 12 : $hour % 12;
    return $hour12 . ':' . $minute . ($hour >= 12 ? ' PM' : ' AM');
}

/** Opening hours as an array. */
function site_hours()
{
    $hours = unserialize(SITE_HOURS);
    return is_array($hours) ? $hours : [];
}

/** Half-hour seating slots from 12:00 to 22:30. */
function time_slots()
{
    $slots = [];
    for ($index = 0; $index < 22; $index++) {
        $minutes = (12 * 60) + ($index * 30);
        $hour = intdiv($minutes, 60);
        $minute = $minutes % 60;
        $hour12 = ($hour % 12 === 0) ? 12 : $hour % 12;
        $slots[] = [
            'value' => sprintf('%02d:%02d', $hour, $minute),
            'label' => $hour12 . ':' . sprintf('%02d', $minute) . ($hour >= 12 ? ' PM' : ' AM'),
        ];
    }
    return $slots;
}

/** A random, human-readable booking reference such as "ES-4KD9PZ". */
function make_reference($prefix = 'ES')
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($index = 0; $index < 6; $index++) {
        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    return $prefix . '-' . $code;
}

/** Path to a bundled image. */
function image_url($file)
{
    return 'assets/images/' . rawurlencode((string) $file);
}

/** Splits the comma separated tag column into an array. */
function split_tags($csv)
{
    $tags = array_map('trim', explode(',', (string) $csv));
    return array_values(array_filter($tags));
}

/** Is this a plausible email address? */
function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

/** Counts the digits in a string. */
function digit_count($value)
{
    return strlen(preg_replace('/\D/', '', (string) $value));
}

/* ----------------------------- CSRF + flash ----------------------------- */

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

/** Hidden input for a form. */
function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** True when the submitted token matches. */
function csrf_check()
{
    $sent = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';
    $known = isset($_SESSION['csrf']) && is_string($_SESSION['csrf']) ? $_SESSION['csrf'] : '';
    return $sent !== '' && $known !== '' && hash_equals($known, $sent);
}

function flash_set($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Returns the pending flash message and clears it. */
function flash_get()
{
    if (!empty($_SESSION['flash']) && is_array($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/** Today's date as YYYY-MM-DD, used to validate booking dates. */
function today_iso()
{
    return date('Y-m-d');
}

/** Escapes a value for output inside a MySQL LIKE query. */
function like($value)
{
    return addcslashes((string) $value, '%_\\');
}
