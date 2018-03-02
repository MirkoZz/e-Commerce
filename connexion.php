<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

// si l'utilisateur demande une déconnexion
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{
    session_destroy();
    // OU unset($_SESSION['membre']);
}

// si l'utilisateur est déjà connecté, on redirige sur profil.php
if(connected())
{
    header('location:profil.php');
}

$pseudo = '';
$mdp = '';

if(isset($_POST['pseudo']) && isset($_POST['mdp']))
{
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);
    
    $resultat = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    
    $resultat->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
    
    $resultat->execute();
    
    $infos_membre = $resultat->fetch(PDO::FETCH_ASSOC);
    
    if($resultat->rowCount() == 0)
    {
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Pseudo ou mot de passe incorrect</span><br>Veuillez vérifier votre saisie</div>';
    }
    else
    {
        if(password_verify($mdp, $infos_membre['mdp'])){

            $_SESSION['membre'] = array();
            $_SESSION['membre']['id_membre'] = $infos_membre['id_membre'];
            $_SESSION['membre']['pseudo'] = $infos_membre['pseudo'];
            $_SESSION['membre']['nom'] = $infos_membre['nom'];
            $_SESSION['membre']['prenom'] = $infos_membre['prenom'];
            $_SESSION['membre']['email'] = $infos_membre['email'];
            $_SESSION['membre']['sexe'] = $infos_membre['sexe'];
            $_SESSION['membre']['ville'] = $infos_membre['ville'];
            $_SESSION['membre']['code_postal'] = $infos_membre['code_postal'];
            $_SESSION['membre']['adresse'] = $infos_membre['adresse'];
            $_SESSION['membre']['statut'] = $infos_membre['statut'];
            
            header('location:profil.php');
            
        }else{
            $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Pseudo ou mot de passe incorrect</span><br>Veuillez vérifier votre saisie</div>';
        }     
    }
}

require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");
?>

<div class="container">
<?= $msg; ?>

    <div class="starter-template">
    <h1><span class="glyphicon glyphicon-check mon_icone"></span> Connexion</h1>
    </div>
    
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 ">
            <form method="post" action="">
                <div class="form-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" class="form-control" id="pseudo" placeholder="Votre pseudo ..." name="pseudo" value="<?= $pseudo ?>">
                </div>
                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <input type="password" class="form-control" id="mdp" placeholder="Votre mot de passe ..." name="mdp" value="<?= $mdp ?>">
                </div>
                <button type="submit" name="connexion" id="connexion" class="col-sm-12 btn btn-primary"><span class="glyphicon glyphicon-pencil"></span> Connexion</button>
            </form>
            <br><br>
        </div>
    </div>

</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");