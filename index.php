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

    fieldset {
        border: var(--4Mbps-text-color) 1px dashed;
    }

    b, strong {
        color: white;
    }
</style>
<body>
<div>
    <div>
        <label for="translator">Your name:&nbsp;</label>
        <input type="text" id="translator">
    </div>
    <table>
        <thead>
        <tr>
            <th hidden>Unix timestamp</th>
            <th>Category</th>
            <th>Title</th>
            <th>Time</th>
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
    <fieldset>
        <legend>Not in the list?</legend>
        <table style="border: initial">
            <tbody>
            <tr>
                <td><label for="url-in">URL:&nbsp;</label></td>
                <td><input type="url" id="url-in" size="50" placeholder="<?php echo HOST ?>/en-us/article/"
                           pattern="<?php echo HOST ?>/en-us/article/.*"></td>
            </tr>
            <tr>
                <td><label for="title-in">Title: </label></td>
                <td><input type="text" id="title-in"></td>
            </tr>
            <tr>
                <td><label for="date-in">Date: </label></td>
                <td><input type="date" id="date-in"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="invert" id="custom-go" type="submit">Go!</button>
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>
</div>
<script type="text/javascript">
    document.querySelectorAll('button.take').forEach((b) => {
        b.addEventListener('click', () => {
            const url = b.parentNode.parentNode.querySelector('a').getAttribute('href')
            // TODO
        })
    })

    document.getElementById('custom-go').addEventListener('click', () => {
        const url = document.getElementById('url-in').value
        // TODO
    })
</script>
</body>
</html>
