<?php header('Access-Control-Allow-Origin: *');

//errors
if ($_GET['errors'] === '') {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

$target = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $_SERVER['REDIRECT_URL'];
$path = preg_split('|/|', $target, -1, PREG_SPLIT_NO_EMPTY);

if ($path[0] == 'http:' || $path[0] == 'https:') {
		$url = $path[0] . '//' . join('/', array_slice($path, 1));
	} else {
		$url = 'http://' . join('/', $path);;
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