<?php
// Fichier: get_reviews.php
// Ce script permet de récupérer les données de la collection 'reviews' pour affichage.

ini_set('display_errors', 1);
error_reporting(E_ALL);

$mongoUri = 'mongodb+srv://ViteetGourmand:Vite%26Gourmand@cluster0.ypwko9k.mongodb.net/?appName=Cluster0';

try {
    $manager = new \MongoDB\Driver\Manager($mongoUri);
    
    // 1. Lister les documents de la collection 'reviews' dans la base 'ecoride'
    $query = new \MongoDB\Driver\Query([]); 
    $cursor = $manager->executeQuery('ecoride.reviews', $query);
    
    $reviews = $cursor->toArray();
    
    header('Content-Type: application/json');
    echo json_encode($reviews, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
