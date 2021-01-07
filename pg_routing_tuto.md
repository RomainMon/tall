# Tutoriel pg_routing

Pour suivre ce tutoriel, il est nécessaire d’avoir préalablement installé PostgreSQL, pgAdmin, et QGIS.
Ce tutoriel a été réalisé avec QGIS 3.14, PostgreSQL 13, pgAdmin 4 et le système d’exploitation Windows 10.
Pour l’utilisateur curieux de comprendre les paramètres des requêtes pgRouting, il peut cliquer [ici](https://docs.pgrouting.org/pdf/en/pgRoutingDocumentation-2.6.0.pdf) pour accèder à la documentation pgRouting.

Une grande partie de ce travail a été possible grâce à l’ouvrage de [« Géomatique Webmapping en Open Source » édition 2019 de David Collado](https://www.decitre.fr/livres/geomatique-webmapping-en-open-source-9782340029682.html).

<div align=center>https://www.decitre.fr/livres/geomatique-webmapping-en-open-source-9782340029682.html</div>


## Table des matières

[Étape 1 : installer les extensions nécessaires et connectez votre BD à QGIS](#etape1)

[Étape 2 : choisir son jeu de données et le préparer](#etape2)

[Étape 3 : importer la donnée dans la base de données «webmapping» via QGIS](#etape3)

[Étape 4 : les bases de la topologie et création de la topologie au jeu de données routes](#etape3)

[Étape 5 : calculer le plus court chemin](#etape5)

[Étape 6 : permettre au client d’élaborer un itinéraire](#etape6)


## Étape 1 : installer les extensions nécessaires et connectez votre BD à QGIS <a name="etape1"></a>

Dans pgAdmin créer une nouvelle « data base » et ajoutez à cette dernière les extensions suivantes :

1.	PostGIS
2.	PostGIS_topology
3.	Pg_routing

<div align=center><img width="200" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931275-56ae5100-5120-11eb-93fe-d587045d3039.png"></div>
<div align=center>Figure 1 : Extensions correctement installées</div>

Certaines versions de PostGIS ont nativement pg_routing. Si ce n’est pas le cas, il est nécessaire d’installer pg_routing. Pour cela rendez-vous sur https://pgrouting.org/.
Une fois les extensions installées, connectez QGIS à votre « data base ». Il suffit de faire un clic droit sur l’icône PostGIS dans l’explorateur QGIS et de faire « nouvelle connexion ».

<div align=center><img width="250" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931300-60d04f80-5120-11eb-974a-def5001fab5e.png"></div>
<div align=center>Figure 2 : Connexion à la data base</div>

## Étape 2 : choisir son jeu de données et le préparer <a name="etape2"></a>

Choisissez un jeu de données correspondant à votre zone d’étude et dont la numérisation est correcte (ex : pas d’espacement entre deux tronçons de route).
Gardez que les informations utiles dans la table attributaire. Je laisse au lecteur le soin de garder les éléments qui lui semble utile.
Dans ce tuto, j’utilise la base de données « Route 500 » de l’IGN. Elle est gratuite et reflète bien la réalité du terrain. Les données OSM peuvent être également utilisées si elles vous paraissent assez complètes sur votre zone d’étude.

## Étape 3 : importer la donnée dans la data base « webmapping » via QGIS <a name="etape3"></a>
Dans QGIS cliquez sur « Base de données » puis « DB manager » (cf. figure3).

<div align=center><img width="200" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931317-66c63080-5120-11eb-969a-98acc812453f.png"></div>
<div align=center>Figure 3 : Accès à DB manager</div>

Sélectionnez votre schéma contenant votre « data base » (cf. figure 4).

<div align=center><img width="500" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931329-6d54a800-5120-11eb-8344-8f221d689c10.png"></div>
<div align=center>Figure 4 : Sélection du schéma</div>

Cliquez sur « Import de couche/fichier » (cf. figure 5).

<div align=center><img width="300" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931338-72b1f280-5120-11eb-8084-f27dbc927f3c.png"></div>
<div align=center>Figure 5 : Bouton import de couche/fichier</div>

Par défaut vous avez la couche de routes qui est proposé en « source », si ce n’est pas le cas sélectionnez là. Laissez les paramètres par défaut et cliquez seulement dans « options » sur « créer un index spatial » (cf. figure 6).

<div align=center><img width="400" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931362-79d90080-5120-11eb-952a-df11256ad123.png"></div>
<div align=center>Figure 6 : paramétrage de la couche à importer</div>

<span style="color:red">Attention !</span> Parfois l’import de données en « Lambert 93 » n’est pas fonctionnel, une erreur apparait (cf. figure 7).

<div align=center><img width="500" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931386-83faff00-5120-11eb-9816-b3b56286b96c.png"></div>
<div align=center>Figure 7 : Erreur d’importation</div>

Dans ce cas, convertissez vos données en WGS84 (ou autre système de projection) puis recommencer.

## Étape 4 : les bases de la topologie et création de la topologie au jeu de données routes <a name="etape4"></a>

Dans le domaine des SIG, « la topologie est un ensemble de règles qui définissent comment des points, des lignes ou des polygones partagent des géométries coïncidentes » .
Une topologie est composée de 3 éléments de bases :
*	Les nœuds (vertices) qui modélisent les points
*	Les arêtes qui modélisent les entités linéaires. Ces entités ne peuvent se chevaucher, elles ont un sens défini par un nœud de départ et un nœud d’arrivée (sens antihoraire). Cette structure se nomme « graphe orienté »
*	Les faces modélisent les polygones. Les faces sont constituées d’arêtes.
Ces 3 éléments définis précédemment se retrouvent dans la SGBDR comme nous le verrons plus tard.
Pour créer la topologie de nos routes, nous allons rédiger un ensemble de requêtes SQL.
Ouvrez pgAdmin.

Entrez la requête qui suit dans pgAdmin :

    -- Requête 1
    -- créer la topology des routes : créer un nouveau schéma

    SELECT topology.CreateTopology('routes_topo',4326)

Ceci crée un nouveau schéma « routes_topo » avec une nouvelle topologie se composant de 4 tables (cf. figure 8). 

<div align=center><img width="200" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931404-8a897680-5120-11eb-8973-94d2a88e4ac6.png"></div>
<div align=center>Figure 8 : Tables créées</div>

Ajoutez la colonne "topo_geom" de type topogeometry à la table de vos routes, dans mon cas « routes_grand_lyon_84 » :

    -- Requête 2
    -- ajout d'une colonne "topo_geom" de type topogeometry à la table routes_grand_lyon_84

    SELECT topology.AddTopoGeometryColumn('routes_topo', 'public','routes_grand_lyon_84', 'topo_geom','LINESTRING')
    
Convertissez la géométrie initiale des routes en des références topologiques :
 
    -- Requête 3
    -- Convertissez la géométrie initiale des routes en des références topologiques

    UPDATE routes_grand_lyon_84 set topo_geom = topology.toTopoGeom(geom,'routes_topo', 1)
 
Ajoutez une colonne « longueur » en double précision dans la table « edge_data » dans le schéma « routes_topo » :
 
    -- Requête 4
    -- ajout colonne longueur en double précision

    alter table routes_topo.edge_data add column longueur double precision
    
    -- Requête 5
    -- MAJ colonne longueur

    UPDATE routes_topo.edge_data set longueur=ST_Length(geom)
 
## Étape 5 : calculer le plus court chemin <a name="etape5"></a>

Glissez-déposez dans l’espace « couches » de QGIS la donnée « edge_data » de PostGIS. Le réseau routier topologique s’affiche.
Ouvrez la table attributaire de « edge_data ». On remarque les nœuds, les arêtes et les faces de la topologie définis précédemment (cf. figure 9).

<div align=center><img width="800" alt="img1" src="https://user-images.githubusercontent.com/57360765/103931429-9412de80-5120-11eb-889b-80a46594100b.png"></div>
<div align=center>Figure 9 : Table attributaire</div>
 
Pour calculer le plus court chemin, entrez la requête suivante :

    -- Requête 6
    -- calculer l'itinéraire le plus court. 3985 est le noeud de départ et 912 le noeud d'arrivé.

    select * from
    pgr_dijkstra('select edge_id as id,start_node as  source,end_node as target, longueur as cost from routes_topo.edge_data', 3985, 912, false)
 
Pour calculer le plus court chemin et l’afficher dans QGIS, allez dans le « gestionnaire BD » de QGIS --> sélectionnez le schéma détenant les routes topologiques --> appuyez sur la touche « F2 » --> entrez la requête suivante :

    -- Requête 7
    -- calculer l'itinéraire le plus court et récupérer sa géométrie pour l'afficher dans qgis. 3985 est le noeud de départ et 912 le noeud d'arrivé

    with dijkstra as (
    select * from
    pgr_dijkstra('select edge_id as id,start_node as  source,end_node as target, longueur as cost from routes_topo.edge_data', 3985, 912, false))
    select edge_id as id, geom
    from routes_topo.edge cross join dijkstra
    where edge_id = dijkstra.edge

Cliquez sur « chargez en tant que nouvelle couche » puis « charger ».
Le tronçon sélectionné est à présent visible sur la carte.

## Étape 6 : permettre au client d’élaborer un itinéraire<a name="etape6"></a>

Pour que le client puisse établir un itinéraire, nous allons demander qu’il clique sur un lieu de départ et un lieu d’arrivée.
Tout d’abord, lorsque le client clique sur la carte Leaflet, il nous faut récupérer les coordonnées des deux clics.
Dans le code PHP, il y aura la requête vue précédemment qui permet de calculer le plus court chemin et de récupérer les géométries pour les afficher, mais aussi une nouvelle requête SQL qui permet de trouver le nœud de départ et le nœud d’arrivée qui soient les plus proches de là où l’utilisateur a cliqué (cf. requête qui suit).

    -- Requête 8
    -- calculer l'itinéraire en fonction des coordonées entrée au clic par l'utilisateur à mettre dans le PHP

    WITH dijkstra AS (SELECT * FROM pgr_dijkstra('select edge_id as id,start_node as  source,end_node as target, longueur as cost from routes_topo.edge_data',
    (SELECT node_id FROM routes_topo.node
    WHERE ST_Expand(ST_GeomFromText('POINT(coordonées_utilisateur_départ)',4326),1000)&&geom
    ORDER BY ST_Distance(geom,ST_GeomFromText('POINT(coordonées_utilisateur_départ)',4326))LIMIT 1),
    (SELECT node_id FROM routes_topo.node
    WHERE ST_Expand(ST_GeomFromText('POINT(coordonées_utilisateur_arrivée)',4326),1000)&&geom
    ORDER BY ST_Distance(geom,ST_GeomFromText('POINT(coordonées_utilisateur_arrivée)',4326))LIMIT 1),
    false
    ))
    select edge_id as id, geom
    from routes_topo.edge cross join dijkstra
    where edge_id = dijkstra.edge

Là où il y a « coordonées_utilisateur_départ » et « coordonées_utilisateur_arrivée » il faut mettre les coordonnées de l’utilisateur.

