<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

// récupération des produits dans la BDD
$liste_produit = $pdo->query("SELECT * FROM produit");

// récupération de la liste des catégories dans la BDD
$liste_categorie = $pdo->query("SELECT DISTINCT categorie FROM produit");

// récupération de la liste des couleurs dans la BDD
$liste_couleur = $pdo->query("SELECT DISTINCT couleur FROM produit");

// récupération de la liste des sexes dans la BDD
$liste_sexe = $pdo->query("SELECT DISTINCT sexe FROM produit");


// récupération des produits selon la catégorie 
$liste_produit_categorie = $pdo->prepare("SELECT * FROM produit WHERE categorie=:categorie");

$categorie_en_cours = '';

if(isset($_GET['categorie']))
{
   $categorie_en_cours = $_GET['categorie']; 
}

$liste_produit_categorie->bindValue(":categorie", $categorie_en_cours, PDO::PARAM_STR);

$liste_produit_categorie->execute();

// récupération des produits selon la couleur
$liste_produit_couleur = $pdo->prepare("SELECT * FROM produit WHERE couleur=:couleur");

$couleur_en_cours = '';

if(isset($_GET['couleur']))
{
   $couleur_en_cours = $_GET['couleur']; 
}

$liste_produit_couleur->bindValue(":couleur", $couleur_en_cours, PDO::PARAM_STR);

$liste_produit_couleur->execute();




require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");

?>

<div class="container">
<?= $msg; ?>

  <div class="starter-template">
    <h1><span class="glyphicon glyphicon-home mon_icone"></span> Boutique</h1>
  </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="list-group">
                <?php
                // affichage du menu catégorie
                if(!isset($_GET['categorie']) || (isset($_GET['categorie']) && $_GET['categorie'] == 'all'))
                {
                    echo '<a class="list-group-item active" href="?categorie=all" >Toutes Catégories</a>';
                }
                else
                {
                    echo '<a class="list-group-item" href="?categorie=all" >Toutes Catégories</a>';
                }
                while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC))
                {
                    if(isset($_GET['categorie']) && $_GET['categorie'] == $categorie['categorie'])
                    {
                        echo '<a class="list-group-item active" href="?categorie=' . $categorie['categorie'] . '">' . ucfirst($categorie['categorie']) . '</a>';
                    }
                    else
                    {
                        echo '<a class="list-group-item" href="?categorie=' . $categorie['categorie'] . '">' . ucfirst($categorie['categorie']) . '</a>';
                    }
                }
                ?>
            </div>
        </div>
        <div class="col-sm-1"></div>
            
        <div class="col-sm-8 catalogue">
            <?php
            // affichage des produits
            echo '<div class="row">';
            $i = 1; // variable de controle pour connaitre le nombre de tours dans la boucle
            
            if(!isset($_GET['categorie']) || $_GET['categorie'] == 'all')
            {
                $affichage = $liste_produit;
            }
            else
            {
                $affichage = $liste_produit_categorie;
            }
            while($catalogue = $affichage->fetch(PDO::FETCH_ASSOC))
            {            
                echo '<div class="thumbnail col-sm-3 fiche_produit">';
                echo '<img src="' . URL . $catalogue['photo'] . '" alt="' . $catalogue['categorie'] . '">';
                echo '<div class="caption detail">';
                echo '<div>';
                echo '<h3>' . $catalogue['titre'] . '</h3>';
                echo '<p>' . $catalogue['description'] . '</p>';
                echo '</div>';
                echo '<p class="prix">' . $catalogue['prix'] . '€</p>';
                echo '</div>';
                echo '<p><a href="' . URL . 'fiche_article.php?id_produit=' . $catalogue['id_produit'] . '" class="btn btn-primary bouton_boutique" role="button">Voir le produit</a>'; 
                echo '</div>';
                
                if($i % 4 == 0)
                {
                    echo '</div><div class="row">';
                }
                $i++;
            }
            echo '</div>';
            ?>
        </div>
        
    </div>
</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");
?>
    
<script src="<?= URL; ?>js/lightbox.js"></script>
<script>
   
</script>