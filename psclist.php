<?php
session_start();
$_SESSION['addressInput'] = $_REQUEST['zip'];

$weblabendpoint = "https://labs.medivo.com";

$weblabusername = "dtcmdpsc";

$weblabpassword = "pwn";

$to = "orders@dtcmd.com";

$patient = "35361";



$source = $_GET['source'];

$zipcode = $_GET['zip'];

if(isset($_GET['rad'])) { $radius  = $_GET['rad']; }



$zipPrefix = substr($zipcode, 0, 3);

if ($zipPrefix=="055" || ($zipPrefix >= "010" && $zipPrefix <= "027")) $weblabusername = "getstdtest";



if ($source != "ppmd") $weblabusername = "getstdtest";



$url = $weblabendpoint ."/find_psc/".$zipcode."?rad=1";



$headers[] = "Accept: application/xml";

$headers[] = "Content-Type: application/xml";

$ch = curl_init(); 

//	curl_setopt($ch, CURLOPT_POST,1);

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	curl_setopt($ch,CURLOPT_USERPWD, $weblabusername . ":" . $weblabpassword);

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($ch, CURLOPT_HEADER, 0);

//    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

	$response = curl_exec($ch);



	print $response;



?>