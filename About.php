<?php
session_start();
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>À propos</title>
<style>
  body {margin: 0; background: #FAF6F1; overflow-x: hidden;}
a {color: #f8f2f0; text-decoration: none;}
a:hover {color: #795548;}
.Produit-page {
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

.btn-btn {
  color: #3E2723;
  background: #C69C6D;
  padding: 10px 18px;
  border-radius: 6px;
}
    .about-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
    }
    .about-hero {
        text-align: center;
        margin-bottom: 40px;
    }
    .about-hero h1 {
        font-size: 2.5rem;
        color: #333;
        margin-bottom: 10px;
    }
    .about-hero p {
        color: #666;
        font-size: 1.1rem;
    }
    .about-content {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        align-items: center;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    .about-text {
        flex: 1;
        min-width: 280px;
    }
    .about-text h2 {
        color: #2c3e50;
        margin-bottom: 15px;
    }
    .about-text p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    .about-image {
        flex: 1;
        min-width: 280px;
        text-align: center;
    }
    .about-image img {
        max-width: 100%;
        border-radius: 8px;
        height: auto;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    .feature-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-left: 4px solid #2c3e50;
    }
    .feature-card h3 {
        margin-top: 0;
        color: #2c3e50;
    }
    .feature-card p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 0;
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
    <li><a href="Produits.php" class="nav-link ">Livres</a></li>
    <li><a href="About.php" class="nav-link active">À propos</a></li>
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

 <main class="about-container">
    <div class="about-hero">
        <h1>À propos de Books Store</h1>
        <p>Votre librairie moderne au cœur de Casablanca.</p>
    </div>
    <div class="about-content">
        <div class="about-text">
            <h2>Qui sommes-nous ?</h2>
            <p><strong>Books Store</strong> est une librairie moderne spécialisée dans la vente de livres et d'ouvrages académiques ou littéraires, offrant un large choix de références à sa clientèle.</p>
            <p>Idéalement localisée dans le <strong>Quartier des Facultés à Casablanca</strong>, notre structure est directement au cœur de la zone étudiante, ce qui nous permet d'être au plus près de vos besoins au quotidien.</p>
        </div>
        <div class="about-image">
            <img src="Uploads/about-book.jpg" alt="Librairie Books Store">
        </div>
    </div>

  
    <div class="about-content" style="background: transparent; box-shadow: none; padding: 0;">
        <div class="about-text" style="flex: 2; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h2>Notre Solution Numérique & Click & Collect</h2>
            <p>Aujourd'hui, face au rythme de vie intense des étudiants et au manque de temps pour parcourir les rayons physiques des librairies, le besoin d'une solution numérique rapide et efficace est devenu indispensable. Pour répondre à cette problématique, notre librairie s'inscrit dans une dynamique de digitalisation.</p>
            <p>Notre plateforme permet aux étudiants de consulter le catalogue, commander en ligne en quelques clics, et récupérer leurs ouvrages directement au magasin en <strong>Click & Collect</strong> grâce à un code de retrait unique, facilitant ainsi l'accès aux livres en plein milieu de leur parcours ou sur leur chemin.</p>
        </div>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <h3>📚 Ouvrages Variés</h3>
            <p>Un grand choix de livres académiques, littéraires et de références pour réussir vos études et nourrir votre passion.</p>
        </div>
        <div class="feature-card">
            <h3>⚡ Gain de Temps</h3>
            <p>Commandez en ligne rapidement sans perdre de temps à chercher dans les rayons physiques pendant vos révisions.</p>
        </div>
        <div class="feature-card">
            <h3>🔑 Click & Collect Sécurisé</h3>
            <p>Récupérez votre commande facilement au magasin grâce à un code de retrait unique et personnalisé.</p>
        </div>
    </div>
 </main>
   <footer class="footer">
      <span>Quartier des Facultés à Casablanca.</span>
      <span>Lundi-Samedi: 9h00-20h00 <br>Dimanche: 10h00-18h00 · </span>
      <span>+212 6 12 34 56 78</span>
      <span>Ce site web est réalisé par : </span>
        <span style="color: #ddd;">
        <a href="https://eddahby-khadija-portfolio.netlify.app/#about">Eddahby Khadija</a> et <br>
        <a href="https://hibatallah-hamidi-portfolio.netlify.app/">HibatAllah Hamidi</a>
</span>  </footer>
</div>
</body>
</html>
