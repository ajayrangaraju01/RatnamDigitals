<?php
session_start();

const APP_ROOT = __DIR__ . '/..';
const DB_PATH = APP_ROOT . '/db.sqlite3';
const MEDIA_ROOT = APP_ROOT . '/media';

if (!is_dir(MEDIA_ROOT)) {
    mkdir(MEDIA_ROOT, 0777, true);
}

require_once __DIR__ . '/functions.php';
ensureAppDatabase();
