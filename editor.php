<?php

require_once 'vendor/autoload.php';
require 'Converter.php';
require '4Mbps.php';

use voku\helper\HtmlDomParser;

if (isset($_GET['url'])) {
    // TODO cache page
    $url = $_GET['url'];
    assert(str_starts_with($url, PATH_PREFIX), 'Invalid path');

    $article = get_articles()[HOST . $url];
    $dom = HtmlDomParser::file_get_html($article['url']);
} else if (defined('DEV')) {
   die('DEBUG ENVIRONMENT DISABLED');
} else {
    http_response_code(403);
    die();
}

$result = (new Converter("Around the Block: Basalt Deltas",
    "/en-us/article/around-block--basalt-deltas",
    "45gfg9",
    new DateTime("13 August 2020 14:45:03 UTC"),
    $dom))->get_result();
?>
<!DOCTYPE HTML>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>4Mbps Editor</title>
    <link rel="stylesheet" href="4Mbps.css">
    <style>
        div.word-block {
            margin: 1em;
        }

        div.result {
            text-align: center;
        }

        #formatted {
            text-align: initial;
            font-size: initial;
        }
    </style>
</head>
<body>
<div><?php
    foreach ($result[1] as $i => $text) {
        echo <<< EO4
<div class="word-block" data-id="$i">
<p><span>$text</span></p>
<div class="div-input" contenteditable="true"></div>
</div><br>
EO4;
    }
    ?>
</div>
<hr>
<div class="result">
    <button class="invert" id="get-result">Get Result!</button>
    <pre id="formatted" contenteditable="true" hidden></pre>
</div>
<script type="text/plain" id="raw-bbcode"><?php echo $result[0]; ?></script>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script type="text/javascript">
    function getBBCode() {
        let ret = document.getElementById('raw-bbcode').textContent
        document.querySelectorAll('.word-block').forEach((b) => {
            const id = b.getAttribute('data-id')
            const text = b.querySelector('.div-input').textContent.trim()

            const suffix = text ? '' : '\n'

            ret = ret.replace(`{{${id}}}` + suffix, text)
        })
        const output = document.getElementById('formatted')
        output.textContent = ret
        output.hidden = undefined

        return ret
    }

    new ClipboardJS('#get-result', {text: getBBCode})
</script>
</body>
</html>
