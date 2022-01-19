# Winkel - Projet de fin d'année

## Serveur
### Technologies
    - Symfony
    - API Platform

### Instructions
Cloner le repo et installer les dépendances avec 
```bash
    composer install
```
Lancer le serveur:
```bash
    php -S localhost:8000 -t public
```
*OU*
```bash
    symfony serve
```
**L'API sera dispobible à l'addresse: "http://localhost:8000/api"**

Pour les personnes utilisant *Docker*, lancer le service "mysql":
```bash
    docker-compose up -d
```
La base de donnée se configurera seule.

RK1PF