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

[Étape 2 : choisir son jeu de données et le préparer](#etape2)

[Étape 3 : importer la couche dans la base de données «webmapping» via QGIS](#etape3)

[Étape 4 : les bases de la topologie et création de la topologie au jeu de données routes](#etape3)

[Étape 5 : calculer le plus court chemin](#etape5)

[Étape 6 : permettre au client d’élaborer un itinéraire](#etape6)


## Étape 1 : installer les extensions nécessaires et connectez votre BD à QGIS <a name="etape1"></a>

Dans pgAdmin créer une nouvelle « data base » et ajoutez à cette dernière les extensions suivantes :

1.	PostGIS
2.	PostGIS_topology
3.	Pg_routing


<div align=center>Figure 1 : Extensions correctement installées</div>

Certaines versions de PostGIS ont nativement pg_routing. Si ce n’est pas le cas, il est nécessaire d’installer pg_routing. Pour cela rendez-vous sur https://pgrouting.org/.
Une fois les extensions installées, connectez QGIS à votre « data base ». Il suffit de faire un clic droit sur l’icône PostGIS dans l’explorateur QGIS et de faire « nouvelle connexion ».

<div align=center>Figure 2 : Connexion à la data base</div>

## Étape 2 : choisir son jeu de données et le préparer <a name="etape2"></a>

Choisissez un jeu de données correspondant à votre zone d’étude et dont la numérisation est correcte (ex : pas d’espacement entre deux tronçons de route).
Gardez que les informations utiles dans la table attributaire. Je laisse au lecteur le soin de garder les éléments qui lui semble utile.
Dans ce tuto, j’utilise la base de données « Route 500 » de l’IGN. Elle est gratuite et reflète bien la réalité du terrain. Les données OSM peuvent être également utilisées si elles vous paraissent assez complètes sur votre zone d’étude.

## Étape 3 : importer la couche dans la data base « webmapping » via QGIS <a name="etape3"></a>
Dans QGIS cliquez sur « Base de données » puis « DB manager » (cf. figure3).

<div align=center>Figure 3 : Accès à DB manager</div>

Sélectionnez votre schéma contenant votre « data base » (cf. figure 4).

<div align=center>Figure 4 : Sélection du schéma</div>

Cliquez sur « Import de couche/fichier » (cf. figure 5).

<div align=center>Figure 5 : Bouton import de couche/fichier</div>

Par défaut vous avez la couche de routes qui est proposé en « source », si ce n’est pas le cas sélectionnez là. Laissez les paramètres par défaut et cliquez seulement dans « options » sur « créer un index spatial » (cf. figure 6).

<div align=center>Figure 6 : paramétrage de la couche à importer</div>

<span style="color: red;">Attention !</span> Parfois l’import de données en « Lambert 93 » n’est pas fonctionnel, une erreur apparait (cf. figure 7).

<div align=center>Figure 7 : Erreur d’importation</div>

Dans ce cas, convertissez vos données en WGS84 (ou autre système de projection) puis recommencer.

## Étape 4 : les bases de la topologie et création de la topologie au jeu de données routes. <a name="etape4"></a>

Dans le domaine des SIG, « la topologie est un ensemble de règles qui définissent comment des points, des lignes ou des polygones partagent des géométries coïncidentes » .
Une topologie est composée de 3 éléments de bases :
•	Les nœuds (vertices) qui modélisent les points
•	Les arêtes qui modélisent les entités linéaires. Ces entités ne peuvent se chevaucher, elles ont un sens défini par un nœud de départ et un nœud d’arrivée (sens antihoraire). Cette structure se nomme « graphe orienté »
•	Les faces modélisent les polygones. Les faces sont constituées d’arêtes.
Ces 3 éléments définis précédemment se retrouvent dans la SGBDR comme nous le verrons plus tard.
Pour créer la topologie de nos routes, nous allons rédiger un ensemble de requêtes SQL.
Ouvrez pgAdmin.

Entrez la requête qui suit dans pgAdmin :

-- Requête 1
-- créer la topology des routes : créer un nouveau schéma

SELECT topology.CreateTopology('routes_topo',4326)


