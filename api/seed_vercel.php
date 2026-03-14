<?php
// api/seed_vercel.php
// Script temporaire pour injecter de faux utilisateurs + photos sur Vercel/TiDB
header('Content-Type: application/json', true, 200);

require_once __DIR__ . '/../noyau_backend/configuration/db.php';

try {
    $pdo->beginTransaction();

    $users = [
        ['pseudo' => 'Sophie M.', 'email' => 'sophie@ecoride.com', 'photo' => 'sophie.png'],
        ['pseudo' => 'Thomas G.', 'email' => 'thomas@ecoride.com', 'photo' => 'thomas.png'],
        ['pseudo' => 'Marc D.', 'email' => 'marc@ecoride.com', 'photo' => 'marc.png'],
        ['pseudo' => 'Julie D.', 'email' => 'julie@ecoride.com', 'photo' => 'julie.png'],
        ['pseudo' => 'Camille R.', 'email' => 'camille@ecoride.com', 'photo' => 'camille.png'],
        ['pseudo' => 'Antoine G.', 'email' => 'antoine@ecoride.com', 'photo' => 'antoine.png']
    ];

    $password_hash = password_hash('password123', PASSWORD_BCRYPT);
    $user_ids = [];
    $logs = ["Démarrage du seeding Vercel..."];

    foreach ($users as $u) {
        try {
            // First check if email exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$u['email']]);
            $existingId = $checkStmt->fetchColumn();

            if ($existingId) {
                // Update photo/pseudo
                $stmt = $pdo->prepare("UPDATE users SET photo = ?, pseudo = ?, role = 'conducteur' WHERE id = ?");
                $stmt->execute([$u['photo'], $u['pseudo'], $existingId]);
                $user_ids[] = $existingId;
                $logs[] = "Mis à jour : " . $u['pseudo'] . " (ID: " . $existingId . ")";
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO users (pseudo, email, password_hash, role, credits, photo, created_at) VALUES (?, ?, ?, 'conducteur', 100, ?, NOW())");
                $stmt->execute([$u['pseudo'], $u['email'], $password_hash, $u['photo']]);
                $newId = $pdo->lastInsertId();
                $user_ids[] = $newId;
                $logs[] = "Inséré : " . $u['pseudo'] . " (ID: $newId)";
            }
        }
        catch (Exception $e) {
            $logs[] = "Erreur sur " . $u['pseudo'] . " : " . $e->getMessage();
        }
    }

    if (empty($user_ids)) {
        throw new Exception("Aucun ID utilisateur valide récupéré.");
    }

    // Associer aléatoirement ces IDs aux trajets existants
    $stmt = $pdo->prepare("SELECT id FROM trips");
    $stmt->execute();
    $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updatedTrips = 0;
    foreach ($trips as $t) {
        $random_driver = $user_ids[array_rand($user_ids)];
        // Add updated_at if needed, but here we just update driver_id
        $updU = $pdo->prepare("UPDATE trips SET driver_id = ? WHERE id = ?");
        if (!$updU->execute([$random_driver, $t['id']])) {
            $logs[] = "Erreur UPDATE trip $t[id]";
        }
        $updatedTrips++;
    }

    $logs[] = "Seeding terminé. $updatedTrips trajets assignés aléatoirement.";
    $pdo->commit();
    echo json_encode(['status' => 'success', 'logs' => $logs]);

}
catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'logs' => $logs ?? [], 'trace' => $e->getTraceAsString()]);
}
?>
