# Plugin CGE IPX V3 pour Jeedom V4 

plugin-jeeksenia is a Jeedom V4 plugin for the KSENIA lares 16IP alarm



## Utilisation

- activer le plugin et choisir une frequence de mise a jour , 5-6 sec est un bon choix
- creer un equipement racine pour la centrale ( avec addr ip, port , user name, pwd pour l'acces au site web de la centrale )
- au prochain save de l'equipement racine les equipements Zones seront crees


## Commandes

KSenia Root equipment
- **Etat** : 1 si la connectivité fonctionne et que l'on recoit des donnees
- **Product Name** : return the KSenia product name. this plugin only works for KSENIA lares 16IP
- **Product Version** : return the KSenia product version : High.Low.Build

KSenia Zone equipment
- **Présence** : detection de présence ( 1 ) ou abscense (0 )
- **Etat** : Etat du capteur de la zone ( UN_BYPASS en mode normal , LOST si perdu )

## Change Log

[Change Log](changelog.md)

## Installation

after installation, the device appear on your dashboard this way
![ipxdevice](../images/kseniadevice.png)
