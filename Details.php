<?php
session_start();
include 'config.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails</title>
   <style>
    :root {
	--ink: #332820;
	--brown: #5d4037;
	--muted: #75685f;
	--paper: #f5f2ec;
	--line: #d8d0c7;
}

* {
	box-sizing: border-box;
}

body {
	margin: 0;
	background: var(--paper);
	color: var(--ink);
	font-family: Georgia, 'Times New Roman', serif;
}

.container {
	width: min(1000px, calc(100% - 40px));
	margin: 40px auto;
}

.btn-back {
	display: inline-block;
	margin-bottom: 20px;
	color: var(--brown);
	font-size: 15px;
	text-decoration: none;
}

.btn-back:hover {
	text-decoration: underline;
}

.detail-wrapper {
	display: grid;
	grid-template-columns: minmax(240px, 340px) minmax(0, 1fr);
	align-items: start;
	gap: 40px;
	padding: 30px;
	border: 1px solid var(--line);
	background: #fff;
}

.detail-image img {
	display: block;
	width: 100%;
	max-height: 480px;
	object-fit: contain;
}

.add-to-cart-btn {
    background-color: #795548;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.add-to-cart-btn:hover {
    background-color: #5d4037; 
    transform: translateY(-2px); 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.add-to-cart-btn:active {
    transform: translateY(0); 
}
   </style>
</head>
<body>

<div class="container">
    <a href="Produits.php" class="btn-back">&larr; Retour à la page des produits</a>

    <div class="detail-wrapper">
        <div class="detail-image">
            <img src="uploads/<?php echo htmlspecialchars($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['titre']); ?>">
        </div>

        <div class="detail-info">
            <h1><?php echo htmlspecialchars($produit['titre']); ?></h1>
            <div class="price-tag">
                  <label>Prix : </label>
                <?php echo htmlspecialchars($produit['Prix']); ?> DH
            </div>

            <div class="specs-grid">
                <div class="spec-item">
                    <label>Titre : </label>
                    <?php echo htmlspecialchars($produit['titre']); ?>
                </div>
                <div class="spec-item">
                    <label>Auteur : </label>
                    <?php echo htmlspecialchars($produit['auteur']); ?>
                </div>
                <div class="spec-item">
                    <label>Catégorie : </label>
                    <?php echo htmlspecialchars($produit   ['categorie']); ?>
                </div>
            </div>
            <div class="description-box">
                <h3>Description : </h3>
                <p><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
            </div>
            <div class="add-to-cart">
    <form action="Panier.php" method="post" onsubmit="alert('livre ajouté au panier avec succès !');">
        <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
        <button type="submit" name="submit" value="submit" class="add-to-cart-btn">Ajouter au panier</button>
    </form>
</div>
            
        </div>
    </div>
</div>
</body>
</html>