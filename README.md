# Test technique pour Withings


### Installation et librairies utilisées
Installation : 
``` composer install ```

Librairies :    
  - guzzlehttp/guzzle    
  - symfony/var-dumper   


### Description 
 - Récupération d'un athentification token
 - Récupération d'un access token avec le premier
 - Récupération des données utilisateurs
 - Trie et affichage de la donnée souhaitée

### Points d'amélioration
 - Design 
 - Test notamment pour vérifier que la valeur qu'on récupère est bien la dernière en date (on suppose que la première retournée est la plus récente)
 - Séparation en plusieurs pages (création d'un classe pourquoi pas)
