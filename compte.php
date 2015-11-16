<?php
include 'config/bdd.php';
include 'fonctions/fonctionsLayout.php';
include 'fonctions/fonctionsCompte.php';
include 'fonctions/fonctionsPanier.php';
include 'header.php';

session_start();
verifierAdresse();
construirePanier();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="content-language" content="fr" />
<link href="style.css"	title="D�faut" rel="stylesheet" type="text/css" media="screen" />
</head>
<body> 
<div id="header"><a href="index.php"><img src="images/header.png" alt="" style="-moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px;"/></a><br/>
	<div align="center"><marquee behavior="alternate" loop="2.5"><font color="black" size="4px">Taupe Achat se creuse la t&ecirc;te pour vous chercher les meilleurs prix!</font></marquee></div>
	</div>
	<div id="conteneur"><!-- D�but #Conteneur --> 
   
		<div id="contenu"><!-- D�but #contenu --> 	
		
			<div id="left"><!-- D�but Colonne gauche #left -->
			
				<?php afficherBarreRecherche(); ?>
				<div class="hautmenu">Rubriques</div>
				<div class="fondmenu"><!-- Menu vertical --> 					
					<?php afficherMenuGauche(); ?>
				</div>
				<div class="basmenu"></div><!-- Fin Menu vertical -->
				
	 
			</div><!-- Fin Colonne gauche #left -->	 		
			
			<div id="right"><!-- D�but Colonne gauche #right --> 
				
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
							
		 
			</div><!-- Fin Colonne droite #right -->	 	 
					
			<div id="center"><!-- D�but Colonne centrale #center --> 	
				
				<div class="news_haut"></div>
				<div class="news_fond"><!-- Cadre de News -->	
					<?php afficherCompte(); ?>
				</div>
				<div class="news_bas"></div><!-- Fin Cadre de News -->
							  
			</div><!-- Fin Colonne centrale #center-->
		

		</div><!-- Fin #Contenu -->
			
	</div><!-- Fin #Conteneur -->		
	   
</body>
</html>