<?php


$url = sprintf(
    '%s://%s/sys/app_dev.php/internal/services/services',
    empty($_SERVER['HTTPS']) ? 'http' : 'https',
    $_SERVER['HTTP_HOST']
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
        $parent_categories[] = $sub_type['group'];
    }
    if (!isset($service_types[$sub_type['id']])) {
        $service_types[] = $sub_type;
    }
}


if (!isset($_GET['cat'])) {
    $type = 'show_parent';
} else {
    $type = 'show_category';
}

if ()
?>
