<?php
$statusMessage = "";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!empty($name) && !empty($email) && !empty($message)) {
        $to = "storebooks@gmail.com";
        $subject = "Nouveau message de contact - Books Store";
        
        $body = "Nom: " . $name . "\n";
        $body .= "Email: " . $email . "\n\n";
        $body .= "Message:\n" . $message;
        
        $headers = "From: " . $email;

        if (mail($to, $subject, $body, $headers)) {
            $statusMessage = "<p style='color: green; font-weight: bold; margin-bottom: 15px;'>Votre message a été envoyé avec succès !</p>";
        } else {
            $statusMessage = "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>Erreur lors de l'envoi. Réessayez plus tard.</p>";
        }
    } else {
        $statusMessage = "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>Veuillez remplir tous les champs.</p>";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Contact </title>
<link rel="stylesheet" href="./Produits.css">
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
    .contact-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
    }
    .contact-hero {
        text-align: center;
        margin-bottom: 40px;
    }
    .contact-hero h1 {
        font-size: 2.5rem;
        color: #333;
        margin-bottom: 10px;
    }
    .contact-hero p {
        color: #666;
        font-size: 1.1rem;
    }
    /* Grid bash ykonou les deux cards wahed hda lakhor */
    .contact-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        align-items: flex-start;
    }
    .contact-info-card, .contact-form-card {
        flex: 1;
        min-width: 280px;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .contact-info-card h2, .contact-form-card h2 {
        color: #2c3e50;
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }
    .info-item {
        margin-bottom: 20px;
    }
    .info-item strong {
        display: block;
        color: #2c3e50;
        margin-bottom: 5px;
        font-size: 1.05rem;
    }
    .info-item p {
        color: #666;
        margin: 0;
        line-height: 1.5;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        box-sizing: border-box;
    }
    textarea.form-control {
        resize: vertical;
        height: 120px;
    }
    .submit-btn {
        background-color: #2c3e50;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        width: 100%;
        transition: background 0.3s;
    }
    .submit-btn:hover {
        background-color: #1a252f;
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
    <li><a href="About.php" class="nav-link ">À propos</a></li>
    <li><a href="Panier.php" class="nav-link">Panier</a></li>
    <li><a href="Contact.php" class="nav-link active">Contact</a></li>

    <?php if (isset($_SESSION['user_id'])): ?>
        <li><span class="nav-link" style="color:#C69C6D;">Bonjour, <?= htmlspecialchars($_SESSION['user_prenom']) ?></span></li>
        <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
    <?php else: ?>
        <li><a href="login.php" class="nav-link">Connexion</a></li>
    <?php endif; ?>
</ul>
    </nav>
 </header>

 <main class="contact-container">
    <div class="contact-hero">
        <h1>Contactez-nous</h1>
        <p>Une question ? Un renseignement ? N'hésitez pas à nous écrire.</p>
    </div>

    <div class="contact-grid">
        <!-- Les Coordonnées -->
        <div class="contact-info-card">
            <h2>Nos Coordonnées</h2>
            
            <div class="info-item">
                <strong>📍 Adresse</strong>
                <p>Quartier des Facultés à Casablanca.</p>
            </div>

            <div class="info-item">
                <strong>🕒 Horaires</strong>
                <p>Lundi-Samedi : 9h00 - 20h00<br>Dimanche : 10h00 - 18h00</p>
            </div>

            <div class="info-item">
                <strong>📞 Téléphone</strong>
                <p>+212 6 12 34 56 78</p>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="contact-form-card">
            <h2>Envoyez-nous un message</h2>
            
            <?php echo $statusMessage; ?>

            <form action="Contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Votre nom" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="votre.email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" placeholder="Votre message ici..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">Envoyer le message</button>
            </form>
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
        <a href="https://hibatallah-hamidi-portfolio.netlify.app/">HibatAllah Hamidi</a></span>
    </footer>
</div>
</body>
</html>