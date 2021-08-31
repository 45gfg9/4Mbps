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
        require_once '4Mbps.php';

        foreach (get_articles() as $timestamp => $article) {
            if (!str_starts_with($article['url'], HOST . PATH_PREFIX))
                continue;

            $categories = $article['categories'];
            echo '<tr>';
            echo "<td hidden>$timestamp</td>";
            echo '<td><b>' . $categories[0] . '</b>';
            if (count($categories) > 1)
                echo '<i>: ' . join(': ', array_slice($categories, 1)) . '</i>';
            echo '</td>';
            echo "<td class='title'><a class='title' href='${article['url']}' target='_blank'>${article['title']}</a></td>";
            echo '<td class="date">' . date_format($article['date'], 'Y-m-d H:i:s') . '</td>';
            echo "<td><button class='invert take'>Take!</button></td>";
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<div>
    <form name="custom" action="editor.php" method="post">
        <input type="hidden" name="translator">
        <fieldset>
            <legend>Not in the list?</legend>
            <table style="border: initial">
                <tbody>
                <tr>
                    <td><label for="url-in">URL:&nbsp;</label></td>
                    <td><input type="url" name="path" id="url-in" size="50"
                               placeholder="<?php echo HOST ?>/en-us/article/"
                               pattern="<?php echo HOST . PATH_PREFIX ?>.*" required></td>
                </tr>
                <tr>
                    <td><label for="title-in">Title: </label></td>
                    <td><input type="text" name="title" id="title-in" required></td>
                </tr>
                <tr>
                    <td><label for="date-in">Date: </label></td>
                    <td><input type="date" name="date" id="date-in" required></td>
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
    </form>
</div>
<script type="text/javascript">
    const HOST = '<?php echo HOST ?>'
    const PATH_PREFIX = '<?php echo PATH_PREFIX ?>'

    const form = document.forms['custom']

    function getPath(url) {
        if (url.startsWith(HOST))
            url = url.substr(HOST.length)
        if (!url.startsWith(PATH_PREFIX))
            throw 'Given URL is not a valid minecraft.net article!'
        return url
    }

    function getTranslator() {
        return document.getElementById('translator').value || '<Anonymous>'
    }

    document.querySelectorAll('button.take').forEach((b) => {
        b.addEventListener('click', () => {
            const tr = b.parentNode.parentNode
            const path = tr.querySelector('a.title').href
            const title = tr.querySelector('td.title').textContent
            const date = tr.querySelector('td.date').textContent.substr(0, 10)

            form.path.value = getPath(path)
            form.title.value = title
            form.date.value = date

            form.translator.value = getTranslator()

            form.submit()
        })
    })

    form.addEventListener('submit', () => {
        form.translator.value = getTranslator()

        const urlInput = form.querySelector('#url-in')
        urlInput.value = getPath(urlInput.value)
    })
</script>
</body>
</html>
