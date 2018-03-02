<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

//vider le panier
if(isset($_GET['action']) && $_GET['action'] == 'vider')
{
    unset($_SESSION['panier']);
    header('location:panier.php');
}

// payer le panier
$erreur = false;

if(isset($_GET['action']) && $_GET['action'] == 'payer' && !empty($_SESSION['panier']['id_produit']))
{
    // l'utilisateur a cliqué sur le bouton Finaliser votre commande
    //vérification du stock pour chacun des produits présents dans le panier
    $nb_produit = sizeof($_SESSION['panier']['id_produit']);
    for($i = 0; $i < $nb_produit; $i++)
    {
        $id_verif_stock = $_SESSION['panier']['id_produit'][$i];
        $produit = $pdo->query("SELECT * FROM produit WHERE id_produit = $id_verif_stock");
        $info_produit = $produit->fetch(PDO::FETCH_ASSOC);
        if($info_produit['stock'] < $_SESSION['panier']['quantite'][$i])
        {
            // si on rentre dans cette condition alors il y a un soucis car le stock est inférieur à la quantité demandée
            // 2 possibilités: stock à 0 ou stock inférieur
            if($info_produit['stock'] > 0)
            {
                // il reste du stock mais moins que la quantité demandée
                $_SESSION['panier']['quantite'][$i] = $info_produit['stock'];
                $msg .= '<div class="erreur alert alert-warning alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Attention, la quantité du produit ' . $_SESSION['panier']['reference'][$i] . ' a été réduite car notre stock est insuffisant</span><br>Veuillez vérifier vos achats</div>';
                $erreur = true;
            }
            else
            {
                // le stock est à 0 on supprime le produit du panier
                $_SESSION['panier']['quantite'][$i] = $info_produit['stock'];
                $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Attention, le produit ' . $_SESSION['panier']['reference'][$i] . ' a été retiré de votre commande car le produit est en rupture de stock</span><br>Veuillez vérifier vos achats</div>';
                $erreur = true;
                
                supprimer_produit_panier($id_verif_stock);
                // on enlève 1 à la variable $i pour ne pas rater le produit ayant le nouvel indice
                $i--;
                // on enlève 1 à $nb_produit pour faire un tour de moins
                $nb_produit--;
            }
        }
    }
    if(!$erreur)
    {
        // s'il n'y a pas eu d'erreur, on lance l'enregistrement des commandes en bdd
        $id_membre = $_SESSION['membre']['id_membre'];
        $total = montant_total();
        $pdo->query("INSERT INTO commande (id_membre, montant, date_enregistrement) VALUES ($id_membre, $total, NOW())");
        
        $id_commande = $pdo->lastInsertID(); // on récupère l'id crée par la dernière requête 
        
        $nb_produit = count($_SESSION['panier']['id_produit']);
        for($i = 0; $i < $nb_produit; $i++)
        {
            $id_produit_en_cours = $_SESSION['panier']['id_produit'][$i];
            $quantite_en_cours = $_SESSION['panier']['quantite'][$i];
            $prix_en_cours = $_SESSION['panier']['prix'][$i];
            $pdo->query("INSERT INTO details_commande (id_commande, id_produit, quantite, prix) VALUES ($id_commande, $id_produit_en_cours, $quantite_en_cours, $prix_en_cours)");
            
            // mise à jour du stock pour chaque produit
            $pdo->query("UPDATE produit SET stock = stock - $quantite_en_cours WHERE id_produit = $id_produit_en_cours");
        }
        unset($_SESSION['panier']);
        header('location:panier.php');
    }
}

// création panier
creation_panier();

// retirer un produit du panier
if(isset($_GET['action']) && $_GET['action'] == 'retirer' && !empty($_GET['id_produit']))
{
    supprimer_produit_panier($_GET['id_produit']);
}


// ajouter panier
if(isset($_POST['ajout_panier']) && isset($_POST['id_produit']) && isset($_POST['quantite']))
{
    $id_produit = $_POST['id_produit'];
    $quantite = $_POST['quantite'];
    
    $recup_prix = $pdo->prepare("SELECT * FROM produit WHERE id_produit=:id_produit");
    
    $recup_prix->bindValue('id_produit', $id_produit, PDO::PARAM_STR);
    
    $recup_prix->execute();
    
    $info_produit = $recup_prix->fetch(PDO::FETCH_ASSOC);
    
    ajout_produit_panier($id_produit, $info_produit['reference'], $quantite, $info_produit['prix'], $info_produit['titre'], $info_produit['taille'], $info_produit['photo']);
    
    // on redirie sur la même page pour perdre les informations dans POST pour éviter de rajouter le même produit avec F5
    header("location:panier.php");
}



/*echo '<pre>'; var_dump($_SESSION['panier']); echo '</pre>';*/


require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");

/*echo'<pre>'; var_dump($_SESSION); echo'</pre>';
echo'<pre>'; var_dump($_POST); echo'</pre>';*/
?>

<div class="container">
<?= $msg; ?>

    <div class="starter-template">
    <h1><span class="glyphicon glyphicon-shopping-cart mon_icone"></span> Panier</h1>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <!-- Affichage du panier -->
            <table class="table table_bordered">
                <tr>
                    <th>Référence</th>
                    <th>Titre</th>
                    <th>Taille</th>
                    <th>Photo</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                </tr>
                <?php
                $nb_produit = count($_SESSION['panier']['id_produit']);
                
                if(count($_SESSION['panier']['id_produit']) > 0)
                {
                    $total = 0;
                    for($i = 0; $i < $nb_produit; $i++)
                    {
                        
                        echo '<tr>';
                            echo '<td>' . $_SESSION['panier']['reference'][$i] . '</td>';
                            echo '<td>' . $_SESSION['panier']['titre'][$i] . '</td>';
                            echo '<td>' . strtoupper($_SESSION['panier']['taille'][$i]) . '</td>';
                            echo '<td><img src="' . $_SESSION['panier']['photo'][$i] . '" width="50"></td>';
                            echo '<td class="quantite" data-num="' . $_SESSION['panier']['id_produit'][$i] . '">' . $_SESSION['panier']['quantite'][$i] . '</td>';
                            echo '<td>' . $_SESSION['panier']['prix'][$i] . '€</td>';
                            echo '<td><a href="?action=retirer&id_produit=' . $_SESSION['panier']['id_produit'][$i] . '" class="btn btn-primary btn-sm bouton_action" onclick="return(confirm(\'Êtes-vous sûr? \'));" ><span class="glyphicon glyphicon-trash"></span> Retirer</a></td>';
                        echo '</tr>';
                    }

                    echo '<tr>';
                        echo '<td colspan="4" id="total"><b>Total: </b>' . montant_total() . '€</td>';
                        echo '<td colspan="2"></td>';
                        echo '<td  style="text-align: left;"><a href="?action=vider" class="btn btn-primary" onclick="return(confirm(\'Êtes-vous sûr? \'));">Vider le panier</a></td>';
                        
                    echo '</tr>';
                }
                else
                {
                    echo '<tr><td colspan="6" id="panier_vide">Votre panier est vide</td></tr>';
    
                    echo '<tr><td id="retour_boutique"><a href="index.php" class="btn btn-primary">Retour à la boutique</a></td></tr>';
                }
                ?>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php
            if(connected())
            {
            ?>
                <div class="list-group">
                    <span class="list-group-item disabled">
                        <h3 class="label_profil">Informations de livraison</h3>
                    </span>
                    <span class="list-group-item">
                        <span class="label_profil">Nom:</span> <b>
                        <?php echo $_SESSION['membre']['nom']; ?></b>
                    </span>
                    <span class="list-group-item">
                        <span class="label_profil">Prénom:</span> <b>
                        <?php echo $_SESSION['membre']['prenom']; ?></b>
                    </span>
                    <span class="list-group-item">
                        <span class="label_profil">Email:</span> <b>
                        <?php echo $_SESSION['membre']['email']; ?></b>
                    </span>
                    <span class="list-group-item">
                        <span class="label_profil">Adresse:</span> <b>
                        <?php echo $_SESSION['membre']['adresse']; ?></b>
                    </span>
                    <span class="list-group-item">
                        <span class="label_profil">Code postal:</span> <b>
                        <?php echo $_SESSION['membre']['code_postal']; ?></b>
                    </span>
                    <span class="list-group-item">
                        <span class="label_profil">Ville:</span> <b>
                        <?php echo $_SESSION['membre']['ville']; ?></b>
                    </span>
                </div>
            <?php
                if($nb_produit > 0)
                {
                    echo '<a href="?action=payer" class="btn btn-primary" onclick="return(confirm(\'Êtes-vous sûr? \'));">Finaliser votre commande</a>';
                }
            }
            else
            {
            ?>
                <div id="connexion_panier">
                    <h3>Veuillez vous connecter.</h3>
                    <p><a href="connexion.php" class="btn btn-primary">Connexion</a> <a href="inscription.php" class="btn btn-primary">Inscription</a></p>
                </div>
                
            <?php   
            }
            
            
            ?>
        </div>
    </div>

</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");
?>
<script>
    // récupération de la cellule du tableau contenant la quantité
    var cellQuantity = document.getElementsByClassName("quantite");
    
    console.log(cellQuantity);
    
    // le nombre déléments dans le tableau contenant les éléments ayants la classe "quantite"
    var tabLength = cellQuantity.length;
    
    for(var i = 0; i < tabLength; i++)
    {
        cellQuantity[i].addEventListener("click", function () {
            console.log(this.textContent);
        });
        cellQuantity[i].addEventListener("dblclick", function () {
            //console.log(this.textContent);
            this.innerHTML = '<input type="text" value="" id="quantityChange" style="width: 100%;">';
            document.getElementById("quantityChange").addEventListener("blur", function () {
                var newQuantity = this.value;
                var id = this.parentNode.dataset.num;
                this.parentNode.innerHTML = newQuantity;
                
                //ajax
                var file = 'ajax.php';
                if(window.XMLHttpRequest) {
                    var xhttp = new XMLHttpRequest();
                }else{
                    var xhttp = new ActiveXObject("Microsoft.XMLHttp");
                }
                
                var param ="quantite="+newQuantity+'&id_produit='+id;
                console.log(param);
                xhttp.open("POST", file, true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                
                xhttp.onreadystatechange = function () {
                    console.log(xhttp.status);
                    console.log(xhttp.readyState);
                    if(xhttp.status == 200 && xhttp.readyState == 4) {
                        console.log(xhttp.responseText);
                    }
                }
                xhttp.send(param);
                
                /*$post("ajax/test.html", function)*/
                
            });
            
        });
        
        
    }
    
    

</script>


