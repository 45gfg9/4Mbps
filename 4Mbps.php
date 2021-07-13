<?php

const HOST = 'https://www.minecraft.net';
const API = HOST . '/content/minecraft-net/_jcr_content.articles.grid';

const DEV = 0;

function request(string $url): string {
    $req = curl_init($url);
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
    $ret = curl_exec($req);
    curl_close($req);

    return $ret;
}

function read_api(): array {
    $json = defined('DEV') ? file_get_contents('api.json') : request(API);
    return json_decode($json, true);
}

function parse_api(array $raw_articles): array {
    $articles = [];
    foreach ($raw_articles as $raw_article) {
        $title = $raw_article['default_tile'];
        $entry['title'] = $title['title'];
        $entry['sub_header'] = $title['sub_header'];
        $entry['lang'] = $raw_article['articleLang'];
        $entry['category'] = $raw_article['primary_category'];
        $entry['categories'] = $raw_article['categories'];
        $entry['url'] = HOST . $raw_article['article_url'];
        $date = date_create($raw_article['publish_date']);
        $entry['date'] = $date;
        $articles[date_timestamp_get($date)] = $entry;
    }
    return $articles;
}

function get_articles(): array {
    return parse_api(read_api()['article_grid']);
}
