#
# -*- coding: utf-8 -*-
#!/usr/bin/python3

import os
from os import path
import shapefile
from osgeo import gdal
from osgeo import ogr
from osgeo import gdal
import pandas as pd
import geopandas as gpd
import psycopg2
import random
import time
from sqlalchemy import create_engine
import string
import geoalchemy2
from datetime import datetime



# Connexion à la base de données postgresql :
#identifiant pour la connexion :
HOST = "localhost"

USER = "postgres"

PASSWORD = "******"

DATABASE = "TALL"

conn = psycopg2.connect("host=%s dbname=%s user=%s password=%s" % (HOST, DATABASE, USER, PASSWORD))
#print (conn)

#Setting auto commit false
conn.autocommit = True

#création d'un curseur pour parcourir la base de donnée :
cur = conn.cursor()

#Suppression de la table utilisateur si elle existe déjà
cur.execute("drop table if exists utilisateur cascade")
print("Table utilisateur supprimee")

# sélection des nom dans la table catégorie
cur.execute("""SELECT id_cate FROM categorie""")

#on cherche tous les éléments
rows = cur.fetchall()

#création de la liste à remplir
liste_categorie = []

#remplissage de la liste des catégories:
for row in rows :
    liste_categorie.append(row[0])

#Vérification que la liste est écrite :
#print (liste_categorie)

#selections des associations dans la base de données
cur.execute("""SELECT id_asso FROM association""")
rows = cur.fetchall()
liste_association = []
for row in rows :
    liste_association.append(row[0])

print(liste_association)

# Close connection
conn.close()
#print (conn)

#lecture des fichiers shape de base avec la bibliothèque géopandas pour créer des tableaux.
adresse = 'data_travail/ADRESSE_GL_QUARTIER_4326.shp'
#chemin vers le fichier shp déjà créé si il existe (test par la suite)
adresse_1 = 'data_travail/ADRESSE_GL_QUARTIER_1_4326.shp'

#Création du fichier shape de 1 % aléatoire des adresses si il n'existe pas déjà :
if path.exists(adresse_1) :
    print("c'est moins long")
    gdf_adresse_1 = gpd.read_file(adresse_1,encoding = 'UTF-8')
    #print(gdf_adresse_1.head())
    #print(gdf_adresse_1.shape)
    #print(gdf_adresse_1.shape[0])
    #print(gdf_adresse_1.index)
else :
    #création de 1% d'adresse :
    print("c'est plus long")
    gdf_adresse = gpd.read_file(adresse,encoding = 'UTF-8')    
    gdf_adresse_1 = gdf_adresse.sample(frac = 0.01) #Sélection aléatoire de 1% d'individu.
    #print(gdf_adresse_1.head())
    #print(gdf_adresse_1.shape)
    #print(gdf_adresse_1.shape[0])
    #print(gdf_adresse_1.index)
    gdf_adresse_1.to_file('data_travail/ADRESSE_GL_QUARTIER_1_4326.shp')

#remplissage des champs de la table utilisateurs création de liste qui corresponde au nombre d'utilisateurs généré aléatoirement plus haut.
#Création du prénom sous la forme UtiliN°_utilisateur
liste_prenom = ["Utili"+str(i) for i in gdf_adresse_1.index]
#Création du prénom sous la forme SateurN°_utilisateur
liste_nom = ["Sateur"+str(i) for i in gdf_adresse_1.index]
#Mail sous la forme prenom.nomN°_utilisateur@tallmail.com
liste_mail = ["Utili.Sateur"+str(i)+"@tallmail.com" for i in gdf_adresse_1.index]
#Date d'inscription : heure locale sous le format timestamp finalement pas utilisé.
#liste_date_inscription = [datetime.timestamp(datetime.now()) for i in gdf_adresse_1.index]
# creation des associations des utilisateurs
liste_association_par_utilisateur = [random.choice(liste_association) for i in gdf_adresse_1.index]
#Création d'un mot de passe aléatoire pour chaque utilisateur afin de bien remplir la base de données.
def getPassword(length):
    #Générer une chaîne aléatoire de longueur fixe
    stri = string.ascii_lowercase
    return ''.join(random.choice(stri) for i in range(length))
liste_mdp = [getPassword(10) for i in gdf_adresse_1.index]
#Numéro de téléphone laissé nul pour ne pas créer aléatoirement des numéros qui existent.
liste_tel = ['null' for i in gdf_adresse_1.index]
#Liste des id utilisateurs : equivalent à un serial pas utilisé
#liste_id_utilisateur = [i for i in gdf_adresse_1.index]

#Fonction de remplissage des catégories d'intérêt :
#Création des listes : 
def liste_cate(liste) : 
    #nombre de catégorie pour l'utilisateur :   
    nb_cate = random.randint(1,5)
    #création de la liste pour chaque utilisateur
    liste_cate_par_utilisateur = []
    #Itération sur le nombre de catégorie pour chaque utilisateur :
    for i in range(nb_cate) : 
        #choix aléatoire de catégorie       
        nb_i = random.choice(liste)
        #ajout d'un choix aléatoire de catégorie à la liste
        liste_cate_par_utilisateur.append(nb_i)
        #suppression de ce choix de la liste initiale pour éviter les doublons
        liste.remove(nb_i)        
    
    #La première liste est remplie de nouveau par celle de l'utilisateur :
    for j in liste_cate_par_utilisateur :        
        liste.append(j)

    #Si le nombre de catégorie pour l'utilisateur est de moins de 5 on remplie par un champs null
    if nb_cate < 5 :
        #on fait la différence avec 5 pour le nb de null à rajouter    
        for i in range(5-nb_cate) :
            liste_cate_par_utilisateur.append('null')
    else :
        pass
    
    #La fonction renvoi la liste pour un utilisateur
    return(liste_cate_par_utilisateur)

#Création d'une liste des listes pour chaque utilisateur :
liste_cate_total_utilisateur = []
#itération sur le nombre de rangée du tableau :
for i in gdf_adresse_1.index :    
    i = liste_cate(liste_categorie)
    liste_cate_total_utilisateur.append(i)

#Création d'un tableau pandas avec le nom des colonnes que l'on ajoutera au tableau gdf_adresse_1
df_cate_utilisateur = pd.DataFrame(liste_cate_total_utilisateur,columns = ["id_cate_1","id_cate_2","id_cate_3","id_cate_4","id_cate_5"])

#gdf_adresse_1['id_utilisateur']=liste_id_utilisateur
gdf_adresse_1['prenom']=liste_prenom
gdf_adresse_1['nom']=liste_nom
gdf_adresse_1['email']=liste_mail
gdf_adresse_1['mdp']=liste_mdp
gdf_adresse_1['telephone']=liste_tel
#gdf_adresse_1['date_inscription']=liste_date_inscription
gdf_adresse_1['id_asso'] = liste_association_par_utilisateur

#Concaténation des deux tableaux :
gdf_adresse_1 = pd.concat([gdf_adresse_1, df_cate_utilisateur], axis = 1) #axis = 1 pour que la juxtaposition se fasse par colonnes
  
#Vérification que le tableau est bon :
print(gdf_adresse_1)


gdf_adresse_1.rename(columns={'ID':'id_adresse'},inplace = True)

print(gdf_adresse_1.iloc[0])

print(gdf_adresse_1.columns)

#gdf_adresse_1.rename(columns={'geometry':'geom'},inplace = True)

gdf_adresse_1.drop(gdf_adresse_1.iloc[:,1:11],1,inplace=True)

print(gdf_adresse_1.columns)

#export du tableau géopandas vers la base de données :
db_connection_url = "postgres://postgres:******@localhost:5432/TALL"
engine = create_engine(db_connection_url)
#export au format postgis du tableau
gdf_adresse_1.to_postgis(name="utilisateur", con=engine)

#Reprise de la table pour ajouter le champs id_utilisateur serial et le champs date_inscription en timestamp et les contraintes non null sur les bonnes colonnes:
conn2 = psycopg2.connect("host=%s dbname=%s user=%s password=%s" % (HOST, DATABASE, USER, PASSWORD))

#Setting auto commit false
conn2.autocommit = True

#création d'un curseur pour parcourir la base de donnée :
cur2 = conn2.cursor()

#Requêtes
#Creation et remplissage de l'id utilisateur et clef primaire
cur2.execute("ALTER TABLE utilisateur Add COLUMN id_utilisateur SERIAL")
cur2.execute("ALTER TABLE utilisateur ADD PRIMARY KEY (id_utilisateur)")
#Creation et remplissage date inscription :
cur2.execute("ALTER TABLE utilisateur ADD COLUMN date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
#Changement des colonnes de null en non null
cur2.execute("ALTER TABLE utilisateur ALTER COLUMN prenom SET NOT NULL")
cur2.execute("ALTER TABLE utilisateur ALTER COLUMN nom SET NOT NULL")
cur2.execute("ALTER TABLE utilisateur ALTER COLUMN email SET NOT NULL")
#La colonne geometry est renommée en geom pour être cohérent avec les autres
cur2.execute("ALTER TABLE utilisateur RENAME geometry TO geom")
conn2.close()