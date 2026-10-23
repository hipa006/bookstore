<?php
session_start();
require_once('config.php'); 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = $pdo->prepare('SELECT * FROM produit WHERE id_produit = ?');
    $query->execute([$id]);
    $livre = $query->fetch(PDO::FETCH_ASSOC);

    if (!$livre) {
        die("Ce livre n'existe pas !");
    }
} else {
    header('Location: gestion_livres.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = trim($_POST['titre']);
    $auteur      = trim($_POST['auteur']);
    $categorie   = $_POST['categorie'];
    $prix        = (float)$_POST['prix'];
    $stock       = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    
    $nom_image = $livre['image']; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $nom_image = basename($_FILES["image"]["name"]);
        $target_file = $nom_image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $update = $pdo->prepare('UPDATE produit SET titre = ?, auteur = ?, categorie = ?, Prix = ?, stock = ?, description = ?, image = ? WHERE id_produit = ?');
    $update->execute([$titre, $auteur, $categorie, $prix, $stock, $description, $nom_image, $id]);

    header('Location: gestion_livres.php?success=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Livre</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; padding: 40px; margin: 0; }
        .form-container { 
            max-width: 550px; 
            margin: auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-top: 5px solid #795548;
        }
        h2 { color: #3E2723; border-bottom: 2px solid #faf6f1; padding-bottom: 10px; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #3E2723; font-size: 14px; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        input:focus, textarea:focus, select:focus { border-color: #C69C6D; outline: none; }
        .current-img { width: 60px; height: 80px; object-fit: cover; border-radius: 4px; margin-top: 8px; display: block; border: 1px solid #ddd; }
        .btn-save { 
            background-color: #795548;
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            cursor: pointer; 
            font-weight: bold;
            font-size: 15px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .btn-save:hover { background-color: #3E2723; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #795548; text-decoration: none; font-size: 14px; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Modifier le livre</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Titre du livre :</label>
            <input type="text" name="titre" value="<?php echo htmlspecialchars($livre['titre']); ?>" required>
        </div>

        <div class="form-group">
            <label>Auteur :</label>
            <input type="text" name="auteur" value="<?php echo htmlspecialchars($livre['auteur'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Catégorie :</label>
            <select name="categorie" required>
                <?php 
                $categories = ['Drama','Imagination','Enfants','Littérature','Philosophie','Politique','Rélegions','Romantique','psychologique'];
                foreach ($categories as $cat): 
                    $selected = ($livre['categorie'] === $cat) ? 'selected' : '';
                ?>
                    <option value="<?php echo $cat; ?>" <?php echo $selected; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Prix (DH) :</label>
            <input type="number" step="0.01" name="prix" value="<?php echo htmlspecialchars($livre['Prix']); ?>" required>
        </div>

        <div class="form-group">
            <label>Stock :</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($livre['stock']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description :</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($livre['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Couverture du livre (Optionnel) :</label>
            <input type="file" name="image" accept="image/*">
            <small style="color: #888; display:block; margin-top:5px;">Image actuelle :</small>
            <img src="<?php echo htmlspecialchars($livre['image']); ?>" class="current-img">
        </div>

        <button type="submit" class="btn-save">Enregistrer les modifications</button>
    </form>

    <a href="gestion_livres.php" class="back-link">← Retour à la gestion</a>
</div>

</body>
</html>