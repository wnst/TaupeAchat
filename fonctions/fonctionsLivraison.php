<?php
function verifierAdresse()
{
	if(isset($_SESSION['erreur'])) unset($_SESSION['erreur']);
	
	//si l'utilisateur a validé le forumulaire de livraison
	if(isset($_POST['livraison']))
	{
		extract($_POST);

		foreach($_POST as $key => $val)
		{
			//on vérifie qu'aucun champ n'est vide
			if(str_replace(' ', '', $val) == '') $_SESSION['erreur'][$key] = 'Entrée invalide.';
		}
		
		if(!preg_match('/^[0-9]{5}$/', $cp))
		{
			if(!isset($_SESSION['erreur']['cp'])) $_SESSION['erreur']['cp'] = 'Code postal invalide.';
		}
		
		if(!preg_match('/^0([0-9]{9}$)/', $telephone))
		{
			if(!isset($_SESSION['erreur']['telephone'])) $_SESSION['erreur']['telephone'] = 'Numéro de téléphone invalide.';
		}
		
		//si l'adresse a été complété sans erreur alors on redirige l'utilisateur vers le paiement
		if(!isset($_SESSION['erreur']))
		{
			//on sauvegarde l'adresse de livraison souhaitée par le client
			$_SESSION['livraison']['civilite'] = $civilite;
			$_SESSION['livraison']['nom'] = $nom;
			$_SESSION['livraison']['prenom'] = $prenom;
			$_SESSION['livraison']['adresse'] = $adresse;
			$_SESSION['livraison']['cp'] = $cp;
			$_SESSION['livraison']['ville'] = $ville;
			$_SESSION['livraison']['telephone'] = $telephone;
			
			header('Location: paiement.php');
		}
	}
}

function afficherFormulaire($data)
{
	foreach($data as $elt)
	{
		echo '<br /><br />
			<label for="'.$elt['name'].'" class="formLabel">'.$elt['label'].'</label>';
			//s'il y a une erreur concernant cet input alors on affiche l'erreur à côté de l'input
			if(isset($_SESSION['erreur'][$elt['name']])) echo '<input class="'.$elt['class'].'" type="'.$elt['type'].'" id="'.$elt['name'].'" name="'.$elt['name'].'"/><span class="erreur">'.$_SESSION['erreur'][$elt['name']].'</span>';
			else
			{	//s'il n'y a pas d'erreur alors on initialise l'input avec la valeur entrée par l'utilisateur (pour le else il n'y a pas de valeur à afficher car l'utilisateur vient d'arriver sur la page)
				if(isset($_POST[$elt['name']])) echo '<input class="'.$elt['class'].'" type="'.$elt['type'].'" id="'.$elt['name'].'" name="'.$elt['name'].'" value="'.$_POST[$elt['name']].'"/>';
				else echo '<input class="'.$elt['class'].'" type="'.$elt['type'].'" id="'.$elt['name'].'" name="'.$elt['name'].'" value="'.$elt['value'].'"/>';
			};
	}
}

function afficherLivraison()
{
	echo '<h1>Etape 1/2</h1><br /><br />
	<form action="livraison.php" method="post">
		<div>
			
			<div class="formCadre">
				<h2>Adresse de livraison</h2>';
				$result = mysql_query('select civilite, nom, prenom, adresse, cp, ville, telephone from utilisateur where id="'.$_SESSION['id'].'"');
				$adresse = mysql_fetch_assoc($result);
				echo '<br /><br /><label for="civilite" class="formLabel">Civilité:</label>';
				
				if($adresse['civilite']!='M') echo '<input type="radio" name="civilite" value="M" style="margin-left:0px"/>M.';
				else echo '<input type="radio" name="civilite" value="M" checked="checked" style="margin-left:0px"/>M.';

				if($adresse['civilite']!='Mme') echo '<input type="radio" name="civilite" value="Mme"/>Mme.';
				else echo '<input type="radio" name="civilite" value="Mme" checked="checked"/>Mme.';
				
				if($adresse['civilite']!='Mlle') echo '<input type="radio" name="civilite" value="Mlle"/>Mlle.';
				else echo '<input type="radio" name="civilite" value="Mlle" checked="checked"/>Mlle.';
				
				$data[] = array('name'=>'nom', 'type'=>'text', 'label'=>'Nom:', 'class'=>'inputMoyen', 'value'=>$adresse['nom']);				
				$data[] = array('name'=>'prenom', 'type'=>'text', 'label'=>'Prénom:', 'class'=>'inputMoyen', 'value'=>$adresse['prenom']);				
				$data[] = array('name'=>'adresse', 'type'=>'text', 'label'=>'Adresse:', 'class'=>'inputGrand', 'value'=>$adresse['adresse']);			
				$data[] = array('name'=>'cp', 'type'=>'text', 'label'=>'Code postal:', 'class'=>'inputPetit', 'value'=>$adresse['cp']);
				$data[] = array('name'=>'ville', 'type'=>'text', 'label'=>'Ville:', 'class'=>'inputMoyen', 'value'=>$adresse['ville']);
				$data[] = array('name'=>'telephone', 'type'=>'text', 'label'=>'Téléphone:', 'class'=>'inputMoyen', 'value'=>$adresse['telephone']);
				afficherFormulaire($data);
				unset($data);
		echo'</div><br />
			
			<input id="submit" name="livraison" type="submit" value="Valider" style="margin:0px"/>
			
		</div>
	</form>';
}
?>