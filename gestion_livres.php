<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$nom_admin = $_SESSION['user_nom'] ?? 'Admin';

if (isset($_GET['delete_id'])) {
    $id_to_delete = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM produit WHERE id_produit = ?");
    $stmt->execute([$id_to_delete]);
    header("Location: gestion_livres.php?msg=Livre supprimé");
    exit();
}

// Recherche et récupér des livres
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE titre LIKE ? OR auteur LIKE ? OR categorie LIKE ?  ORDER BY categorie ASC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT * FROM produit ORDER BY categorie ASC");
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; display: flex; }
        
        .sidebar { width: 250px; background-color: #3E2723; color: white; height: 100vh; padding: 20px; position: fixed; box-sizing: border-box; }
        .sidebar h2 { color: #C69C6D; font-size: 20px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar p { font-size: 14px; color: #d7ccc8; }
        .sidebar a { display: block; color: #ccc; text-decoration: none; padding: 12px 0; border-bottom: 1px solid #4E342E; transition: 0.3s; }
        .sidebar a:hover { color: #C69C6D; padding-left: 10px; }

        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); box-sizing: border-box; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
        h1 { color: #3E2723; font-size: 24px; margin: 0; }
        
        .search-form { display: flex; gap: 8px; align-items: center; }
        .search-input { padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; width: 250px; font-size: 14px; }
        .search-input:focus { border-color: #C69C6D; outline: none; }
        .btn-search { background: #3E2723; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-search:hover { background: #C69C6D; }
        .btn-reset { background: #ccc; color: #333; text-decoration: none; padding: 10px 12px; border-radius: 5px; font-weight: bold; }

        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background-color: #faf6f1; color: #3E2723; text-transform: uppercase; font-size: 12px; border-bottom: 2px solid #C69C6D; }
        
        .img-preview { width: 50px; height: 65px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        
        .btn-add { background: #C69C6D; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background: #3E2723; }
        .btn-delete { color: #ff4d4d; text-decoration: none; font-weight: bold; margin-left: 10px; }
        .btn-delete:hover { text-decoration: underline; }
        .btn-edit { color: #3E2723; text-decoration: none; font-weight: bold; }
        .btn-edit:hover { text-decoration: underline; }

        .badge { background: #faf6f1; color: #3E2723; padding: 4px 8px; border-radius: 4px; font-size: 12px; border: 1px solid #e0d7ce; }
        .price { font-weight: bold; color: #3E2723; }
    </style>
</head>
<body>

    <div class="sidebar">
       <h2>Books Store</h2>
        <p style="font-size: 14px;">Bienvenue, <strong style="color: #f4f1f0;"><?php echo htmlspecialchars($nom_admin); ?></strong></p>
        <hr style="border: 0.5px solid #4E342E;">
        <a href="admin_dashboard.php" > Tableau de Bord</a>
        <a href="gestion_livres.php"> Gestion des Livres</a>
        <a href="gestion_commandes.php"> Commandes Clients</a>
        <a href="index.php"> Voir le site</a>
        <a href="logout.php" style="color: #ff4d4d; margin-top: 50px;"> Déconnexion</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <h1>Gestion du Catalogue</h1>
            
            <form action="gestion_livres.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Titre, categorie ou auteur..." 
                       value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="btn-search">Chercher</button>
                
                <?php if (!empty($search)): ?>
                    <a href="gestion_livres.php" class="btn-reset" title="Annuler la recherche">X</a>
                <?php endif; ?>
            </form>

            <a href="ajouter_livres.php" class="btn-add">+ Ajouter un nouveau livre</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($livres)): ?>
                        <?php foreach ($livres as $l): ?>
                        <tr>
                            <td>
                               <img src="Uploads/<?php echo htmlspecialchars($l['image']); ?>" class="img-preview" alt="couverture">  </td>
                            <td>
                                <strong><?php echo htmlspecialchars($l['titre']); ?></strong><br>
                                <small style="color: #888;"><?php echo htmlspecialchars(substr($l['description'] ?? '', 0, 40)); ?>...</small>
                            </td>
                            <td><?php echo htmlspecialchars($l['auteur'] ?? '-'); ?></td>
                            <td><span class="badge"><?php echo htmlspecialchars($l['categorie']); ?></span></td>
                            <td class="price"><?php echo number_format($l['Prix'], 2); ?> DH</td>
                            <td><?php echo (int)$l['stock']; ?></td>
                            <td>
                                <a href="modifier_livre.php?id=<?php echo $l['id_produit']; ?>" class="btn-edit">Modifier</a>
                                <a href="gestion_livres.php?delete_id=<?php echo $l['id_produit']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #777; padding: 30px;">
                                Aucun livre trouvé pour votre recherche.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>