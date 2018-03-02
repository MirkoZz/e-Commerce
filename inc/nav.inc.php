<?php

$page = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1);

?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo URL; ?>index.php">MaBoutique</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li <?php class_active(URL . 'index.php'); ?>><a href="<?php echo URL; ?>index.php">Boutique</a></li>
                <li <?php class_active(URL . 'panier.php'); ?>><a href="<?php echo URL; ?>panier.php">Panier
                    <?php
                    if(isset($_SESSION['panier']['id_produit']))
                    {
                        $nb_produit = count($_SESSION['panier']['id_produit']);
                        if($nb_produit > 0)
                        {
                            echo '&nbsp;<span class="badge badge_panier">' . $nb_produit . '</span>';
                        }
                    }
                    ?>
                    </a></li>
                    
                    

                <?php if(!connected()) { ?>

                <li <?php class_active(URL . 'connexion.php'); ?>><a href="<?php echo URL; ?>connexion.php">Connexion</a></li>
                <li <?php class_active(URL . 'inscription.php'); ?>><a href="<?php echo URL; ?>inscription.php">Inscription</a></li>

                <?php } else {?>

                <li <?php class_active(URL . 'profil.php'); ?>><a href="<?php echo URL; ?>profil.php">Profil</a></li>
                <li <?php class_active(URL . 'deconnexion.php'); ?>><a href="<?php echo URL; ?>connexion.php?action=deconnexion">Déconnexion</a></li>

                <?php }

                // si l'utilisateur est admin alors on lui affiche les liens des pages d'administration
                if(statut_admin()) { ?>

                <li id="btn_admin"<?php class_active(URL . 'admin/gestion_produit.php'); class_active(URL . 'admin/gestion_membre.php'); class_active(URL . 'admin/gestion_commande.php');?>>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Admin <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" id="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="<?php echo URL; ?>admin/gestion_produit.php">Gestion produit</a></li>
                            <li><a href="<?php echo URL; ?>admin/gestion_membre.php">Gestion membre</a></li>
                            <li><a href="<?php echo URL; ?>admin/gestion_commande.php">Gestion commande</a></li>
                        </ul>
                    </div>
                </li>

                <?php } ?>


            </ul>
        </div><!--/.nav-collapse -->
      </div>
</nav>
