<?php
session_start();

if (empty($_SESSION['commande_en_cours']) || empty($_SESSION['code_retrait'])) {
    header('Location: Panier.php');
    exit();
}

$confirmation = $_SESSION['commande_en_cours'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>
    <style>
        :root { --paper: #f5f1e9; --surface: #fffdf9; --ink: #30251f; --muted: #746459; --line: #d8cec2; --accent: #684632; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: Georgia, 'Times New Roman', serif;
        }
        .confirmation-wrap {
            width: min(100% - 40px, 700px);
            margin: 60px auto;
            padding: 40px 30px;
            border: 1px solid var(--line);
            background: var(--surface);
            border-radius: 12px;
        }
        .confirmation-card {
            text-align: center;
        }
        .card-title {
            margin: 0 0 12px;
            font-size: 32px;
        }
        .card-lead {
            margin: 0 0 28px;
            color: #5d4037;
            font-size: 16px;
        }
        .pickup-code {
            display: block;
            margin-bottom: 28px;
            padding: 24px;
            border-radius: 8px;
            background: #3e2723;
            color: #c69c6d;
            font-family: ui-monospace, Menlo, monospace;
            font-size: 52px;
            letter-spacing: 6px;
        }
        .order-summary {
            padding: 20px 24px;
            border-radius: 8px;
            background: #faf6f1;
            text-align: left;
            font-size: 15px;
            line-height: 1.9;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }
        .summary-row span:last-child {
            text-align: right;
        }
        .confirmation-note {
            margin: 24px 0 20px;
            color: #795548;
            font-size: 14px;
        }
        .home-button {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 6px;
            background: #5d4037;
            color: #fff;
            font-size: 15px;
            text-decoration: none;
        }
        .home-button:hover {
            background: #4e342d;
        }
    </style>
</head>
<body>
    <main class="confirmation-wrap">
        <div class="confirmation-card">
            <h1 class="card-title">Commande enregistrée</h1>
            <p class="card-lead">Présentez ce code au magasin pour retirer vos livres.</p>
            <strong class="pickup-code"><?php echo htmlspecialchars($confirmation['code']); ?></strong>

            <div class="order-summary">
                <div class="summary-row"><span>Nom</span><span><?php echo htmlspecialchars($confirmation['nom']); ?></span></div>
                <div class="summary-row"><span>Téléphone</span><span><?php echo htmlspecialchars($confirmation['telephone']); ?></span></div>
                <div class="summary-row"><span>Livres</span><span><?php echo (int) $confirmation['articles']; ?> article(s)</span></div>
                <div class="summary-row"><span>Total à payer au magasin</span><span><?php echo number_format((float) $confirmation['total'], 2, ',', ' '); ?> DH</span></div>
                <div class="summary-row"><span>À retirer avant</span><span><?php echo htmlspecialchars($confirmation['date_limite']); ?></span></div>
            </div>

            <p class="confirmation-note">Notez bien votre code : il ne sera plus affiché.</p>
            <a href="index.php" class="home-button">Retour à l'accueil</a>
        </div>
    </main>
</body>
</html>