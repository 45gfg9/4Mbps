<?php

namespace spx;

require_once '4Mbps.php';

function translate_bug(string|array $id): array {
    // Bug translations provided by SPX
    static $tr_bugs;

    if (is_null($tr_bugs))
        $tr_bugs = json_decode(request('https://spx.spgoding.com/bugs'), true);

    if (is_string($id)) {
        return array_key_exists($id, $tr_bugs) ? $tr_bugs[$id] : [];
    } else {
        return array_map('\spx\translate_bug', $id);
    }
}
