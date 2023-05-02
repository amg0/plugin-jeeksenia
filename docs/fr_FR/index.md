# Plugin KSENIA LARES 16IP pour Jeedom V4 

plugin-jeeksenia is a Jeedom V4 plugin for the KSENIA lares 16IP alarm



## Utilisation

- activer le plugin et choisir une frequence de mise a jour , 5-6 sec est un bon choix
- creer un equipement racine pour la centrale ( avec addr ip, port , user name, pwd pour l'acces au site web de la centrale et PIN code pour les scenarios ). 
- Choisir un objet parent pour l'equipement racine.
- Au prochain save de l'equipement racine les equipements Zones seront crees. les equipements sont crees par defaut sous le meme objet parent que la racine.
- des commandes action sont crees sur l'equipement racine pour chacun des scenarios commandables sur la KSenia


## Commandes

KSenia Root equipment
- **Etat** : 1 si la connectivité fonctionne et que l'on recoit des donnees
- **Présence** : compte le nombre de zone qui detecte une presence ( 0 rien,  1 ou plus = le nombre de detecteur(s) avec présence confirmée)
- **Product Name** : return the KSenia product name. this plugin only works for KSENIA lares 16IP
- **Product Version** : return the KSenia product version : High.Low.Build
- **per each scenario** : an action command is created for every single scenario programmed in the KSenia. the command's name is the scenario's name. triggering the action, will call the scenario in KSenia, based on the PIN code entered in the root equipement configuration

Encryption: le `password` et le `pincode` sont encryptés dans la base de donnees.

KSenia Zone equipment
- **Présence** : detection de présence ( 1 ) ou abscense (0 )
- **Etat** : Etat du capteur de la zone ( UN_BYPASS en mode normal , LOST si perdu )

## Change Log

[Change Log](changelog.md)

## Installation

after installation, the device appear on your dashboard this way
![ipxdevice](../images/kseniadevice.png)
