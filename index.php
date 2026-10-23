<?php
session_start();
include 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM Produit WHERE id_produit <= 4 ORDER BY id_produit ASC");
    $produit = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categories = $pdo->query(
    "SELECT DISTINCT categorie FROM Produit
     WHERE categorie IS NOT NULL AND categorie <> ''
     ORDER BY categorie ASC"
  )->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Accueil</title>
  <style>
    body {
  margin: 0;
   background: #FAF6F1;
   overflow-x: hidden;
}

a {
  color: #f8f2f0;
  text-decoration: none;
}

a:hover {
  color: #795548;
}

.page-container {
  width: 1280px;
  height: 860px;
  box-sizing: border-box;
  background: #FAF6F1;
  color: #3E2723;
  font-family: Georgia, 'Times New Roman', serif;
  display: flex;
  flex-direction: column;
}

.navbar {
  background: #3E2723;
  color: #FAF6F1;
  padding: 0 64px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.logo-img {
    height: 60px;
}

.brand-title {
  font-size: 26px;
}
.menu {
    display: flex;
    gap: 30px;
    list-style: none;
    align-items: center;
}
.nav-links {
  display: flex;
  gap: 32px;
  font-size: 16px;
  font-family: system-ui, sans-serif;
  align-items: center;
  justify-content: space-between;
}

.nav-link {
  color: #FAF6F1;
  font-family: system-ui, sans-serif;
  font-size: 16px;
}

.nav-link.active {
  color: #C69C6D;
}

.user-name {
  color: #C69C6D;
  font-family: system-ui, sans-serif;
  font-size: 16px;
  font-weight: bold;
}

.btn-btn {
  color: #3E2723;
  background: #C69C6D;
  padding: 10px 18px;
  border-radius: 6px;
}

.hero-section {
  background: #EFEBE9;
  padding: 56px 64px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.hero-content {
  max-width: 620px;
}

.hero-title {
  margin: 0 0 16px;
  font-size: 48px;
  line-height: 1.15;
}

.hero-description {
  margin: 0 0 28px;
  font-size: 18px;
  color: #5D4037;
  font-family: system-ui, sans-serif;
}

.btn-primary {
  display: inline-block;
  background: #5D4037;
  color: #fff;
  padding: 14px 28px;
  border-radius: 6px;
  font-family: system-ui, sans-serif;
  font-size: 16px;
}

.store-photo {
  width: 320px;
  height: 320px;
  background: #A1887F;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FAF6F1;
  font-family: system-ui, sans-serif;
  font-size: 14px;
}
.store-photo img{
  width: 300px;
  height: 300px; 
}

.news-section {
  padding: 32px 64px 0;
}

.section-title {
  font-size: 22px;
  margin: 0 0 20px 0;
  font-weight: normal;
}

.books-grid {
  display: flex;
  gap: 24px;
}

.book-card {
  flex: 1;
  background: #EFEBE9;
  border-radius: 12px;
  padding: 16px;
  font-family: system-ui, sans-serif;
}

.book-cover {
  height: 200px;
  border-radius: 6px;
  margin-bottom: 12px;
}

.cover-1 { background: #795548; }
.cover-2 { background: #5D4037; }
.cover-3 { background: #A1887F; }
.cover-4 { background: #C69C6D; }

.book-title {
  font-size: 16px;
}

.book-author {
  font-size: 14px;
  color: #795548;
}

.book-price {
  margin-top: 8px;
  color: #8a5a1f;
  font-size: 16px;
 }

.catalogue-main {
  padding: 32px 64px 0;
  font-family: system-ui, sans-serif;
}

.page-title {
  margin: 0 0 20px;
  font-family: Georgia, serif;
  font-size: 36px;
}

.filters {
  display: flex;
  gap: 16px;
  margin-bottom: 28px;
}

.search-input,
.filter-select {
  border: 1px solid #A1887F;
  border-radius: 6px;
  background: #fff;
  font-size: 15px;
  padding: 12px 16px;
  box-sizing: border-box;
}

.search-input {
  flex: 1;
}

.filter-select {
  min-width: 180px;
}

.filter-select--price {
  min-width: 160px;
}

.filter-button {
  padding: 12px 28px;
  border: 0;
  border-radius: 6px;
  background: #5D4037;
  color: #fff;
  font-size: 15px;
  cursor: pointer;
}

.catalogue-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.book-card {
  background: #EFEBE9;
  border-radius: 12px;
  padding: 16px;
}

.book-cover {
  border-radius: 6px;
  margin-bottom: 12px;
  background: #795548;
  width: 100%;
  object-fit: cover;
  border-radius: 8px;
}

.book-title {
  margin: 0;
  font-size: 18px;
  font-weight: bold;
}

.book-author {
  margin-top: 4px;
  font-size: 14px;
  color: #795548;
  font-weight: 500;
}

.book-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

.book-price {
  color: #8a5a1f;
}

.detail-button {
  border: 0;
  background: #5D4037;
  color: #fff;
  padding: 8px 14px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}
.footer {
  margin-top: auto;
  background: #3E2723;
  color: #D7CCC8;
  padding: 24px 64px;
  font-family: system-ui, sans-serif;
  font-size: 14px;
  display: flex;
  justify-content: space-between;
  content: none;
}
  </style>
</head>
<body>
  <div class="page-container">
<header>
    <nav class="navbar">
    <a href="index.php" class="logo">
        <img src="logo.png" alt="Logo" class="logo-img">
        <div class="brand-title">Books Store</div>
    </a>
    <ul class="menu">
        <li><a href="index.php" class="nav-link active">Accueil</a></li>
        <li><a href="Produits.php" class="nav-link">Livres</a></li>
        <li><a href="About.php" class="nav-link">À propos</a></li>
        <li><a href="Panier.php" class="nav-link">Panier</a></li>
        <li><a href="Contact.php" class="nav-link">Contact</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><span class="user-name">Bonjour, <?= htmlspecialchars($_SESSION['user_prenom']) ?></span></li>
            <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="login.php" class="nav-link">Connexion</a></li>
        <?php endif; ?>
    </ul>
</header>

    <section class="hero-section">
      <div class="hero-content">
        <h1 class="hero-title">Commandez en ligne, retirez en magasin</h1>
        <p class="hero-description">Choisissez vos livres, recevez votre code de retrait, payez sur place.</p>
        <a href="Produits.php" class="btn-primary">Voir les livres</a>
      </div>
      <div class="store-photo">
        <img src="store.jpg" alt="">
    </div>
    </section>

    <section class="news-section">
      <h2 class="section-title">Nouveautés</h2>
      <div class="catalogue-grid">
        <?php if (count($produit)==0): ?>
            <p>Aucun livre disponible.</p>
        <?php else: ?>
            <?php foreach ($produit as $p): ?>
                <article class="book-card" data-category="<?php echo htmlspecialchars($p['categorie']); ?>">
                    <img src="Uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['titre']); ?>" class="book-cover">
                    <div class="book-title"><?php echo htmlspecialchars($p['titre']); ?></div>
                    <div class="book-author"><?php echo htmlspecialchars($p['auteur']); ?></div>
                    <div class="book-meta">
                        <span class="book-price"><?php echo htmlspecialchars($p['Prix']); ?> DH</span>
                        <button type="button" class="detail-button" >
                            <a href="Details.php?id=<?php echo $p['id_produit']; ?>">voir details</a>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    </section>

   <footer class="footer">
      <span>Quartier des Facultés à Casablanca.</span>
      <span>Lundi-Samedi: 9h00-20h00 <br>Dimanche: 10h00-18h00 · </span>
      <span>+212 6 12 34 56 78</span>
       <span>Ce site web est réalisé par : </span>
        <span style="color: #ddd; content: none;">
        <a href="https://eddahby-khadija-portfolio.netlify.app/#about">Eddahby Khadija</a> et <br>
        <a href="https://hibatallah-hamidi-portfolio.netlify.app/">HibatAllah Hamidi</a>
</span>  </footer>

  </div>
</body>
</html>