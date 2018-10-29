<?php
/**
*	@file AfficheTauxRemplissage.php
*	@brief Ce script permet de :
*	- faire une requète pour récupérer les données stockées dans la base influxDB
*	- afficher le résultat sous forme graphique.
*
*	@author Alexandre PERETJATKO (APE)
*	@version 18 sept. 2018	: APE	- Création.
*/ // ______________________________________________________________________________________________
define("DEBUG", false);	// true : affiche les traces permettant de débuger le script, FALSE pour le reste du temps.

$nbPlaceDisponibleDansLaSalle	= 150;	// Le nombre de place assise disponibles.



// LIBRAIRIE influxDB ------------------------------------------------------------------------------
require 'vendor/autoload.php';




// CREATION D'UN OBJET CLIENT INFLUXDB
$client = new InfluxDB\Client(
		"193.52.19.20",		// host
		"8086",				// port
		"",					// user
		""					// mot de passe
		);





// CONNECTION À LA BDD -----------------------------------------------------------------------------
$database	= $client->selectDB('mesures');													




// CONSTRUCTION DE LA REQUETE DE RECUPERATION DES DONNEES ------------------------------------------
// On va chercher le cumul des entrées/sorties pour les 8 dernières heures.
// Attention, les heures sont au format GMT+0, il faut donc faire +2 heures pour avoir la bonne heure. 
// le champ "position" correspond à l'ID du capteur.
// Celui en place à la BU à l'ID : 'bu'
// Celui en place à l'UOF à l'ID : 'dehors'
$query	= "SELECT SUM(\"capteur\") as sum_capteur FROM autogen.passage WHERE position = 'bu' AND time > now() - 8h";
if (DEBUG) echo "Execution de la requète :<br/>".$query;


try {
	// On essaie de faire la requète
	$result	= $database->query($query);
	$points = $result->getPoints();
	
} catch( Exception $e) {
	
	// Si l'execution de la requète se passe mal, on a une exeption qui est levée et on dump l'exeption. 
	var_dump($e);
	exit();
}

if(DEBUG) {
	echo "<pre>";
	var_dump($points);
	echo "</pre>";
} 

// FORMATAGE DES DONNEES ---------------------------------------------------------------------------
$nbPersonnesComptees	= $points[0]['sum_capteur'];

?>

<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
			var data = new google.visualization.arrayToDataTable([
						['Places', 'Compteur'],
						['Places disponibles',     <?php echo $nbPlaceDisponibleDansLaSalle;?>],
						['Personnes dans la BU',    <?php echo $nbPersonnesComptees;?>]     			
					]);
        

			var options = {
	    	    legend: 'none',
		        pieSliceText: 'label',
		        title: "Taux de remplissage de la BU Lettre du Bouguen",
		        pieStartAngle: 100,
      		};

			var chart = new google.visualization.PieChart(document.getElementById('piechart'));
			chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="piechart" style="width: 900px; height: 600px;"></div>
  </body>
</html>