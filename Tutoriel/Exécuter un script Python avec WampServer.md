# Tutoriel --> Exécuter un script Python avec WampServer

Pour suivre ce tutoriel, il est nécessaire d'avoir une installation de WampServer fonctionnelle ainsi que des connaissances de base sur le fonctionnement d'une architecture web et de WampServer.

## Table des matières

## Étape 1 : Installation de python

WampServer émule un serveur sur notre machine. De ce postulat, on comprend que notre machine est un serveur, notre serveur. Nous allons installer python dans sur le serveur pour ensuite y faire appel lors de l'excution de scripts en Python.

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

3. Remplacez :

        Options +Indexes +FollowSymLinks +Multiviews
        
Par :

        Options +Indexes +FollowSymLinks +Multiviews +ExecCGI

4. Contrôle + F : "addhandler"
5. Remplacez :

        #AddHandler cgi-script .cgi
        
Par :

        AddHandler cgi-script .cgi .py
        
6. Sauvegarder les modifications
7. Faites un clic gauche sur le logo Wampserver présent sur la barre des tâches
8. Cliquez sur "Redémarrez les services"

Notre serveur est maintenant en mesure d'exécuter des scripts en Python.

## Étape 3 : Exécuter un script python par le biais de WampServer

Après avoir créé un "Virtual Host" pour l'occasion, je mets un script python dans celui-ci. Ce fichier doit avoir la configation suivante :

        #on écrit le lien vers notre installation python dans un commentaire de la façon suivante :
        #!C:/Python3.8.6/python.exe

        print("content-type: text/html\n\n" ) # je crée un contenu html
        print("<br><B>hello la team</B>") #j'affiche hello la team en HTML

Si vous excutez en double cliquant dessus, ou en mettant directement le lien vers celui ci dans votre barre de recherche, votre page web affichera "hello la team".

## Étape 4 : Exécuter un script python par le biais de php

Créez un fichier php dans le même "Virtual Host" que votre script python. Dans le script php assurez vous dans un premier temps que vous êtes dans le même dossier que le script python et que celui est lisible. Pour cela, je vous conseille de faire le script suivant et de l'ouvrir par le biais de votre "Virtual Host" :
        
        <?php
            $id = opendir("./"); //ouverture d'un fichier. './' = le dossier courant
            while($str = readdir($id)){ // renvoie le contenu du dossier. boucle pour renvoyer chaque élément.
                echo $str;
                echo " ".filetype($str); //permet de connaitre le type de fichier
                echo "<br />"; //pour avoir le dossier à la ligne
            };
        ?>
 
 Si tout vous semble bon, remplacez le script précédent par celui :
 
        <?php
            exec('hi.py'); //permet d'excuter un programme. Le paramètres en le script Python.
        ?>
        
  Enfin, exécutez ce script en passant toujours par votre "Virtual Host".
  
  
## Étape 5 : Exécuter un script python contenant des libraries par le biais de php

Rassurez-vous, vous venez de faire le plus dur. Pour installer des libraries utilisablent par le script python exécuté dans WampServer, il suffit de faire les étpaes suivantes :
1. Ouvrez votre "invite de commandes" en tant qu'administrateur
2. Par défaut vous êtes dans "C:\WINDOWS\system32", faite :

        cd ..
4. A présent vous êtes dans "C:\Windows", retournez à la racine de votre disque dur en faisant une nouvelle fois :

        cd ..
5. Maintenant vous êtes dans "C:\", et on veut aller dans notre espace python 3.8.6, pour cela faite :

        cd Python3.8.6
6. Installez les packages qui vous intéressante grâce à la commande "pip", exemple :

        C:\Python3.8.6>pip install requests
        
7. Une fois vos packages installés, retournez dans votre "Virtual Host" et exécutez le script python faisant appel à des librairies avec votre script php comme vu dans l'étape 5.

Exemple de script en python faisant appel à une librarie :

        #!C:/Python3.8.6/python.exe

        import requests # appel de la librairie requests installé via la commande pip

        response = requests.get('https://api.github.com')
        fichier_statut  = open("fichier_stat.txt", "w")
        statut_request = response.status_code
        if response.status_code == 200:
            fichier_statut.write("ça marche")
            fichier_statut.close()

        else:
            fichier_statut.write("ça ne marche pas")
            fichier_statut.close()
