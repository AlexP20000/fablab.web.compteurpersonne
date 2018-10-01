# fablab.web.compteurpersonne
Page PHP d'exemple pour accéder aux données du compteur de personne (dans une base influxDB)

#Prérequis
- [Composer](https://getcomposer.org/download/)
- client git ( [gitkraken](https://www.gitkraken.com/download/windows64) par exemple)
- serveur apache ou [WAMP](http://www.wampserver.com/)
- PHP

#Installation
## Librairie influxdb-php
Nous allons utiliser [Composer](https://getcomposer.org/download/) afin d'installer la [librairie influxDB pour PHP](https://github.com/influxdata/influxdb-php), ses dépendances et mettre à jour le path du sytème.

Pour cela, dans un répertoire (qui sera le répertoire racine du serveur d'exploitation du code), par exemple */www/influxDB*,  executer la commande `composer require influxdb/influxdb-php`

## Exemple (github)
Un exemple d'utilisation de la librairie est disponible sur github sur le depot https://github.com/AlexP20000/fablab.web.compteurpersonne