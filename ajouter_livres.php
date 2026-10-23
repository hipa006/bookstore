<?php
session_start();
require_once('config.php'); 
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {
    $sql_cats = "SELECT DISTINCT categorie FROM produit WHERE categorie IS NOT NULL AND categorie != ''";
    $stmt_cats = $pdo->query($sql_cats);
    $categories_db = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur catégories : " . $e->getMessage());
}

if (isset($_POST['ajouter'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie']; 
    $auteur = $_POST['auteur'];
    $stock = $_POST['stock']; 

    // Gestion de l'image
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    
   
    $target_dir = dirname(__DIR__) . "/image/"; 
    $target_file = $target_dir . basename($image_name);

    // Vérification du dossier image
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }


    if (move_uploaded_file($image_tmp, $target_file)) {
        try {
            $sql = "INSERT INTO produit (titre, description, prix, categorie, auteur, stock, image) 
                    VALUES (:titre, :description, :prix, :categorie, :auteur, :stock, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre'       => $titre,
                ':description' => $description,
                ':prix'        => $prix,
                ':categorie'   => $categorie,
                ':auteur'      => $auteur,
                ':stock'       => $stock,
                ':image'       => $image_name
            ]);
            
            $success = "Le livre a été ajouté avec succès !";
            
            $stmt_cats = $pdo->query($sql_cats);
            $categories_db = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = "Erreur SQL : " . $e->getMessage();
        }
    } else {
        $error = "Erreur lors de l'upload de l'image.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Livre </title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; display: flex; }
        .sidebar { width: 250px; background-color: #1a1a1a; color: white; height: 100vh; padding: 20px; position: fixed; box-sizing: border-box; }
        .sidebar h2 { color: #b5a26a; font-size: 20px; text-transform: uppercase; }
        .sidebar a { display: block; color: #ccc; text-decoration: none; padding: 15px 0; border-bottom: 1px solid #333; }
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); box-sizing: border-box; }
        
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-submit { background: #b5a26a; color: white; padding: 15px; border: none; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold; text-transform: uppercase; }
        .btn-submit:hover { background: #272725; }
        .msg { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .msg-success { background: #d4edda; color: #155724; }


        .msg-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <hr style="border: 0.5px solid #333;">
        <a href="admin_dashboard.php"> Dashboard</a>
        <a href="gestion_livres.php"> Gestion des livres</a>
        <a href="logout.php" style="color: #ff4d4d;">Déconnexion</a>
    </div>

    <div class="main-content">
        <h1>Ajouter un nouveau livre</h1>

        <div class="form-card">
            <?php if(isset($success)) echo "<div class='msg msg-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='msg msg-error'>$error</div>"; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre :</label>
                    <input type="text" name="titre" required>
                </div>

                <div class="form-group">
                    <label>Catégorie :</label>
                    <select name="categorie" required>
                        <option value="">-- Choisir une catégorie --</option>
                        <?php foreach ($categories_db as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['categorie']); ?>">
                                <?php echo htmlspecialchars($cat['categorie']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Prix (DH) :</label>
                    <input type="number" name="prix" step="0.01" required placeholder="Ex: 85">
                </div>

                <div class="form-group">
                    <label>Description :</label>
                    <textarea name="description" rows="4" placeholder="Détails du livre..."></textarea>
                </div>

                <div class="form-group">
                    <label>Auteur :</label>
                    <input type="text" name="auteur" required>
                </div>

                <div class="form-group">
                    <label>Stock :</label>
                    <input type="number" name="stock" required>
                </div>

                <div class="form-group">
                    <label>Image du livre :</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>

                <button type="submit" name="ajouter" class="btn-submit">Enregistrer le livre</button>
            </form>
        </div>
    </div>

</body>
</html>