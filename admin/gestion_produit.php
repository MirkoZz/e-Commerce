<?php
require_once("../inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

// controle du statut d'administrateur
if(!statut_admin())
{
    header('location:' . URL . 'index.php');
    exit(); // dans le cas d'une redirection le script suivant ne sera pas executé
}

//********************************************************************
//******************** SUPPRIMER UN PRODUIT **************************
//********************************************************************

if(isset($_GET['action']) && $_GET['action'] == 'supprimer')
{
    if(!empty($_GET['id_produit']))
    {
        // on récupère les informations du produit à supprimer car nous avons besoin de son src pour supprimer la photo correspondante
        $suppr_produit = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
        
        $suppr_produit->bindValue(":id_produit", $_GET['id_produit'], PDO::PARAM_STR);
        
        $suppr_produit->execute();
        
        $info_suppr_produit = $suppr_produit->fetch(PDO::FETCH_ASSOC);
        
        // chemin de la photo à supprimer
		$suppr_produit_chemin = $_SERVER["DOCUMENT_ROOT"] . '/PHP/site/' . $info_suppr_produit['photo'];

		if(!empty($info_suppr_produit['photo']) && file_exists($suppr_produit_chemin))
		{
			// si nous avons bien un chemin et si le fichier existe alors on le supprime:
			unlink($suppr_produit_chemin);
		}
        
        // suppression du produit dans la BDD        
        $suppression = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
        
        $suppression->bindValue(":id_produit", $_GET['id_produit'], PDO::PARAM_STR);
        
        $suppression->execute();
        
        // on modifie $_GET['action']
        $_GET['action'] = 'afficher';
        
        $msg .= '<div class="erreur alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Le produit a bien été supprimé</div>';
    }
}

//********************************************************************
//******************** FIN SUPPRIMER UN PRODUIT **********************
//********************************************************************


//********************************************************************
//********** AJOUTER UN PRODUIT && MODIFIER UN PRODUIT ***************
//********************************************************************


// création des variables du formulaire par défaut vide
$id_produit = '';

$reference = '';
$categorie = '';
$titre = '';
$description = '';
$couleur = '';
$taille = '';
$sexe = '';
$prix = '';
$stock= '';

$erreur = false;

if(isset($_POST['reference']) && isset($_POST['categorie']) && isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['couleur']) && isset($_POST['taille']) && isset($_POST['sexe']) && isset($_POST['prix']) && isset($_POST['stock']))
{    
    $reference = trim($_POST['reference']);
    $categorie = trim($_POST['categorie']);
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $couleur = trim($_POST['couleur']);
    $taille = trim($_POST['taille']);
    $sexe = trim($_POST['sexe']);
    $prix = trim($_POST['prix']);
    $stock = trim($_POST['stock']);
    
    // controle sur la référence car c'est un champ index unique en BDD
    $verif_reference = $pdo->prepare("SELECT * FROM produit WHERE reference = :reference");
    
    $verif_reference->bindValue(":reference", $reference, PDO::PARAM_STR);
    
    $verif_reference->execute();
    
    $infos_membre = $verif_reference->fetch(PDO::FETCH_ASSOC);
    
    if($verif_reference->rowCount() > 0 && isset($_GET['action']) && $_GET['action'] == 'ajouter')
    {
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Attention, la référence existe déjà</span><br>Veuillez vérifier votre saisie</div>';
        $erreur = true;
    }
    
    // création d'une variable vide pour le src des images dans le cas où l'utilisateur ne charge pas d'image
    $photo_bdd = '';
    
    if(isset($_POST['photo_actuelle']))
	{
		$photo_bdd = $_POST['photo_actuelle'];
	}
    
    if(!empty($_FILES['photo']['name']) && !$erreur)
    {
        // on vérifie si l'extension est valide
        if(verif_photo())
        {
            // on concatène la référence (unique) pour ne pas écraser une autre photo
            $nom_photo = $reference . $_FILES['photo']['name'];
            
            // on prépare le src que l'on va enregistrer en bdd
            $photo_bdd = "photo/" . $nom_photo;
            
            $photo_dossier = $_SERVER['DOCUMENT_ROOT'] . '/PHP/site/photo/' . $nom_photo;
            
            copy($_FILES['photo']['tmp_name'], $photo_dossier);
            // copy() permet de copier un élément depuis un emplacement (1er argument) vers un autre emplacement (2eme argument)
        }
        else
        {
            $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Attention, l\'extension de la photo n\'est pas valide</span><br>Extensions autorisées: jpg / jpeg / png / gif /svg</div>';
            $erreur = true;
        }
        
    }
    
    // vérification s'il y a eu une erreur au préalable
    if(!$erreur)
    {
        
        if(isset($_GET['action']) && $_GET['action'] == 'ajouter')
		{
		
			$enregistrement_produit = $pdo->prepare("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, sexe, prix, stock, photo) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :prix, :stock, '$photo_bdd')");
            
            $enregistrement_produit->bindValue(":reference", $reference, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":categorie", $categorie, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":titre", $titre, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":description", $description, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":couleur", $couleur, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":taille", $taille, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":sexe", $sexe, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":prix", $prix, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":stock", $stock, PDO::PARAM_STR);

            $enregistrement_produit->execute();

            // on modifie $_GET['action']
            $_GET['action'] = 'afficher';
            
            $msg .= '<div class="erreur alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Le produit a bien été ajouté</div>';
		
		}
		elseif(isset($_GET['action']) && $_GET['action'] == 'modifier')
		{
			$enregistrement_produit = $pdo->prepare("UPDATE produit SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, sexe = :sexe, prix = :prix, stock = :stock, photo = '$photo_bdd' WHERE id_produit = :id_produit");
            
			$enregistrement_produit->bindValue(":id_produit", $_POST['id_produit'], PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":reference", $reference, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":categorie", $categorie, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":titre", $titre, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":description", $description, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":couleur", $couleur, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":taille", $taille, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":sexe", $sexe, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":prix", $prix, PDO::PARAM_STR);
            $enregistrement_produit->bindValue(":stock", $stock, PDO::PARAM_STR);

            $enregistrement_produit->execute();

            // on modifie $_GET['action']
            $_GET['action'] = 'afficher';
            
            $msg .= '<div class="erreur alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Le produit a bien été modifié</div>';
		}
            
        
    }
}

//********************************************************************
//********* FIN AJOUTER UN PRODUIT && MODIFIER UN PRODUIT ************
//********************************************************************

require_once("../inc/header.inc.php");
require_once("../inc/nav.inc.php");
?>


<div class="container">
<?= $msg; ?>

  <div class="starter-template">
    <h1><span class="glyphicon glyphicon-tags mon_icone"></span> Gestion des produits</h1>
  </div>
    
    <div class="row">
        <div class="col-sm-12" style="text-align: center;">
            <hr>
            <a href="?action=ajouter" class="btn btn-info">Ajouter un produit</a>
            <a href="?action=afficher" class="btn btn-primary">Afficher les produits</a>
            <hr>
        </div>
        
        <?php 
        if(isset($_GET['action']) && ($_GET['action'] == 'ajouter' || $_GET['action'] == 'modifier'))
        {
            // on teste si l'indice id_produit dans GET, si c'est le cas on est en modification
            if(!empty($_GET['id_produit']))
            {
                $recup_produit = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
                $recup_produit->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
                $recup_produit->execute();
                
                $article_actuel = $recup_produit->fetch(PDO::FETCH_ASSOC);
                
                $id_produit = $article_actuel['id_produit'];
                $reference = $article_actuel['reference'];
                $categorie = $article_actuel['categorie'];
                $titre = $article_actuel['titre'];
                $description = $article_actuel['description'];
                $couleur = $article_actuel['couleur'];
                $taille = $article_actuel['taille'];
                $sexe = $article_actuel['sexe'];
                $prix = $article_actuel['prix'];
                $stock = $article_actuel['stock'];
                
                $photo_src = $article_actuel['photo'];
                
            }
        ?>
        
        <div class="col-sm-6 col-sm-offset-3">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="hidden" class="form-control" id="id_produit" name="id_produit" value="<?= $id_produit; ?>">
                </div>                
                <div class="form-group">
                    <label for="reference">Référence</label>
                    <input type="text" class="form-control" id="reference" placeholder="La référence du produit..." name="reference" value="<?= $reference; ?>">
                </div>
                <div class="form-group">
                    <label for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-control">
                        <option value="pantalon">Pantalon</option>
                        <option value="t_shirt" <?php if($categorie == "t_shirt") { echo 'selected'; } ?> >T-shirt</option>
                        <option value="chemise" <?php if($categorie == "chemise") { echo 'selected'; } ?>>Chemise</option>
                        <option value="pull" <?php if($categorie == "pull") { echo 'selected'; } ?>>Pull</option>
                        <option value="jupe" <?php if($categorie == "jupe") { echo 'selected'; } ?>>Jupe</option>
                        <option value="veste" <?php if($categorie == "veste") { echo 'selected'; } ?>>Veste</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" class="form-control" id="titre" placeholder="Le titre du produit..." name="titre" value="<?= $titre; ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" placeholder="La description du produit..." name="description"><?= $description; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="couleur">Couleur</label>
                    <select id="couleur" name="couleur" class="form-control">
                        <option value="noir">Noir</option>
                        <option value="blanc" <?php if($couleur == "blanc") { echo 'selected'; } ?>>Blanc</option>
                        <option value="gris" <?php if($couleur == "gris") { echo 'selected'; } ?>>Gris</option>
                        <option value="bleu" <?php if($couleur == "bleu") { echo 'selected'; } ?>>Bleu</option>
                        <option value="rouge" <?php if($couleur == "rouge") { echo 'selected'; } ?>>Rouge</option>
                        <option value="jaune" <?php if($couleur == "jaune") { echo 'selected'; } ?>>Jaune</option>
                        <option value="vert" <?php if($couleur == "vert") { echo 'selected'; } ?>>Vert</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="taille">Taille</label>
                    <select id="taille" name="taille" class="form-control">
                        <option value="xs">XS</option>
                        <option value="s" <?php if($taille == "s") { echo 'selected'; } ?>>S</option>
                        <option value="m" <?php if($taille == "m") { echo 'selected'; } ?>>M</option>
                        <option value="l" <?php if($taille == "l") { echo 'selected'; } ?>>L</option>
                        <option value="xl" <?php if($taille == "xl") { echo 'selected'; } ?>>XL</option>
                        <option value="xxl" <?php if($taille == "xxl") { echo 'selected'; } ?>>XXL</option>
                        <option value="xxxl" <?php if($taille == "xxxl") { echo 'selected'; } ?>>XXXL</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sexe">Sexe</label>
                    <select id="sexe" name="sexe" class="form-control">
                        <option value="m">Homme</option>
                        <option value="f" <?php if($sexe == "f") { echo 'selected'; } ?>>Femme</option>
                        <option value="mixte" <?php if($sexe == "mixte") { echo 'selected'; } ?>>Mixte</option>
                    </select>
                </div>
                
                <?php 
                if(isset($article_actuel))
                {
                    echo '<div>';
                    echo '<b>Photo actuelle:</b><br>';
                    
                    echo '<img src="' . URL . $photo_src . '" style="max-width: 100%;" />';
                    
                    echo '<input type="hidden" class="form-control" id="photo_actuelle" name="photo_actuelle" value="' . $photo_src . '">';
                        
                }
                ?>
                
                <div class="form-group">
                    <label for="photo">Photo</label>
                    <input type="file" class="form-control" id="photo" placeholder="" name="photo">
                </div>
                <div class="form-group">
                    <label for="prix">Prix</label>
                    <input type="text" class="form-control" id="prix" placeholder="Le prix du produit" name="prix" value="<?= $prix; ?>">
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="text" class="form-control" id="stock" placeholder="Le stock du produit..." name="stock" value="<?= $stock; ?>">
                </div>
                <button type="submit" id="valider" class="col-sm-12 btn btn-primary"><span class="glyphicon glyphicon-pencil"></span> <?php echo ucfirst($_GET['action']);  ?></button>
                <br><br><br><br><br>
            </form>
        </div><!-- Fin formulaire d'ajout -->
        <?php } // fermeture de la condition si action == 'ajouter' ?> 
        
        <?php if(isset($_GET['action']) && $_GET['action'] == 'afficher') 
        {
    
            // récupération de tous les produits en BDD
            //affichage dans un tableau HTML
            // l'image doit etre visible (donc dans un img src)
    
            // récupération des produits et affichage dans tableau
            $resultat = $pdo->query("SELECT * FROM produit");

            echo '<table class="table">';
            
            $nb_col = $resultat->columnCount();

            echo '<tr>';
            for($i = 0; $i < $nb_col; $i++)
            {
                $colonne = $resultat->getColumnMeta($i);
                echo '<th>' . ucfirst($colonne['name']) . '</th>';
            }
    
            echo '</tr>';
            while($ligne = $resultat->fetch(PDO::FETCH_ASSOC))
            {
                echo '<tr>';
                foreach($ligne AS $indice => $valeur)
                {
                    
                    if($indice == 'photo')
                    {
                        echo '<td><a href="' . URL . $valeur . '" data-lightbox="' . $ligne['id_produit'] . '" data-title="' . $ligne['titre'] .'"><img src="' . URL . $valeur . '" width="50"></a></td>';
                    
                    }
                    elseif($indice == 'taille')
                    {
                        echo '<td>' . strtoupper($valeur) . '</td>';    
                    }
                    elseif($indice == 'prix')
                    {
                        echo '<td>' . $valeur . '€</td>';
                    }
                    else
                    {
                        echo '<td>' . ucfirst($valeur) . '</td>';
                    
                    }
                }
                
                
                echo '<td><a href="?action=modifier&id_produit=' . $ligne['id_produit'] . '" class="btn btn-info btn-sm bouton_action"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>
                
                <a href="?action=supprimer&id_produit=' . $ligne['id_produit'] . '" class="btn btn-primary btn-sm bouton_action" onclick="return(confirm(\'Êtes-vous sûr? \'));" ><span class="glyphicon glyphicon-trash"></span> Supprimer</a></td>';
                
                echo '<tr>';
                
                $i++;
            }
            echo'</table>';

                echo '</tr>';
    
        }
        ?>
    
    </div><!-- div class="row" -->

</div><!-- /.container -->

<?php

require_once("../inc/footer.inc.php");
?>
<script src="<?= URL; ?>js/lightbox.js"></script>
<script>
    lightbox.option({
        'alwaysShowNavOnTouchDevices': true,
    })
</script>