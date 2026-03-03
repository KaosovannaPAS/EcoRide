# Charte Graphique - EcoRide

Ce document définit les règles visuelles du projet EcoRide, inspirées des plateformes modernes de covoiturage (style BlaBlaCar) tout en conservant une forte identité écologique.

## 1. Philosophie du Design

*   **Aéré et Lumineux :** De larges espaces blancs (`margin` et `padding` généreux) pour donner une impression de fluidité et de clarté.
*   **Moderne et Doux :** Utilisation systématique de coins arrondis (`border-radius: 1rem` soit 16px) sur les cartes, les boutons et les champs de formulaire.
*   **Hiérarchie par les Ombres :** Au lieu de bordures dures, nous utilisons des ombres portées douces (`box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)`) pour détacher le contenu du fond.

## 2. Palette de Couleurs

L'identité d'EcoRide repose sur un "Vert Écologique" vibrant et rassurant, associé à des teintes neutres pour le confort de lecture.

### Couleurs Principales
*   🟢 **Vert Primaire (Eco Primary) :** `#054752` - Un vert sapin profond, utilisé pour les titres principaux, le texte mis en valeur et l'identité de marque (Brand).
*   💚 **Vert Accent (Eco Accent) :** `#00cc66` - Un vert clair, énergique et vibrant. Utilisé pour les appels à l'action (Call to Action / Boutons principaux), les badges de succès, et les icônes.

### Couleurs d'Arrière-plan
*   🤍 **Fond Principal (Background) :** `#f8f9fa` (Gris très clair) - Couleur de fond de l'application (Body) pour éviter le blanc pur éblouissant.
*   ⚪ **Fond des Cartes (Surface) :** `#ffffff` (Blanc) - Pour les cartes de contenu, créant un contraste doux avec le gris clair.
*   🌿 **Fonds Thématiques :** `#f0fdf4` (Vert pastel très léger) - Utilisé ponctuellement, par exemple dans le Footer ou pour mettre en évidence les zones de "Crédits" ou d'"Impact".

## 3. Typographie

Pour garantir une lisibilité optimale sur tous les appareils, nous utilisons la pile de polices système moderne (System Fonts Stack).
*   **Famille de Polices :** `system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`
*   **Titres (H1, H2, H3) :** Graisse `bold` (700) ou `extra-bold` (800) avec la couleur `var(--eco-primary)` ou sombre (`#212529`).
*   **Texte Courant (p, span) :** Graisse `normal` (400) ou `medium` (500), couleur `text-muted` (`#6c757d` ou `#495057`) pour réduire la fatigue visuelle.

## 4. Éléments d'Interface (UI)

### Boutons
*   **Bouton Principal (Call to Action) :** Fond `#00cc66`, texte blanc `bold`, coins totalement arrondis (`rounded-pill`). Effet au survol : fond légèrement assombri.
*   **Bouton Secondaire :** Fond gris clair ou bordure primaire, texte foncé.
*   **Bouton de Connexion (NavBar) :** Bouton `rounded-pill` pour ressortir de la navigation.

### Cartes (Cards)
*   Toutes les cartes affichant des données (Trajets, Profil utilisateur, Avis) doivent avoir des coins arrondis (`rounded-4` ou 16px), aucune bordure visible, et une ombre douce (Shadow-sm).

### Badges
*   **Badge Écologique (`.eco-badge`) :** Typiquement de couleur claire avec texte `var(--eco-primary)` ou texte blanc sur fond vert, utilisé pour marquer la spécificité "Véhicule Électrique" (EV).

## 5. Maquettes (Wireframes/Mockups)

*(Les maquettes statiques - 3 Desktop, 3 Mobile - demandées dans l'ECF peuvent être générées ou intégrées ici sous forme de captures d'écran Figma/Adobe XD).*

L'interface a été conçue en "Mobile First" via Bootstrap 5, assurant une parfaite responsivité sur :
1.  **Mobile (< 768px) :** Les cartes de trajets prennent 100% de la largeur, le menu de navigation devient un menu hamburger, l'image de fond du héros est centrée.
2.  **Tablette (768px - 992px) :** Grille en 2 colonnes pour les témoignages, affichage optimisé des filtres.
3.  **Desktop (> 992px) :** Affichage étendu avec division de l'écran (ex: page d'inscription séparée 50% image / 50% formulaire).
