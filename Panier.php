<?php
session_start();
include 'config.php';

if (!isset($_SESSION['panier'])) {$_SESSION['panier'] = [];
}

// Récupération des informations client
$clientNom =$_SESSION['nom_client'] ?? 'Client';
$clientTelephone =$_SESSION['telephone'] ?? 'Non renseigné';

if (isset($_SESSION['user_id'])) {
    try {
        $stmt =$pdo->prepare("SELECT * FROM client WHERE id_client = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $client =$stmt->fetch(PDO::FETCH_ASSOC);

        if ($client) {$clientNom = $client['nom'] ?? $clientNom;
            $clientTelephone = $client['telephone'] ?? $clientTelephone;
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// Ajouter / Modifier panier + Vérification Stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit'])) {$id = (int)$_POST['id_produit'];$cartAction = $_POST['cart_action'] ?? 'add';$redirect = 'Panier.php';

    if ($id > 0) {
        $stmt =$pdo->prepare("SELECT id_produit, titre, prix, stock FROM produit WHERE id_produit = ?");
        $stmt->execute([$id]);
        $produit =$stmt->fetch(PDO::FETCH_ASSOC);

        if ($produit) {$stock = (int)$produit['stock'];$qteActuelle = $_SESSION['panier'][$id]['quantity'] ?? 0;

            if ($cartAction === 'add') {
                if ($qteActuelle + 1 >$stock) {
                    echo "<script>alert('Stock insuffisant ! Stock disponible : $stock'); window.location.href='Details.php?id=$id';</script>";
                    exit();
                }
                if (isset($_SESSION['panier'][$id])) {
                    $_SESSION['panier'][$id]['quantity']++;
                } else {
                    $_SESSION['panier'][$id] = ['name' => $produit['titre'], 'price' => (float)$produit['prix'], 'quantity' => 1];
                }
                $redirect = 'Details.php?id=' . $id . '&added=1';

            } elseif ($cartAction === 'increase') {
                if ($qteActuelle + 1 >$stock) {
                    echo "<script>alert('Stock insuffisant ! Stock disponible : $stock'); window.location.href='Panier.php';</script>";
                    exit();
                }
                $_SESSION['panier'][$id]['quantity']++;

            } elseif ($cartAction === 'decrease') {
                if (isset($_SESSION['panier'][$id])) {$_SESSION['panier'][$id]['quantity'] = max(1,$_SESSION['panier'][$id]['quantity'] - 1);                 }             } elseif ($cartAction === 'remove') {
                unset($_SESSION['panier'][$id]);
            }

            unset($_SESSION['code_retrait'],$_SESSION['commande_en_cours']);
        }
    }
    header("Location: " . $redirect);
    exit();
}

// Vider le panier
if (isset($_GET['action']) && $_GET['action'] === 'clear') {$_SESSION['panier'] = [];
    unset($_SESSION['code_retrait'],$_SESSION['commande_en_cours']);
    header("Location: Panier.php");
    exit();
}

// Confirmation de la commande & Insertion f table commande + concerner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {$_SESSION['error_commande'] = 'Vous devez être connecté pour confirmer une commande.';
        header('Location: Panier.php');
        exit();
    }

    if (empty($_SESSION['panier'])) {
        header('Location: Panier.php');
        exit();
    }

    $idClient =$_SESSION['user_id'];
    $codeRetrait = strtoupper(bin2hex(random_bytes(4)));$totalCommande = 0;
    $nbArticles = 0;

    try {
        // Calcul du total global de la commande
        foreach ($_SESSION['panier'] as $item) {$totalCommande += (float)$item['price'] * (int)$item['quantity'];
        }

        $pdo->beginTransaction();

        // 1. Insertion dans la table `commande`
        $stmtCommande =$pdo->prepare("INSERT INTO commande (id_client, total, mode_livraison, statut, code_retrait) VALUES (?, ?, 'magasin', 'en_cours', ?)");
        $stmtCommande->execute([$idClient, $totalCommande,$codeRetrait]);
        
        // Récupération de l'ID de la commande générée
        $idCommande =$pdo->lastInsertId();

        // Préparation des requêtes pour `concerner` et la mise à jour du stock
        $stmtConcerner =$pdo->prepare("INSERT INTO concerner (id_commande, id_produit, quantite) VALUES (?, ?, ?)");
        $stmtUpdateStock =$pdo->prepare("UPDATE produit SET stock = stock - ? WHERE id_produit = ?");

        // 2. Insertion dans la table `concerner` pour chaque article du panier
        foreach ($_SESSION['panier'] as $idProduit =>$item) {
            $qte = (int)$item['quantity'];
            $nbArticles +=$qte;

            // Insertion dans la table `concerner` avec la quantité
            $stmtConcerner->execute([$idCommande, $idProduit,$qte]);

            // Mise à jour du stock produit
            $stmtUpdateStock->execute([$qte,$idProduit]);
        }

        $pdo->commit();

        $_SESSION['code_retrait'] =$codeRetrait;
        $_SESSION['commande_en_cours'] = [
            'code' => $codeRetrait,
            'nom' => $clientNom,
            'telephone' => $clientTelephone,
            'articles' => $nbArticles,
            'total' => $totalCommande,
            'date_limite' => date('d/m/Y', strtotime('+48 hours'))
        ];

        unset($_SESSION['error_commande']);$_SESSION['panier'] = [];
        header("Location: Confirmation.php");
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {$pdo->rollBack();
        }
        die("Erreur commande : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <style>
        :root { --paper: #f5f1e9; --surface: #fffdf9; --ink: #30251f; --muted: #746459; --line: #d8cec2; --accent: #684632; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: Georgia, serif; }
        .cart-page { width: min(100% - 40px, 900px); margin: 0 auto; padding: 38px 0 60px; }
        .back-link { color: var(--accent); text-decoration: none; }
        h1 { margin: 24px 0; font-size: 36px; font-weight: normal; }
        .cart-panel, .notice { padding: 24px; border: 1px solid var(--line); background: var(--surface); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        th, td { padding: 13px 12px; border-bottom: 1px solid var(--line); text-align: left; }
        th { color: var(--muted); font-size: 13px; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; }
        .inline-form { display: inline-flex; margin: 0; }
        .quantity-button, .remove-button { border: 1px solid var(--line); background: var(--surface); cursor: pointer; }
        .quantity-button { width: 30px; height: 30px; }
        .remove-button { padding: 7px 10px; }
        .total { text-align: right; font-size: 20px; }
        .actions { display: flex; gap: 12px; }
        .btn { padding: 11px 16px; background: var(--accent); color: #fff; text-decoration: none; border: none; cursor: pointer; }
        .btn-secondary { background: transparent; color: var(--accent); border: 1px solid var(--accent); }
        .menu { display: flex; gap: 30px; list-style: none; padding: 20px 40px; background: #30251f; margin: 0; }
        .nav-link { color: #FAF6F1; text-decoration: none; }
    </style>
</head>
<body>

<ul class="menu">
    <?php if (isset($_SESSION['user_id'])): ?>
        <li><span class="nav-link" style="color:#C69C6D;">Bonjour, <?= htmlspecialchars($clientNom) ?></span></li>
        <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
    <?php else: ?>
        <li><a href="login.php" class="nav-link">Connexion</a></li>
    <?php endif; ?>
</ul>

<main class="cart-page">
    <a href="Produits.php" class="back-link">&larr; Continuer mes achats</a>
    <h1>Votre panier</h1>

    <?php if (!empty($_SESSION['error_commande'])): ?>
        <section class="notice" style="border-color: #b5654d; background: #fff4f0; margin-bottom: 20px;">
            <p style="color: #8a3b2c; font-weight: bold; margin:0;"><?= htmlspecialchars($_SESSION['error_commande']); ?></p>
            <a href="login.php" class="btn" style="margin-top: 12px; display:inline-block;">Se connecter</a>
        </section>
        <?php unset($_SESSION['error_commande']); ?>
    <?php endif; ?>

    <?php if (empty($_SESSION['panier'])): ?>
        <section class="notice">
            <p>Votre panier est vide.</p>
            <a href="Produits.php" class="btn">Découvrir les livres</a>
        </section>
    <?php else: ?>
        <section class="cart-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_global = 0;
                        foreach ($_SESSION['panier'] as$id => $item):$total_item = (float)$item['price'] * (int)$item['quantity'];
                            $total_global +=$total_item;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']); ?></td>
                                <td><?= number_format((float)$item['price'], 2, ',', ' '); ?> DH</td>
                                <td>
                                    <div class="quantity-controls">
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="id_produit" value="<?= (int)$id; ?>">
                                            <button type="submit" name="cart_action" value="decrease" class="quantity-button">&minus;</button>
                                        </form>
                                        <span><?= (int)$item['quantity']; ?></span>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="id_produit" value="<?= (int)$id; ?>">
                                            <button type="submit" name="cart_action" value="increase" class="quantity-button">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td><?= number_format($total_item, 2, ',', ' '); ?> DH</td>
                                <td>
                                    <form method="post" class="inline-form">
                                        <input type="hidden" name="id_produit" value="<?= (int)$id; ?>">
                                        <button type="submit" name="cart_action" value="remove" class="remove-button">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="total"><strong>Total : <?= number_format($total_global, 2, ',', ' '); ?> DH</strong></p>
            <div class="actions">
                <a href="Panier.php?action=clear" class="btn btn-secondary">Vider le panier</a>
                <form method="post">
                    <button type="submit" name="confirm_order" value="1" class="btn">Confirmer la commande</button>
                </form>
            </div>
        </section>
    <?php endif; ?>
</main>

</body>
</html>