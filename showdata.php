<!DOCTYPE html>
<?php 
/******************************************************************************************************************************************************************/
/******************************************************************************************************************************************************************/
/******************************************************************************************************************************************************************/
//phpinfo() ;
require 'php_include/functions.php';

	if ($_POST['orbit'] && $_POST['showmeta']){
		$orbit_id = $_POST['orbit'];
//		if ($_POST['echofrom']){$echoFrom = $_POST['echofrom'];} else {$echoFrom = 1;}
		if ($_POST['echoto'])  {$echoFrom = $_POST['echofrom']; $echoTo   = $_POST['echoto'];}   
		else {$echoFrom = 1; $echoTo   = 5000;}
	}

?>
<html lang="en">
<!-- HEAD -->
<head>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="PlanetServer: Planetary Service of EarthServer - WP-260">
	<meta name="author" content="PlanetServer">

	<title>PlanetServer</title>
	

	<!-- Jumbotron/planetserver -->
	<link href="./assets/css/planetserver.css" rel="stylesheet" media="screen" /> 
	

</head>

<!-- BODY -->
<body>
<script>
function changeImage(target) {
    var image = document.getElementById(target);
    if (image.src.match("showradargram")) {
        image.src = <?php echo "\"showsim.php?orbit=$orbit_id&echofrom=$echoFrom&echoto=$echoTo&filter=$_POST[filter]&timeto=$_POST[timeto]&timefrom=$_POST[timefrom]\"" ?>;
    } else {
        image.src = <?php echo "\"showradargram.php?orbit=$orbit_id&echofrom=$echoFrom&echoto=$echoTo&filter=$_POST[filter]&timeto=$_POST[timeto]&timefrom=$_POST[timefrom]\"" ?>;
    }
}
</script>
<?php include_once("analyticstracking.php") ?>
<div id="container">
	<div id="image_bar">
		<img src="./assets/images/logos/vector-logo-earthserver_with-text_RGB_70px-high.png">
			<img src="./assets/images/logos/jacobs_70px-high_logo.png">
			<img src="./assets/images/mars70.png">
			<img src="./assets/images/ess-logo_v1.2_70px.png">
	</div>
<div id="navigation-bar">
<nav>

</nav>
</div>


<div id="inner_container">
<?php 
	$conn = pg_connect("dbname=marsisdb user=rasdaman");

//	include 'php_include/alphapage.php';
	echo show_metadata($conn);

//	echo show_radargram();
	
	pg_close($conn);
?>
	
</div>
</div>
</body>
</html>
