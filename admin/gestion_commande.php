<?php
require_once("../inc/init.inc.php"); // include => si le fichier n'est pas trouvé = erreur fatale

// controle du statut d'administrateur
if(!statut_admin())
{
    header('location:' . URL . 'index.php');
    exit();
}

//********************************************************************
//******************** SUPPRIMER UNE COMMANDE **************************
//********************************************************************

if(isset($_GET['action']) && $_GET['action'] == 'supprimer')
{
    if(!empty($_GET['id_commande']))
    {
        // on récupère les informations du produit à supprimer car nous avons besoin de son src pour supprimer la photo correspondante
        $suppr_commande = $pdo->prepare("SELECT * FROM commande WHERE id_commande = :id_commande");
        
        $suppr_commande->bindValue(":id_commande", $_GET['id_commande'], PDO::PARAM_STR);
        
        $suppr_commande->execute();
        
        $info_suppr_commande = $suppr_commande->fetch(PDO::FETCH_ASSOC);
        
        $suppression = $pdo->prepare("DELETE FROM commande WHERE id_commande = :id_commande");
        
        $suppression->bindValue(":id_commande", $_GET['id_commande'], PDO::PARAM_STR);
        
        $suppression->execute();
        
        $msg .= '<div class="erreur alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="span_msg">La commande a bien été supprimé</div>';
        
        header('location:gestion_commande.php');
    }
}

//********************************************************************
//******************** FIN SUPPRIMER UN PRODUIT **********************
//********************************************************************


//********************************************************************
//******************CHANGEMENT STATUT COMMANDE************************
//********************************************************************

if (isset($_GET['action']) && $_GET['action'] == 'traite') 
{
	$req_traite = $pdo->prepare("UPDATE commande SET etat = 'traite' WHERE id_commande = :id_commande");

	$req_traite->bindValue(":id_commande", $_GET['id_commande'], PDO::PARAM_STR);
	$req_traite->execute();
	//header('location:gestion_commande.php');
}

if (isset($_GET['action']) && $_GET['action'] == 'livre') 
{
	$req_traite = $pdo->prepare("UPDATE commande SET etat = 'livre' WHERE id_commande = :id_commande");

	$req_traite->bindValue(":id_commande", $_GET['id_commande'], PDO::PARAM_STR);
	$req_traite->execute();
	header('location:gestion_commande.php');
}


require_once("../inc/header.inc.php");
require_once("../inc/nav.inc.php");

?>

<div class="container">
<?= $msg; ?>

    <div class="starter-template">
        <h1><span class="glyphicon glyphicon-home mon_icone"></span> Gestion des commandes</h1>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <?php 
        
        //********************************************************************
        //******************* AFFICHAGE TABLEAU COMMANDES ********************
        //********************************************************************
        
            // récupération de tous les membres en BDD
            //affichage dans un tableau HTML
    
            // récupération des commandes et affichage dans tableau
            if (isset($_GET['tri'])) 
            {
	            if($_GET['tri'] == 'montantasc')
	            {
	            	$resultat = $pdo->query("SELECT * FROM commande ORDER BY montant ASC");
	            }
	            elseif ($_GET['tri'] == 'montantdesc') 
	            {
	            	$resultat = $pdo->query("SELECT * FROM commande ORDER BY montant DESC");
	            }
	            elseif($_GET['tri'] == 'dateasc')
	            {
	            	$resultat = $pdo->query("SELECT * FROM commande ORDER BY date_enregistrement ASC");
	            }
	            elseif ($_GET['tri'] == 'datedesc') 
	            {
	            	$resultat = $pdo->query("SELECT * FROM commande ORDER BY date_enregistrement DESC");
	            }
	           
	        }
	        else
	        {
	            $resultat = $pdo->query("SELECT * FROM commande");
	        }

            echo '<table class="table">';
            
            $nb_col = $resultat->columnCount();

            echo '<tr>';
            for($i = 0; $i < $nb_col; $i++)
            {
                $colonne = $resultat->getColumnMeta($i);

                if ($colonne['name'] == 'montant') 
                {
                	 echo '<th>' . ucfirst($colonne['name']) . ' <a href="?tri=montantasc"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?tri=montantdesc"><span class="glyphicon glyphicon-chevron-up"></span></a></th>';
                }
                elseif ($colonne['name'] == 'date_enregistrement') 
                {
                	echo '<th>' . ucfirst($colonne['name']) . ' <a href="?tri=dateasc"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?tri=datedesc"><span class="glyphicon glyphicon-chevron-up"></span></a></th>';
                }
                else
                {
                echo '<th>' . ucfirst($colonne['name']) . '</th>';
            }
              
            }

            echo "<th>Actions</th>";
    
            echo '</tr>';
            $nb_commande = $resultat->rowCount();
            //echo $nb_commande;
            $a = 0;
            while($ligne = $resultat->fetch(PDO::FETCH_ASSOC))
            {
            	
                echo '<tr>';
                foreach($ligne AS $indice => $valeur)
                {

                    if($indice == 'montant')
                    {
                        echo '<td>' . $valeur . '€</td>';
                    }
                    elseif($indice == 'etat')
                    {
                        if($valeur == "traite")
                        {
                            echo '<td>traitée</td>';
                        }
                        elseif($valeur == "livre")
                        {
                            echo '<td>livrée</td>';
                        }
                        else
                        {
                            echo '<td>en cours de traitement</td>';
                        }
                    }
                    else
                    {
                        echo '<td>' . $valeur . '</td>';    
                    }
                    
                    
                    
                }

        
                	if (empty($_GET['action']) || $_GET['action'] != 'details') 
                	{
                		echo '<td><a href="?action=details&id_commande=' . $ligne['id_commande'] . '" class="btn btn-info btn-sm bouton_action"><span class="glyphicon glyphicon-pencil"></span> Détails</a> ';
                	}

                else
                {
                	if ($_GET['id_commande'] == $ligne['id_commande']) 
                	{
                		echo '<td><a href="?action=reduire&id_commande=' . $ligne['id_commande'] . '" class="btn btn-info btn-sm bouton_action"><span class="glyphicon glyphicon-pencil"></span> Réduire</a>';
                	}
                	else
                	{
                		echo '<td><a href="?action=details&id_commande=' . $ligne['id_commande'] . '" class="btn btn-info btn-sm bouton_action"><span class="glyphicon glyphicon-pencil"></span> Détails</a>';
                	}
                	
                	echo "<td>";
                }
                
             //    if (isset($_GET['action']) && $_GET['action'] != 'details' && $_GET['id_commande'] == $ligne['id_commande']) 
             //    {
             //    	echo '<td><a href="?action=reduire&id_commande=' . $ligne['id_commande'] . '" class="btn btn-info btn-sm bouton_action"><span class="glyphicon glyphicon-pencil"></span> Réduire</a>';
            	// }
               	
            	
            	// else
            	// {
            	// 	 echo '<td><a href="?action=details&id_commande=' . $ligne['id_commande'] . '" class="btn btn-info btn-sm bouton_action"><span class="glyphicon glyphicon-pencil"></span> Détails</a>';
            	// 	}
            		
                
                echo'<a href="?action=supprimer&id_commande=' . $ligne['id_commande'] . '" class="btn btn-primary btn-sm bouton_action" onclick="return(confirm(\'Êtes-vous sûr? \'));" ><span class="glyphicon glyphicon-trash"></span> Supprimer</a></td>';
                
                echo '</tr>';
                
                // affichage du détail de la commande
                if(isset($_GET['action']) && $_GET['action'] == 'details' && $ligne['id_commande'] == $_GET['id_commande'])
                {
                    $id_commande = $_GET['id_commande'];
                    
                    $req_details = $pdo->prepare("SELECT * FROM produit p, details_commande dc, membre m, commande c WHERE dc.id_commande = :id_commande AND dc.id_produit = p.id_produit AND m.id_membre = c.id_membre AND c.id_commande = dc.id_commande");
                    
                    $req_details->bindValue(':id_commande', $id_commande, PDO::PARAM_STR);
                    
                    $req_details->execute();
                    
                   
                    
                   
                    
                    echo '</table><div class="flex"><div class="list-group details"><span class="list-group-item disabled"><b>Détails de la commande n°' . $_GET['id_commande'] . '</b></span>';

                    while ($details = $req_details->fetch(PDO::FETCH_ASSOC)) 
                    {
                    	//echo '<pre>'; var_dump($details); echo '</pre>';
	                    foreach ($details as $indice => $valeur) 
	                    {
	                    	if ($indice == 'reference') 
	                    	{
	                    		echo '<div class="list-group-item"<span class="label_profil"><b>Référence: </b>' . $valeur . '</span></div>';
	                    	}
	                    	
	                    	elseif ($indice ==  'prix') 
	                    	{
	                    		echo '<div class="list-group-item"<span class="label_profil"><b>Prix: </b>' . $valeur . '€</span></div>';
	                    	}
	                    	elseif ($indice == 'quantite') 
	                    	{
	                    		echo '<div class="list-group-item"<span class="label_profil"><b>Quantité: </b>' . $valeur . '</span></div>';
	                    	}
	                    	elseif ($indice == 'titre') 
	                    	{
	                    		echo '<div class="list-group-item"<span class="label_profil"><b>Titre: </b>' . $valeur . '</span></div>';
	                    	}
	                    	
	                    	elseif ($indice == 'photo') 
	                    	{
	                    		echo '<div class="list-group-item"<span class="label_profil"><img src="'.URL . $valeur . '" width="100"</span></div>';
	                    	}

	                  
	                    }

	    
	                }
	                echo'</div>';
	                 $req_details = $pdo->prepare("SELECT * FROM produit p, details_commande dc, membre m, commande c WHERE dc.id_commande = :id_commande AND dc.id_produit = p.id_produit AND m.id_membre = c.id_membre AND c.id_commande = dc.id_commande");
                    
                    $req_details->bindValue(':id_commande', $id_commande, PDO::PARAM_STR);
                    
                    $req_details->execute();
                    $details = $req_details->fetch(PDO::FETCH_ASSOC);
                    echo "<div class='client'>";
                    echo '<div class="list-group-item disabled"><span class="label_profil"><b>Détails client</b></span></div>';
	                echo '<div class="list-group-item"><span class="label_profil"><b>Pseudo: </b>' . $details['pseudo'] . '</span></div>';
	                echo '<div class="list-group-item"><span class="label_profil"><b>Nom: </b>' . $details['nom'] . '</span></div>';
	                echo '<div class="list-group-item"><span class="label_profil"><b>Prénom: </b>' . $details['prenom'] . '</span></div>';
	                echo '<div class="list-group-item"><span class="label_profil"><b>Adresse: </b>' . $details['adresse'] . '</span></div>';
	                echo '<div class="list-group-item"><span class="label_profil"><b>Ville: </b>' . $details['ville'] . '</span></div>';
	                echo '<div class="list-group-item"><span class="label_profil"><b>Code postal: </b>' . $details['code_postal'] . '</span></div>';
	                echo "<div class='list-group-item'><a href='?action=traite&id_commande=".$ligne['id_commande']."' class='btn btn-warning btn-block'>Commande traitée</a></div>";
	                echo "<div class='list-group-item'><a href='?action=livre&id_commande=".$ligne['id_commande']."' class='btn btn-success btn-block'>Commande livrée</a></div>";

                    echo'</div>';
                    echo'</div>';
                    
                    if ($a < $nb_commande -1) 
                    {
                    	echo'<table class="table">';
	                    echo '<tr>';
			            for($i = 0; $i < $nb_col; $i++)
			            {
			                $colonne = $resultat->getColumnMeta($i);
			                
			                echo '<th>' . ucfirst($colonne['name']) . '</th>';
			              
			            }

			            echo "<th>Actions</th>";
			    	
			            echo '</tr>';
	                    
                    }

                }
                
                $a++;
                
            }
            echo'</table>';

                
        
        //********************************************************************
        //****************** FIN AFFICHAGE TABLEAU COMMANDES *******************
        //********************************************************************
        
        
        ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            
            
            
            
            
            
            
            
            
        </div>
    </div>

</div><!-- /.container -->


<?php
require_once("../inc/footer.inc.php");