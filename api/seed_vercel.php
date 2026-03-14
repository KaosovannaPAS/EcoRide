<?php
// api/seed_vercel.php
// Script temporaire ROBUSTE pour injection TiDB Vercel
header('Content-Type: application/json', true, 200);
require_once __DIR__ . '/../noyau_backend/configuration/db.php';

try {
    $pdo->beginTransaction();
    $logs = ["Départ injection ciblée..."];

    // 1. Définir nos conducteurs vedettes
    $users = [
        ['pseudo' => 'Sophie M.', 'email' => 'sophie@ecoride.com', 'photo' => 'sophie.png'],
        ['pseudo' => 'Thomas G.', 'email' => 'thomas@ecoride.com', 'photo' => 'thomas.png'],
        ['pseudo' => 'Marc D.', 'email' => 'marc@ecoride.com', 'photo' => 'marc.png'],
        ['pseudo' => 'Julie D.', 'email' => 'julie@ecoride.com', 'photo' => 'julie.png'],
        ['pseudo' => 'Camille R.', 'email' => 'camille@ecoride.com', 'photo' => 'camille.png']
    ];

    $password_hash = password_hash('password123', PASSWORD_BCRYPT);
    $user_ids = [];

    // 2. Cibler spécifiquement l'ancien "EcoDriver" s'il existe pour le transformer
    $stmtEco = $pdo->prepare("SELECT id FROM users WHERE pseudo = 'EcoDriver' OR email = 'chauffeur@ecoride.fr' LIMIT 1");
    $stmtEco->execute();
    $ecoId = $stmtEco->fetchColumn();

    if ($ecoId) {
        $pdo->prepare("UPDATE users SET pseudo='Marc D.', photo='marc.png' WHERE id=?")->execute([$ecoId]);
        $user_ids[] = $ecoId;
        $logs[] = "Ancien EcoDriver (ID:$ecoId) transformé en Marc D.";
    }

    // 3. Insérer les autres
    foreach ($users as $u) {
        // Skip if we already used this pseudo in step 2 (simple logic)
        if ($u['pseudo'] === 'Marc D.' && $ecoId)
            continue;

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$u['email']]);
        $uid = $check->fetchColumn();

        if ($uid) {
            $pdo->prepare("UPDATE users SET pseudo=?, photo=?, role='chauffeur' WHERE id=?")
                ->execute([$u['pseudo'], $u['photo'], $uid]);
            $user_ids[] = $uid;
        }
        else {
            $sql = "INSERT INTO users (pseudo, email, password_hash, role, credits, photo, bio, pref_smoking, pref_animals, pref_music, created_at) 
                    VALUES (?, ?, ?, 'chauffeur', 100, ?, '', 0, 0, 0, NOW())";
            $pdo->prepare($sql)->execute([$u['pseudo'], $u['email'], $password_hash, $u['photo']]);
            $user_ids[] = $pdo->lastInsertId();
        }
    }

    // 4. Réassigner TOUS les trajets aux IDs trouvés
    $trips = $pdo->query("SELECT id FROM trips")->fetchAll(PDO::FETCH_COLUMN);
    $count = 0;
    foreach ($trips as $tid) {
        $rid = $user_ids[array_rand($user_ids)];
        $pdo->prepare("UPDATE trips SET driver_id = ? WHERE id = ?")->execute([$rid, $tid]);
        $count++;
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'logs' => $logs, 'updated_trips' => $count]);

}
catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
