<?php
require '4Mbps.php';

$articles = get_articles();
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>4Mbps</title>
    <link rel="stylesheet" href="4Mbps.css">
</head>
<style>
    a.title {
        color: inherit;
        font-weight: inherit;
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
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($articles as $timestamp => $article) {
            $categories = $article['categories'];
            echo '<tr>';
            echo "<td hidden>$timestamp</td>";
            echo '<td>'
                . '<b>' . $categories[0] . '</b>';
            if (count($categories) > 1)
                echo '<i>: '. join(': ', array_slice($categories, 1)) . '</i>';
            echo '</td>';
            echo "<td><a class='title' href='${article['url']}' target='_blank'>${article['title']}</td>";
            echo '<td>' . date_format($article['date'], 'Y-m-d h:m:s') . '</td>';
            echo "<td><button class='invert take'>Take!</button></td>";
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<div>
    <label for="url-in">... Or provide your URL:&nbsp;</label>
    <input type="text" id="url-in" size="50">
    <button class="invert" id="custom-go" type="submit">Go</button>
</div>
<script type="text/javascript">
    document.querySelector('#custom-go').addEventListener('click', () => {
        const input = document.querySelector('#url-in').value
        // TODO
    })

    document.querySelectorAll('button.take').forEach((b) => {
        b.addEventListener('click', () => {
            const timestamp = b.parentNode.parentNode.firstChild.textContent;
            // TODO
        })
    })
</script>
</body>
</html>
