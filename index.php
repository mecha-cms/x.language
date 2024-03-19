<?php

namespace {
    $files = [];
    $name = $state->language ?? \P;
    // Queue default translation item(s) from this extension
    if (\is_file($v = __DIR__ . \D . 'state' . \D . $name . '.php')) {
        $files[] = $v;
    }
    // Queue custom translation item(s) from third party extension(s) and layout(s)
    foreach (\glob(\LOT . \D . '{x,y}' . \D . '*' . \D . 'language' . \D . $name . '.php', \GLOB_BRACE | \GLOB_NOSORT) as $v) {
        if (!\is_file(\dirname($v, 2) . \D . 'index.php')) {
            continue;
        }
        $files[] = $v;
    }
    // Load and merge translation item(s) in queue
    foreach ($files as $file) {
        $data = \fire(function () {
            \extract(\lot());
            return (array) require $this->path;
        }, [], (object) ['path' => $file]);
        // Automatic lower-case mode
        foreach ($data as $k => $v) {
            $kk = \strtolower($k);
            if ($k === $kk) {
                continue;
            }
            $data[$kk] = \l($v);
        }
        \lot('I', \array_replace(\lot('I') ?? [], $data));
    }
}

namespace x\language {
    function route($content, $path) {
        if (null !== $content) {
            return $content;
        }
        if ($path !== '/language.json') {
            return $content;
        }
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            // TODO
            return $content;
        }
        \extract(\lot(), \EXTR_SKIP);
        $files = $lot = [];
        $fire = $_GET['fire'] ?? null;
        $names = \explode(',', $_GET['name'] ?? $state->language ?? 'en');
        foreach ($names as $name) {
            if (!\is_file($v = __DIR__ . \D . 'state' . \D . $name . '.php')) {
                continue;
            }
            $files[] = $v;
            foreach (\glob(\LOT . \D . '{x,y}' . \D . '*' . \D . 'language' . \D . $name . '.php', \GLOB_BRACE | \GLOB_NOSORT) as $v) {
                if (!\is_file(\dirname($v, 2) . \D . 'index.php')) {
                    continue;
                }
                $files[] = $v;
            }
        }
        foreach ($files as $file) {
            $lot = \array_replace($lot, \fire(function () {
                \extract(\lot());
                return (array) require $this->path;
            }, [], (object) ['path' => $file]));
            foreach ($lot as $k => $v) {
                $kk = \strtolower($k);
                if ($k === $kk) {
                    continue;
                }
                $lot[$kk] = \l($v);
            }
        }
        $r = [
            'alert' => [],
            'count' => 0,
            'description' => $lot['.description'] ?? null,
            'lot' => [],
            'name' => \end($names), // The last name
            'names' => $names,
            'status' => 404,
            'title' => $lot['.title'] ?? \end($names)
        ];
        unset($lot['.description'], $lot['.title']);
        if ($lot) {
            $r['alert']['success'][] = \i('Success.');
            $r['count'] = \count($lot);
            $r['lot'] = $lot;
            $r['status'] = 200;
            // Validate function name
            if ($fire && !\preg_match('/^[a-z_$][\w$]*(\.[a-z_$][\w$]*)*$/i', $fire)) {
                $fire = false;
                $r['alert']['error'][] = \i('Invalid function name: %s', '`' . $fire . '`');
                $r['lot'] = [];
                $r['status'] = 403;
            }
        } else {
            $r['alert']['error'][] = \i('Error.');
        }
        $age = 60 * 60 * 24; // Cache for a day
        $content = \To::JSON($r);
        \status($r['status'], $lot ? [
            'cache-control' => 'max-age=' . $age . ', private',
            'content-type' => 'application/' . ($fire ? 'javascript' : 'json') . '; charset=utf-8',
            'expires' => \gmdate('D, d M Y H:i:s', $age + $_SERVER['REQUEST_TIME']) . ' GMT',
            'pragma' => 'private'
        ] : [
            'cache-control' => 'max-age=0, must-revalidate, no-cache, no-store',
            'content-type' => 'application/' . ($fire ? 'javascript' : 'json') . '; charset=utf-8',
            'expires' => '0',
            'pragma' => 'no-cache'
        ]);
        return ($fire ? $fire . '(' : "") . $content . ($fire ? ');' : "");
    }
    \Hook::set('route', __NAMESPACE__ . "\\route", 1);
}