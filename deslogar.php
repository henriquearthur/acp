<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

session_start();

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}


session_destroy();

echo '<script>location.replace("/acp");</script>';
