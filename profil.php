<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

// si l'utilisateur est déjà connecté, on redirige sur profil.php
if(!connected())
{
    header('location:connexion.php');
}





require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");

?>

<div class="container">
<?= $msg; ?>

    <div class="starter-template">
        <h1><span class="glyphicon glyphicon-user mon_icone"></span> Profil</h1>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="list-group">
                <span class="list-group-item disabled">
                    <span class="label_profil">Bonjour</span> <h3>
                    <?php echo $_SESSION['membre']['nom'] . ' ' . $_SESSION['membre']['prenom']; ?></h3>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Pseudo:</span> <b>
                    <?php echo $_SESSION['membre']['pseudo']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Nom:</span> <b>
                    <?php echo $_SESSION['membre']['nom']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Prénom:</span> <b>
                    <?php echo $_SESSION['membre']['nom']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Email:</span> <b>
                    <?php echo $_SESSION['membre']['email']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Sexe:</span> <b>
                    <?php echo $_SESSION['membre']['sexe']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Ville:</span> <b>
                    <?php echo $_SESSION['membre']['ville']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Code postal:</span> <b>
                    <?php echo $_SESSION['membre']['code_postal']; ?></b>
                </span>
                <span class="list-group-item">
                    <span class="label_profil">Adresse:</span> <b>
                    <?php echo $_SESSION['membre']['adresse']; ?></b>
                </span>
            </div>
        </div>
        
        <div class="col-sm-6">
            <img id="photo_profil" src="img/photo_profil.png" alt="photo de profil" class="img-thumbnail">
            
            <p class="label_profil">Votre statut: 
                <?php
                if(statut_admin())
                { 
                    echo 'Administrateur'; 
                }
                else
                {
                    echo 'Membre';
                }
                ?>
            </p>
        </div>
        
    
    
    
        

</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");
