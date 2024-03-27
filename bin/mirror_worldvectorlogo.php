<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to make a curl request
function curlRequest($url) {
    echo "Loading URL {$url}";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    echo " got ".strlen($response)." bytes\n";
    curl_close($curl);
    return $response;
}

$letters = array_merge(range('a', 'z'), range(0, 9));
$file = fopen("svgs", "w");
foreach ($letters as $l) {
    $p = 1;
    $nextpage = 2;
    while ($nextpage != "") {
        if ($p == 1) {
            $u = "https://worldvectorlogo.com/alphabetical/$l";
        } else {
            $u = "https://worldvectorlogo.com/alphabetical/$l/$p";
        }
        $html = curlRequest($u);
        $html = str_replace('<a', "\n<a", $html);
        file_put_contents(".svgs.letter-$l-page-$p", $html);
        $nextpage = "";
        $lines = file(".svgs.letter-$l-page-$p");
        foreach ($lines as $line) {
            if (strpos($line, '<a class="button') === 0 && strpos($line, '>Next') !== false) {
                $nextpage = trim(str_replace('<a class="button', '', $line), '">Next');
                break;
            }
        }
        $lines = file(".svgs.letter-$l-page-$p");
        foreach ($lines as $line) {
            if (strpos($line, 'logo__img') !== false) {
		//echo "Processling Line '{$line}'\n";
                preg_match('/<a class="logo" href="https:\/\/worldvectorlogo.com\/logo\/([^"]*)".*<img.*src="([^"]*)".*alt="([^"]*) logo vector".*$/', $line, $matches);
		//var_dump($matches);
                $id = $matches[1];
                $logo = $matches[2];
                $name = $matches[3];
                $tagsHtml = curlRequest("https://worldvectorlogo.com/logo/$id");
                $tags = [];
                preg_match_all('/<a href="\/tags\/[^"]*">([^<]*)<\/a>/', $tagsHtml, $tagMatches);
                foreach ($tagMatches[1] as $tag) {
                    $tags[] = $tag;
                }
                $tagsStr = implode(",", $tags);
		$lineStr = "{\"id\": \"$id\", \"name\": \"$name\", \"logo\": \"$logo\", \"tags\": [$tagsStr]},\n";
		//echo "Writing: {$lineStr}";
                fwrite($file, $lineStr);
            }
        }
        echo count(file("svgs")) . " SVGs added for '$l' page '$p'\n";
//        shell_exec("cat svgs.tmp >> svgs");
        if ($nextpage != "") {
            $p++;
        }
    }
}

echo count(file("svgs")) . " Total SVGs\n";
//unlink("svgs.tmp");
fclose($file);
