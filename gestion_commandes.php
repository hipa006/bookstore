<?php 
session_start();
require_once('config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$nom_admin = $_SESSION['user_nom'] ?? 'Admin';

try {
    $sql_auto_cancel = "UPDATE commande 
                        SET statut = 'annulee' 
                        WHERE statut = 'en_cours' 
                        AND date_commande < NOW() - INTERVAL 48 HOUR";
    $pdo->exec($sql_auto_cancel);
} catch (PDOException $e) {
    
}

// Changement manuel du statut
if (isset($_POST['update_statut']) && isset($_POST['id_commande']) && isset($_POST['statut'])) {
    $id_cmd = (int)$_POST['id_commande'];
    $nouveau_statut = $_POST['statut'];
    
    $stmt_status = $pdo->prepare("UPDATE commande SET statut = ? WHERE id_commande = ?");
    $stmt_status->execute([$nouveau_statut, $id_cmd]);
    
    header("Location: gestion_commandes.php?msg=statut_updated");
    exit();
}


if (isset($_GET['delete_id'])) {
    $id_to_delete = (int)$_GET['delete_id'];
    
    $stmt = $pdo->prepare("DELETE FROM commande WHERE id_commande = ?");
    $stmt->execute([$id_to_delete]);
    
    header("Location: gestion_commandes.php?msg=deleted");
    exit();
}

// Récupération de la recherche si elle existe
$search_code = trim($_GET['search_code'] ?? '');

try {
    $sql = "SELECT c.id_commande, 
                   c.code_retrait, 
                   c.total, 
                   c.date_commande, 
                   c.statut,
                   u.nom AS client_nom, 
                   u.prenom AS client_prenom, 
                   u.email AS client_email, 
                   u.telephone AS client_telephone,
                   IFNULL(
                       GROUP_CONCAT(CONCAT(p.titre, ' (x', co.quantite, ')') SEPARATOR '<br>'), 
                       '<em style=\"color:#aaa;\">Aucun article</em>'
                   ) AS livres_details
            FROM commande c
            LEFT JOIN client u ON c.id_client = u.id_client
            LEFT JOIN concerner co ON c.id_commande = co.id_commande
            LEFT JOIN produit p ON co.id_produit = p.id_produit";

    $params = [];
    
    // Si l'utilisateur a écrit un code de retrait dans la recherche
    if (!empty($search_code)) {
        $sql .= " WHERE c.code_retrait LIKE ?";
        $params[] = "%" . $search_code . "%";
    }

    $sql .= " GROUP BY c.id_commande
              ORDER BY c.date_commande DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; display: flex; }
        .sidebar { width: 250px; background-color: #3E2723; color: white; height: 100vh; padding: 20px; position: fixed; box-sizing: border-box; }
        .sidebar h2 { color: #C69C6D; font-size: 20px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar p { font-size: 14px; color: #d7ccc8; }
        .sidebar a { display: block; color: #ccc; text-decoration: none; padding: 12px 0; border-bottom: 1px solid #4E342E; transition: 0.3s; }
        .sidebar a:hover { color: #C69C6D; padding-left: 10px; }
        
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); box-sizing: border-box; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { color: #3E2723; font-size: 24px; margin: 0; }
        
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; vertical-align: top; }
        th { background-color: #faf6f1; color: #3E2723; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; border-bottom: 2px solid #C69C6D; }
        
        .code-badge { background: #3E2723; color: #C69C6D; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-family: monospace; font-size: 14px; }
        .livres-list { color: #3E2723; font-weight: 600; font-size: 0.95em; line-height: 1.6; }
        .client-info { font-size: 0.9em; color: #555; }
        .price { font-weight: bold; color: #3E2723; }
        
        .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; display: inline-block; }
        .status-en_cours { background: #fff3cd; color: #856404; }
        .status-annulee { background: #f8d7da; color: #721c24; }
        .status-recuperer { background: #d4edda; color: #155724; }

        .btn-delete { color: #ff4d4d; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-delete:hover { text-decoration: underline; }
        
        .select-status { padding: 4px; border-radius: 4px; border: 1px solid #ccc; font-size: 12px; }
        .btn-status { background: #3E2723; color: #fff; border: none; padding: 5px 8px; border-radius: 4px; cursor: pointer; font-size: 11px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Books Store</h2>
        <p style="font-size: 14px;">Bienvenue, <strong style="color: #f4f1f0;"><?php echo htmlspecialchars($nom_admin); ?></strong></p>
        <hr style="border: 0.5px solid #4E342E;">
        <a href="admin_dashboard.php" > Tableau de Bord</a>
        <a href="gestion_livres.php" > Gestion des Livres</a>
        <a href="gestion_commandes.php" class="active"> Commandes Clients</a>
        <a href="index.php"> Voir le site</a>
        <a href="logout.php" style="color: #ff4d4d; margin-top: 50px;"> Déconnexion</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <h1>Commandes Clients</h1>
            
            <!-- Formulaire de recherche -->
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search_code" placeholder="Entrer le code de retrait..." value="<?php echo htmlspecialchars($search_code); ?>" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 220px; font-size: 14px;">
                <button type="submit" style="background: #3E2723; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Chercher</button>
                <?php if (!empty($search_code)): ?>
                    <a href="gestion_commandes.php" style="padding: 8px 12px; background: #e0e0e0; color: #333; text-decoration: none; border-radius: 4px; font-size: 14px;">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code Retrait</th>
                        <th>Client & Contact</th>
                        <th>Livres Commandés</th> 
                        <th>Total</th>
                        <th>Date Commande</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($commandes) > 0): ?>
                        <?php foreach ($commandes as $c): ?>
                            <?php 
                                $st = ($c['statut'] === 'récupérer') ? 'recuperer' : $c['statut'];
                                $statusClass = 'status-' . $st;
                            ?>
                            <tr>
                                <td>#<?php echo $c['id_commande']; ?></td>
                                <td><span class="code-badge"><?php echo htmlspecialchars($c['code_retrait'] ?? 'N/A'); ?></span></td>
                                <td>
                                    <div class="client-info">
                                  <strong><?php echo htmlspecialchars(($c['client_nom'] ?? '') . ' ' . ($c['client_prenom'] ?? '')); ?></strong><br>
                                        📧 <?php echo htmlspecialchars($c['client_email'] ?? 'Non renseigné'); ?><br>
                                        📞 <?php echo htmlspecialchars($c['client_telephone'] ?? 'Non renseigné'); ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="livres-list">
                                        <?php echo $c['livres_details']; ?>
                                    </div>
                                </td>
                                
                                <td class="price"><?php echo number_format($c['total'] ?? 0, 2); ?> DH</td>
                                
                                <td style="font-size: 12px; color: #666;">
                                    <?php echo date('d/m/Y H:i', strtotime($c['date_commande'])); ?>
                                </td>
                                
                                <td>
                                    <span class="badge-status <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($c['statut']); ?>
                                    </span>
                                    
                                    <form method="POST" style="margin-top: 8px;">
                                        <input type="hidden" name="id_commande" value="<?php echo $c['id_commande']; ?>">
                                        <select name="statut" class="select-status">
                                            <option value="en_cours" <?php echo $c['statut'] == 'en_cours' ? 'selected' : ''; ?>>en_cours</option>
                                            <option value="récupérer" <?php echo $c['statut'] == 'récupérer' ? 'selected' : ''; ?>>récupérer</option>
                                            <option value="annulee" <?php echo $c['statut'] == 'annulee' ? 'selected' : ''; ?>>annulee</option>
                                        </select>
                                        <button type="submit" name="update_statut" class="btn-status">OK</button>
                                    </form>
                                </td>

                                <td>
                                    <a href="gestion_commandes.php?delete_id=<?php echo $c['id_commande']; ?>" class="btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer cette commande ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px; color: #999;">Aucune commande trouvée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>