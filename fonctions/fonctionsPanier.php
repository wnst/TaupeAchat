<?php
//la syntaxe du cookie est id_prod*quantite,id_prod*quantite,...
//initialise $_COOKIE['panier'] en fonction de $_SESSION['panier']
function construireCookie()
{	
	$cookie = '';
	
	if(isset($_SESSION['panier']))
	{
		foreach($_SESSION['panier'] as $id_prod => $val) $cookie = $cookie.$id_prod.'*'.$val['Quantite'].',';
	}
	
	setcookie('panier', $cookie, time() + 60*3600, null, null, false, true);
}

//initialise $_SESSION['panier'] en fonction de $_COOKIE['panier']
function construirePanier()
{
	if(!isset($_SESSION['panier']))
	{	
		if(isset($_COOKIE['panier']))
		{
			$listeAchat = explode(',', $_COOKIE['panier'], -1);
			
			foreach($listeAchat as $elt)
			{
				$achat = explode('*', $elt);		
				$result = mysql_query('select id_prod, Libelle, Prix from produit where id_prod = '.$achat[0]);
				$article = mysql_fetch_assoc($result);			
				$_SESSION['panier'][$article['id_prod']] = array('Libelle' => $article['Libelle'], 'Prix' => $article['Prix'], 'Quantite' => $achat[1]);
			}
		}
	}
}

//on augmente la quantité de l'article $id_prod de 1
function ajouterPanier($id_prod)
{	
	$_SESSION['locked'] = false;
	
	$result = mysql_query('select Libelle, Prix from produit where id_prod = '.$id_prod);			
	$article = mysql_fetch_assoc($result);		
	
	//si l'article n'est pas déjà présent dans le panier alors on l'ajoute
	if(!isset($_SESSION['panier'][$id_prod]))
	{
		$_SESSION['panier'][$id_prod] = array('Libelle' => $article['Libelle'], 'Prix' => $article['Prix'], 'Quantite' => 1);
	}
	else //sinon on incrémente la quantité de 1
	{
		$_SESSION['panier'][$id_prod] = array('Libelle' => $article['Libelle'], 'Prix' => $article['Prix'], 'Quantite' => $_SESSION['panier'][$id_prod]['Quantite']+1);
	}
	
	construireCookie();
}

//on diminue la quantité de l'article $id_prod de 1
function retirerPanier($id_prod)
{
	if(isset($_SESSION['panier'][$id_prod]))
	{
		$_SESSION['locked'] = false;
		
		if($_SESSION['panier'][$id_prod]['Quantite'] == 1) unset($_SESSION['panier'][$id_prod]);
		else $_SESSION['panier'][$id_prod]['Quantite']--;
	
		if(count($_SESSION['panier']) == 0) unset($_SESSION['panier']);
		
		construireCookie();
	}
}

//on supprime toutes les occurences de l'article $id_prod
function supprimerPanier($id_prod)
{
	if(isset($_SESSION['panier'][$id_prod]))
	{
		$_SESSION['locked'] = false;
		unset($_SESSION['panier'][$id_prod]);
		
		if(count($_SESSION['panier']) == 0) unset($_SESSION['panier']);
		
		construireCookie();
	}
}

function afficherPanier()
{
	if(isset($_SESSION['panier']))
	{
		echo '<h1>Détail de votre panier :</h1><br /><br />
			<div class="entetePanier" style="width:90px">Prix</div>
			<div class="entetePanier" style="width:70px; border-right:none">Quantité</div>
			<div class="entetePanier" style="width:94px; border-right:none">Prix unitaire</div>
			<div class="entetePanier" style="width:82px; border-right:none">Supprimer</div>';
		
		$i = 1;
		$nbArticle = count($_SESSION['panier']);
		
		foreach($_SESSION['panier'] as $id_prod => $article)
		{
			echo '<div class="panier"'; 
			if($i != $nbArticle) echo ' style="border-bottom:none"';
				
			echo '>
					<div class="libellePanier">'.$article['Libelle'].'</div>
					
					<div class="colonnePanier" style="width:82px; padding-top:14px;">
						<a href="panier.php?suppr='.$id_prod;
						if(isset($_GET['id_rub'])) echo '&amp;id_rub='.$_GET['id_rub'];
							echo '"><img src="images/panierSuppr.png" alt=""/>'.
						'</a>
					</div>
					
					<div class="colonnePanier" style="width:94px; padding-top:17px;">'.$article['Prix'].' €</div>
					
					<div class="colonnePanier" style="width:70px; padding-top:9px;">
						<a href="panier.php?retirer='.$id_prod;
						if(isset($_GET['id_rub'])) echo '&amp;id_rub='.$_GET['id_rub'];
							echo '"><img src="images/panierMoins.jpg" alt=""/>&nbsp;&nbsp;&nbsp;'.
						'</a>'.
						$article['Quantite'].'&nbsp;&nbsp;&nbsp;							
						<a href="panier.php?ajouter='.$id_prod;
						if(isset($_GET['id_rub'])) echo '&amp;id_rub='.$_GET['id_rub'];
							echo '"><img src="images/panierPlus.jpg" alt=""/>
						</a>
					</div>
					
					<div class="colonnePanier" style="width:90px; padding-top:17px;">'.$article['Prix'] * $article['Quantite'].' €</div>
				
				</div>';
			
			$i++;
		}
		
		$total = 0;
		
		foreach($_SESSION['panier'] as $article) $total += $article['Prix'] * $article['Quantite'];
		
		echo '<div class="piedPanier">'.$total.' €</div>
			<br /><br /><br /><br />
			<a href="livraison.php" class="bouton">Valider ma commande</a>';
			
		if(isset($_GET['id_rub'])) echo '<a href="index.php?id_rub='.$_GET['id_rub'].'" class="bouton" style="margin-right:30px">Continuer mes achats</a>';
	}
	else echo '<h1>Votre panier est vide.</h1><br/><br/><br/><br/><br/><br/>';
}
?>