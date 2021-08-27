<?php

$id = $state->language;
$files = [];

// Queue default translation item(s) from this extension
if (is_file($file = __DIR__ . DS . 'state' . DS . $id . '.php')) {
    $files[] = $file;
}

// Queue custom translation item(s) from third party extension(s)
foreach (glob(__DIR__ . DS . '..' . DS . '*' . DS . 'lot' . DS . 'language' . DS . $id . '.php', GLOB_NOSORT) as $file) {
    $files[] = $file;
}

// Queue custom translation item(s) from third party layout
if (is_file($file = __DIR__ . DS . '..' . DS . '..' . DS . 'layout' . DS . 'language' . DS . $id . '.php')) {
    $files[] = $file;
}

// Load and merge translation item(s) from queue
foreach ($files as $file) {
    $data = (function($f) {
        extract($GLOBALS, EXTR_SKIP);
        return (array) require $f;
    })($file);
    // Automatic lower-case mode
    foreach ($data as $k => $v) {
        $kk = strtolower($k);
        if ($k === $kk) {
            continue;
        }
        $data[$kk] = l($v);
    }
    $GLOBALS['I'] = array_replace($GLOBALS['I'] ?? [], $data);
}