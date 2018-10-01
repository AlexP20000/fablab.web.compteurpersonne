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
define("DEBUG", false);

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




// CONSTRUCTION DE LA REQUETE ----------------------------------------------------------------------
// On va chercher le cumul des entrées/sorties par heure 8h en arrière par rapport à maintenant.
$query	= "SELECT SUM(\"capteur\") as sum_capteur FROM autogen.passage WHERE position = 'dehors' AND time > now() - 8h  GROUP BY time(30m)";
if (DEBUG) echo "Execution de la requète :<br/>".$query;


try {
	// On essaie de faire la requète
	$result	= $database->query($query);
	
} catch( Exception $e) {
	
	// Si l'execution de la requète se passe mal, on a une exeption qui est levée et on dump l'exeption. 
	var_dump($e);
	exit();
}



// TRANSFORMATION DES DONNÉES RÉCUPÉRÉES EN POINTS -------------------------------------------------
$points = $result->getPoints();
if(DEBUG) {
	echo "<pre>";
	var_dump($points);
	echo "</pre>";
} 

// FORMATAGE DES DONNÉES POUR LE GOOGLE CHART ------------------------------------------------------
// CREATION DES COLONNES
$Result->cols[] = array(
		"id" 		=> "",
		"label" 	=> "Cumul",
		"pattern" 	=> "",
		"type" 		=> "string"
);
$Result->cols[] = array(
		"id" 		=> "",
		"label" 	=> "Heure",
		"pattern" 	=> "",
		"type" 		=> "number"
);
// CREATION DES LIGNES
foreach( $points as $point){
	$heure	= substr($point['time'], 11,5);
	$Result->rows[]["c"]	= array(
			array( "v" => $heure, "f" => null),
			array( "v" => abs($point['sum_capteur']), "f" => null),
	);
}
$TAB_json	= json_encode($Result, JSON_PRETTY_PRINT);

if(DEBUG){
	echo "<pre>";
	var_dump($TAB_json);
	echo "</pre>";
}





// FORMATTAGE DES DONNÉES --------------------------------------------------------------------------



?>

<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable(<?php echo $TAB_json; ?>);
        

      var options = {
    	      legend: 'none',
        pieSliceText: 'label',
        title: "Répartition du nombre de personnes par heure",
        pieStartAngle: 100,
      };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="piechart" style="width: 900px; height: 500px;"></div>
  </body>
</html>