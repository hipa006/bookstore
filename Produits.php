<?php
session_start();
include 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM Produit ORDER BY categorie ASC");
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
<title>Produits</title>
<link rel="stylesheet" href="./Produits.css">
<style>
  body {margin: 0; background: #FAF6F1; overflow-x: hidden;}
  a {color: #f8f2f0; text-decoration: none;}
  a:hover {color: #795548;}
  .Produit-page {
    width: 1280px;
    min-height: 100vh;
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
  .logo-img {height: 60px;}
  .brand-title {font-size: 26px;}

  .menu {
    display: flex;
    gap: 30px;
    list-style: none;
  }
  .nav-links {
    display: flex;
    gap: 32px;
    font-size: 16px;
    font-family: system-ui, sans-serif;
    align-items: center;
    justify-content: space-between;
  }

  .nav-link {color: #FAF6F1;}
  .nav-link.active {color: #C69C6D;}

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

  .search-input { flex: 1; }
  .filter-select { min-width: 180px; }

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
    height: 220px;
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
    font-weight: bold;
  }

  .detail-button {
    border: 0;
    background: #5D4037;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    text-align: center;
  }

  .detail-button a {
    color: #fff;
    text-decoration: none;
    display: block;
  }

  .btn-out-of-stock {
    background: #A1887F;
    color: #FFF;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: bold;
    cursor: not-allowed;
    display: inline-block;
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
  }
</style>
</head>
<body>
<div class="Produit-page">
  <header>
    <nav class="navbar">
      <a href="#" class="logo">
        <img src="logo.png" alt="Logo" class="logo-img">
        <div class="brand-title">Books Store</div>
      </a>
      <ul class="menu">
        <li><a href="index.php" class="nav-link">Accueil</a></li>
        <li><a href="Produits.php" class="nav-link active">Livres</a></li>
        <li><a href="About.php" class="nav-link">À propos</a></li>
        <li><a href="Panier.php" class="nav-link">Panier</a></li>
        <li><a href="Contact.php" class="nav-link">Contact</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><span class="nav-link" style="color:#C69C6D;">Bonjour, <?= htmlspecialchars($_SESSION['user_prenom']) ?></span></li>
            <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="login.php" class="nav-link">Connexion</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main class="catalogue-main">
    <h1 class="page-title">Livres</h1>

    <div class="filters">
      <input class="search-input" aria-label="Recherche" placeholder="Rechercher un titre ou un auteur">
      <select class="filter-select" aria-label="Catégorie">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $categorie): ?>
          <option value="<?php echo htmlspecialchars($categorie); ?>">
            <?php echo htmlspecialchars($categorie); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="button" class="filter-button">Filtrer</button>
    </div>

    <div class="catalogue-grid">
        <?php if (count($produit) == 0): ?>
            <p>Aucun livre disponible.</p>
        <?php else: ?>
            <?php foreach ($produit as $p): ?>
                <article class="book-card" data-category="<?php echo htmlspecialchars($p['categorie']); ?>">
                    <img src="Uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['titre']); ?>" class="book-cover">
                    <div class="book-title"><?php echo htmlspecialchars($p['titre']); ?></div>
                    <div class="book-author"><?php echo htmlspecialchars($p['auteur']); ?></div>
                    <div class="book-meta">
                        <span class="book-price"><?php echo htmlspecialchars($p['Prix']); ?> DH</span>
                        
                        <?php if (isset($p['quantite']) && $p['quantite'] <= 0): ?>
                            <span class="btn-out-of-stock">Épuisé</span>
                        <?php else: ?>
                            <button type="button" class="detail-button">
                                <a href="Details.php?id=<?php echo $p['id_produit']; ?>">Voir détails</a>
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
  </main>

   <footer class="footer">
      <span>Quartier des Facultés à Casablanca.</span>
      <span>Lundi-Samedi: 9h00-20h00 <br>Dimanche: 10h00-18h00 · </span>
      <span>+212 6 12 34 56 78</span>
            <span>+212 6 12 34 56 78</span>
        <span>Ce site web est réalisé par : </span>
        <span style="color: #ddd;">
        <a href="https://eddahby-khadija-portfolio.netlify.app/#about">Eddahby Khadija</a> et <br>
        <a href="https://hibatallah-hamidi-portfolio.netlify.app/">HibatAllah Hamidi</a></span> </footer>
</div>

<script>
  const searchInput = document.querySelector('.search-input');
  const categorySelect = document.querySelector('.filter-select');
  const filterButton = document.querySelector('.filter-button');
  const books = document.querySelectorAll('.book-card');

  function filterBooks() {
    const query = searchInput.value.trim().toLowerCase();
    const category = categorySelect.value.trim().toLowerCase();

    books.forEach(book => {
      const title = book.querySelector('.book-title').textContent.toLowerCase();
      const author = book.querySelector('.book-author').textContent.toLowerCase();
      const bookCategory = book.dataset.category.trim().toLowerCase();
      const matchesText = title.includes(query) || author.includes(query);
      const matchesCategory = !category || bookCategory === category;

      book.hidden = !(matchesText && matchesCategory);
    });
  }

  searchInput.addEventListener('input', filterBooks);
  filterButton.addEventListener('click', filterBooks);
</script>
</body>
</html>