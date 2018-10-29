<?php
/**
*	@file RepartitionDuNbDePersonne.php
*	@brief Ce script permet de :
*	- faire une requète pour récupérer les données stockées dans la base influxDB
*	- formater les données pour qu'elles soient utilisable par les google charts 
*	- afficher le résultat sous forme graphique.
*
*	@author Alexandre PERETJATKO (APE)
*	@version 18 sept. 2018	: APE	- Création.
*/ // ______________________________________________________________________________________________
define("DEBUG", false);	// true : affiche les traces permettant de débuger le script, FALSE pour le reste du temps.




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
// On va chercher le cumul des entrées/sorties par heure 8h en arrière par rapport à maintenant.
// Attention, les heures sont au format GMT+0, il faut donc faire +2 heures pour avoir la bonne heure. 
// le champ "position" correspond à l'ID du capteur.
// Celui en place à la BU à l'ID : 'bu'
// Celui en place à l'UOF à l'ID : 'dehors'
$query	= "SELECT SUM(\"capteur\") as sum_capteur FROM autogen.passage WHERE position = 'bu' AND time > now() - 8h  GROUP BY time(60m)";
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



// FORMATAGE DES DONNÉES POUR LE GOOGLE CHART ------------------------------------------------------
// Pour pouvoir utiliser les google chart, il faut formatter les données selon le format décrit sur
// la page : https://developers.google.com/chart/interactive/docs/datatables_dataviews

// Nous allons donc faire un tableau à 2 colonnes dans lequel on va injecter nos données (les lignes)

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
		"type" 		=> "string"
);

// CREATION DES LIGNES
date_default_timezone_set('Europe/Paris');	// Ajustement 
foreach( $points as $point){
	$l_TIM_Date	= strtotime($point['time']);
	$Result->rows[]["c"]	= array(
			array( "v" => date("H", $l_TIM_Date)." h", "f" => null),
			array( "v" => abs($point['sum_capteur']), "f" => null),
	);
}
// Les données doivent être au format JSON
$TAB_json	= json_encode($Result, JSON_PRETTY_PRINT);

if(DEBUG){
	echo "<pre>";
	var_dump($TAB_json);
	echo "</pre>";
}


?>

<html>
  <head>
  	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">


	<title>Compteur personne : Répartition du nombre de passage par heure</title>
  	
  
  
  
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable(<?php echo $TAB_json; ?>);
        

      var options = {
    	      backgroundColor: { fill:'transparent' },
    	      is3D:true,
      };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
  	<?php include_once 'menu.php';?>
  	
  	
  	
  	
  	<div class="container">
		<div class="jumbotron">
		  	<h1 class="display-4">Répartition du nombre de passage à la B.U Lettres du Bouguen par heure</h1>
			<p class="lead">Le taux de remplissage est calculé en temps réel grace à un capteur de passage situé à l'entrée de la Bibliothèque.</p>
		</div>
	  	
	  	
	  	
	
		<!-- emplacement pour le graphique de visualisation -->
	    <div id="piechart" style="width: 100%; height: 900px;"></div>
    
    
    
    
  	</div>
  </body>
</html>