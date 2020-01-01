<?php

if (is_file($f = __DIR__ . DS . 'state' . DS . $state->language . '.php')) {
    $data = (function($f) {
        extract($GLOBALS, EXTR_SKIP);
        return (array) require $f;
    })($f);
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
