<?php
include_once __DIR__ . '/header.php';
if (!isset($_POST['url'])) {
    $_POST['url'] = "https://www.youtube.com/watch?v=WbnG6-SmcXA";
}

$date['metaTags']['description']['value'] = $date['title'] = '';
if (!empty($_POST['url'])) {
    $date               = getUrlData($_POST['url']);
    $web['description'] = $date['metaTags']['description']['value'];

    $youtube_id = getYTid($_POST['url']);

    $url      = "https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v={$youtube_id}&format=json";
    $contents = file_get_contents($url);
    $contents = utf8_encode($contents);
    $ytb      = json_decode($contents, true);
    // die(var_export($ytb));
    $web['author'] = $ytb['author_name'];
    $web['title']  = $ytb['title'];

    echo json_encode($web);
}

function getUrlData($url)
{
    $result   = false;
    $contents = getUrlContents($url);
    if (isset($contents) && is_string($contents)) {
        $title    = null;
        $metaTags = null;
        preg_match('/<title>([^>]*)<\/title>/si', $contents, $match);
        if (isset($match) && is_array($match) && count($match) > 0) {
            $title = strip_tags($match[1]);
        }
        preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
        if (isset($match) && is_array($match) && count($match) == 3) {
            $originals = $match[0];
            $names     = $match[1];
            $values    = $match[2];
            if (count($originals) == count($names) && count($names) == count($values)) {
                $metaTags = array();
                for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
                    $metaTags[$names[$i]] = array(
                        'html'  => htmlentities($originals[$i]),
                        'value' => $values[$i],
                    );
                }
            }
        }
        $result = array(
            'title'    => $title,
            'metaTags' => $metaTags,
        );
    }

    return $result;
}

function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0)
{
    $result   = false;
    $contents = vita_get_url_content($url);
    // die(var_export($contents));
    // Check if we need to go somewhere else
    if (isset($contents) && is_string($contents)) {
        preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);
        if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1) {
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

//遠端取得資料
function vita_get_url_content($url)
{
    if (function_exists('curl_init')) {
        $ch      = curl_init();
        $timeout = 5;

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
    } elseif (function_exists('file_get_contents')) {
        $file_contents = file_get_contents($url);
    }

    return $file_contents;
}

//檢查必要函數
function chk_function()
{
    $main = "";
    if (!function_exists('curl_init')) {
        $main .= "<div style='color:red;'>" . sprintf(_MI_TADLINK_NO_FUNCTION, 'curl_init') . "</div>";
    } else {
        $main .= "<div style='color:blue;'>" . sprintf(_MI_TADLINK_FUNCTION_OK, 'curl_init') . "</div>";
    }

    if (!function_exists('file_get_contents')) {
        $main .= "<div style='color:red;'>" . sprintf(_MI_TADLINK_NO_FUNCTION, 'file_get_contents') . "</div>";
    } else {
        $main .= "<div style='color:blue;'>" . sprintf(_MI_TADLINK_FUNCTION_OK, 'file_get_contents') . "</div>";
    }

    return $main;
}

if (!function_exists('json_encode')) {
    function json_encode($a = false)
    {
        if (is_null($a)) {
            return 'null';
        }
        if ($a === false) {
            return 'false';
        }
        if ($a === true) {
            return 'true';
        }
        if (is_scalar($a)) {
            if (is_float($a)) {
                // Always use "." for floats.
                return (float) (str_replace(",", ".", (string) ($a)));
            }

            if (is_string($a)) {
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));

                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            } else {
                return $a;
            }
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
            if (key($a) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList) {
            foreach ($a as $v) {
                $result[] = json_encode($v);
            }

            return '[' . join(',', $result) . ']';
        } else {
            foreach ($a as $k => $v) {
                $result[] = json_encode($k) . ':' . json_encode($v);
            }

            return '{' . join(',', $result) . '}';
        }
    }
}
