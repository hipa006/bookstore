<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
    body {
        margin: 0;
        background: #FAF6F1;
        overflow-x: hidden;
        font-family: Georgia, 'Times New Roman', serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    a {
        color: #f8f2f0;
        text-decoration: none;
    }

    a:hover {
        color: #C69C6D;
    }

    /* Navbar */
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
        color: #FAF6F1;
    }

    .menu {
        display: flex;
        gap: 30px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-link {
        color: #FAF6F1;
        font-family: system-ui, sans-serif;
        font-size: 16px;
    }

    .nav-link.active {
        color: #C69C6D;
        font-weight: bold;
    }

    /* Form Section */
    .form-container {
        max-width: 450px;
        margin: 50px auto;
        background: #FFFFFF;
        padding: 35px 40px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(62, 39, 35, 0.1);
        border: 1px solid #E0D7CE;
    }

    .form-container h2 {
        color: #3E2723;
        margin-top: 0;
        margin-bottom: 25px;
        text-align: center;
        font-size: 28px;
    }

    .form-group {
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group label {
        font-size: 14px;
        color: #5D4037;
        font-family: system-ui, sans-serif;
    }

    .form-container input,
    .form-container select {
        width: 100%;
        padding: 12px;
        border: 1px solid #D7CCC8;
        border-radius: 5px;
        font-size: 15px;
        box-sizing: border-box;
        background-color: #FAF6F1;
        color: #3E2723;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-container input:focus,
    .form-container select:focus {
        border-color: #795548;
        background-color: #FFF;
    }

    .submit-btn {
        width: 100%;
        background-color: #795548;
        color: #FFF;
        border: none;
        padding: 14px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s ease;
    }

    .submit-btn:hover {
        background-color: #3E2723;
    }

    .link-log {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #795548;
        font-family: system-ui, sans-serif;
        font-size: 14px;
        text-decoration: underline;
    }

    .link-log:hover {
        color: #3E2723;
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

<header>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="logo.png" alt="Logo" class="logo-img">
            <div class="brand-title">Books Store</div>
        </a>
        <ul class="menu">
            <li><a href="index.php" class="nav-link">Accueil</a></li>
            <li><a href="Produits.php" class="nav-link">Livres</a></li>
            <li><a href="About.php" class="nav-link">À propos</a></li>
            <li><a href="Panier.php" class="nav-link">Panier</a></li>
            <li><a href="Contact.php" class="nav-link">Contact</a></li>
            <li><a href="connexion.php" class="nav-link active">Connexion</a></li>
        </ul>
    </nav>
</header> 

<main>
    <div class="form-container">
        <h2>Créer un compte</h2>
        <form action="enregister_connexion.php" method="POST">
            <div class="form-group">
                <input type="text" name="nom" placeholder="Votre Nom" required>
            </div>
            
            <div class="form-group">
                <input type="text" name="prenom" placeholder="Votre Prénom" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Votre Email" required>
            </div>

            <div class="form-group">
                <input type="password" name="mot_de_passe" placeholder="Votre Mot de passe" required>
            </div>

            <div class="form-group">
                <input type="tel" name="telephone" placeholder="Téléphone (ex: 0612345678)" required>
            </div>

            <button type="submit" class="submit-btn">S'inscrire</button>
            
            <a href="login.php" class="link-log">J'ai déjà un compte</a>
        </form>
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

</body>
</html>