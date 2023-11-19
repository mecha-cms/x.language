<?php

Hook::set('_', function ($_) use ($state) {
    if ('.state' !== $_['path']) {
        return $_;
    }
    if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot'])) {
        $languages = [];
        foreach (g(LOT . D . 'x' . D . 'language' . D . 'state', 'php') as $k => $v) {
            $languages[$n = basename($k, '.php')] = $n;
        }
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['language'] = [
            'hint' => 'en',
            'lot' => $languages,
            'name' => 'state[language]',
            'stack' => 30.1,
            'type' => 'text',
            'value' => $state->language ?? null
        ];
    }
    return $_;
}, 0);