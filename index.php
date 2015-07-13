<?php header('Access-Control-Allow-Origin: *');

$url = $_GET['url'];

if (strpos($url,'http://') === 0 or strpos($url,'https://') === 0) {
	//good url
	} else {
	$url = 'http://' . $url;
}

$uri = parse_url(urldecode($url));
$scheme = $uri['scheme'];
$host = $uri['host'];
$domain = $scheme . '://' . $host;
$server = '//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

$output = file_get_contents($url);

//custom replacement
if (isset($_GET['replace']) && isset($_GET['with'])) {
   $replace = stripcslashes($_GET['replace']);
   $with = stripcslashes($_GET['with']);
   $output = str_ireplace($replace, $with, $output); //use str_replace for case-sensetive
}

//flags
if ($_GET['flag'] === '') {
	$output = str_replace('</body>', "<script>alert('flag')</script></body>", $output);
}

//errors
if ($_GET['errors'] === '') {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//preset replacements
$dom = new DOMDocument;
$dom->loadHTML($output);

$head = $dom->getElementsByTagName('head')->item(0);

//base tag
$base = $dom->createElement('base');
$base->setAttribute('href', $domain);
$head->insertBefore($base,$head->firstChild);

//script tag
$script = $dom->createElement('script');
$script_src= $dom->createAttribute('src');
$script_src->value =  $server . '/script.js';;
$script->appendChild($script_src);
$head->appendChild($script);

echo $dom->saveHTML();