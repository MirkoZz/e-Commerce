<?php
// connexion à la BDD
$pdo = new PDO(
    'mysql:host=db725265053.db.1and1.com;dbname=db725265053;port=3306',
    'dbo725265053',
    'Fred@11210032',
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
);

// déclaration d'une variable nous permettant de passer des messages à l'utilisateur
$msg = '';

// appel de notre fichier contenant les fonctions utilisateur
require_once("fonction.inc.php");

// démarrage de la session 
session_start();

// déclaration du chemin absolu pour l'accès à notre projet
define('URL', 'http://www.fredericroth.fr/projets/php-sql/');
// OU define('URL', '/PHP/site/');
