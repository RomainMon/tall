# Tuto Git et GitHub

![GitHub Logo](https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png)

NB : la rédaction dans GitHub se fait en Markdown. CF le [guide de syntaxe](https://docs.github.com/en/free-pro-team@latest/github/writing-on-github/basic-writing-and-formatting-syntax#content-attachments)

## Introduction aux concepts de base

Git = le système de **contrôle de version** le plus largement utilisé aujourd'hui. Un contrôleur de versions est un programme
qui permet aux développeurs de conserver un historique des modifications et des versions de tous les fichiers. Ce qui permet :
1. De travailler à plusieurs sans risquer de supprimer les modifications des autres collaborateurs ;
2. De revenir en arrière en cas de problème ;
3. De suivre l'évolution étape par étape d'un code source pour retenir les modifications effectuées sur chaque fichier.

Dans Git, chaque copie de travail du code est aussi un dépôt (***repository***) qui contient l'historique complet de tous les changements.
Un dépôt est l'élément le plus fondamental de GitHub. C'est un **dossier de projet** (contient tous les dossiers de projet et stocke les révisions).
En général, sur GitHub, 1 dépôt = 1 projet. Les **panneaux de projet** nous aident eux à organiser le travail.

Différence entre **Git** et **GitHub** :
1. Git = l'outil qui nous permet de **créer un dépôt local** et de **gérer les versions de nos fichiers** ;
2. GitHub = un service en ligne qui va **héberger notre dépôt**, qui sera donc distant. C'est un entrepôt virtuel.

Un projet collaboratif sur GitHub nécessite quelques réflexes : 
- Regarder la doc ;
- **Rapatrier le dépôt distant sur notre dépôt local** (principe de duplication et d'utilisation des branches) ;
- Associer une fonctionnalité à une branche ;
- Envoyer des modifications + messages de description ;
- Nettoyer l'historique (`git rebase`, cf infra) ;
- Éventuellement : mobiliser GitFlow, une architecture Git qui permet de séparer le travail et de toucher le moins possible à la branche principale.

Le dépôt local servira à améliorer les versions, le dépôt distant à stocker certaines versions, afin de garder un historique délocalisé, mais aussi
de les rendre publiques pour que chacun y apporte ses évolutions. Le principal atout de Git est le système de **branches**. Les branches sont les copies
du code principal à un instant *t* (pas d'impact sur le code principal), tandis que la **branche principale** (*master*, *main*, etc. selon le nom que vous
choisissez lors de l'installation de Git) doit rester inchangée jusqu'à validation des modifications sur les autres branches (on procède alors à une
intégration sur la branche master : Git s'occupe alors de la fusion, et de la gestion des erreurs). En gros, on crée une branche virtuelle sur laquelle 
tous nos changements sont enregistrés, et on les ajoute à la branche principale quand on le souhaite.

Pour information : **GitLab** est une alternative à GitHub, qui se veut plus puissante. Fonctionne en CI/CD (intégration continue/déploiement continu) =
permet l'automatisation de tests, de déploiement d'application, etc. 

## Première prise en main

GitHub nécessite d'abord **la création d'un compte**. Sur la vue générale du profil, on trouve une série d'onglets, dont **Repositories** (les dépôts).
- Onglet *Pull requests* : permet de réaliser des demandes de *pull*. Nous permet d'informer les autres sur les modifications appliquées
à une branche. Une fois la demande de pull ouverte, on peut discuter et examiner les modifications avec les **collaborateurs** et ajouter des validations de
suivi avant que nos modifications ne soient fusionnées dans la **branche de base** (*master*).
- *Explore* = trouver des projets OS (Open Source) sur lesquels travailler.
- Créer un nouveau *repository* ![Repo](https://docs.github.com/assets/images/help/repository/create-repository-desc.png)
- "Issues" : gestion des bugs. Permettent aux utilisateurs / collaborateurs d'indiquer des bugs afin qu'ils soient corrigés par d'autres. Une fois le bug résolu,
passer l'issue au statut "clos".

Pour installer notre projet sur GitHub. Cliquer sur la petite croix depuis le profil, puis "New repository", choisir le nom,
s'il est public/privé, ajouter un README.

Maintenant, installer [Git](https://git-scm.com/download/win). À la fin de l'installation, lancer ***Git Bash***.
- Initialiser Git : renseigner nom et adresse (grâce à l'option `--global` on n'a besoin de le faire qu'une fois)
```
$ git config --global user.name "John Doe"
$ git config --global user.email johndoe@example.com
```
- Taper `git config -list` pour vérifier que les changements ont été pris en compte
- Éventuellement, modifier éditeur et outil de merge (par défaut : Vim et Vimdiff), via la commande :

```
$ git config --global core.editor notepad++
$ git config --global merge.tool vimdiff
```
- Créer un dépôt local en 1) créant un dépôt local vide ou 2) clonant un dépôt distant. 
1. Créer dépôt local : créer dossier sur le disque **ou** accéder à un dossier et lancer la ligne suivante dans Git Bash :

```
$ cd Documents/Fichiers/Git/PremierProjet
johndoe ~/Documents/Fichiers/Git/PremierProjet
$ git init
Initialized empty Git repository in c:/users/JohnDoe/Documents/Fichiers/Git/PremierProjet/
```
NB : le dossier n'a rien dedans, sauf création d'un dossier caché .git

2. Cloner dépôt distant : récupérer [l'URL du dépôt distant sur GitHub](https://github.com/RomainMon/tall.git ) (tall)
via GitHub (profil) - "Code", "HTPPS" et copier l'URL. Puis sur Git Bash taper : `git remote add tall https://github.com/RomainMon/tall.git`
(pointer vers le dépôt distant) et ensuite `git clone https://github.com/RomainMon/tall.git` (cloner)

On a maintenant un nouveau dépôt et dans ce dossier, tous les fichiers clonés. Normalement, l'en-tête de GitBash indique le chemin vers le dépôt.

***EN CAS DE MESSAGE "fatal: not a git repository (or any of the parent directories)"***:
Vous n'êtes pas dans le dépôt Git. Pour cela, naviguer vers le bon dossier par la commande `ls` et enregistrer le chemin : `$ cd tall/`

* Pour connaître les branches présentes dans le projet : `$ git branch`. L'étoile indique sur quelle branche on se situe.
* Créer une branche : `git branch generateur` : création de la branche "generateur" en local (n'est pas duppliquée sur le dépôt distant). Pour basculer dessus :
`git checkout generateur`, et en retapant `git branch`, on voit, par l'étoile, qu'on est passés sur cette branche.

Pour rappel, la branche = un dossier virtuel. On reste physiquement sur tall, mais on est sur la branche generateur. On peut alors y effectuer des évolutions
**sans toucher à la branche principale**. Si l'on a réalisé des retouches sur generateur, on demande à Git de les enregistrer, c'est un ***commit***.

* Réaliser un commit : `git commit -m ''Ecriture du script Python''`. Les modifications sont maintenant enregistrées avec une description du travail effectué.
* Réaliser un push (envoi des modifications que l'on a réalisées en local sur le dépôt distant) : `git push` (cf infra pour plus de détails)

Git gère les versions de nos travaux locaux via 3 zones locales majeures :
* Le répertoire de travail (wd) ;
* L'index (ou stage) : fichiers que l'on souhaite voir apparaître dans notre prochain commit. C'est avec `git add` que l'on ajoute un fichier au stage ;
* Le dépôt local, ie historique local de l'ensemble de nos actions. Archivage se fait avec `git commit` et l'accès à l'historique par `git reflog`

![3 zones Git](https://user.oc-static.com/upload/2019/07/02/1562070846258_07.jpg)

Exemple d'utilisation : créer un fichier texte et réaliser un commit :
```
git add premierfichier.txt
git commit
```
Puis indiquer un message (dans un éditeur de texte : taper la description, fermer l'éditeur, enregistrer).

Il existe trois types d'objet dans Git :
1. Le *tree* = dépôt. Il référence une liste de sous-répertoires et fichiers ;
2. Le *commit* = pointe vers un arbre spécifique et le marque ;
3. Le *blob* (*Binary Large OBject*) = un fichier.
Ces objets ne sont pas identifiés par leur nom MAIS par leur ***id SHA-1*** (40 caractères environ).

## Travailler en équipe avec Git et GitHub

* Git merge

Une fois des modifications réalisées en local, on peut fusionner le travail fait sur différentes branches = fonction ***merge***, ou `git merge`.
Ne devrait être utilisé QUE pour la récupération INTÉGRALE et FINALE d'une branche dans une autre. Il combine plusieurs séquences de commits en un historique
unifié. Le plus souvent, il est utilisé pour combiner deux branches + crée un commit de merge. Attention à être sur la bonne branche avant la fusion !
Exemple : fusion branche master avec la branche fctMerge :
```
git checkout fctMerge
git status
git checkout master
git merge fctMerge
```
NB : si les deux branches que l'on essaie de fusionner modifient toutes les deux la MÊME PARTIE DU MÊME FICHIER, Git interrompt le processus = résolution manuelle.

* Git pull et Git push

Git pull rapatrie les modifications qui ont eu lieu sur le dépôt distant, vers le dépôt local. C'est à la fois un `git fetch` (téléchargement du contenu du dépôt
distant, PAS DE FUSION avec les modifications locales) ET un `git merge` (fusion). À l'inverse, `git push` envoie des modifications réalisées en local sur le dépôt distant.

![différence pull et push](https://user.oc-static.com/upload/2019/07/02/1562073008936_13.jpg)

Git fetch permet de contrôler le moment où l'on souhaite fusionner les données, à l'inverse de git pull.

![actions fetch pull push](https://user.oc-static.com/upload/2019/07/02/1562072253722_14.jpg)

* Git rebase

C'est une sorte de git merge (transfère changements d'une branche à une autre), si ce n'est que rebase permet de garder un historique plus clair et plus compréhensible.
ATTENTION : ne jamais faire de `git rebase` sur des commits pushés sur le dépôt public : cela remplacerait les anciens commits du dépôt public = perte d'historique.
Utilisation dans GitBash : `git rebase -i`, qui ouvre une session interactive qui permet de déplacer et modifier les commits un à un via les commandes :
```
# Commandes :
# p, pick = utilisez le commit
# r, reword = utilisez le commit, mais éditez le message de commit
# e, edit = utilisez le commit, mais arrêtez-vous pour apporter des changements
# s, squash = utilisez le commit, mais intégrez-le au commit précédent
# f, fixup = commande similaire à "squash", mais qui permet d'annuler le message de log de ce commit
# x, exec = exécutez la commande (le reste de la ligne) à l'aide de Shell 
# d, drop = supprimez le commit
```
Exemple d'utilisation : sélection des deux derniers commits et suppression :
```
git rebase -i HEAD~2
drop idSHA commit52
drop idSHA commit53
```
Il est important de nettoyer son historique avant d'envoyer sur le dépôt distant.

* Git bisect

Permet de rechercher un bug, par comparaison de commits (d'où l'intérêt de faire des commits réguliers).
Utilisation :
```
git bisect start [bad] [good]
```
Avec à la place de `[bad]` le hash d'un commit où le bug est présent et à la place de `[good]` le hash d'un commit où le bug n'était pas présent.

NB : ***HEAD*** = pointeur, référence sur notre position actuelle dans le wd Git. Par défaut, HEAD pointe vers la branche courante et peut être déplacé vers
une autre branche/commit.

## En cas de boulette

1. J'ai **modifié la branche principale par erreur** :
   * J'ai modifié la branche master avant de créer une branche, et je n'ai pas fait le commit. On fait alors une remise (mettre nos modifications de côté 
   le temps de créer notre nouvelle branche, et ensuite appliquer cette remise sur la nouvelle branche. Taper `git status` : on voit état des fichiers
   sur une branche donnée. Taper `git stash` = créer la remise PUIS basculer sur la branche d'intérêt PUIS appliquer la remise (`git stash apply`) (applique
   la dernière remise).
   Si l'on retape `git status`, on peut vérifier que tout est OK. 
   Si l'on a créé plusieurs remises et que l'on veut en appliquer une en particulier : `git stash list` et `git stash apply stash@{id de la remise}`
   * J'ai réalisé des modifications, et j'ai fait un commit. C'est plus complexe, car modifications enregistrées sur branche principale. On tape alors `git log` :
   liste les derniers commits. On récupère l'id du commit qui pose problème que l'on appelle le hash (par défaut, liste par ordre chronologique **INVERSÉ** des
   commits réalisés). PUIS taper `git reset --hard HEAD^` ce qui permet de supprimer le dernier commit de la branche master (le head^ = c'est le dernier commit
   que l'on veut supprimer). PUIS passer sur la bonne branche, réaliser un `git reset` mais qui va permettre d'appliquer le commit sur notre nouvelle branche :
   `git reset --hard 9a14306e` (les 8 premiers caractères suffisent).
   
 Exemple d'utilisation de la remise : application de changement faits sur Branch1 vers Branch2 :
 ```
 git status
 git stash
 git branch Branch2
 git checkout Branch2
 git stash apply
 ```

2. J'ai **oublié un fichier dans le dernier commit** :
```
git add fichieroublie.txt
git commit --amend --no-edit
```
En gros, `git commit --amend` permet de sélectionner le dernier commit afin d'y ajouter de nouveaux changements en attente.

3. **Corriger des erreurs sur le dépôt distant** :
   * Annuler son commit public avec `git revert HEAD^` (annule un commit en créant un nouveau commit d'annulation). Aucun
   impact sur l'historique. `git revert` = annule les changements commités VS `git reset` = annule changements **non commités**
   Attention : `git revert` peut écraser nos fichiers dans un répertoire de travail, il est donc conseillé de **commiter nos
   modifications** ou de **les remiser**. 
   `git reset` est un outil polyvalent pour l'annulation de changements. Peut être appelé avec `--soft` (pour se placer sur un
   commit spécifique / créer une branche en partant d'un ancien commit ; cette commande ne supprime rien), avec `--mixed` (revenir
   juste après le dernier commit ou le commit spécifié, sans supprimer nos modifications en cours), `--hard` (revenir à n'importe
   quel commit mais en oubliant tout ce qui s'est passé après). EX : `git reset CommitCible --hard` (à manipuler avec précaution)

![3 utilisations de git reset](https://user.oc-static.com/upload/2019/07/02/15620712098159_09.jpg)

NB : `git reset` revient à l'état précédent sans créer de nouveau commit VS `git revert` créer un nouveau commit.

![Différence revert et reset](https://user.oc-static.com/upload/2019/07/02/15620714617275_10.jpg)

4. **L'accès à distance ne fonctionne pas** : dans GitBash taper :
```
ssh-keygen -t rsa -b 4096 -C "adressemail"
```
* Renseigner nom de document (facultatif), un mdp (facultatif). Appuyer deux fois sur entrée sinon.
* Dans C : afficher les dossiers masqués. 
* Cliquer sur .ssh et l'on a une clé publique (id_rsa.pub) et une clé privée (id_rsa.txt). 
* Dans GitHub : notre compte - "settings" - "SSH and GPG keys" - "new SSH key".
    
5. **Corriger un commit raté** : `git log` (énumère les commits réalisés, les plus récents apparaissent en premier).
On identifie chaque commit, son id SHA, son auteur, la date et le message. Pour revenir à une action : `git checkout idSHA`
`git blame monFichier.php` = examiner le contenu d'un fichier ligne par ligne + déterminer la date à laquelle chaque ligne
a été modifiée et par qui.
