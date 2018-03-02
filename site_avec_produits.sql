-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  lun. 18 déc. 2017 à 09:17
-- Version du serveur :  10.1.28-MariaDB
-- Version de PHP :  7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `site`
--

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(3) NOT NULL,
  `id_membre` int(3) DEFAULT NULL,
  `montant` int(3) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `etat` enum('en cours de traitement','traité','livré') NOT NULL DEFAULT 'en cours de traitement'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `id_membre`, `montant`, `date_enregistrement`, `etat`) VALUES
(3, 2, 36, '2017-12-13 16:22:24', ''),
(4, 2, 70, '2017-12-13 16:22:30', 'en cours de traitement'),
(5, 2, 172, '2017-12-13 16:22:50', 'en cours de traitement'),
(6, 2, 163, '2017-12-13 16:56:06', 'en cours de traitement');

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_details_commande` int(3) NOT NULL,
  `id_commande` int(3) NOT NULL,
  `id_produit` int(3) DEFAULT NULL,
  `quantite` int(3) NOT NULL,
  `prix` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_details_commande`, `id_commande`, `id_produit`, `quantite`, `prix`) VALUES
(4, 3, 10, 1, 36),
(5, 4, 11, 1, 70),
(6, 5, 14, 3, 24),
(7, 5, 16, 4, 25),
(8, 6, 10, 2, 36),
(9, 6, 14, 1, 24),
(10, 6, 15, 1, 18),
(11, 6, 22, 1, 49);

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `id_membre` int(3) NOT NULL,
  `pseudo` varchar(30) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `sexe` enum('m','f') NOT NULL DEFAULT 'm',
  `ville` varchar(50) NOT NULL,
  `code_postal` int(5) UNSIGNED ZEROFILL NOT NULL,
  `adresse` text NOT NULL,
  `statut` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `membre`
--

INSERT INTO `membre` (`id_membre`, `pseudo`, `mdp`, `nom`, `prenom`, `email`, `sexe`, `ville`, `code_postal`, `adresse`, `statut`) VALUES
(2, 'admin', '$2y$10$Q5Lu9zJYpJv.F2.X86ttTewc27uU9kh5uII.G2PmjhP3x42K.yzjK', 'Frédéric', 'Roth', 'mail@mail.fr', 'm', 'Montpellier', 34000, '2 rue du Faisan', 1),
(3, 'test', '$2y$10$5D82vx9ymdnZxP0Pb0lvHuv1KZ9PO.fIRIe/V6LTj155zrk.aM5b6', 'Frédéric', 'Roth', 'test@mail.fr', 'm', 'Montpellier', 34000, '2 rue du Faisan', 0),
(4, 'MirkoZ', '$2y$10$4qm9qMJuG/65NqHmE4hB3.r6.AyMlRCZndLpHNiSZwH4wBxbFma72', '', '', 'mirkoz1982@gmail.com', 'm', '', 00000, '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(3) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `categorie` varchar(20) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `couleur` varchar(20) NOT NULL,
  `taille` varchar(5) NOT NULL,
  `sexe` enum('m','f','mixte') NOT NULL DEFAULT 'm',
  `photo` varchar(255) NOT NULL,
  `prix` int(3) NOT NULL,
  `stock` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `reference`, `categorie`, `titre`, `description`, `couleur`, `taille`, `sexe`, `photo`, `prix`, `stock`) VALUES
(9, 'NI122S07D', 'pantalon', 'Redefined Rebel', 'Jean slim', 'noir', 'm', 'm', 'photo/NI122S07DR0622G004-Q11@10.jpg', 32, 0),
(10, 'MI168Q07D', 'pantalon', 'Yourturn', 'Jean slim', 'bleu', 'l', 'm', 'photo/MI168Q07DYO122GA27-K12@10.jpg', 36, 9),
(11, 'MDG458Q07D', 'pantalon', 'Selected Femme', 'Pantalon classique', 'rouge', 's', 'f', 'photo/MDG458Q07D_SE521A0AP-G11__default__16.jpg', 70, 22),
(12, 'SFF6548Q07D', 'pantalon', 'Selected Femme', 'Pantalon classique', 'vert', 'm', 'f', 'photo/SFF6548Q07D_SE521A09O-M11__default__14.jpg', 59, 36),
(13, 'ZER489Q07D', 't_shirt', 'Sfsanni Tee', 'T-shirt basique', 'jaune', 'xs', 'f', 'photo/ZER489Q07DSE521D0B2-E11@9.jpg', 22, 42),
(14, 'H0421D01A', 't_shirt', 'Hollister Co', 'T-shirt imprimé', 'rouge', 'm', 'f', 'photo/H0421D01A_H0421D01A-G11__default__8.jpg', 24, 30),
(15, 'XCV894Q324', 't_shirt', 'Shdpin Nec', 'T-shirt basique', 'noir', 'xl', 'm', 'photo/XCV894Q324_SE622O0BZ-Q12__default__10.jpg', 18, 24),
(16, 'SDR451Q324', 't_shirt', 'Shdpine Neck', 'T-shirt basique', 'bleu', 'xxl', 'm', 'photo/SDR451Q324_SE622O0FN-K11__default__13.jpg', 25, 34),
(17, 'SDF4456924', 'chemise', 'Slim Shirt', 'Chemisier', 'noir', 'xs', 'f', 'photo/SDF4456924_GS121E04R-Q11__default__12.jpg', 70, 28),
(18, 'DRF6986924', 'chemise', 'Slim Fit', 'Chemise', 'jaune', 'xxxl', 'm', 'photo/DRF6986924_PO222D0B3-E11__default__11.jpg', 104, 32),
(19, 'REYU65924', 'veste', 'Sherpa Trucker', 'Veste en jean', 'noir', 'l', 'm', 'photo/REYU65924LE222T003-Q11@10.jpg', 139, 14),
(20, 'N1242F09F', 'veste', 'Aeroshield', 'Veste de running', 'blanc', 'xl', 'm', 'photo/N1242F09FN1242F09F-A11@15.jpg', 299, 9),
(21, 'PO222D0BS', 'chemise', 'Slim Fit', 'Chemise', 'rouge', 's', 'm', 'photo/PO222D0BS_PO222D0BS-G11__default__10.jpg', 109, 23),
(22, 'BJ721E066', 'chemise', 'Dillion Stripe', 'Chemisier', 'gris', 'xs', 'f', 'photo/BJ721E066_BJ721E066-C11__default__8.jpg', 49, 27),
(23, 'VE121I0RA', 'pull', 'Vmagoura', 'Pullover', 'noir', 'xl', 'f', 'photo/VE121I0RA_VE121I0RA-Q11__default__18.jpg', 22, 17),
(24, 'PM921I05F', 'pull', 'Promod', 'Pullover', 'bleu', 's', 'f', 'photo/PM921I05F_PM921I05F-K11__default__12.jpg', 35, 21),
(25, 'L4721J00G', 'pull', 'Lacoste Live', 'Sweatshirt', 'rouge', 'xs', 'f', 'photo/L4721J00G_L4721J00G-G11__default__10.jpg', 99, 26),
(29, 'MF921U005', 'veste', 'Miss Selfridge', 'Manteau classique', 'rouge', 's', 'f', 'photo/MF921U005_MF921U005-G11__default__10.jpg', 84, 9),
(30, 'MA321U00G', 'veste', 'Marc O\'Polo', 'Veste d\'hiver', 'vert', 'm', 'f', 'photo/MA321U00G_MA321U00G-M11__default__12.jpg', 319, 12),
(31, 'SA322Q01J', 'pull', 'Gees On', 'Pullover', 'jaune', 'xxxl', 'm', 'photo/SA322Q01J_SA322Q01J-E11__default__8.jpg', 42, 39),
(32, 'SE622Q09N', 'pull', 'Shhred Crew', 'Pullover', 'blanc', 'xxl', 'm', 'photo/SE622Q09N_SE622Q09N-A11__default__8.jpg', 39, 28),
(34, 'M9122E08S', 'pantalon', 'Mango', 'Chino', 'jaune', 'xl', 'm', 'photo/M9122E08S_M9122E08S-E11__default__9.jpg', 49, 53),
(38, 'HI122O03T', 't_shirt', 'Hilfiger Denim', 'T-shirt basique', 'blanc', 'l', 'm', 'photo/HI122O03T_HI122O03T-A11__default__10.jpg', 24, 54);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_membre` (`id_membre`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_details_commande`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`id_membre`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_details_commande` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `id_membre` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_membre`) REFERENCES `membre` (`id_membre`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `details_commande_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `details_commande_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
