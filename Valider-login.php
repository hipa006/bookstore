<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $pass_raw = $_POST['mot_de_passe'] ?? '';

    if (!$email || !$pass_raw) {
        header("Location: login.php?error=1");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM client WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérification si le mot de passe est haché ou clair (pour l'admin D123 ou hiba123)
            $is_password_correct = password_verify($pass_raw, $user['mot_de_passe']) || ($pass_raw === $user['mot_de_passe']);

            if ($is_password_correct) {
                // Stockage des informations dans la session
                $_SESSION['user_id']     = $user['id_client'];
                $_SESSION['user_nom']    = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_role']   = $user['role'];

                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php"); 
                } else {
                    header("Location: index.php"); 
                }
                exit();
            }
        }

        header("Location: login.php?error=1");
        exit();

    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit();
}
?>