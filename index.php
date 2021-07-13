<?php

require '4Mbps.php';

$articles = get_articles();
?>
<html lang="en">
<head>
    <title>4Mbps</title>
</head>
<style>
    * {
        font-size: large;
    }

    body {
        background-color: #2a2c42;
        color: #b8cadb;
        font-family: Avenir, sans-serif;
        /*text-align: center;*/
        align-items: center;
    }

    div {
        margin: 1em;
    }

    a {
        color: aqua;
        font-weight: bold;
    }

    table {
        border: greenyellow 1px dashed;
    }

    table, th, td {
        padding: .5em;
    }

    span.outgoing {
        color: aqua;
        font-weight: bold;
    }

    #url-in {
        width: 300px;
    }
</style>
<body>
<div>
    <table>
        <thead>
        <tr>
            <th hidden>Unix timestamp</th>
            <th>Category</th>
            <th>Title</th>
            <th>Date</th>
            <th>URL</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($articles as $timestamp => $article) {
            echo '<tr>';
            echo "<td hidden>{$timestamp}</td>";
            echo '<td>' . join(': ', $article['categories']) . '</td>';
            echo "<td>${article['title']}</td>";
            echo '<td>' . date_format($article['date'], 'Y-m-d h:m:s') . '</td>';
            echo "<td><span class='outgoing'>Here</span></td>";
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<div>
    <label for="url-in">... Or provide your URL:&nbsp;</label>
    <input type="text" id="url-in">
</div>
</body>
</html>
