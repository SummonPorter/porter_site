<?php
function incl ($file, $ext='php')
{
    return realpath(__DIR__.'/'.$file.'.'.$ext);
}

$parts = parse_url($_SERVER['REQUEST_URI']); // /development/asd/asd.html?asdasdasd

if (DEVELOPMENT && fnmatch('/development/*', $parts['path'])) {
    $parts['path'] = substr($parts['path'], strlen('/development'));
}

$parts['dirs'] = explode('/', trim($parts['path'],'/'));
if ($parts['path'] == '/') {
    $file = incl('index');
} else if (fnmatch('/become-a-porter/', $parts['path']) || fnmatch('/become-a-porter', $parts['path'])) {
    $file = incl('become-a-porter/index');
} else if (fnmatch('/how-it-works/', $parts['path']) || fnmatch('/how-it-works', $parts['path'])) {
    $file = incl('how-it-works/index');
} else if (fnmatch('/privacy-policy/', $parts['path']) || fnmatch('/privacy-policy', $parts['path'])) {
    $file = incl('privacy-policy/index');
} else if (fnmatch('/terms-of-use/', $parts['path']) || fnmatch('/terms-of-use', $parts['path'])) {
    $file = incl('terms-of-use/index');
} else if (fnmatch('/login/', $parts['path']) || fnmatch('/login', $parts['path'])) {
    $file = incl('login/index');
} else if (preg_match('#/order/[a-z0-9_-]+/[a-z0-9_-]+/?#', $parts['path'])) {
    $_GET['page'] = $parts['dirs'][2];
    $file = incl('order/index');
} else if (fnmatch('/order/*', $parts['path']) || preg_match('#/order/[a-z0-9_-]+/#', $parts['path'])) {
    $_GET['page'] = $parts['dirs'][1];
    $file = incl('order/index');
} else if (fnmatch('/category/*', $parts['path'])) {
    $_GET['cat'] = $parts['dirs'][1];
    $file = incl('category/index');
}

require_once 'snippets/categories.php';
include $file;
