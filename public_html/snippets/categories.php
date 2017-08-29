<?php
if (fnmatch('*/development/*', __FILE__)) {
    define('DEVELOPMENT', true);
} else {
    define('DEVELOPMENT', false);
}

function httpStrip($url) {
    if (substr($url, 0, '5') != 'https') {
        return substr($url, strpos($url, '//'));
    }
    return $url;
}

if (!preg_match('/^www/', $_SERVER['HTTP_HOST'])) {
    header("Location: https://www.{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    exit;
}

function nameToSlug ($name)
{
    $slug = strtolower($name);
    $slug = str_replace(" ", "-", $slug);
    $slug = str_replace("&", "", $slug);
    $slug = str_replace("  ", " ", $slug);
    $slug = str_replace("--", "-", $slug);

    return $slug;
}

function slugToName ($slug)
{
    $slug = str_replace("---", " - ", $slug);
    $slug = str_replace("--", " & ", $slug);
    $slug = ucfirst(preg_replace_callback("/(\-\w| \w)/", function ($match) {
        return strtoupper(str_replace("-", " ", $match[0]));
    }, $slug));
    return str_replace(" A ", " a ", $slug);
}

function slugToCategoryId($slug)
{
    global $parent_categories;
    $name = slugToName($slug);
    foreach ($parent_categories as $cat)
    {
        if (slugMatches($slug, nameToSlug($cat['name'])))
            return $cat['id'];
    }
}

function slugToWords ($slug)
{
    return explode('-', $slug);
}

function slugMatches ($slug1, $slug2)
{
    $slug1 = slugToWords($slug1);
    $slug2 = slugToWords($slug2);

    if (count($slug1) != count($slug2)) {
        return false;
    }

    for ($i=0, $l=count($slug1); $i<$l; $i++) {
        if ($slug1[$i] != $slug2[$i])
            return false;
    }
    return true;
}

function slugToServiceId($slug)
{
    global $service_types;
    $name = slugToName($slug);
    foreach ($service_types as $cat)
    {
        if (slugMatches($slug, nameToSlug($cat['name'])))
            return $cat['id'];
    }
}

function makeUrl ()
{
    $args = func_get_args();
    if (DEVELOPMENT) {
        $args[0] = '/development/' . ltrim($args[0], '/');
    }
    return call_user_func_array('sprintf', $args);
}

$url_prefixes = makeUrl('/sys' . (DEVELOPMENT ? '/app_dev.php' : ''));

$url = sprintf(
    "%s://%s{$url_prefixes}/internal/services/services",
    empty($_SERVER['HTTPS']) ? 'http' : 'https',
    $_SERVER['HTTP_HOST']
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
if (DEVELOPMENT) {
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY ) ;
    curl_setopt($ch, CURLOPT_USERPWD, "porter-dev:fast123!DEV");
}
$result = curl_exec($ch);
$service_data = json_decode($result, true);
$page_data = null;

$parent_categories = [];
$service_types = [];
$wildcard_services = []; // if a service has no group, always show it.

foreach ($service_data as $sub_type) {
    if (!isset($sub_type['group']) || !isset($sub_type['group']['id'])) {
        $wildcard_services[] = $sub_type;
    } else if (!isset($parent_categories[$sub_type['group']['id']])) {
        $parent_categories[$sub_type['group']['id']] = $sub_type['group'];
    }
    if (!isset($service_types[$sub_type['id']])) {
        $service_types[$sub_type['id']] = $sub_type;
    }
}

// siteground doesnt handle htaccess properly, so take url info from REQUEST_URI
$uri_bits = explode('/', $_SERVER['REQUEST_URI']);

if ($uri_bits[1] == 'category') {
    $_GET['cat'] = $uri_bits[2];
} else if ($uri_bits[1] == 'order') {
    if (isset($uri_bits[3]))
        $_GET['page'] = $uri_bits[3];
    else
        $_GET['page'] = $uri_bits[2];
}

$name = slugToName($_GET['cat']);

if (!isset($_GET['cat']) || empty($_GET['cat'])) {
    $category_display_type = 'show_parent';
} else {
    $category_id = slugToCategoryId($_GET['cat']);

    $category_display_type = 'show_category';
}
