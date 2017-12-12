<?php

/*Parametre til database forbindelse*/
$db         = "waterworld"; /*Databasenavn*/
$dbhost     = "localhost"; /*Host navn*/
$dbuser     = "root"; /*Username*/
$dbpassword = ""; /*Password*/

/*Opret forbindelse og læg det i et objekt*/
$mysqli = new mysqli($dbhost,$dbuser,$dbpassword,$db);


//Giv fejl ved mislykket forsøg på at opnå forbindelse
if ($mysqli->connect_error) {
    die('FEJL : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}


?>