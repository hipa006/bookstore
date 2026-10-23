<?php
session_start();
require 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Nettoyage des entrées
    $nom        = trim($_POST['nom'] ?? '');
    $prenom     = trim($_POST['prenom'] ?? '');
    $email      = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $telephone  = trim($_POST['telephone'] ?? '');
    $pass_raw   = $_POST['mot_de_passe'] ?? '';

    $role = 'user'; 

    // 2. Vérification des champs requis
    if (!$nom || !$prenom || !$email || !$pass_raw || !$telephone) {
        die("Erreur : Veuillez remplir tous les champs correctement.");
    }

    try {
        // 3. Vérifier si l'email existe déjà
        $check_email = $pdo->prepare("SELECT id_client FROM client WHERE email = ?");
        $check_email->execute([$email]);

        if ($check_email->fetch()) {
            die("Erreur : Cet e-mail est déjà utilisé.");
        }

        // 4. Hachage du mot de passe
        $mot_de_passe = password_hash($pass_raw, PASSWORD_BCRYPT);

        // 5. Insertion dans la base de données
        $sql  = "INSERT INTO client (nom, prenom, email, mot_de_passe, telephone, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nom, $prenom, $email, $mot_de_passe, $telephone, $role])) {
            
            // Stockage dans la session
            $_SESSION['user_id']     = $pdo->lastInsertId();
            $_SESSION['user_nom']    = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_role']   = $role;

            // Redirection vers la page d'accueil
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
    }
} else {
    // Si la page est accessible sans méthode POST
    header("Location: connexion.php");
    exit();
}
?>