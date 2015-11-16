<?php
include 'header.php';

$connect = mysql_connect('localhost', 'thomas', 'gevutema28') or die ('erreur de connexion');

mysql_query('drop database if exists `projet`');

mysql_query('create database `projet`');

mysql_select_db('projet', $connect) or die ('erreur de connexion base');

mysql_query('CREATE TABLE IF NOT EXISTS `appartient` (
  `id_prod` int(11) NOT NULL,
  `id_rub` int(11) NOT NULL,
  PRIMARY KEY (`id_prod`,`id_rub`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

mysql_query('CREATE TABLE IF NOT EXISTS `appartient2` (
  `id_prod` int(11) NOT NULL,
  `id_prop` int(11) NOT NULL,
  `valeur_prop` varchar(40) NOT NULL,
  PRIMARY KEY (`id_prod`,`id_prop`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

mysql_query('CREATE TABLE IF NOT EXISTS `commande` (
  `id_com` bigint(20) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `civilite` varchar(4) NOT NULL,
  `nom` varchar(40) NOT NULL,
  `prenom` varchar(40) NOT NULL,
  `adresse` varchar(160) NOT NULL,
  `cp` int(11) NOT NULL,
  `ville` varchar(80) NOT NULL,
  `telephone` varchar(10) NOT NULL,
  PRIMARY KEY (`id_com`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

mysql_query('CREATE TABLE IF NOT EXISTS `detail` (
  `id_com` bigint(30) NOT NULL,
  `id_prod` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`id_com`,`id_prod`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

mysql_query('CREATE TABLE IF NOT EXISTS `hierarchie` (
  `id_parent` int(11) NOT NULL,
  `id_enfant` int(11) NOT NULL,
  PRIMARY KEY (`id_parent`,`id_enfant`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

mysql_query('CREATE TABLE IF NOT EXISTS `produit` (
  `id_prod` int(11) NOT NULL AUTO_INCREMENT,
  `Libelle` varchar(80) NOT NULL,
  `Prix` float NOT NULL,
  `UniteDeVente` varchar(80) NOT NULL,
  `Descriptif` text NOT NULL,
  `Photo` varchar(80) NOT NULL,
  PRIMARY KEY (`id_prod`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;');

mysql_query('CREATE TABLE IF NOT EXISTS `propriete` (
  `id_prop` int(11) NOT NULL AUTO_INCREMENT,
  `libelle_prop` varchar(40) NOT NULL,
  PRIMARY KEY (`id_prop`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;');

mysql_query('CREATE TABLE IF NOT EXISTS `rubrique` (
  `id_rub` int(11) NOT NULL AUTO_INCREMENT,
  `Libelle_rub` varchar(80) NOT NULL,
  PRIMARY KEY (`id_rub`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;');

mysql_query('CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(40) NOT NULL,
  `sel` varchar(40) NOT NULL,
  `mdp` varchar(40) NOT NULL,
  `email` varchar(80) NOT NULL,
  `jour` int(2) NOT NULL,
  `mois` int(2) NOT NULL,
  `annee` int(4) NOT NULL,
  `rang` int(1) NOT NULL,
  `civilite` varchar(4) NOT NULL,
  `nom` varchar(40) NOT NULL,
  `prenom` varchar(40) NOT NULL,
  `adresse` varchar(160) NOT NULL,
  `cp` int(11) NOT NULL,
  `ville` varchar(80) NOT NULL,
  `telephone` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;');

mysql_query("INSERT INTO `utilisateur` (`id`, `login`, `mdp`, `email`, `jour`, `mois`, `annee`, `rang`, `civilite`, `nom`, `prenom`, `adresse`, `cp`, `ville`, `telephone`) VALUES (12, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin@gmail.com', 3, 7, 1989, 1, 'M', 'ADMIN', 'Admin', '1 rue des admins', 99000, 'Admin City', '0304050607');");

header('Location: connexion.php');  
?>
