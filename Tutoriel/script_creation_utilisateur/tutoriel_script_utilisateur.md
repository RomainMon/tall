# Tutoriel Script python de création d'utilisateur aléatoires

Afin de remplir notre base de données et d'avoir des utilisateurs potentiels nous avons créé un script python qui permet de générer des utilisateurs à partir de la base de données adresse que l'on a disposition.

En résumé :

* Connexion à la base de donnée pour récupérer les catégories dans une liste

* le script récupère les données des adresses dans un fichier shape des adresses (j'ignore pourquoi mais la récupération directement depuis la base de données retourne un bug lors de la sélection aléatoire de 1% des adresses).

* Création d'un tableau géopandas avec la librairie géopandas à partir de ces données 

* Remplissage automatiques selon des règles des champs, récupération dans des listes:

  * prenom
  * nom
  * mail
  * mdp

* Sélection pour chaque utilisateur de 5 catégories d'intérêts aléatoirement (l'utilisateur peut avoir de 1 à 5 intérêts, ce nombre est fixé de façon aléatoire les champs intérêts non remplis prennent la valeur nulle). Ces données sont récupérés dans une liste de liste (par utilisateur) puis un tableau pandas.

* Remplissage du tableau géopandas à partir des données créées précédemment.

* Concaténation des tableaux et suppression des champs non-intéressants

* Export vers la base de données 

* Ajout et changement sur certains champs de la table dans la base de données.

Le script python est commenté pour comprendre les étapes.


