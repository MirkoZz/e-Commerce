<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

// on vérifie si id_produit est présent dans l'URL
if(!isset($_GET['id_produit']))
{
    header("location:index.php");
    exit(); // bloque l'execution du code qui suit
}

// récupération de l'id_produit dans l'url
if(isset($_GET['id_produit']))
{
    $id_produit = $_GET['id_produit'];
}

// récupération des informations du produit dans la BDD
$req_info_produit = $pdo->prepare("SELECT * FROM produit WHERE id_produit=:id_produit");

$req_info_produit->bindValue(":id_produit", $id_produit, PDO::PARAM_STR);

$req_info_produit->execute();

// on vérifie si on récupère bien un produit
if($req_info_produit->rowCount() <= 0)
{
    header("location:index.php");
    exit(); // bloque l'execution du code qui suit
}

$info_produit = $req_info_produit->fetch(PDO::FETCH_ASSOC);

$taille = $info_produit['taille'];

require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");

?>

<div class="container-fluid">
<?= $msg; ?>

    <div class="starter-template">
    <h1><span class="glyphicon glyphicon-tag mon_icone"></span> <?= $info_produit['titre'] ?></h1>
    </div>
    
    <div class="row">
        <div class="col-sm-5 col-sm-offset-2">
            <div class="thumbnail">
                <a href="<?= URL . $info_produit['photo'] ?>" data-lightbox="<?= $info_produit['id_produit'] ?>" data-title="<?= $info_produit['titre']?>"><img src="<?= $info_produit['photo'] ?>" alt="photo de <?= $info_produit['categorie'] ?>"></a>
            </div>
        </div>
        <div class="col-sm-4 fiche_article">
            <div class="thumbnail ">
                <div class="caption detail">
                    <div>
                        <h3><?= $info_produit['titre'] ?></h3>
                        <p><?= $info_produit['description'] ?></p>
                    </div>
                    
                    <p class="prix"><?= $info_produit['prix'] ?>€</p>
                </div>
                <p><span class="titre">Catégorie:</span> <?= ucfirst($info_produit['categorie']) ?></p>
                <p><span class="titre">Couleur:</span> <?= ucfirst($info_produit['couleur']) ?></p>
                <p><span class="titre">Sexe:</span> <?= ucfirst($info_produit['sexe']) ?></p>
                <p><span class="titre">Stock disponible:</span> <?= $info_produit['stock'] ?></p>
                <form method="post" action="panier.php">
                    <div class="form-group">
                        <label for="taille">Taille</label>
                        <select id="taille" name="taille" class="form-control">
                            <option value="xs">XS</option>
                            <option value="s" <?php if($taille == "s") { echo 'selected'; } ?> >S</option>
                            <option value="m" <?php if($taille == "m") { echo 'selected'; } ?> >M</option>
                            <option value="l" <?php if($taille == "l") { echo 'selected'; } ?> >L</option>
                            <option value="xl" <?php if($taille == "xl") { echo 'selected'; } ?> >XL</option>
                            <option value="xxl" <?php if($taille == "xxl") { echo 'selected'; } ?> >XXL</option>
                            <option value="xxxl" <?php if($taille == "xxxl") { echo 'selected'; } ?> >XXXL</option>
                        </select>
                    </div>
                    
                    <?php
                    echo '<input type="hidden" name="id_produit" id="id_produit" value="' . $id_produit . '">';
                    
                    if($info_produit['stock'] > 0)
                    {
                    ?>
                    <div class="form-group">
                        <label for="quantite">Quantité</label>
                        <select id="quantite" name="quantite" class="form-control">
                            <?php
                            for($i = 1; $i <= $info_produit['stock'] && $i <= 5; $i++)
                            {
                                echo '<option>' . $i . '</option>';
                            }

                            ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" name="ajout_panier"><span class="glyphicon glyphicon-shopping-cart"></span> Ajouter au panier</button>
                    
                    <?php
                    }
                    else
                    {
                        echo '<p id="rupture">Rupture de stock sur ce produit</p>';
                    }
                    ?>
                </form>
                
            </div>
            <?php
            if(isset($_SERVER['HTTP_REFERER']))
            {
                echo '<a href="'. $_SERVER['HTTP_REFERER'] . '" class="btn btn-primary">Retour vers votre selection</a>';
            }
            else
            {
                echo '<a href="index.php?categorie=' . $info_produit['categorie'] . '" class="btn btn-primary">Retour vers votre selection</a>';    
            }
            ?>

            
                
           
        </div>
    </div>
</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");
?>

<script src="<?= URL; ?>js/lightbox.js"></script>
<script>
    lightbox.option({
        'alwaysShowNavOnTouchDevices': true,
    })
</script>