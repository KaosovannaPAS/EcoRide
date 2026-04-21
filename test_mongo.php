<?php
// Fichier: test_mongo.php
// Ce script permet de vérifier si votre connexion à MongoDB fonctionne correctement.

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Test de connexion à MongoDB</h2>";

// ⚠️ N'oubliez pas de remplacer <db_password> par votre vrai mot de passe
$mongoUri = 'mongodb+srv://ViteetGourmand:Vite%26Gourmand@cluster0.ypwko9k.mongodb.net/?appName=Cluster0';

echo "<p>URI utilisée : <code>" . htmlspecialchars(substr($mongoUri, 0, 30)) . "...</code> (mot de passe masqué)</p>";

// 1. Vérification de l'extension PHP MongoDB
if (!extension_loaded('mongodb')) {
    echo "<div style='color: white; background: red; padding: 10px; border-radius: 5px;'>";
    echo "<strong>ERREUR : L'extension PHP MongoDB n'est pas installée / activée dans XAMPP !</strong><br>";
    echo "Pour Windows, vous devez télécharger <code>php_mongodb.dll</code>, le placer dans le dossier <code>C:\xampp\php\ext\</code><br>";
    echo "et ajouter <code>extension=mongodb</code> dans votre fichier <code>C:\xampp\php\php.ini</code>.";
    echo "</div>";
    exit;
} else {
    echo "<p style='color:green;'>✔️ L'extension PHP MongoDB est bien installée et chargée.</p>";
}

// 2. Test de la connexion
try {
    $manager = new \MongoDB\Driver\Manager($mongoUri);
    // On lance une commande 'ping' sur la base de données par défaut de l'utilisateur ('admin')
    $command = new \MongoDB\Driver\Command(['ping' => 1]);
    $cursor = $manager->executeCommand('admin', $command);
    
    $response = $cursor->toArray()[0];
    if (isset($response->ok) && $response->ok == 1) {
        echo "<div style='color: white; background: green; padding: 10px; border-radius: 5px;'>";
        echo "<strong>✔️ Connexion réussie à MongoDB Atlas !</strong>";
        echo "</div>";
    } else {
        echo "<p style='color:orange;'>La connexion semble ok mais le ping a renvoyé un résultat inattendu.</p>";
    }
} catch (\MongoDB\Driver\Exception\Exception $e) {
    echo "<div style='color: white; background: darkred; padding: 10px; border-radius: 5px;'>";
    echo "<strong>❌ Échec de la connexion :</strong><br>";
    echo "<em>" . date('Y-m-d H:i:s') . " - " . $e->getMessage() . "</em><br><br>";
    echo "<strong>Vérifiez :</strong><br>";
    echo "1. Que vous avez bien remplacé <code>&lt;db_password&gt;</code> par votre mot de passe.<br>";
    echo "2. Que votre adresse IP actuelle est autorisée (Network Access) sur le dashboard de MongoDB Atlas.<br>";
    echo "</div>";
}
