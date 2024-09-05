<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function curlRequest($url, &$httpCode) {
    echo "Loading URL {$url}";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'cURL error: ' . curl_error($curl);
        return false;
    } else {
        // Get the HTTP status code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        echo 'HTTP Status Code: ' . $httpCode;
    }
    echo " got ".strlen($response)." bytes\n";
    curl_close($curl);
    return $response;
}

$svgs = [];
$pagesProcessed = 0;
if ($_SERVER['argc'] > 1) {
    $limitWrite = true;
    $letters = str_split(str_replace(' ', '', strtolower($_SERVER['argv'][1])));
} else {
    $limitWrite = false;
    $letters = array_merge(range('a', 'z'), range(0, 9));
}
@mkdir(__DIR__.'/../cache/html-logo', true);
@mkdir(__DIR__.'/../cache/html-letter', true);
foreach ($letters as $l) {
    @mkdir(__DIR__.'/../svg/'.$l, true);
    $p = 1;
    $nextpage = 2;
    while ($nextpage != "") {
        $file = __DIR__."/../cache/html-letter/.svgs.html-letter-$l-page-$p";
        if (!file_exists($file)) {
            if ($p == 1) {
                $u = "https://worldvectorlogo.com/alphabetical/$l";
            } else {
                $u = "https://worldvectorlogo.com/alphabetical/$l/$p";
            }
            $html = curlRequest($u, $code);
            if ($html === false) {
                continue;
            }
            if ($code != 200) {
                echo "got {$code} response code\n";
                continue;
            }
            $html = str_replace('<a', "\n<a", $html);
            file_put_contents($file, $html);
        }
        $nextpage = "";
        $lines = file($file);
        foreach ($lines as $line) {
            if (strpos($line, '<a class="button') === 0 && strpos($line, '>Next') !== false) {
                $nextpage = trim(str_replace('<a class="button', '', $line), '">Next');
                break;
            }
        }
        $lines = file($file);
        foreach ($lines as $line) {
            if (strpos($line, 'logo__img') !== false) {
                preg_match('/<a class="logo" href="https:\/\/worldvectorlogo.com\/logo\/([^"]*)".*<img.*src="([^"]*)".*alt="([^"]*) logo vector".*$/', $line, $matches);
                $id = $matches[1];
                $logo = $matches[2];
                $name = $matches[3];
                $file = __DIR__."/../cache/html-logo/.svgs.html-logo-".urldecode($id);
                if (!file_exists($file)) {
                    $u = "https://worldvectorlogo.com/logo/{$id}";
                    $tagsHtml = curlRequest($u, $code);
                    if ($tagsHtml === false) {
                        continue;
                    }
                    if ($code != 200) {
                        echo "got {$code} response code getting {$u}\n";
                        continue;
                    }
                    file_put_contents($file, $tagsHtml);
                } else {
                    $tagsHtml = file_get_contents($file);
                }                                  
                $tags = [];
                preg_match_all('/<a[^>]*href="[^"]*\/tag\/[^"]*">([^<]*)<\/a>/', $tagsHtml, $tagMatches);
                foreach ($tagMatches[1] as $tag) {
                    $tags[] = $tag;
                }
                $file = urldecode(basename($logo));
                if (!file_exists(__DIR__.'/../svg/'.$l.'/'.$file)) {
                    $svg = curlRequest($logo, $code);
                    if ($svg === false) {
                        continue;
                    }
                    if ($code != 200) {
                        echo "got {$code} response code getting {$logo}\n";
                        continue;
                    }
                    file_put_contents(__DIR__.'/../svg/'.$l.'/'.$file, $svg);
                }
                $svgs[$id] = ['id' => $id, 'name' => $name, 'logo' => $logo, 'tags' => $tags];
            }
        }
        $pagesProcessed++;
        if ($pagesProcessed % 5 == 0) {
            //file_put_contents(__DIR__.'/../svgs.json', json_encode($svgs, JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES));
        }
        if ($nextpage != "") {
            $p++;
        }
    }
}

echo count($svgs) . " Total SVGs\n";
file_put_contents(__DIR__.'/../svgs.json', json_encode($svgs, JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES));
