<?php

/**
 * FontAwesome Pro Downloader
 * By NimaH79
 * http://nimah79.ir.
 */
class FontAwesomeDownloader
{
    private $base_url = 'https://kit-pro.fontawesome.com';

    private $version;
    private $download_path;

    const LATEST_VERSION = 'latest';

    public function __construct($version, $download_path = null)
    {
        if ($version == self::LATEST_VERSION) {
            $this->version = json_decode($this->curlRequest('https://api.github.com/repos/FortAwesome/Font-Awesome/releases/latest')['response'], true)['tag_name'];
        } else {
            $this->version = $version;
        }
        if (is_null($download_path)) {
            $this->download_path = __DIR__.'/fontawesome_v'.$this->version.'_pro';
        } else {
            $this->download_path = rtrim($download_path, '/');
        }
        file_exists($this->download_path) || mkdir($this->download_path, 0755, true);
    }

    public function download()
    {
        $css_base_url = $this->base_url.'/releases/v'.$this->version.'/css';
        $fonts_path = $this->download_path.'/fonts';
        $css = $this->curlRequest($css_base_url.'/pro.min.css')['response'];
        file_exists($fonts_path) || mkdir($fonts_path, 0755, true);
        foreach ($this->extractCssPaths($css) as $path) {
            $font_url = $path;
            if (filter_var($path, FILTER_VALIDATE_URL) === false) {
                $font_url = $this->normalizeUrl($css_base_url.'/'.$path);
            }
            $font_name = preg_replace('/\??#(iefix|fontawesome)/', '', basename($path));
            $this->curlDownloadFile($font_url, $fonts_path.'/'.$font_name);
            $css = str_replace($path, 'fonts/'.preg_replace('/\??#(iefix|fontawesome)/', '?#$1', basename($path)), $css);
        }
        file_put_contents($this->download_path.'/pro.min.css', $css);
    }

    private function curlRequest($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['response' => $response, 'http_code' => $http_code];
    }

    public function curlDownloadFile($url, $file_path)
    {
        $ch = curl_init($url);
        $fp = fopen($file_path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    private function extractCssPaths($css)
    {
        if (preg_match_all('/url\((.*?)\)/', $css, $paths)) {
            return $paths[1];
        }

        return [];
    }

    private function normalizeUrl($url)
    {
        $parsed_url = parse_url($url);

        return $parsed_url['scheme'].'://'.$parsed_url['host'].$this->normalizePath($parsed_url['path']);
    }

    private function normalizePath($path)
    {
        $parts = preg_split(':[\\\/]:', $path);

        for ($i = 0; $i < count($parts); $i++) {
            if ($parts[$i] === '..') {
                if ($i === 0) {
                    throw new Exception('Cannot resolve path, path seems invalid: '.$path);
                }
                unset($parts[$i - 1]);
                unset($parts[$i]);
                $parts = array_values($parts);
                $i -= 2;
            } elseif ($parts[$i] === '.') {
                unset($parts[$i]);
                $parts = array_values($parts);
                $i -= 1;
            }
            if ($i > 0 && $parts[$i] === '') {
                unset($parts[$i]);
                $parts = array_values($parts);
            }
        }

        return implode('/', $parts);
    }
}
