-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.4.3 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage des données de la table books_db.client : ~9 rows (environ)
INSERT INTO `client` (`id_client`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `role`) VALUES
	(1, 'khadija', 'eddahby', 'eddahbykhadija04@gmail.com', '$2y$10$gDIvkXFgLAHzunWNlrPLruOSM15jwh2tdwiM9.0fEWF5FvUZTrN/2', '0789765435', 'user'),
	(2, 'marie', 'ma', 'ma@gmail.com', '$2y$10$OP0cHnogGr7L4UX898lE2ufl5p5bwYrl9WOT9ODq69UP/5S8n4HRO', '0788990988', 'user'),
	(3, 'hamidi', 'hiba', 'hibahmd@gmail.com', 'hiba123', '0644740585', 'user'),
	(4, 'admin', 'directeur', 'directeur@gmail.com', 'D123', '0661237689', 'admin'),
	(5, 'hibatallah', 'hamidi', 'hamidi@gmail.com', '$2y$10$uuXryakV07fpUzN1zaVMPeI9LxmfHEZHFqY5YSwKR8yXJlX8nfpye', '0722227044', 'user'),
	(6, 'maroi', 'salma', 'ms@gmail.com', '$2y$10$gqNGSRdlFs4Tz10wcn6eLeaSQCXXWKgQWaBAfCV0ufMjM8Fk4Xzv2', '0767896532', 'user'),
	(7, 'hamidi', 'hafsa', 'hafsa@gmail.com', '$2y$10$47jB8ex/kGBSiLPYHj/Nv.munQIhPn5OF/Jz6ydBUfBIUkuz73iTa', '0765435987', 'user'),
	(8, 'fati', 'fati', 'fati@gmail.com', '$2y$10$LgTutAvKAxamoVEq6eHQtuGeIvcd0.D2nx6gL6gAiK3xLzQfZBowu', '0765432345', 'user'),
	(9, 'eddahby', 'khadija', 'eddahby@gmail.com', '$2y$10$FsQma1dUv9GR/wnggOE2PuWUW7.t.rC6Z.avTUsINzWKNblX/B4BW', '0722227044', 'user');

-- Listage des données de la table books_db.commande : ~3 rows (environ)
INSERT INTO `commande` (`id_commande`, `id_client`, `id_produit`, `total`, `date_commande`, `mode_livraison`, `statut`, `code_retrait`) VALUES
	(5, 7, NULL, 240.00, '2026-10-06 20:09:00', 'magasin', 'récupérer', '8D7B9AD5'),
	(6, 7, NULL, 120.00, '2026-10-06 20:13:43', 'magasin', 'annulee', '2E6DE512'),
	(8, 9, NULL, 1240.00, '2026-10-09 23:39:14', 'magasin', 'en_cours', '0CEE6EF0');

-- Listage des données de la table books_db.concerner : ~4 rows (environ)
INSERT INTO `concerner` (`id_produit`, `id_commande`, `quantite`) VALUES
	(1, 5, 2),
	(11, 6, 1),
	(11, 8, 4),
	(12, 8, 4);

-- Listage des données de la table books_db.produit : ~19 rows (environ)
INSERT INTO `produit` (`id_produit`, `titre`, `categorie`, `Prix`, `description`, `image`, `stock`, `auteur`) VALUES
	(1, 'L`Étranger', 'Littérature', 120.00, 'Roman philosophique de Albert Camus.', 'etranger.jpg', 13, 'Albert Camus'),
	(2, 'La République', 'Philosophie', 150.50, 'livre sur la philosophie et la justice de écrivain Platon.', 'republique.jpg', 10, 'Platon'),
	(3, 'Les Misérables', 'Drama', 220.00, 'Roman de Victor hugo dramatique.', 'miserables.jpg', 18, 'Victor Hugo'),
	(4, 'Le crocodile à grandes dents', 'Enfants', 100.00, 'Roman d\'enfants Le crocodile à grandes dents de Michael.', 'e1.jpg', 20, 'Michaël Escoffier'),
	(5, 'Petites Histoires du soir', 'Enfants', 100.00, 'Roman d\'enfants raconte des histoires du soir de Pol Léon.', 'e3.jpg', 19, 'Pol Léon'),
	(6, 'Mes Histoires de fille', 'Enfants', 100.00, 'Roman d\'enfants à 10 racontes incroyables pour vos enfants', 'e2.jpg', 20, 'Mes petite Histoires'),
	(7, 'Monsieur le philosophe', 'Philosophie', 80.00, 'Inventeur du roman moderne et de la critique d\'art, familier des équations de Newton et auteur de théâtre iconoclaste, artisan acharné de l\'Encyclopédie.', 'p1.jpg', 20, 'Francis Devaux'),
	(8, 'Pensées pour moi-même  ', 'Philosophie', 200.00, 'Les 12 Livres Complets : La Nouvelle Traduction Moderne avec Analyses Approfondies de Son Stoïcisme et de Sa Philosophie.', 'p2.jpg', 20, 'MARC AURÈLE'),
	(9, 'Threshing Day', 'Littérature', 111.00, 'Return to the Empyrean world with thirteen stories starring your favourite Fourth Wing characters.', 't2.jpg', 20, 'Rebecca Yarros'),
	(10, ' Al-Manhaj', 'Littérature', 500.00, 'Méthode d\'Arabe Littéral - Al-Manhaj', 't3.jpg', 20, 'Mahfoud Boudaakkar '),
	(11, 'Those About to Die', 'Drama', 120.00, 'If you can imagine a superior American sports writer suddenly being transported back in time to cover the ancient Roman games', 'd2.jpg', 14, 'Daniel P. Mannix '),
	(12, 'Les tragédies romantiques d\'un drama king (1)', 'Drama', 190.00, 'Les tragédies romantiques d\'un drama king (1)', 'd3.jpg', 16, 'Harry Trevaldwyn et Vanessa Rubio-Barreau'),
	(13, 'El-Ameer El-Saghir / Le Petit Prince', 'Enfants', 210.00, 'Roman historique d\'enfants El-Ameer El-Saghir / Le Petit Prince de Antoine.', 'e4.jpg', 30, 'Antoine de Saint Exupéry '),
	(14, 'The Study Quran', 'Rélegions', 500.00, 'An accessible and accurate translation of the Quran that offers a rigorous analysis of its theological, metaphysical, historical, and geographical teachings and backgrounds, and includes extensive study notes, special introductions by experts in the field, and is edited by a top modern Islamic scholar, respected in both the West and the Islamic world.', 'r1.jpg', 20, ' Seyyed Hossein Nasr'),
	(15, 'Muhammad: His Life Based on the Earliest Sources', 'Rélegions', 400.00, 'Muhammad Acclaimed worldwide as the definitive biography of the Prophet in the English language. This book will be read with equal enjoyment by those already familiar with Muhammad\'s life and those coming to it for the first time. ', 'r2.jpg', 20, 'Martin Lings '),
	(16, 'Coran', 'Rélegions', 220.00, 'Saint Coran 14 X 20 tajweed (couverture dorée) - (Arabe)', 'r3.jpg', 20, '-'),
	(17, 'Imaginative Realism', 'Imagination', 270.00, 'How to Paint What Doesn\'t Exist', 'i1.jpg', 20, 'James Gurney'),
	(18, 'Ces présidents qui nous ont fait tant de mal', 'Politique', 210.00, 'Pascal Perri dans cette enquête entre économie et politique démontre que le drame de la France s\'est bien joué au sommet. Les présidents de gauche ont systématiquement affaibli la valeur du travail et ceux de droite ont renoncé aux ambitions industrielles. ', 'po1.jpg', 20, 'Pascal Perri'),
	(19, 'INTELLIGENCE ÉMOTIONNELLE', 'psychologique', 190.00, '4 livres en 1 Renforce l\'Estime de soi, l\'Autodiscipline et la Pensée Positive, Gérer l\'Anxiété et le Stress| Langage corporel : PNL, TCC, Psychologie Noire et Manipulation', 'psy1.jpg', 0, 'Ryan J.D. Golema');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
