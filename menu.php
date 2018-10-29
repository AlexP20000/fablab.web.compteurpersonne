<?php
/**
*	@file menu.php
*	@brief Menu principal pour l'affichage des pages.
*
*	@author Alexandre PERETJATKO (APE)
*	@version 29 oct. 2018	: APE	- Création.
*/ // ______________________________________________________________________________________________

?>

<div class="container-fluid">
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="#">Compteur</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	
	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav mr-auto">
	      <li class="nav-item ">
		    <a class="nav-link" href="AfficheTauxRemplissage.php">Taux de remplissage</a>
	      </li>
	      <li class="nav-item">
		    <a class="nav-link" href="RepartitionDuNbDePersonne.php">Répartition de la pfréquentation par heure</a>
	      </li>
	  </div>
	</nav>
</div>