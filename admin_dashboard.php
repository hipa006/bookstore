<?php
session_start();
require_once('config.php'); 
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$nom_admin = $_SESSION['user_nom'] ?? 'Admin';

try {
    $count_cmd = $pdo->query("SELECT COUNT(*) FROM commande")->fetchColumn();
    $total_revenue = $pdo->query("SELECT IFNULL(SUM(total), 0) FROM commande")->fetchColumn();
    $count_produit = $pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();

    // Récentes commandes avec les données de la table client (id_client)
    $stmt = $pdo->query("SELECT c.*, u.nom as client_nom, u.prenom as client_prenom 
                         FROM commande c 
                         LEFT JOIN client u ON c.id_client = u.id_client 
                         ORDER BY c.date_commande DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin </title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; display: flex; }
        .sidebar { width: 250px; background-color: #3E2723; color: white; height: 100vh; padding: 20px; position: fixed; box-sizing: border-box; }
        .sidebar h2 { color: #C69C6D; font-size: 20px; margin-bottom: 20px; }
        .sidebar a { display: block; color: #ccc; text-decoration: none; padding: 12px 0; border-bottom: 1px solid #4E342E; transition: 0.3s; }
        .sidebar a:hover { color: #C69C6D; padding-left: 10px; }
        .active { color: #C69C6D !important; font-weight: bold; }
        
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); box-sizing: border-box; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #C69C6D; }
        .stat-card h4 { margin: 0; color: #777; font-size: 14px; text-transform: uppercase; }
        .stat-card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #3E2723; }

        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        th { color: #3E2723; text-transform: uppercase; font-size: 13px; border-bottom: 2px solid #C69C6D; }
        
        .btn-logout { background-color: #854c57; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px; }
        .status { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Books Store</h2>
        <p style="font-size: 14px;">Bienvenue, <strong style="color: #f4f1f0;"><?php echo htmlspecialchars($nom_admin); ?></strong></p>
        <hr style="border: 0.5px solid #4E342E;">
        <a href="admin_dashboard.php" class="active"> Tableau de Bord</a>
        <a href="gestion_livres.php"> Gestion des Livres</a>
        <a href="gestion_commandes.php"> Commandes Clients</a>
        <a href="index.php"> Voir le site</a>
        <a href="logout.php" style="color: #ff4d4d; margin-top: 50px;"> Déconnexion</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <h1>Tableau de Bord</h1>
            <a href="logout.php" class="btn-logout">Se déconnecter</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Commandes</h4>
                <p><?php echo $count_cmd; ?></p>
            </div>
            <div class="stat-card">
                <h4>Chiffre d'Affaires</h4>
                <p><?php echo number_format($total_revenue, 2); ?> DH</p>
            </div>
            <div class="stat-card">
                <h4>Livres en Stock</h4>
                <p><?php echo $count_produit; ?></p>
            </div>
        </div>
        <!-- Dernières Commandes -->
        <div class="card">
            <h3>Dernières Commandes</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id_commande']; ?></td>
                                <td><?php echo htmlspecialchars(($order['client_nom'] ?? '') . ' ' . ($order['client_prenom'] ?? '')); ?></td>
                                <td><?php echo number_format($order['total'], 2); ?> DH</td>
                                <td><?php echo $order['date_commande']; ?></td>
                                <td><span class="status"><?php echo htmlspecialchars($order['statut']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #777;">Aucune commande trouvée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>