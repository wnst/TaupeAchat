<?php
//construit le cheminement des clics de l'utilisateur et le stocke dans $_SESSION['chemin']
//chaque élément de $_SESSION['chemin'] est un tableau de taille 2 dans lequel on enregistre l'id d'une rubrique puis le libelle de cette rubrique
function construireChemin($GET)
{
	extract($GET);
	
	$trouve=false;

	if(isset($id_rub)) //si on a cliqué sur une rubrique
	{
		//on récupère le libellé de la rubrique cliquée
		$result = mysql_query('SELECT Libelle_rub FROM rubrique WHERE id_rub = '.$id_rub);
		$lib_rub = mysql_fetch_row($result);
		$lib_rub = $lib_rub[0];

		//on construit le cheminement des clics de l'utilisateur
		if(isset($source)) //si on a cliqué sur une rubrique qui n'a pas de supérieure (le menu de gauche)
		{
			if($source=="menu")
			{
				$_SESSION['chemin'] = array();
				$_SESSION['chemin'][] = array($id_rub, $lib_rub); //on ajoute la rubrique au chemin
			}
		}
		else
		{
			$i=1;
			
			foreach($_SESSION['chemin'] as $elt)
			{
				//la rubrique est déjà dans le chemin donc on efface tous les éléments qui la succèdent dans le chemin car on est revenu en arrière
				if($elt[1] == $lib_rub)
				{
					array_splice($_SESSION['chemin'], $i);
					$trouve = true;
					break;
				}

				$i++;
			}
			
			//si la rubrique n'est pas déjà dans le chemin, on l'ajoute
			if(!$trouve) $_SESSION['chemin'][] = array($id_rub, $lib_rub ); 	
		}
	}
	else $_SESSION['chemin'] = array();
}

function afficherChemin()
{
	//si le chemin n'est pas vide alors on l'affiche
	if(($nbElt = count($_SESSION['chemin'])) > 0)
	{
		for($i=0; $i<$nbElt-1; $i++) echo '<a href="index.php?id_rub='.$_SESSION['chemin'][$i][0].'" title="">'.$_SESSION['chemin'][$i][1].'</a> -> ';

		echo '<a href="index.php?id_rub='.$_SESSION['chemin'][$i][0].'" title="">'.$_SESSION['chemin'][$i][1].'</a>';
	}
}

function afficherMenuCentre($GET)
{
	extract($GET);
	
	if(isset($id_rub))
	{
		echo '<div class="news_haut"></div>';
		echo '<div class="news_fond"><div class="sousrub">';
		afficherChemin($_SESSION['chemin']);
		echo '<br />';
		
		$result = mysql_query('select id_rub, Libelle_rub from rubrique, hierarchie where id_parent='.$id_rub.' and id_rub = id_enfant');
		$i = 4;
		
		//on place les sous-rubriques l'une à côté de l'autre par paquet de 4
		while($rub = mysql_fetch_row($result))
		{
			if($i==4) echo '<ul>';
			
			echo '<li><a href="index.php?id_rub='.$rub[0].'" title="">'.$rub[1].'</a></li> ';
			$i--;
			
			if($i==0)
			{
				echo '</ul></div>';
				$i=4;
			}
		}
		
		if($i!=4) echo '</ul></div>';
		
		echo '</div>';
		
	}
}

function afficherListeArticle($GET)
{	
	extract($GET);
	
	if(!isset($page)) $page=1; //si on a pas cliqué sur un numéro de page
	
	$nbAff = 3; //nombre d'articles affichés par page

	//si on a cliqué sur une rubrique
	if(isset($id_rub))
	{	
		//on crée une table temporaire pour chaque filtre souhaité par l'utilisateur, contenant les id_prod relatifs à la valeur sélectionnée dans le menu déroulant
		$i = 0;
		if(isset($_POST['filtre']))
		{		
			//On sauvegarde $_POST dans une session car si l'utilisateur change de numéro de page il ne faudra pas réinitialiser les filtres. Or si l'utiliasteur change de page $_POST est vidé.
			$_SESSION['$_POST'] = $_POST;
			
			foreach($_POST as $libelle_prop => $valeur_prop)
			{
				if($valeur_prop != 'Filtrer' && $valeur_prop != '--')
				{
					$result = mysql_query('select id_prop from propriete where libelle_prop ="'.$libelle_prop.'"');
					$id_prop = mysql_fetch_row($result);

					mysql_query("CREATE TEMPORARY TABLE T$i 
								SELECT id_prod
								FROM appartient2
								WHERE id_prop = '$id_prop[0]'
								AND valeur_prop = '$valeur_prop';");

					$i++;
				}
			}
		}
		
		//On aura besoin d'insérer dans notre requête SQL toutes les tables temporaires crées. On va donc créer les variables $table et $filtre.
		$table = '';
		$filtre = '';
		for($j=0; $j<$i; $j++)
		{
			$table = $table.', T'.$j;
			$filtre = $filtre.' AND P.id_prod=T'.$j.'.id_prod';
		}
		
		$result = mysql_query('select P.id_prod from produit P, appartient A where P.id_prod=A.id_prod AND A.id_rub='.$id_rub);
		
		//S'il y a des articles à afficher alors on affiche le menu déroulant "trier par". On redirige automatiquement l'utilisateur vers la page triée quand il sélectionne une valeur dans le menu déroulant.
		if(mysql_num_rows($result) > 0)
		{
			echo '<div class="floatRight" style="margin-bottom:15px">
			Trier par: <select onchange="location = this.options[this.selectedIndex].value">';
			if(!isset($_GET['tri']))
			{
				echo '<option value="index.php?id_rub='.$id_rub.'&amp;tri=nouveaute">Nouveauté</option>
				<option value="index.php?id_rub='.$id_rub.'&amp;tri=prixCroissant">Prix croissant</option>
				<option value="index.php?id_rub='.$id_rub.'&amp;tri=prixDecroissant">Prix décroissant</option>';
			}
			else
			{
				if($_GET['tri']=='nouveaute') echo '<option selected="selected" value="index.php?id_rub='.$id_rub.'&amp;tri=nouveaute">Nouveauté</option>';
				else echo '<option value="index.php?id_rub='.$id_rub.'&amp;tri=nouveaute">Nouveauté</option>';
				
				if($_GET['tri']=='prixCroissant') echo '<option selected="selected" value="index.php?id_rub='.$id_rub.'&amp;tri=prixCroissant">Prix croissant</option>';
				else echo '<option value="index.php?id_rub='.$id_rub.'&amp;tri=prixCroissant">Prix croissant</option>';
				
				if($_GET['tri']=='prixDecroissant') echo '<option selected="selected" value="index.php?id_rub='.$id_rub.'&amp;tri=prixDecroissant">Prix décroissant</option>';
				else echo '<option value="index.php?id_rub='.$id_rub.'&amp;tri=prixDecroissant">Prix décroissant</option>';
			}
			
			echo '</select>
				</div>';				
		}
		
		unset($_SESSION['proprietes']);
		
		//on récupère les propriétés correpondant aux articles que l'utilisateur souhaite afficher ainsi que la valeur de ces propriétés
		while($id_prod = mysql_fetch_row($result))
		{
			$result2 = mysql_query('select id_prop, valeur_prop from appartient2 where id_prod="'.$id_prod[0].'"');
			
			//on parcourt toutes les propriétés de l'article $id_prod[0]
			while($prop = mysql_fetch_assoc($result2))
			{	
				//on récupere le libelle de la propriété $prop['id_prop']
				$result3 = mysql_query('select libelle_prop from propriete where id_prop="'.$prop['id_prop'].'"');
				$libelle_prop = mysql_fetch_row($result3);

				//exemple on fait $_SESSION['proprietes']['Socket'][] = 'AM3'; à condition que la valeur AM3 ne soit pas déjà présente dans $_SESSION['proprietes']['Socket']
				if(isset($_SESSION['proprietes'][$libelle_prop[0]]))
				{
					if(array_search($prop['valeur_prop'], $_SESSION['proprietes'][$libelle_prop[0]])===false) $_SESSION['proprietes'][$libelle_prop[0]][] = $prop['valeur_prop'];
				}
				else $_SESSION['proprietes'][$libelle_prop[0]][] = $prop['valeur_prop'];
			}
		}
		
		//si on a des propriétés à afficher
		if(isset($_SESSION['proprietes']))
		{
			//on affiche les menu déroulants permettant à l'utilisateur de sélectionner les valeurs des propriétés
				echo '<div class="article">';
					echo '<form action="index.php?id_rub='.$id_rub.'" method="post">';
						foreach($_SESSION['proprietes'] as $libelle => $valeurs)
						{
							echo '<div class="propriete">
									<label style="padding-right:5px">'.$libelle.':</label>';
								echo '<select name="'.$libelle.'">
										<option>--</option>';
							foreach($valeurs as $valeur)
							{
								if(isset($_POST[$libelle]) && $_POST[$libelle]==$valeur) echo '<option selected="selected">'.$valeur.'</option>';
								else echo '<option>'.$valeur.'</option>';
							}
								echo '</select>
								</div>';
						}
						echo '<div style="float:right; clear:left"><input id="submit" name="filtre" type="submit" value="Filtrer"/></div>';
					echo '</form>';
				echo '</div>';
		}
		
		//on calcule le nombre de pages
		$result = mysql_query('select * from produit P, appartient A'.$table.' where P.id_prod=A.id_prod AND A.id_rub='.$id_rub.$filtre);
		$nbArticle = mysql_num_rows($result);
		$nbPage = ceil($nbArticle/$nbAff);

		//on récupère juste le nombre d'articles que l'on souhaite afficher
		if(!isset($_GET['tri'])) $result = mysql_query('select * from produit P, appartient A'.$table.' where P.id_prod=A.id_prod AND A.id_rub='.$id_rub.$filtre.' order by P.id_prod DESC LIMIT '.(($page-1)*$nbAff).', '.($page*$nbAff));
		else
		{
			if($_GET['tri']=='nouveaute') $result = mysql_query('select * from produit P, appartient A'.$table.' where P.id_prod=A.id_prod AND A.id_rub='.$id_rub.$filtre.' order by P.id_prod DESC LIMIT '.(($page-1)*$nbAff).', '.($page*$nbAff));
			else if($_GET['tri']=='prixCroissant') $result = mysql_query('select * from produit P, appartient A'.$table.' where P.id_prod=A.id_prod AND A.id_rub='.$id_rub.$filtre.' order by P.prix ASC LIMIT '.(($page-1)*$nbAff).', '.($page*$nbAff));
			else if($_GET['tri']=='prixDecroissant') $result = mysql_query('select * from produit P, appartient A'.$table.' where P.id_prod=A.id_prod AND A.id_rub='.$id_rub.$filtre.' order by P.prix DESC LIMIT '.(($page-1)*$nbAff).', '.($page*$nbAff));
		}
	}
	else if(isset($_POST['recherche']))
	{
		$tab1 = explode(' ', $_POST['recherche']);
		
		$result = mysql_query('select id_prod, Descriptif from produit');
		
		while($article = mysql_fetch_assoc($result))
		{
			$pertinence = 0;
			$tab2 = explode(' ', $article['Descriptif']);
			
			foreach($tab1 as $val1)
			{
				foreach($tab2 as $val2)
				{
					if(strtolower($val1) == strtolower($val2))
					{
						$pertinence++;
						break;
					}
				}
			}
			
			$resultat[$article['id_prod']] = $pertinence;
		}
		
		foreach($resultat as $id_prod=>$pertinence)
		{
			if($pertinence == 0) unset($resultat[$id_prod]);
		}
		
		asort($resultat);
		$resultat = array_reverse($resultat, true);
	}
	else //on récupère les $nbAff articles les plus récents
	{
		echo '<h1>Nouveautés</h1>';
		$result = mysql_query('select * from produit order by id_prod DESC LIMIT 0, '.$nbAff);
		$nbPage = 1;
	}

	if(!isset($_POST['recherche']))
	{
		if(mysql_num_rows($result) > 0)
		{		
			//on affiche les $nbAff premiers articles correspondants
			for($i=0; $i<$nbAff; $i++)
			{
				if($article = mysql_fetch_assoc($result))
				{
					echo '<div class="article">
							<div class="floatRight">
								<a href="panier.php?ajouter='.$article['id_prod'];
									if(isset($id_rub)) echo '&amp;id_rub='.$id_rub;
									echo '">';
									echo '<img src = "images/caddy.png" alt=""/>
									<br />
									Ajouter au panier
								</a>
							</div>
							<img src = "images/produit/'.$article['Photo'].'" alt="" class="floatLeft"/>
							<span>'.$article['Libelle'].'</span><br /><br />
							<div class="prix">'
								.$article['Prix'].' €
							</div> '
							.$article['UniteDeVente'].			
						'</div>';
				}
				else break; //il y a moins de 5 articles à afficher
			}
		}
		else echo '<br />Désolé, aucun article ne correspond à ces critères.<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>';
	}
	
	if(isset($_POST['recherche']))
	{
		echo '<h1>Résultat de la recherche:</h1>';
		
		if(count($resultat)>0)
		{
			$i = 0;

			foreach($resultat as $id_prod=>$pertinence)
			{
				if($i == $nbAff) break;
				
				$result = mysql_query('select * from produit where id_prod="'.$id_prod.'"');
				$article = mysql_fetch_assoc($result);
				
				echo '<div class="article">
							<div class="floatRight">
								<a href="panier.php?ajouter='.$article['id_prod'];
									if(isset($id_rub)) echo '&amp;id_rub='.$id_rub;
									echo '">';
									echo '<img src = "images/caddy.png" alt=""/>
									<br />
									Ajouter au panier
								</a>
							</div>
							<img src = "images/produit/'.$article['Photo'].'" alt="" class="floatLeft"/>
							<span>'.$article['Libelle'].'</span><br /><br />
							<div class="prix">'
								.$article['Prix'].' €
							</div> '
							.$article['UniteDeVente'].			
						'</div>';
						
				$i++;
			}
		}
		else echo '<br />Désolé, aucun article ne correspond à ces critères.';
	}
	
	//affichage des numéros de page en bas de page
	if(isset($id_rub))
	{
		if($nbArticle>$nbAff)
		{
			//on affiche le bouton précédent si on est pas sur la première page
			if($page!=1)
			{
				if(!isset($_GET['tri'])) echo '<div class="pageLong"><a href="index.php?id_rub='.$id_rub.'&amp;page='.($page-1).'">&lt; Précédent</a></div>';
				else echo '<div class="pageLong"><a href="index.php?id_rub='.$id_rub.'&amp;tri='.$_GET['tri'].'&amp;page='.($page-1).'">&lt; Précédent</a></div>';
			}
			
			for($i=0; $i<$nbPage; $i++)
			{
				if($page != $i+1)
				{
					if(!isset($_GET['tri'])) echo '<div class="page"><a href="index.php?id_rub='.$id_rub.'&amp;page='.($i+1).'">'.($i+1).'</a></div>';
					else echo '<div class="page"><a href="index.php?id_rub='.$id_rub.'&amp;tri='.$_GET['tri'].'&amp;page='.($i+1).'">'.($i+1).'</a></div>';
				}
				else
				{
					if(!isset($_GET['tri'])) echo '<div class="pageActive"><a href="index.php?id_rub='.$id_rub.'&amp;page='.($i+1).'">'.($i+1).'</a></div>';
					else echo '<div class="pageActive"><a href="index.php?id_rub='.$id_rub.'&amp;tri='.$_GET['tri'].'&amp;page='.($i+1).'">'.($i+1).'</a></div>';
				}
			}
			
			//on affiche le bouton suivant si on est pas sur la dernière page
			if($page!=$nbPage)
			{
				if(!isset($_GET['tri'])) echo '<div class="pageLong"><a href="index.php?id_rub='.$id_rub.'&amp;page='.($page+1).'">Suivant &gt;</a></div>';
				else echo '<div class="pageLong"><a href="index.php?id_rub='.$id_rub.'&amp;tri='.$_GET['tri'].'&amp;page='.($page+1).'">Suivant &gt;</a></div>';
			}
		}
	}
}
?>