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
            'trip_id' => '65e0f1a2b3c4d5e6f7g8h9i0',
            'reviewer_id' => 'user_123',
            'reviewee_id' => 'driver_456',
            'rating' => 5,
            'comment' => "Super voyage, chauffeur très prudent et ponctuel !",
            'status' => 'approved',
            'created_at' => new MongoDB\BSON\UTCDateTime(strtotime("-2 days") * 1000)
        ],
        [
            'trip_id' => '65e0f1a2b3c4d5e6f7g8h9i1',
            'reviewer_id' => 'user_789',
            'reviewee_id' => 'driver_456',
            'rating' => 2,
            'comment' => "Un peu de retard au départ, dommage.",
            'status' => 'pending',
            'created_at' => new MongoDB\BSON\UTCDateTime(strtotime("-1 day") * 1000)
        ],
        [
            'trip_id' => '65e0f1a2b3c4d5e6f7g8h9i2',
            'reviewer_id' => 'user_111',
            'reviewee_id' => 'driver_222',
            'rating' => 1,
            'comment' => "Comportement inapproprié pendant le trajet.",
            'status' => 'rejected',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]
    ];

    foreach ($reviews as $review) {
        $bulk->insert($review);
    }

    $result = $manager->executeBulkWrite('ecoride.reviews', $bulk);
    echo json_encode(["status" => "success", "inserted" => $result->getInsertedCount()]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
