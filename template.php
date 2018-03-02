<?php
require_once("inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale






require_once("inc/header.inc.php");
require_once("inc/nav.inc.php");

?>

<div class="container">
<?= $msg; ?>

  <div class="starter-template">
    <h1><span class="glyphicon glyphicon-home mon_icone"></span> Template</h1>
  </div>

</div><!-- /.container -->


<?php
require_once("inc/footer.inc.php");