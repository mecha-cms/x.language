<?php

namespace {
    function language(): array {
        $languages = [];
        foreach (\explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "") as $v) {
            if (false === \strpos($v = \trim($v), ';')) {
                $languages[$v] = 1.0;
                continue;
            }
            $any = \explode(';', $v);
            $language = \trim(\array_shift($any));
            $order = 1.0;
            foreach ($any as $vv) {
                if (0 === \strpos($vv = \trim($vv), 'q=')) {
                    $order = (float) \substr($vv, 2);
                    break;
                }
            }
            $languages[$language] = $order;
        }
        \arsort($languages);
        return $languages;
    }
}

namespace x\language {
    function of(array $names) {
        $files = [];
        $lot = (array) \lot('I');
        foreach ($names as $k => $v) {
            if (!\is_file($file = __DIR__ . \D . 'state' . \D . $v . '.php')) {
                unset($names[$k]);
                continue;
            }
            // Queue language item(s) from this extension
            $files[] = $file;
            // Queue language item(s) from other extension(s) and layout(s)
            foreach (\glob(\LOT . \D . '{x,y}' . \D . '*' . \D . 'language' . \D . $v . '.php', \GLOB_BRACE | \GLOB_NOSORT) as $file) {
                if (!\is_file(\dirname($vv, 2) . \D . 'index.php')) {
                    continue;
                }
                $files[] = $file;
            }
        }
        if (!$files) {
            return [$names, $lot];
        }
        // Load and merge language item(s) in queue
        foreach ($files as $file) {
            $lot = \array_replace($lot, \fire(function () {
                \extract(\lot());
                return (array) require $this->path;
            }, [], (object) ['path' => $file]));
            // Automatic lower-case mode
            foreach ($lot as $k => $v) {
                $kk = \strtolower($k);
                if ($k === $kk) {
                    continue;
                }
                $lot[$kk] = \l($v);
            }
        }
        return [$names, $lot];
    }
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
        $fire = $_GET['fire'] ?? null;
        $languages = \array_merge(\explode(',', (string) ($_GET['name'] ?? $state->language ?? 'en')), \array_keys(\language()));
        [$names, $lot] = of($languages = \array_unique($languages));
        $r = [
            'alert' => [],
            'count' => 0,
            'description' => $lot['.description'] ?? null,
            'lot' => [],
            'name' => $name = \end($names) ?: null, // The last name
            'names' => $names,
            'status' => 404,
            'title' => $lot['.title'] ?? $name
        ];
        unset($lot['[description]'], $lot['[title]']);
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
    function set() {
        \extract(\lot());
        \lot('I', of(\explode(',', (string) $state->language ?? \P))[1]);
    }
    \Hook::set('route', __NAMESPACE__ . "\\route", 1);
    \Hook::set('set', __NAMESPACE__ . "\\set", 1);
}