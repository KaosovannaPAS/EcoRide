<?php
// Fichier: seed_reviews.php
// Ce script peuple la collection 'reviews' avec des données de test.

ini_set('display_errors', 1);
error_reporting(E_ALL);

$mongoUri = 'mongodb+srv://ViteetGourmand:Vite%26Gourmand@cluster0.ypwko9k.mongodb.net/?appName=Cluster0';

try {
    $manager = new \MongoDB\Driver\Manager($mongoUri);
    $bulk = new \MongoDB\Driver\BulkWrite;

    $reviews = [
        [
            'trajet_id' => '65e0f1a2b3c4d5e6f7g8h9i0',
            'auteur_id' => 'user_123',
            'cible_id' => 'driver_456',
            'note' => 5,
            'commentaire' => "Super voyage, chauffeur très prudent et ponctuel !",
            'statut' => 'approuve',
            'date_creation' => new MongoDB\BSON\UTCDateTime(strtotime("-2 days") * 1000)
        ],
        [
            'trajet_id' => '65e0f1a2b3c4d5e6f7g8h9i1',
            'auteur_id' => 'user_789',
            'cible_id' => 'driver_456',
            'note' => 2,
            'commentaire' => "Un peu de retard au départ, dommage.",
            'statut' => 'en_attente',
            'date_creation' => new MongoDB\BSON\UTCDateTime(strtotime("-1 day") * 1000)
        ],
        [
            'trajet_id' => '65e0f1a2b3c4d5e6f7g8h9i2',
            'auteur_id' => 'user_111',
            'cible_id' => 'driver_222',
            'note' => 1,
            'commentaire' => "Comportement inapproprié pendant le trajet.",
            'statut' => 'rejete',
            'date_creation' => new MongoDB\BSON\UTCDateTime()
        ]
    ];

    foreach ($reviews as $review) {
        $bulk->insert($review);
    }

    $result = $manager->executeBulkWrite('ecoride.avis', $bulk);
    echo json_encode(["status" => "success", "inserted" => $result->getInsertedCount()]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
