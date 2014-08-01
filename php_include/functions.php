<?php
function MARSIS_connect()
{
	$conn = pg_connect("dbname=marsisdb user=rasdaman");
	return ($conn);
}

function area_selection()
{
	
	$html = "
		<table border=\"0\" style=\"width:150px\">
		<tr>
  			<td></td>
  			<td>N <input type=\"number\" name=\"N\" value=\"90\" min=\"-90\" max=\"90\" size=\"3\"></td>
  			<td></td>
		</tr>
		<tr>
  			<td>W <input type=\"number\" name=\"W\" value=\"0\" min=\"0\" max=\"360\" size=\"3\"></td>
  			<td></td>
  			<td>E <input type=\"number\" name=\"E\" value=\"360\" min=\"0\" max=\"360\" size=\"3\"></td>
		</tr>
		<tr>
  			<td></td>
  			<td>S <input type=\"number\" name=\"S\" value=\"-90\" min=\"-90\" max=\"90\" size=\"3\"></td>
  			<td></td>
		</tr>

		</table> ";
	return $html;
}

function orbit_selection($conn)
{
	if ($_POST['N']) {$range['N'] = $_POST['N'];}
	else 		 {$range['N'] = '90';}

	if ($_POST['S']) {$range['S'] = $_POST['S'];}
	else 		 {$range['S'] = '-90';}

	if ($_POST['W']) {$range['W'] = $_POST['W'];}
	else 		 {$range['W'] = '0';}

	if ($_POST['E']) {$range['E'] = $_POST['E'];}
	else 		 {$range['E'] = '360';}

	$query = "SELECT distinct orbit_id, (SELECT max(echo_id) from marsis_echoes_meta where orbit_id = o.orbit_id) as echoes from marsis_echoes_meta as o where o.sub_sc_lon between '$range[W]' and '$range[E]' and o.sub_sc_lat between '$range[S]' and '$range[N]'order by orbit_id";
	$orbits = pg_fetch_all(pg_exec($conn, $query));

	foreach ($orbits as $key => $val)
	{
		$orbit["$val[orbit_id]"] = $val;
	}

	$orbit_string = "<select name=\"orbit\" form=\"orbitform\">\n";
	foreach ($orbit as $key => $val)
	{
		$orbit_string = $orbit_string."<option value=\"$key\" >$key ($val[echoes])</option>\n";
	}
	$orbit_string = $orbit_string."</select>\n";
//	var_dump ($orbit);
	return $orbit_string;
	
}

function band_selection()
{
	$bands = array(
			"Frequency 1 (-1)" => "echo_mod_m1_f1",
			"Frequency 1 ( 0)" => "echo_mod_00_f1",
			"Frequency 1 (+1)" => "echo_mod_p1_f1",
			"Frequency 2 (-1)" => "echo_mod_m1_f2",
			"Frequency 2 ( 0)" => "echo_mod_00_f2",
			"Frequency 2 (+1)" => "echo_mod_p1_f2",
		);
	$html = "<select name=\"filter\" form=\"orbitform\">\n";
	foreach ($bands as $key => $val)
	{
		$html = $html."<option value=\"$val\" $selected>$key </option>\n";
	}
	$html = $html."</select>\n";
	return $html;

}

function echoes_selection()
{
	$html = "<p align=\"center\" >From: <input type=\"number\" name=\"echofrom\" value=\"1\" min=\"\" size=\"4\"></p>\n";
$html=  $html  ."<p align=\"center\" >To (leave 0 to select all):   <input type =\"number\" name=\"echoto\" value=\"0\" min=\"\" size=\"4\"></p>\n";
	return $html;
}

function return_selection()
{
	$html = "<p align=\"center\" >From: <input type=\"number\" name=\"timefrom\" value=\"0\"   min=\"\" max=\"511\" size=\"3\"></p>\n";
$html=  $html  ."<p align=\"center\" >To:   <input type=\"number\" name=\"timeto\"   value=\"511\" min=\"\" max=\"511\" size=\"3\"></p>\n";
	return $html;
}

function js_radar_query($conn)
{
	
}

function show_metadata($conn)
{
	if ($_POST['orbit'] && $_POST['showmeta']){
		$orbit_id = $_POST['orbit'];
//		if ($_POST['echofrom']){$echoFrom = $_POST['echofrom'];} else {$echoFrom = 1;}
		if ($_POST['echoto'])  {$echoFrom = $_POST['echofrom']; $echoTo   = $_POST['echoto'];}   
		else {$echoFrom = 1; $echoTo   = 5000;}

	$query = "SELECT echo_id, sub_sc_lon, sub_sc_lat, f1, f2, geom_epoch_utc, solar_lon, mars_sun_dist, sc_alt, loc_solar_t, solar_zen from marsis_echoes_meta where orbit_id = $orbit_id and echo_id between $echoFrom and $echoTo order by echo_id";
	$metadata = pg_fetch_all(pg_exec($conn, $query));

	$echoTo = $echoFrom+sizeof($metadata)-1;
	echo "<strong>Orbit number $orbit_id. Echoes range from $echoFrom to $echoTo</strong> ";
	if ($_POST['showradar'] || $_POST['showsim']){ 
	$bands = array(
	"echo_mod_m1_f1" => "Frequency 1 (-1)",
	"echo_mod_00_f1" => "Frequency 1 ( 0)",
	"echo_mod_p1_f1" => "Frequency 1 (+1)",
	"echo_mod_m1_f2" => "Frequency 2 (-1)",
	"echo_mod_00_f2" => "Frequency 2 ( 0)",
	"echo_mod_p1_f2" => "Frequency 2 (+1)",
	);
	$filter_string = $bands[$_POST['filter']];
	$header_string = $header_string."<strong> - $filter_string</strong> &nbsp;&nbsp;<i>Click on image(s) to swap between data and simulation</i>";
	}
	if ($_POST['showradar']){
//var_dump($_POST['showradar']);
	$header_string = $header_string."<p align=\"center\"> <img id=\"radargram\" onclick=\"changeImage('radargram')\" src=\"showradargram.php?orbit=$orbit_id&echofrom=$echoFrom&echoto=$echoTo&filter=$_POST[filter]&timeto=$_POST[timeto]&timefrom=$_POST[timefrom]\" align=\"middle\"></p>";
	}
	if ($_POST['showsim']){
//var_dump($_POST['showradar']);
	$header_string = $header_string."<p align=\"center\"> <img id=\"simulation\" onclick=\"changeImage('simulation')\" src=\"showsim.php?orbit=$orbit_id&echofrom=$echoFrom&echoto=$echoTo&filter=$_POST[filter]&timeto=$_POST[timeto]&timefrom=$_POST[timefrom]\" align=\"middle\"></p>";
	}
	
$html = $header_string."
<table id=\"meta_table\">
<tr>
  <th>Echo #id</th>
  <th>Longitude</th>
  <th>Latitude</th>
  <th>Frequencies (Hz)</th>
  <th>UTC Epoch</th>
  <th>Mars Solar Longitude</th>
  <th>Mars Sun Distance</th>
  <th>Space craft Altitude</th>";
//  <th>Local Solar Time</th>
//  <th>Solar Zenith</th>
$html=$html."</tr>";

foreach ($metadata as $key => $orbit)
{
	$html=$html."
<tr>
  <td>$orbit[echo_id]</td>
  <td>$orbit[sub_sc_lon]</td>
  <td>$orbit[sub_sc_lat]</td>
  <td>$orbit[f1]<br>$orbit[f2]</td>
  <td>$orbit[geom_epoch_utc]</td>
  <td>$orbit[solar_lon]</td>
  <td>$orbit[mars_sun_dist]</td>
  <td>$orbit[sc_alt]</td>";
//  <td>$orbit[loc_solar_t]</td>
//  <td>$orbit[solar_zen]</td>
$html=$html."</tr>";
}

$html = $html."</table> ";
	return $html;
	}
}


function show_radargram()
{
if ($_POST['showradar']){
	$url = 'http://marsis.planetserver.eu:8080/rasdaman/ows/wcps';
$query = 'for c in (MARSIS_data_2220) return encode((c.echo_mod_00_f1),"png")';
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


	}
}

?>





