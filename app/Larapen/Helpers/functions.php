<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) Mayeul Akpovi. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

/**
 * Default translator (e.g. en/global.php)
 *
 * @param $string
 * @param array $params
 * @param string $file
 * @param null $locale
 * @return string|\Symfony\Component\Translation\TranslatorInterface
 */
function t($string, $params = [], $file = 'global', $locale = null)
{
    if (is_null($locale)) {
        $locale = config('app.locale');
        if (\Illuminate\Support\Facades\Session::has('language_code')) {
            $locale = session('language_code');
        }
    }

    return trans($file . '.' . $string, $params, null, $locale);
}

/**
 * Get URL query parameters
 *
 * @param array $except
 * @return string
 */
function query_params($except = [])
{
    $query = \Illuminate\Support\Facades\Input::query();

    if (!is_array($except)) {
        $except = [$except];
    }

    foreach ($except as $key => $value) {
        if (is_string($key)) {
            $query[$key] = $value;
        } else {
            unset($query[$value]);
        }
    }

    return (http_build_query($query));
}

/**
 * Get default max file upload size (from PHP.ini)
 *
 * @return mixed
 */
function maxUploadSize()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));

    return min($max_upload, $max_post);
}

/**
 * Get max file upload size
 *
 * @return int|mixed
 */
function maxApplyFileUploadSize()
{
    $size = maxUploadSize();
    if ($size >= 5) {
        return 5;
    }

    return $size;
}

/**
 * Check if is an AJAX request
 *
 * exemple:
 * if ( is_ajax() )
 * {
 *        $out = $this->template->load($templates, $page, $data, true);
 *        echo json_encode( array('out' => $out) );
 * }
 * else
 *        $this->template->load($templates, $page, $data);
 */
function is_ajax()
{
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
}

/**
 * Escape JSON string
 *
 * Escape this:
 * \b  Backspace (ascii code 08)
 * \f  Form feed (ascii code 0C)
 * \n  New line
 * \r  Carriage return
 * \t  Tab
 * \"  Double quote
 * \\  Backslash caracter
 *
 * @param $value
 * @return mixed
 */
function escape_json_string($value)
{
    # list from www.json.org: (\b backspace, \f formfeed)
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, trim($value));

    return $result;
}

/**
 * @param string $default_ip
 * @return string
 */
function getIp($default_ip = '')
{
    return \App\Larapen\Helpers\Ip::get($default_ip);
}

/**
 * @return string
 */
function getScheme()
{
	if (isset($_SERVER['HTTPS']) and in_array($_SERVER['HTTPS'], ['on', 1]))
	{
		$protocol = 'https://';
	}
	else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	{
		$protocol = 'https://';
	}
	else if (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true)
	{
		$protocol = 'https://';
	}
	else
	{
        $protocol = 'http://';
    }

    return $protocol;
}


/**
 * Get host (domain with sub-domains)
 *
 * @return string
 */
function getHost()
{
    $host = (trim(\Illuminate\Support\Facades\Request::server('HTTP_HOST')) != '') ? \Illuminate\Support\Facades\Request::server('HTTP_HOST') : $_SERVER['HTTP_HOST'];

    if ($host == '') {
        $parsed_url = parse_url(url()->current());
        if (!isset($parsed_url['host'])) {
            $host = $parsed_url['host'];
        }
    }

    return $host;
}

/**
 * Get domain without any sub-domains
 *
 * @return string
 */
function getDomain()
{
    $host = getHost();
    $tmp = explode('.', $host);
    $tmp = array_reverse($tmp);

    if (isset($tmp[1]) and isset($tmp[0])) {
        $domain = $tmp[1] . '.' . $tmp[0];
    } else if (isset($tmp[0])) {
        $domain = $tmp[0];
    } else {
        $domain = $host;
    }

    return $domain;
}

/**
 * Get sub-domain name
 *
 * @return string
 */
function getSubDomainName()
{
    $host = getHost();
    $name = (substr_count($host, '.') > 1) ? trim(current(explode('.', $host))) : '';

    return $name;
}

/**
 * Generate a querystring url for the application.
 *
 * Assumes that you want a URL with a querystring rather than route params
 * (which is what the default url() helper does)
 *
 * @param null $path
 * @param array $qs
 * @param null $secure
 * @param bool $localized
 * @return mixed|string
 */
function qsurl($path = null, $qs = array(), $secure = null, $localized = true)
{
    if ($localized) {
        $url = lurl($path);
    } else {
        $url = app('url')->to($path, $secure);
    }

    if (count($qs)) {
        foreach ($qs as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $qs[$key][$k] = sprintf('%s[%s]=%s', $key, $k, urlencode($v));
                }
                $qs[$key] = implode('&', $qs[$key]);
            } else {
                $qs[$key] = sprintf('%s=%s', $key, urlencode($value));
            }
        }
        $url = sprintf('%s?%s', $url, implode('&', $qs));
    }

    return $url;
}

/**
 * Get URL (via domain & sub-domain)
 *
 * @param null $path
 * @param null $secure
 * @return mixed
 */
function durl($path = null, $secure = null)
{
    $url = app('url')->to($path, $secure);
    //$url = preg_replace('|([A-Z]{' . strlen(getSubDomainName()) . '}\.' . getDomain() . ')+|i', 'www.' . getDomain(), $url, 1);

    return $url;
}

/**
 * Localized URL
 *
 * @param null $path
 * @param null $locale
 * @return mixed
 */
function lurl($path = null, $locale = null)
{
    return \Larapen\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($locale, $path);
}

/**
 * Non localized URL
 *
 * @param null $path
 * @return mixed
 */
function nolurl($path = null)
{
    return \Larapen\LaravelLocalization\Facades\LaravelLocalization::getNonLocalizedURL($path);
}

/**
 * Format file size
 *
 * @param $bytes
 * @return string
 */
function size_format($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * Format file size (2)
 *
 * @param $size
 * @return string
 */
function file_size($size)
{
    $file_size_name = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

    return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $file_size_name[$i] : '0 Bytes';
}

/**
 * Time ago for human view
 *
 * @param \Carbon\Carbon $dt
 * @param $timeZone
 * @param string $lang_code
 * @return string
 */
function time_ago(\Carbon\Carbon $dt, $time_zone, $lang_code = 'en')
{
    $sec = $dt->diffInSeconds(\Carbon\Carbon::now($time_zone));
    \Carbon\Carbon::setLocale($lang_code);
    $string = mb_ucfirst(\Carbon\Carbon::now($time_zone)->subSeconds($sec)->diffForHumans());

    return $string;
}

/**
 * Get Ad ID from URL segment
 *
 * @return mixed|null
 */
function getAdIdFromURLSegment()
{
    // Get segment
    $segment = getSegment(2);

    // Get the ad ID
    $segment = strip_tags($segment);
    $tmp = explode('-', $segment);
    $last = explode('.', end($tmp));
    $id = current($last);

    if (is_numeric($id)) {
        return $id;
    } else {
        return null;
    }
}

/**
 * @param $index
 * @param null $default
 * @return mixed
 */
function getSegment($index, $default = null)
{
    $index = (int) $index;

    // Default checking
    $segment = \Illuminate\Support\Facades\Request::segment($index, $default);

    // Checking with Default Language parameters
    try {
        $defaultLang = \App\Larapen\Models\Language::where('default', 1)->first();
    } catch (\Exception $e) {
        $defaultLang = null;
    }
    if (!empty($defaultLang)) {
        if (!(config('laravellocalization.hideDefaultLocaleInURL') == true && config('app.locale') == $defaultLang->abbr)) {
            $index = $index + 1;
            $segment = \Illuminate\Support\Facades\Request::segment($index, $default);
        }
    }

    return $segment;
}

/**
 * Get file extension
 *
 * @param $filename
 * @return mixed
 */
function getExtension($filename)
{
    $tmp = explode('?', $filename);
    $tmp = explode('.', current($tmp));
    $ext = end($tmp);

    return $ext;
}

/**
 * String strip
 *
 * @param $string
 * @return string
 */
function str_strip($string)
{
    $string = trim(preg_replace('/\s\s+/u', ' ', $string));

    return $string;
}

/**
 * String cleaner
 *
 * @param $string
 * @return mixed|string
 */
function str_clean($string)
{
    $string = strip_tags($string, '<br><br/>');
    $string = str_replace(array('<br>', '<br/>', '<br />'), "\n", $string);
    $string = preg_replace("/[\r\n]+/", "\n", $string);
    /*
    Remove 4(+)-byte characters from a UTF-8 string
    It seems like MySQL does not support characters with more than 3 bytes in its default UTF-8 charset.
    NOTE: you should not just strip, but replace with replacement character U+FFFD to avoid unicode attacks, mostly XSS:
    http://unicode.org/reports/tr36/#Deletion_of_Noncharacters
    */
    $string = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $string);
    $string = mb_ucfirst(trim($string));

    return $string;
}

/**
 * Fixed: MySQL don't accept the comma format number
 *
 * @param $float
 * @param int $decimals
 * @return mixed
 *
 * @todo: Learn why PHP 5.6.6 changes dot to comma in float vars
 */
function fixFloatVar($float, $decimals = 10)
{
    //$float = number_format($float, $decimals, '.', ''); // Best way !
    //$float = rtrim($float, "0");

    if (strpos($float, ',') !== false) {
        $float = str_replace(',', '.', $float);
    }

    return $float;
}

/**
 * Extract emails from string
 *
 * @param $string
 * @return string
 */
function extract_email_address($string)
{
    $tmp = [];
    preg_match_all('|([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b)|i', $string, $tmp);
    $emails = (isset($tmp[1])) ? $tmp[1] : [];
    $email = head($emails);
    if ($email == '') {
        $tmp = [];
        preg_match("|[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})|i", $string, $tmp);
        $email = (isset($tmp[0])) ? trim($tmp[0]) : '';
        if ($email == '') {
            $tmp = [];
            preg_match("|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b|i", $string, $tmp);
            $email = (isset($tmp[0])) ? trim($tmp[0]) : '';
        }
    }

    return strtolower($email);
}

/**
 * Check if language code is available
 *
 * @param $lang_code
 * @return bool
 */
function is_available_lang($lang_code)
{
    $is_available_lang = collect(\App\Larapen\Models\Language::where('abbr', $lang_code)->first());
    if (!$is_available_lang->isEmpty()) {
        return true;
    } else {
        return false;
    }
}

function getHostByUrl($url)
{
    // in case scheme relative URI is passed, e.g., //www.google.com/
    $url = trim($url, '/');

    // If scheme not included, prepend it
    if (!preg_match('#^http(s)?://#', $url)) {
        $url = 'http://' . $url;
    }

    $urlParts = parse_url($url);

    // remove www
    $domain = preg_replace('/^www\./', '', $urlParts['host']);

    return $domain;
}

/**
 * Auto-link URL in string
 *
 * @param $str
 * @param array $attributes
 * @return mixed|string
 */
function auto_link($str, $attributes = array())
{
    $attrs = '';
    foreach ($attributes as $attribute => $value) {
        $attrs .= " {$attribute}=\"{$value}\"";
    }

    $str = ' ' . $str;
    $str = preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a rel="nofollow" href="$2"' . $attrs . ' target="_blank">$2</a>',
        $str);
    $str = substr($str, 1);

    return $str;
}

/**
 * Check tld is a valid tld
 *
 * @param $url
 * @return bool|int
 */
function check_tld($url)
{
    $parsed_url = parse_url($url);
    if ($parsed_url === false) {
        return false;
    }

    $tlds = config('tlds');
    $patten = implode('|', array_keys($tlds));

    return preg_match('/\.(' . $patten . ')$/i', $parsed_url['host']);
}

/**
 * Get Facebook Page Fans number
 *
 * @param $page_id
 * @return int
 */
function countFacebookFans($page_id)
{
    $count = 0;
    if (config('settings.facebook_page_fans')) {
        $count = (int) config('settings.facebook_page_fans');
    } else {
        $jsonUrl = 'http://api.facebook.com/method/fql.query?format=json&query=select+fan_count+from+page+where+page_id%3D' . $page_id;
        try {
            // Get content
            $json = file_get_contents($jsonUrl);
            $obj = json_decode($json);

            /*
             * Extract the likes count from the JSON object
             * NOTE: Limit the number of requests:
             * https://developers.facebook.com/docs/marketing-api/api-rate-limiting
             */
            if (!isset($obj->error_code) and isset($obj[0])) {
                if (isset($obj[0]->fan_count) and is_numeric($obj[0]->fan_count)) {
                    $count = $obj[0]->fan_count;
                }
            }
        } catch (\Exception $e) {
            $count = (int) config('settings.facebook_page_fans');
        }
    }

    return $count;
}

/**
 * Function to convert hex value to rgb array
 * @param $colour
 * @return array|bool
 *
 * @todo: improve this function
 */
function hex2rgb($colour)
{
    if ($colour[0] == '#') {
        $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
        list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    } elseif (strlen($colour) == 3) {
        list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);

    return array('r' => $r, 'g' => $g, 'b' => $b);
}

/**
 * Convert hexdec color string to rgb(a) string
 *
 * @param $color
 * @param bool $opacity
 * @return string
 *
 * @todo: improve this function
 */
function hex2rgba($color, $opacity = false)
{
    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if (empty($color)) {
        return $default;
    }

    //Sanitize $color if "#" is provided
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb = array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if ($opacity) {
        if (abs($opacity) > 1) {
            $opacity = 1.0;
        }
        $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(",", $rgb) . ')';
    }

    // Return rgb(a) color string
    return $output;
}

/**
 * ucfirst() function for multibyte character encodings
 *
 * @param $string
 * @param string $encoding
 * @return string
 */
function mb_ucfirst($string, $encoding = 'utf-8')
{
    $strlen = mb_strlen($string, $encoding);
    $first_char = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);

    return mb_strtoupper($first_char, $encoding) . $then;
}

/**
 * UTF-8 aware parse_url() replacement
 *
 * @param $url
 * @return mixed
 */
function mb_parse_url($url)
{
    $enc_url = preg_replace_callback('%[^:/@?&=#]+%usD', function ($matches) {
        return urlencode($matches[0]);
    }, $url);

    $parts = parse_url($enc_url);

    if ($parts === false) {
        throw new \InvalidArgumentException('Malformed URL: ' . $url);
    }

    foreach ($parts as $name => $value) {
        $parts[$name] = urldecode($value);
    }

    return $parts;
}

/**
 * Friendly UTF-8 URL for all languages
 *
 * @param $string
 * @param string $separator
 * @return mixed|string
 */
function slugify($string, $separator = '-')
{
    // Remove accents
    $string = remove_accents($string);

    // Slug
    $string = mb_strtolower($string);
    $string = @trim($string);
    $replace = "/(\\s|\\" . $separator . ")+/mu";
    $subst = $separator;
    $string = preg_replace($replace, $subst, $string);

    // Remove unwanted punctuation, convert some to '-'
    $punc_table = array(
        // remove
        "'" => '',
        '"' => '',
        '`' => '',
        '=' => '',
        '+' => '',
        '*' => '',
        '&' => '',
        '^' => '',
        '' => '',
        '%' => '',
        '$' => '',
        '#' => '',
        '@' => '',
        '!' => '',
        '<' => '',
        '>' => '',
        '?' => '',
        // convert to minus
        '[' => '-',
        ']' => '-',
        '{' => '-',
        '}' => '-',
        '(' => '-',
        ')' => '-',
        ' ' => '-',
        ',' => '-',
        ';' => '-',
        ':' => '-',
        '/' => '-',
        '|' => '-'
    );
    $string = str_replace(array_keys($punc_table), array_values($punc_table), $string);

    // Clean up multiple '-' characters
    $string = preg_replace('/-{2,}/', '-', $string);

    // Remove trailing '-' character if string not just '-'
    if ($string != '-') {
        $string = rtrim($string, '-');
    }

    //$string = rawurlencode($string);

    return $string;
}

/**
 * @return mixed|string
 */
function get_locale()
{
    $lang = get_lang();
    $locale = (isset($lang) and !$lang->isEmpty()) ? $lang->get('locale') : 'en_US';

    return $locale;
}

/**
 * @return \Illuminate\Support\Collection|static
 */
function get_lang()
{
    $obj = new App\Larapen\Helpers\Localization\Language();
    $lang = $obj->find();

    return $lang;
}

/**
 * Get file/folder permissions.
 *
 * @param $path
 * @return string
 */
function getPerms($path)
{
    return substr(sprintf('%o', fileperms($path)), -4);
}

/**
 * Get all countries from PHP array (umpirsky)
 *
 * @return array|null
 */
function getCountries()
{
    $countries = new App\Larapen\Helpers\Localization\Helpers\Country();
    $countries = $countries->all();

    if (empty($countries)) return null;

    $arr = [];
    foreach ($countries as $code => $value) {
        if (!file_exists(storage_path('database/geonames/countries/'.strtolower($code).'.sql'))) {
            continue;
        }
        $row = ['value' => $code, 'text' => $value];
        $arr[] = $row;
    }

    return $arr;
}
