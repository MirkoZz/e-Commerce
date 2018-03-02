<?php
require_once('inc/init.inc.php');

if(isset($_POST['quantite']) && isset($_POST['id_produit']))
{
    $position = array_search($_POST['id_produit'], $_SESSION['panier']['id_produit']);
    
    $_SESSION['panier']['quantite'][$position] = $_POST['quantite'];
    echo 'ok';
}