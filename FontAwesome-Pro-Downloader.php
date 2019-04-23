<?php

set_time_limit(0);

define('DOWNLOAD_PATH', __DIR__.'/fontawesome_pro_version');

if (php_sapi_name() == 'cli') {
    define('NEWLINE', PHP_EOL);
} else {
    define('NEWLINE', '<br>');
}

function curl_get_contents($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36');
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

define('VERSION', 'v'.json_decode(curl_get_contents('https://api.github.com/repos/FortAwesome/Font-Awesome/releases/latest'), true)['tag_name']);

echo 'Downloading Font Awesome '.VERSION.'…'.NEWLINE;

$urls = [
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/js/all.js',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/js/solid.js',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/js/regular.js',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/js/brands.js',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/js/fontawesome.js',

    'https://pro-next.fontawesome.com/releases/'.VERSION.'/css/all.css',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/css/solid.css',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/css/regular.css',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/css/brands.css',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/css/fontawesome.css',

    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-regular-400.eot',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-regular-400.woff2',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-regular-400.woff',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-regular-400.ttf',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-regular-400.svg',

    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-light-300.eot',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-light-300.woff2',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-light-300.woff',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-light-300.ttf',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-light-300.svg',

    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-solid-900.eot',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-solid-900.woff2',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-solid-900.woff',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-solid-900.ttf',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-solid-900.svg',

    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-brands-400.eot',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-brands-400.woff2',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-brands-400.woff',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-brands-400.ttf',
    'https://pro-next.fontawesome.com/releases/'.VERSION.'/webfonts/fa-brands-400.svg',
];

function curl_download_font($url, $file_path)
{
    $ch = curl_init($url);
    $fp = fopen($file_path, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Origin: https://fontawesome.com']);
    curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36');
    curl_setopt($ch, CURLOPT_REFERER, 'https://pro-next.fontawesome.com/releases/'.VERSION.'/css/all.css');
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}

$download_path = str_replace('version', VERSION, DOWNLOAD_PATH);

if (file_exists($download_path) || mkdir($download_path)) {
    foreach ($urls as $url) {
        curl_download_font($url, $download_path.'/'.basename($url));
    }
    echo 'Download finished!';
} else {
    echo 'Unable to make directory.';
}
