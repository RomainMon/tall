# Tutoriel pg_routing

Pour suivre ce tutoriel, il est nécessaire d’avoir préalablement installé PostgreSQL, pgAdmin, et QGIS.
Ce tutoriel a été réalisé avec QGIS 3.14, PostgreSQL 13, pgAdmin 4 et le système d’exploitation Windows 10.
Un fichier texte avec les requêtes SQL abordé dans ce tutoriel est fourni.
Pour l’utilisateur curieux de comprendre les paramètres des requêtes pgRouting, voici le lien vers la documentation pgRouting :
<div align=center>https://docs.pgrouting.org/pdf/en/pgRoutingDocumentation-2.6.0.pdf</div>

Une grande partie de ce travail a été possible grâce à l’ouvrage de « Géomatique Webmapping en Open Source » édition 2019 de David Collado.
<div align=center>https://www.decitre.fr/livres/geomatique-webmapping-en-open-source-9782340029682.html</div>


## Table des matières

[Étape 1 : installer les extensions nécessaires et connectez votre BD à QGIS](#etape1)

[Étape 2 : choisir son jeu de données et le préparer](#etape 2)

[Étape 3 : importer la couche dans la base de données «webmapping» via QGIS](#etape3)

[Étape 4 : les bases de la topologie et création de la topologie au jeu de données routes](#etape3)

[Étape 5 : calculer le plus court chemin](#etape5)

[Étape 6 : permettre au client d’élaborer un itinéraire](#etape6)


###Étape 1 : installer les extensions nécessaires et connectez votre BD à QGIS <a name="etape1"></a>

Dans pgAdmin créer une nouvelle « data base » et ajoutez à cette dernière les extensions suivantes :

1.	PostGIS
2.	PostGIS_topology
3.	Pg_routing





