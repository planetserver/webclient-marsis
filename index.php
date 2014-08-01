<!DOCTYPE html>
<?php 
/******************************************************************************************************************************************************************/
/******************************************************************************************************************************************************************/
/******************************************************************************************************************************************************************/
//phpinfo() ;
require 'php_include/functions.php';

$content = $_GET["content"];
if ($content == null) {$content = 'home.php';}

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
	<ul>
		<li><a href="index.php?content=home.php">Home</a></li>
		<li><a href="index.php?content=textgui.php">Text GUI</a>
<!-- 			<ul>
				<li><a href="#">Menu3</a></li>
				<li><a href="#">Menu4</a></li>
				<li><a href="#">Menu5</a></li>
			</ul> -->
		</li>
 		<li><a href="/webclient-master">2D GUI</a>
<!--			<ul>
				<li><a href="#">Menu6</a></li>
				<li><a href="#">Menu7</a></li>
			</ul> -->
		</li>
<!-- 		<li><a href="#">3D GUI</a>
			<ul>
				<li><a href="#">Coming soon</a></li>
			</ul>
		</li> -->

		<li><a href="index.php?content=about.php">About</a></li>
	</ul>
</nav>
</div>


<div id="inner_container">
<?php 
	$conn = pg_connect("dbname=marsisdb user=rasdaman");
	
//	include 'php_include/alphapage.php';
	include $content;

	pg_close($conn);
?>
</div>
</div>
</body>
</html>

