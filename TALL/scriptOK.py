# -*- coding: utf-8 -*-

# on écrit le lien vers notre installation python dans un commentaire de la façon suivante :
#!C:/Python3.8.6/python.exe

"""Ce script est destiné à l'analyse multi-critères comprise dans notre projet géonum.
Elle permet à un utilisateur de notre application de connaître les emplacements optimaux
de tels équipements à tel lieu renseignés par l'utilisateur lui-même. Nous nous basons
sur les méthods d'AMC-SIG classiques : création de couches rasters constituant les
différents critères, standardisation, calculatrice raster ; la méthode d'agrégation est ici
la somme pondérée"""


#__author__ = "AUBERT Clarisse, DUVERNEUIL Bruno, MASCARELL Clément, MONASSIER Romain"

###Import des librairies
import sys
sys.path.append("C:\\Python3.8.6\\python38.zip")
sys.path.append("C:\\Python3.8.6\\DLLs")
sys.path.append("C:\\Python3.8.6\\lib")
sys.path.append("C:\\Python3.8.6")
sys.path.append("C:\\Python3.8.6\\Lib\\site-packages")
sys.path.insert(0, "C:\\Python3.8.6\\Lib\\site-packages") #ce chemin est nécessaire pour trouver les librarie lorsque l'on lance le script via php
import rasterio
from rasterio import mask
import geopandas
import os
import psycopg2
from osgeo import gdal
import ogr
import pandas
import shapely
import numpy
import fiona
import pycrs
import scipy.spatial
from sqlalchemy import create_engine
import cgi
import rtree
import geoalchemy2

os.chdir('C:\wamp64\www\TALL4') #chemin de là où sera écrit le script je dois mettre le raster dans ce dossier là

###Mise en place de la requête : chargement des données et demande à l'utilisateur

## Import urban_atlas
ua_gl = rasterio.open('UrbanAtlas_2012_GL_R.tif') # Chargement de l'Urban Atlas en raster

## Connexion BDD
HOST = "localhost"
USER = "postgres"
PASSWORD = "******"
DATABASE = "TALL"
conn = psycopg2.connect("host=%s dbname=%s user=%s password=%s" % (HOST, DATABASE, USER, PASSWORD))

## Import des données spatiales en gdf
equip_gdf = geopandas.read_postgis("select * from equipement", conn, geom_col='geom', crs="EPSG:4326")
comm_gdf = geopandas.read_postgis("select * from commune", conn, geom_col='geom', crs="EPSG:4326")
bati_gdf = geopandas.read_postgis("select * from bati_bd_topo", conn, geom_col='geom', crs='EPSG:4326')

#récupération des valeurs php
commune = sys.argv[1]
commune = str(commune)
equip_php = sys.argv[2]
equip_php = str(equip_php)

###Filtre spatial des données avec formulaire

zone = comm_gdf.loc[comm_gdf['insee_com'] == commune]
structure = equip_gdf.loc[equip_gdf['type_equip'] == equip_php]

##Reprojection en 2154
shapely.speedups.disable()
zone_l93 = zone.to_crs(crs=2154,epsg=2154)
structure_l93 = structure.to_crs(crs=2154,epsg=2154)
bati_l93 = bati_gdf.to_crs(crs=2154,epsg=2154)



##Filtre spatial
inp, res = zone_l93.sindex.query_bulk(structure_l93.geom, predicate='within')
structure_l93['within'] = numpy.isin(numpy.arange(0, len(structure_l93)), inp)
equip_zone = structure_l93[(structure_l93['within'] == True)]



##Ajout d'une valeur unique au tableau
value = 1
equip_zone['value'] = value

###Création des critères

## Decoupage de l'UA par le territoire d'intérêt
# D'après https://automating-gis-processes.github.io/CSC18/lessons/L6/clipping-raster.html
def getFeatures(gdf):
    """Function to parse features from GeoDataFrame in such a manner that rasterio wants them"""
    import json
    return [json.loads(gdf.to_json())['features'][0]['geometry']]
coords = getFeatures(zone_l93)
out_img, out_transform = mask.mask(ua_gl, coords, crop=True) # Clip
out_meta = ua_gl.meta.copy() # Copie des méta
#epsg_code = 
# pycrs.parse.from_epsg_code(2154).to_proj4()
out_meta.update({"driver": "GTiff",
                "height": out_img.shape[1],
                "width": out_img.shape[2],
                "transform": out_transform,
                "crs" : rasterio.crs.CRS({'init': 'epsg:2154'})}
                ) #Mise à jour des métadonnées
with rasterio.open('ua_gl_clipped.tif', "w", **out_meta) as dest:
    dest.write(out_img)
print("On avance")

## Reclassification UA_GL (cf les différents scénarii)

# D'après : https://qastack.fr/gis/163007/raster-reclassify-using-python-gdal-and-numpy
# Une autre solution : https://www.neonscience.org/resources/learning-hub/tutorials/classify-raster-thresholds-2018-py

with rasterio.open('ua_gl_clipped.tif') as src:
    # Read as numpy array
    ua_gl_array = src.read()
    profile = src.profile

for i in structure['type_equip']:
    if i == 'AMAP':
        ua_gl_array[numpy.where((ua_gl_array == 11) | (ua_gl_array == 20) | (ua_gl_array == 21) | (ua_gl_array == 22) | (ua_gl_array == 30) | (ua_gl_array == 40) | (ua_gl_array == 50) | (ua_gl_array == 60) | (ua_gl_array == 70) | (ua_gl_array == 80) | (ua_gl_array == 90))] = 0
        ua_gl_array[numpy.where(ua_gl_array == 10)] = 255
    else:
        ua_gl_array[numpy.where((ua_gl_array == 10) | (ua_gl_array == 20) | (ua_gl_array == 22) | (ua_gl_array == 30) | (ua_gl_array == 60) | (ua_gl_array == 80) | (ua_gl_array == 90))] = 0
        ua_gl_array[numpy.where(ua_gl_array == 11)] = 64
        ua_gl_array[numpy.where(ua_gl_array == 70)] = 128
        ua_gl_array[numpy.where((ua_gl_array == 40) | (ua_gl_array == 50))] = 192
        ua_gl_array[numpy.where(ua_gl_array == 21)] = 255

with rasterio.open('ua_reclass.tif', 'w', **profile) as dst:
    # Write to disk
    dst.write(ua_gl_array)

## Normalisation des valeurs
#Source : https://stackoverflow.com/questions/18380419/normalization-to-bring-in-the-range-of-0-1
def Fuzzify(data):
    return (data - numpy.min(data)) / (numpy.max(data) - numpy.min(data))
ua_gl_fuzzi = Fuzzify(ua_gl_array)

## Rastérisation des composts de la zone
# D'après https://gis.stackexchange.com/questions/151339/rasterize-a-shapefile-with-geopandas-or-fiona-python/349728
rst = rasterio.open('ua_gl_clipped.tif')
meta = rst.meta.copy()
meta.update(compress='lzw')
out_fn = './structure_zone_r.tif'
if not equip_zone.empty :
    with rasterio.open(out_fn, 'w+', **meta) as out:
        out_arr = out.read(1)
        # this is where we create a generator of geom, value pairs to use in rasterizing
        shapes = ((geom,value) for geom, value in zip(equip_zone.geom, equip_zone.value))
        burned = rasterio.features.rasterize(shapes=shapes, fill=0, out=out_arr, transform=out.transform)
        out.write_band(1, burned)

# Proximité (distance raster) depuis les équipements rastérisés
if not equip_zone.empty:
    src_ds = gdal.Open('structure_zone_r.tif')
    srcband = src_ds.GetRasterBand(1)
    dst_filename = 'prox_r.tif'
    drv = gdal.GetDriverByName('GTiff')
    dst_ds = drv.Create(dst_filename,
                        src_ds.RasterXSize, src_ds.RasterYSize, 1,
                        gdal.GetDataTypeByName('Float32'))
    dst_ds.SetGeoTransform(src_ds.GetGeoTransform())
    dst_ds.SetProjection(src_ds.GetProjectionRef())
    dstband = dst_ds.GetRasterBand(1)
    gdal.ComputeProximity(srcband, dstband, ["DISTUNITS=GEO"]) #Algo de proximité (distance géo et non en pixels)

# Note intéressante : le fichier ne s'écrit totalement QUE quand le traitement est relancé, c'est pourquoi
# Je recopie le MÊME CODE deux fois pour écrire un .tif pourri qui ne servira à rien
if not equip_zone.empty :
    src_ds = gdal.Open('structure_zone_r.tif')
    srcband = src_ds.GetRasterBand(1)
    dst_filename = 'prox_r_useless.tif'
    drv = gdal.GetDriverByName('GTiff')
    dst_ds = drv.Create(dst_filename,
                        src_ds.RasterXSize, src_ds.RasterYSize, 1,
                        gdal.GetDataTypeByName('Float32'))
    dst_ds.SetGeoTransform(src_ds.GetGeoTransform())
    dst_ds.SetProjection(src_ds.GetProjectionRef())
    dstband = dst_ds.GetRasterBand(1)
    gdal.ComputeProximity(srcband, dstband, ["DISTUNITS=GEO"]) #Algo de proximité (distance géo et non en pixels)

if not equip_zone.empty:
    equip_proxi = rasterio.open('prox_r.tif')

# Découper la carte de proximité selon le territoire d'étude
if not equip_zone.empty:
    out_img, out_transform = rasterio.mask.mask(equip_proxi, coords, crop=True) # Clip
    out_meta = equip_proxi.meta.copy() # Copie des méta
    out_meta.update({"driver": "GTiff",
                    "height": out_img.shape[1],
                    "width": out_img.shape[2],
                    "transform": out_transform,
                    "epsg": 2154}
                    ) #Mise à jour des métadonnées
    with rasterio.open('equip_proxi_clipped.tif', "w", **out_meta) as dest:
        dest.write(out_img)
print("un peu de patience")

# Fuzzify distance
if not equip_zone.empty:
    with rasterio.open('equip_proxi_clipped.tif') as src:
        # Read as numpy array
        equip_proxi_array = src.read()
        profile = src.profile
    proxi_fuzzi = Fuzzify(equip_proxi_array)

# Raster calculator : multiplication des deux critères (fuzzifiés)
if not equip_zone.empty:
    proxi_fuzzi.astype(numpy.float32)
    ua_gl_fuzzi.astype(numpy.float32)
    criteres = (proxi_fuzzi*ua_gl_fuzzi)
else:
    criteres=ua_gl_fuzzi

###Exclusion des zones inconstructibles
# Reclassification de l'UA déjà reclassé selon 2 règles : les valeurs à 0 = 0, le reste = 1
# 0 signifie non-constructible pour l'équipement d'intérêt ; 1 = constructible
with rasterio.open('ua_reclass.tif') as src:
    # Read as numpy array
    ua_gl_array = src.read()
    profile = src.profile
    profile['dtype'] = rasterio.float64
ua_gl_array[numpy.where(ua_gl_array == 0)] = 0
ua_gl_array[numpy.where(ua_gl_array >= 1)] = 1


# Exclusion des zones de l'UA égales à 0 (ie non constructibles) via de l'algèbre raster
criteres_filtre = (criteres*ua_gl_array)

# Écriture : array to .tif
with rasterio.open('criteres_filtre.tif', 'w', **profile) as dst:
    # Write to disk
    dst.write(criteres_filtre)

# Du raster à une couche de points
# D'après https://gis.stackexchange.com/questions/268395/converting-raster-tif-to-point-shapefile-using-python
filename='criteres_filtre'
inDs = gdal.Open('criteres_filtre.tif'.format(filename))
outDs = gdal.Translate('criteres_filtre.xyz'.format(filename), inDs, format='XYZ', creationOptions=["ADD_HEADER_LINE=YES"])
outDs = None
try:
    os.remove('result_amc.csv'.format(filename))
except OSError:
    pass
os.rename('criteres_filtre.xyz'.format(filename),'result_amc.csv'.format(filename))

# Du csv au gdf
df = pandas.read_csv(
    'result_amc.csv', delimiter=" ")
geometry = [shapely.geometry.Point(xy) for xy in zip(df.X, df.Y)]
crs = {'init': 'epsg:2154'}
geo_df = geopandas.GeoDataFrame(df, crs=crs, geometry=geometry)
del geo_df['X']
del geo_df['Y']

###Sélection des sites optimaux

# Sélection meilleure(s) valeur(s)
top1000 = geo_df.nlargest(1000,'Z')
# Calcul de distance moyenne entre le jeu de points
top1000_dist = top1000.geometry.apply(lambda g: top1000.distance(g))
top1000_dist['mean_dist'] = top1000_dist.mean(axis=1)
top1000_dist = top1000_dist.drop(top1000_dist.columns[0:-1], axis=1) #On supprime les colonnes inutiles
# Jointure des scores
top1000.columns = ['score', 'geometry']
top1000 = pandas.concat([top1000, top1000_dist], axis=1)
# Sélection de N valeurs selon leur distance relative et leur score
# D'après https://cmdlinetips.com/2019/03/how-to-select-top-n-rows-with-the-largest-values-in-a-columns-in-pandas/
top100 = top1000.nlargest(100, ['score', 'mean_dist'])
# Calcul desserte de population via des buffers
buffer = top100.geometry.buffer(1000) # Buffer de 1 km
buffer_gdf = geopandas.GeoDataFrame(geopandas.GeoSeries(buffer)) # Conversion GeoSeries - GeoDF
buffer_gdf.columns = ['geometry']
buffer_gdf = buffer_gdf.set_crs(epsg=2154)
bati_buffer = geopandas.sjoin(bati_l93, buffer_gdf, op='within')
# Calcul des populations par buffer
grouped = bati_buffer.groupby('index_right')['pop'].sum()
# Jointure des populations au top des valeurs
top100 = pandas.concat([top100, grouped], axis=1)
# Sélection des N meilleures dessertes
top1 = top100.nlargest(1, ['mean_dist', 'pop', 'score'])
print(top1)

# # Jointure entre top et equip_zone pour comparer les dessertes (plus nécessaire)
# equip_zone_geom = equip_zone[['geom']]
# equip_zone_geom.columns = ['geometry']
# equip_join = pandas.concat([top10, equip_zone_geom])

### Calcul de desserte de population

# # Polygones de Voronoi : l'idée est de calculer la desserte de population du nouvel équipement proposé
# # Ce calcul est réalisé à l'aide de polygones de Voronoi + couche des équipements (anciens + le nouveau)
# # D'après https://stackoverflow.com/questions/17246609/pythonic-way-to-create-a-numpy-array-of-coordinates/17246765
# points = fiona.open('equip_join.shp') #On lit le shp des équipements
# geoms = [ shapely.geometry.shape(feat["geometry"]) for feat in points ] #On récupère les géométries
# list_arrays = [ numpy.array((geom.xy[0][0], geom.xy[1][0])) for geom in geoms ] #On crée des objets np.array
# vor = scipy.spatial.Voronoi(list_arrays) # Construction des polygones de Voronoi
#
# # D'après https://stackoverflow.com/questions/27548363/from-voronoi-tessellation-to-shapely-polygons
# # Récupération des polygones
# polygons = {}
# for id, region_index in enumerate(vor.point_region):
#     points = []
#     for vertex_index in vor.regions[region_index]:
#         if vertex_index != -1:  # the library uses this for infinity
#             points.append(list(vor.vertices[vertex_index]))
#     points.append(points[0])
#     polygons[id]=points
#
# # Il faut maintenant exporter le dictionnaire de polygones en format standard (en gdf)
# # To json
# voronoi_json = json.dumps(polygons)
#
# liste_vor_poly=[]
# for key,value in polygons.items() :
#     liste_vor_poly.append(value)
#
# liste_vor = []
# for i in liste_vor_poly:
#     prout = {}
#     prout['coordinates']=i
#     prout['type']='Polygon'
#     liste_vor.append(prout)
#
# poly_list = []
# for i in liste_vor_poly:
#     for j in i:
#         poly_list.append(j)
#         #lat_point_list.append(j[0])
#         #long_point_list.append(j[1])
# for i in liste_vor_poly:
#     clean_geoms = pandas.DataFrame([["Polygon", i]], columns=["field_geom_type", "field_coords"])
#
# polygon_geom = shapely.geometry.Polygon(liste_vor_poly)
# crs = {'init': 'epsg:2154'}
# polygon = geopandas.GeoDataFrame(crs=crs, geometry=[polygon_geom])
# print(polygon.geometry)
# polygon.to_file(filename='polygon1.shp', driver="ESRI Shapefile")
# df = pandas.DataFrame(liste_vor)
# gdf = geopandas.GeoDataFrame(df, geometry='coordinates')

# Découper les polygones de Voronoi selon le territoire d'intérêt


### Fin du script

# Créer un buffer carré de 15m autour du point
buffer = top1.buffer(15)
topbuffer = buffer.envelope
# GeoSeries to Geodf
topgdf = geopandas.GeoDataFrame(geometry=geopandas.GeoSeries(topbuffer))
# Reprojection
topgdf = topgdf.to_crs("EPSG:4326")

# Sortir un résultat dans une nouvelle table de la BD (ou vue matérialisée)
# GeoDataFrame to PostGIS (dans une table existante qui est result_amc)
# D'après https://gis.stackexchange.com/questions/239198/adding-geopandas-dataframe-to-postgis-table
# CF aussi https://naysan.ca/2020/05/09/pandas-to-postgresql-using-psycopg2-bulk-insert-performance-benchmark/
# On remplace la table existante = suppression du résultat précédent
db_connection_url = "postgres://postgres:******@localhost:5432/TALL"
engine = create_engine(db_connection_url)
topgdf.to_postgis(
    con=engine,
    name="result_amc",
    if_exists='replace'
)
# Écraser les fichiers enregistrés
globals().clear()
import os
if os.path.exists("ua_gl_clipped.tif"):
    os.remove("ua_gl_clipped.tif")
if os.path.exists("ua_reclass.tif"):
    os.remove("ua_reclass.tif")
if os.path.exists("structure_zone_r.tif"):
    os.remove("structure_zone_r.tif")
if os.path.exists("prox_r.tif"):
    os.remove("prox_r.tif")
if os.path.exists("prox_r_useless.tif"):
    os.remove("prox_r_useless.tif")
if os.path.exists("equip_proxi_clipped.tif"):
    os.remove("equip_proxi_clipped.tif")
if os.path.exists("criteres_filtre.tif"):
    os.remove("criteres_filtre.tif")
if os.path.exists("result_amc.csv"):
    os.remove("result_amc.csv")

print("<h6>Ligne de fin de script</h6>")

###Fin de connexion avec BDD
#conn.close()

### Sortie : on génère un fichier qui a tjrs le même nom (chq requête écrase le précédent). Chargement de la page
### = input du fichier créé. Veiller à appeler le script.py qui récupère les requêtes du formulaire.
### Voir comment c'est fait en php ? L'enregistrement du fichier par une BDD ? L'idée = sessions utilisateurs =
### On y enregistre ces données = possible. MAIS nécessite juste d'écrire dans le BDD depuis Python (table temporaire).
### Peut-être le mieux = Récupérer requêtes du formulaire (requête ou passer par BDD) + Python + BDD
