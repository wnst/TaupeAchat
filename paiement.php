<?php
session_start();
include 'config/bdd.php';
include 'fonctions/fonctionsLayout.php';
include 'fonctions/fonctionsPaiement.php';
include 'fonctions/fonctionsPanier.php';
include 'header.php';

//si l'utilisateur accède à cette page sans être passé par la page livraison
if(!isset($_SESSION['livraison']))
{
	header('Location: panier.php');
	exit;
}

//si le panier a été modifié après la validation de la commande
if($_SESSION['locked']==false)
{
	$_SESSION['message'] = '<div style="text-align:center"><h1>Le contenu de votre panier a été modifié. Veuillez revalider votre commande.</h1><br /><br /><a href="panier.php">Cliquez ici pour consulter votre panier.</a></div>';
	header('Location: message.php');
	exit;
}

//on risque d'initialiser le formulaire de paiement avec le POST de l'inscrption (attribut nom commun)
if(!isset($_POST['paiement']) && isset($_POST)) unset($_POST);

//si l'utilisateur a validé le forumulaire de paiement
if(isset($_POST['paiement'])) paiement();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="content-language" content="fr" />
<link href="style.css"	title="Défaut" rel="stylesheet" type="text/css" media="screen" />
</head>
<body> 
<div id="header"><a href="index.php"><img src="images/header.png" alt="" style="-moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px;"/></a><br/>
	<div align="center"><marquee behavior="alternate" loop="2.5"><font color="black" size="4px">Taupe Achat se creuse la t&ecirc;te pour vous chercher les meilleurs prix!</font></marquee></div>
	</div>
	<div id="conteneur"><!-- Début #Conteneur --> 
   
		<div id="contenu"><!-- Début #contenu --> 	
		
			<div id="left"><!-- Début Colonne gauche #left -->
			
				<?php afficherBarreRecherche(); ?>
				<div class="hautmenu">Rubriques</div>
				<div class="fondmenu"><!-- Menu vertical --> 					
					<?php afficherMenuGauche(); ?>
				</div>
				<div class="basmenu"></div><!-- Fin Menu vertical --><br/><br/><br/><br/><br/>
				<img src="images/rene.jpg" width="150px" height="150px">
	 
			</div><!-- Fin Colonne gauche #left -->	 		
			
			<div id="right"><!-- Début Colonne gauche #right --> 
				
				<div class="hautmenu">Mon Panier</div>
				<div class="fondmenu"><!-- Menu vertical --> 
					<?php afficherCadrePanier(); ?>
				</div>
				<div class="basmenu"></div><!-- Fin Menu vertical -->  

				<div class="hautmenu">Mon Compte</div>
				<div class="fondmenu"><!-- Menu vertical --> 
					<?php afficherCadreCompte(); ?>
				</div>
				<div class="basmenu"></div><!-- Fin Menu vertical -->	
				<img src="images/rene.jpg" width="150px" height="150px">				
		 
			</div><!-- Fin Colonne droite #right -->	 	 
					
			<div id="center"><!-- Début Colonne centrale #center --> 	
				
				<div class="news_haut"></div>
				<div class="news_fond"><!-- Cadre de News -->	
					<?php afficherPaiement(); ?>
				</div>
				<div class="news_bas"></div><!-- Fin Cadre de News -->
							  
			</div><!-- Fin Colonne centrale #center-->
		

		</div><!-- Fin #Contenu -->
			
	</div><!-- Fin #Conteneur -->		
	   
</body>
</html>