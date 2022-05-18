-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 18 mai 2022 à 07:44
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `annonces`
--

-- --------------------------------------------------------

--
-- Structure de la table `adpictures`
--

DROP TABLE IF EXISTS `adpictures`;
CREATE TABLE IF NOT EXISTS `adpictures` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filePath` varchar(100) NOT NULL,
  `adId` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adId` (`adId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `adpictures`
--

INSERT INTO `adpictures` (`id`, `filePath`, `adId`) VALUES
(1, 'uploaded-files/628356be6e745.jpg', 1);

-- --------------------------------------------------------

--
-- Structure de la table `ads`
--

DROP TABLE IF EXISTS `ads`;
CREATE TABLE IF NOT EXISTS `ads` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `price` smallint(5) UNSIGNED NOT NULL,
  `publicationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `publicationDate` (`publicationDate`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `ads`
--

INSERT INTO `ads` (`id`, `title`, `content`, `price`, `publicationDate`, `userId`) VALUES
(1, 'Livre Java', 'Ces deux livres offrent au lecteur un maximum d\'informations sur Java EE pour le développement d\'applications web responsive. 1141 pages par nos experts. Des éléments complémentaires sont en téléchargement sur le site www.editions-eni.fr.Le livre de référence de la collection Epsilon Java EE - Développez des applications web en Java (Nouvelle édition)Ce livre s\'adresse aux développeurs souhaitant monter en compétences sur le développement d\'applications web, côté serveur, avec les technologies essentielles de la plateforme', 93, '2022-05-17 10:03:10', 1);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adId` mediumint(8) UNSIGNED NOT NULL,
  `fromUser` smallint(5) UNSIGNED NOT NULL,
  `toUser` smallint(5) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `writingDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `adId` (`adId`),
  KEY `fromUser` (`fromUser`),
  KEY `toUser` (`toUser`),
  KEY `writingDate` (`writingDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `hashedPassword` char(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `hashedPassword`) VALUES
(1, 'djermouniH@gmail.com', '$2y$10$zXCq6dWdLopmNdCZoGje2uOzlL/EbwlkQUgOxhMjMB1JFhMIp3D6e');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adpictures`
--
ALTER TABLE `adpictures`
  ADD CONSTRAINT `adpictures_ibfk_1` FOREIGN KEY (`adId`) REFERENCES `ads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`adId`) REFERENCES `ads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`fromUser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`toUser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
