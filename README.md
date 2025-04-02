# WP Activities Manager

Un plugin WordPress pour gérer et afficher des activités sportives et de bien-être avec des catégories, niveaux et certifications.

## Description

WP Activities Manager est un plugin WordPress qui permet de créer et gérer facilement des activités sportives et de bien-être sur votre site. Il offre une interface d'administration intuitive et un affichage élégant des activités sur le frontend.

Fonctionnalités principales :
- Création d'activités avec titre, description, image, durée, niveau et certification
- Catégorisation des activités (Fitness, Combat, Bien-être, Artistique, Aquatique, etc.)
- Marquage des activités populaires
- Affichage des activités avec un design moderne et responsive
- Filtrage des activités par catégorie
- Shortcode personnalisable pour afficher les activités sur n'importe quelle page

## Installation

1. Téléchargez le dossier `wp-activites` dans le répertoire `/wp-content/plugins/` de votre installation WordPress
2. Activez le plugin via le menu 'Extensions' dans WordPress
3. Commencez à créer vos activités via le menu 'Activités' dans le tableau de bord WordPress

## Utilisation

### Création d'une activité

1. Dans le tableau de bord WordPress, allez dans 'Activités' > 'Ajouter'
2. Donnez un titre à votre activité
3. Ajoutez une description détaillée dans l'éditeur
4. Définissez une image mise en avant (recommandé)
5. Dans la section 'Détails de l'activité', spécifiez :
   - Durée (en minutes)
   - Niveau (Tous niveaux, Débutant, Intermédiaire, Avancé)
   - Certification (cochez si l'activité est certifiée)
   - Activité populaire (cochez pour la mettre en avant)
6. Sélectionnez une catégorie d'activité dans la section 'Catégories d'activité'
7. Publiez l'activité

### Affichage des activités

Utilisez les shortcodes `[wp_activities]` ou `[wp_activities_style_2]` pour afficher les activités sur n'importe quelle page ou article.

#### Shortcode `[wp_activities]`

Options du shortcode :
- `category` : Filtrer par slug de catégorie (séparés par des virgules pour plusieurs)
- `limit` : Nombre d'activités à afficher (par défaut : 10)
- `orderby` : Trier par champ (par défaut : date)
- `order` : Direction du tri (ASC ou DESC, par défaut : DESC)

Exemple :
```
[wp_activities category="fitness,combat" limit="6" orderby="title" order="ASC"]
```

#### Shortcode `[wp_activities_style_2]`

Options du shortcode :
- `category` : Filtrer par slug de catégorie (séparés par des virgules pour plusieurs)
- `limit` : Nombre d'activités à afficher (par défaut : 6)

Exemple :
```
[wp_activities_style_2 category="fitness,combat" limit="6"]
```

## Personnalisation

Le plugin utilise des styles CSS modernes et responsives. Vous pouvez personnaliser davantage l'apparence en ajoutant vos propres styles CSS dans votre thème.

## Licence

Ce plugin est distribué sous licence GPL-2.0+.
