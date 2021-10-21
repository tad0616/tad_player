<?php
use Xmf\Request;
require_once __DIR__ . '/header.php';

$url = Request::getUrl('url');

$date['metaTags']['description']['value'] = $date['title'] = '';
if (!empty($url)) {
    $date = getUrlData($url);
    $web['description'] = $date['metaTags']['description']['value'];

    $youtube_id = getYTid($url);

    $url = "https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v={$youtube_id}&format=json";
    $contents = vita_get_url_content($url);
    $contents = utf8_encode($contents);
    $ytb = json_decode($contents, true);
    // die(var_export($ytb));
    $web['author'] = $ytb['author_name'];
    $web['title'] = $ytb['title'];

    echo json_encode($web);
}

function getUrlData($url)
{
    $result = false;
    $contents = getUrlContents($url);
    if (isset($contents) && is_string($contents)) {
        $title = null;
        $metaTags = null;
        preg_match('/<title>([^>]*)<\/title>/si', $contents, $match);
        if (isset($match) && is_array($match) && count($match) > 0) {
            $title = strip_tags($match[1]);
        }
        preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
        if (isset($match) && is_array($match) && 3 == count($match)) {
            $originals = $match[0];
            $names = $match[1];
            $values = $match[2];
            if (count($originals) == count($names) && count($names) == count($values)) {
                $metaTags = [];
                for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
                    $metaTags[$names[$i]] = [
                        'html' => htmlentities($originals[$i]),
                        'value' => $values[$i],
                    ];
                }
            }
        }
        $result = [
            'title' => $title,
            'metaTags' => $metaTags,
        ];
    }

    return $result;
}

function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0)
{
    $result = false;
    $contents = vita_get_url_content($url);
    if (isset($contents) && is_string($contents)) {
        preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);
        if (isset($match) && is_array($match) && 2 == count($match) && 1 == count($match[1])) {
            if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections) {
                return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
            }
            $result = false;
        } else {
            $result = $contents;
        }
    }

    return $contents;
}
