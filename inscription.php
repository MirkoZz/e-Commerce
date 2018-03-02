<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$email = '';
$sexe = '';
$ville = '';
$code_postal = '';
$adresse = '';

if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['verif_mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['sexe']) && isset($_POST['ville']) && isset($_POST['code_postal']) && isset($_POST['adresse']))
{
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);
    $verif_mdp = trim($_POST['verif_mdp']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $sexe = trim($_POST['sexe']);
    $ville = trim($_POST['ville']);
    $code_postal = trim($_POST['code_postal']);
    $adresse = trim($_POST['adresse']);
    
    //mise en place d'une variable pour controler les éventuelles erreurs
    $erreur = false;
    
    // controle sur la taille du pseudo
    if(iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 20)
    {
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Attention, le pseudo doit avoir entr 4 et 20 caractères inclus<span><br>Veuillez vérifier votre saisie</div>';
        $erreur = true;
    }
    
    // controle sur les caractères autorisés dans le pseudo
    if(!preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo))
    {
        // preg_match() permet de tester les caractères dans le pseudo (2ème argument) selon une expression régulière fournie en 1er argument.
        // ^ indique le début de la chaine sinon la chaine pourrait commencer par autre chose
        // + permet d'avoir plusieurs fois le même caractère dans la chaine
        // $ indique la fin de la chaine sinon la chaine pourrait terminer par autre chose
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Attention, caractères autorisés pour le pseudo: A à Z 0 à 9 et - . _</span><br>Veuillez vérifier votre saisie</div>';
        $erreur = true;
    }
    
    // controle sur l'existence du pseudo en BDD car c'est champs index unique !
    $controle_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    
    $controle_pseudo->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
    
    $controle_pseudo->execute();
    
    if($controle_pseudo->rowCount() > 0)
    {
        // si on obtient 1 ligne de résultat, alors le pseudo existe en BDD
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Pseudo indisponible</span><br>Veuillez vérifier votre saisie</div>';
        $erreur = true;
    }
    
    // controle sur le format du mail
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Email incorrect</span><br>Veuillez vérifier votre saisie</div>';
        $erreur = true;
    }
    
    // controle sur le mdp a 1 majuscule, 1 minuscule, 1 chiffre et une taille entre 8 et 14 caractères 
    if(!checkPassword($mdp))
    {
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et une taille entre 8 et 14 caractères inclus</span><br>Veuillez vérifier votre saisie</div>';
        $erreur = true;  
    }
    
    // Controle de la correspondance des mdp
    if($mdp != $verif_mdp)
    {
        $msg .= '<div class="erreur alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">Vos mots de passe ne correspondent pas</span><br>Veuillez recommencer</div>';
        $erreur = true;    
    }
    else
    {
        // traitement du mdp avec un hash
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    }
    
    // enregistrement de l'utilisateur si tous les controles sont ok
    if(!$erreur)
    {
        // si $erreur est égal à false alors il n'y a pas eu d'erreur dans nos traitements préalables, on peut lancer le INSERT INTO
        $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, sexe, ville, code_postal, adresse, statut) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :sexe, :ville, :code_postal, :adresse, 0)");
            
        $enregistrement->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
        $enregistrement->bindValue(":mdp", $mdp, PDO::PARAM_STR);
        $enregistrement->bindValue(":nom", $nom, PDO::PARAM_STR);
        $enregistrement->bindValue(":prenom", $prenom, PDO::PARAM_STR);
        $enregistrement->bindValue(":email", $email, PDO::PARAM_STR);
        $enregistrement->bindValue(":sexe", $sexe, PDO::PARAM_STR);
        $enregistrement->bindValue(":ville", $ville, PDO::PARAM_STR);
        $enregistrement->bindValue(":code_postal", $code_postal, PDO::PARAM_STR);
        $enregistrement->bindValue(":adresse", $adresse, PDO::PARAM_STR);   
        
        $enregistrement->execute();
        
        // si tout est ok alors on redirige sur la page de connexion
        header('location:connexion.php');
    }
}

require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");

//echo '<pre>'; var_dump($_POST); echo '</pre>';

?>

<div class="container">
<?= $msg; ?>

    <div class="starter-template">
        <h1><span class="glyphicon glyphicon-pencil mon_icone"></span> Inscription</h1>
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
                    <input type="password" class="form-control" id="mdp" placeholder="Votre mot de passe ..." name="mdp" value="">
                </div>
                <div class="form-group">
                    <label for="verif_mdp">Vérification mot de passe</label>
                    <input type="password" class="form-control" id="mdp" placeholder="Votre mot de passe ..." name="verif_mdp" value="">
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" placeholder="Votre nom ..." name="nom" value="<?= $nom ?>">
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" placeholder="Votre prénom ..." name="prenom" value="<?= $prenom ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" placeholder="Votre email ..." name="email" value="<?= $email ?>">
                </div>
                <div class="form-group">
                    <label for="sexe">Sexe</label>
                    <select id="sexe" name="sexe" class="form-control">
                        <option value="m">Homme</option>
                        <option value="f" <?php if($sexe == "f") { echo 'selected'; } ?> >Femme</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" class="form-control" id="ville" placeholder="Votre ville ..." name="ville" value="<?= $ville ?>">
                </div>
                <div class="form-group">
                    <label for="code_postal">Code postal</label>
                    <input type="text" class="form-control" id="code_postal" placeholder="Votre code postal ..." name="code_postal" value="<?= $code_postal ?>">
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse"><?= $adresse ?></textarea>
                </div>
                <hr>
                <button type="submit" name="inscription" id="inscription" class="col-sm-12 btn btn-primary"><span class="glyphicon glyphicon-pencil"></span> Inscription</button>
            </form>
            
            <br>
            <br>
            <br>
            
        </div>
    </div>
    
    

</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");