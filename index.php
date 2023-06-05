<?php

$id = $state->language;
$files = [];

// Queue default translation item(s) from this extension
if (is_file($file = __DIR__ . D . 'state' . D . $id . '.php')) {
    $files[] = $file;
}

// Queue custom translation item(s) from third party extension(s) and layout(s)
foreach (glob(LOT . D . '{x,y}' . D . '*' . D . 'language' . D . $id . '.php', GLOB_BRACE | GLOB_NOSORT) as $file) {
    if (!is_file(dirname($file, 2) . D . 'index.php')) {
        continue;
    }
    $files[] = $file;
}

// Load and merge translation item(s) from queue
foreach ($files as $file) {
    $data = (static function ($f) {
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