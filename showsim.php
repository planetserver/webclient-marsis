<?php


$url = 'http://marsis.planetserver.eu:8080/rasdaman/ows/wcps';

$timeto = -$_GET['timeto'];
$timefrom = -$_GET['timefrom'];

$query = "for c in (MARSIS_cscs_$_GET[orbit]) return encode((c.$_GET[filter])[i($timeto:$timefrom),j($_GET[echofrom]:$_GET[echoto])],\"png\")";
$query = urlencode($query);
$query = "query=".$query;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$im = imagecreatefromstring($response);
header('Content-Type: image/png');
imagepng($im);
imagedestroy($im);
?>
