<?php
include_once "header.php";
//$_POST['url']="https://www.youtube.com/watch?v=6i7RcP39NB0";
$date['metaTags']['description']['value'] = $date['title'] = '';
if (!empty($_POST['url'])) {
    $date = getUrlData($_POST['url']);
    //die(var_export($date));
    $web['title']       = $date['title'];
    $web['description'] = $date['metaTags']['description']['value'];

    $youtube_id = getYTid($_POST['url']);

    $url           = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v={$youtube_id}&format=json";
    $contents      = file_get_contents($url);
    $contents      = utf8_encode($contents);
    $ytb           = get_object_vars(json_decode($contents));
    $web['author'] = $ytb['author_name'];

    echo json_encode($web);
}

function getUrlData($url) {
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
                        'value' => $values[$i]
                    );
                }
            }
        }
        $result = array(
            'title'    => $title,
            'metaTags' => $metaTags
        );
    }

    return $result;
}

function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0) {
    $result   = false;
    $contents = @file_get_contents($url);
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

//檢查必要函數
function chk_function() {
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
    function json_encode($a = false) {
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
                return floatval(str_replace(",", ".", strval($a)));
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
