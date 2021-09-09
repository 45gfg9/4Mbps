<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>4Mbps Editor</title>
    <link rel="stylesheet" href="4Mbps.css">
    <link rel="stylesheet" href="editor.css">
</head>
<body>
<div id="editor"></div>
<div class="result">
    <button class="invert" onclick="collect.all()">Get Result</button>
    <div class="result-bbcode" hidden></div>
</div>
<script id="doc-tree" type="application/json"><?php readfile('test.json') ?></script>
<script src="parser.js"></script>
</body>
</html>
