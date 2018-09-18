<?php
/**
*	@file NbPersonne.php
*	@brief Ce script permet d'afficher le nombre de personne actuellement comptées.
*	- Il va chercher les données stockées en base depuis ce matin à 00:00, 
*	- modifie les résultat obtenu pour en faire le cumul 
*	- et affiche le résultat.
*
*	@author Alexandre PERETJATKO (APE)
*	@version 18 sept. 2018	: APE	- Création.
*/ // ______________________________________________________________________________________________


// LIBRAIRIE influxDB ------------------------------------------------------------------------------
include_once 'src/InfluxDB/Client.php';

// CREATION D'UN OBJET CLIENT INFLUXDB
$host	= "193.52.19.20";
$port	= "8089";
$l_OBJ_influxDBClient = new InfluxDB\Client($host, $port);




// RECUPERATION DES DONNÉES DEPUIS CE MATIN À 00:00 ------------------------------------------------

// Calcul de la date de ce matin (au format timestamp)
$l_TIM_ceMatin	= strtotime('today midnight');

// Construction de la requete
$database	= $client->selectDB('mesures');															// Connection à la BDD
$result		= $database->query('select * from "mesures"."autogen"."passage" WHERE time > '.$l_TIM_ceMatin); // Requète (mySQL) pour la récupération des données

// Transformation du résultat en points dans un tableau
$l_TAB_Points	= $result->getPoints();




// FORMATTAGE DES DONNÉES --------------------------------------------------------------------------


// TRACE DE DEBUG DEBUT____________________________________________________________________________________
ini_set( 'html_errors' , 0 );
echo "<pre>";
echo str_repeat("_", 80)."\n";
printf('Fichier : %s, Ligne : %s',__FILE__,__LINE__); echo "\nContenu de  \$l_TAB_Points : ";
var_dump($l_TAB_Points);
echo str_repeat("_", 80)."\n";
echo "</pre>";
// TRACE DE DEBUG FIN _______________________________________________________________________________________ 



?>