<?php
require_once('vendor/riak.php');
require_once("utilities.php");

//Config
$riakHost = "127.0.0.1";
$riakPort = 8098;
$urlBucket = 'urls';
$baseUrl = "http://localhost/";

//Inputs
$newUrl = isset($_POST['new_url']) ? $_POST['new_url'] : false;
$key = isset($_GET['k']) ? $_GET['k'] : false;
$shortUrl = false;

//Processing starts here
if(newUrl || $key) {
	$client = new RiakClient($riakHost, $riakPort);
	$bucket = $client->bucket($urlBucket);
	
	//Save new url
	if($newUrl) {
		$hash = PseudoCrypt::udihash(str_replace('.','',microtime(true)));
		$bucket->newObject($hash, array('url' => $newUrl))->store();
		$shortUrl = $baseUrl . "?k=" . $hash;
	}
	
	//Retrieve saved url
	if($key) {
		$obj = $bucket->get($key);
		header('Location: ' . $obj->data['url']);
	}
}
?>
<html>
<head>
	<title>Simple URL Tinier</title>
</head>
<body>
	<form method="post">
		URL to shorten: <input type="text" name="new_url" /> (e.g. http://www.reddit.com - please include http://)
	</form>
	<br />
	<?php if($shortUrl): ?>
		Stored new url at <a href="<?php echo $shortUrl; ?>"><?php echo $shortUrl; ?></a>
	<?php endif; ?>
</body>
</html>