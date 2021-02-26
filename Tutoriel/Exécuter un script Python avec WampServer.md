# Tutoriel --> Exécuter un script Python avec WampServer

Pour suivre ce tutoriel, il est nécessaire d'avoir une installation WampServer fonctionnelle.

## Table des matières

## Étape 1 : Installation de python

WampServer émule un serveur sur notre machine. De ce postulat, on comprend que notre machine est un serveur, notre serveur. Nous allons installer python dans sur le serveur pour ensuite y faire appel lors de l'excution de script en Python.

Allez sur le site [www.python.org](https://www.python.org/downloads/) et téléchargez la version de python qui vous intéresse. Dans notre cas la "3.8.6".
1. Lancez l'excutable d'installation
2. Cochez "Add Python 3.8 to PATH"
3. Sélectionnez "Customize installation"
4. Laissez les options cochez par défaut dans la fenêtre "Optional Features", et faites "Next"
5. Dans la fnêtre "Advanced Options" modifiez le lien de "Customize install location" et mettez "C:\Python3.8.6"
6. Cliquez sur "Install"

## Étape 2 : Configuration de WampServer

Maintenant que Python est installé sur serveur, il faut configurez WampServer pour qu'il soit capable d'exécuter les scripts en Python. Pour cela nous allons modifier la configuration du serveur Web Apache.

1. Lancez WampServer
2. Faites un clic gauche sur le logo Wampserver présent sur la barre des tâches
3. Allez dans "Apache" --> "Fichiers et documentation" --> "httpd.conf"

A présent que le fichier "httpd.conf" est ouvert, nous allons le paramètrer.
1. Contrôle + F : "directory"
2. Remplacez :

        <Directory />
            AllowOverride none
            Require all denied
        </Directory>

Par :
 
    <Directory />
        AllowOverride none
        Require all granted
    </Directory>





