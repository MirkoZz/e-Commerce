<?php

// fonction pour savoir si l'utilisateur est connecté
function connected()
{
    if(!isset($_SESSION['membre']))
    {
        return false;
    }
    else
    {
        return true;
    }
}

// pour savoir si l'utilisateur est connecté et s'il est administrateur
function statut_admin()
{
    if(connected() && $_SESSION['membre']['statut'] == 1)
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Fonction pour vérifier l'extension des photos
function verif_photo()
{
    // on récupère l'extension du fichier
    $extension = strrchr($_FILES['photo']['name'], '.');
    // strrchr() permet de découper une chaine en partant de la fin. La chaine sera découpée au niveau de la première occurence du caractère fourni (inclu) en 2ème argument

    // on tranforme l'extension en minuscule au cas ou et on coupe le '.'
    $extension = strtolower(substr($extension, 1));

    // on déclare les extensions autorisées dans un tableau ARRAY
    $tab_extension_valide = array('gif', 'jpg', 'jpeg', 'png', 'svg');

    //on vérifie si $extension correspond à une des valeurs présente dans $tab_extension_valide
    $verif_extension = in_array($extension, $tab_extension_valide);

    return $verif_extension;
}

// fonction de création du panier
function creation_panier()
{
    if(!isset($_SESSION['panier']))
    {
        $_SESSION['panier'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['reference'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['prix'] = array();
        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['taille'] = array();
        $_SESSION['panier']['photo'] = array();
    }
}

//fonction ajouter un article dans le panier
function ajout_produit_panier($id_produit, $reference, $quantite, $prix, $titre, $taille, $photo)
{
    // pour ajouter depuis une autre page que panier.php, il est possible de décomenter la ligne suivante pour que le panier soit créer depuis n'importe où
    //creation_panier();
    
    // on vérifie si l'article est déjà présent pour ne changer que sa quantité
    $position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']);
    
    if($position_produit !== false)
    {
        // produit déjà présent dans le panier donc on modifie que sa quantité
        $_SESSION['panier']['quantite'][$position_produit] += $quantite;
    }
    else
    {
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['reference'][] = $reference;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['taille'][] = $taille;
        $_SESSION['panier']['photo'][] = $photo;
    }
}

// supprimer un produit du panier
function supprimer_produit_panier($id_produit)
{
    // array_search nous renvoi l'indice du produit dans le panier
    $position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']);
    
    if($position_produit !== false)
    {
        // array_splice() permet de retirer un élément d'un tableau array et surtout de réordonner les indices afin qu'il n'y ait pas de trou.
        foreach($_SESSION['panier'] AS $indice => $valeur)
        {
            array_splice($_SESSION['panier'][$indice], $position_produit, 1);
        }
    }
}

// montant total du panier
function montant_total()
{
    $total = 0;
    
    $nb_produit = count($_SESSION['panier']['id_produit']);
    
    for($i = 0; $i < $nb_produit; $i++)
    {
        $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
    }
    return round($total, 2);
}

// classe active pour les liens du menu
function class_active($lien)
{
    $lien_self = 'http://localhost' . $_SERVER['PHP_SELF'];
    
    if($lien == $lien_self)
    {
        echo ' class="active"';
    }
    
}

// cette fonction renvoi FALSE si le mot de passe fait moins de 8 caractères, s'il ne contient pas au moins 1 majuscule, 1 minuscule et 1 caractère spécial défini dans l'expression régulière => '/[!@#$%^&*()\-_=+{};:,<.>]/'. Si tout est ok => TRUE
// Adapter au besoin
	
function checkPassword($password) {
   $reg1='/[A-Z]/';  // majuscule
   $reg2='/[a-z]/';  // minuscule
   $reg3='/[!@#$%^&*()\-_=+{};:,.]/';  // caractère spécial
   $reg4='/[0-9]/';  // chiffre

   // preg_match_all() compte le nombre d'occurence d'une expression régulière et place chaque occurence dans un tableau array défini en 3 ème argument (ici $tab) et renvoi un INT représentant le nombre d'occurence total.
   if(preg_match_all($reg1,$password, $tab)<1) return FALSE; // check si au moins une majuscule

   if(preg_match_all($reg2,$password, $tab)<1) return FALSE; // check si au moins une minuscule

   // if(preg_match_all($reg3,$password, $tab)<1) return FALSE; // check si au moins un caractère spécial

   if(preg_match_all($reg4,$password, $tab)<1) return FALSE; // check si au moins un chiffre

   if(mb_strlen($password, 'utf-8')<8) return FALSE; // check si taille inférieure à 8
   
   return TRUE;
}















