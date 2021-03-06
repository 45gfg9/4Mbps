<?php

require_once 'Converter.php';
require_once '4Mbps.php';
require_once 'spx_api.php';

use voku\helper\HtmlDomParser;

if (isset($_POST['path'])) {
    // TODO cache page
    // TODO more elegant form data passing
    $path = $_POST['path'];
    assert(str_starts_with($path, PATH_PREFIX), 'Invalid path');

//    $dom = HtmlDomParser::file_get_html(HOST . $path);
    $dom = HtmlDomParser::str_get_html(request(HOST . $path));

    $title = $_POST['title'];
    $translator = $_POST['translator'];
    try {
        $dt = new DateTime($_POST['date']);
    } catch (Exception $e) {
        http_response_code(400);
        die('Invalid DateTime provided');
    }
} else if (defined('DEV')) {
    die('DEBUG ENVIRONMENT DISABLED');
} else {
    http_response_code(400);
    die();
}

$result = (new Converter($title, HOST . $path, $translator, $dt, $dom))->get_result();
?>
<!DOCTYPE HTML>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>4Mbps Editor</title>
    <link rel="stylesheet" href="4Mbps.css">
    <link rel="stylesheet" href="editor.css">
</head>
<body>
<div><?php
    foreach ($result[1] as $i => $text) {
        echo <<< EO4
<div class="entry-block">
<div class="word-block" data-id="$i">
<p><span>$text</span></p>
<div class="div-input" contenteditable="true"></div>
</div>
<div class="tag">Tag</div>
</div>
EO4;
    }
    ?>
</div>
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

            ret = ret.replace(`{{${id}}}`, text)
        })
        const output = document.getElementById('formatted')
        output.textContent = ret
        output.hidden = undefined

        return ret
    }

    document.querySelectorAll('.div-input').forEach((b) => {
        b.addEventListener('paste', (e) => {
            e.preventDefault()
            const text = e.clipboardData.getData('text/plain')
            // execCommand is obsolete. Is there a better way?
            document.execCommand('insertText', false, text)
        })
    })

    new ClipboardJS('#get-result', { text: getBBCode })
</script>
</body>
</html>
