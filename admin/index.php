<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <html lang="da">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Waterworld</title>
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>
<div class="container">

<?php /*start php*/

	session_start(); /*Fortæller server at vi bruger sessions*/
	include('../includes/dbcon.php');
	include('../includes/standardfunctions.php');
	include('../includes/functions.php');

	

?> <!-- Slut php -->


<header>
	<div id="logo">
	  <a href="../index.php?page=forsiden"><img src="../img/logo.jpg" name="logo" alt="logo"></a>
	</div>
	<div class="slogan">
	  <h1>WaterWorld</h1>
	  
	</div>

</header>

<nav id="navTop">

  <ul>
   <li><a href="index.php?page=forsiden">Forsiden</a></li>
   <li><a href="index.php?page=OmOs">Om Os</a></li>
   <li><a href="index.php?page=Rejser">Rejser</a></li>
   <li><a href="index.php?page=login">login</a></li>
   <li><a href="index.php?page=logout">logout</a></li>
   
  
  </ul>

</nav>

<div class="centerContainer">  
  
<?php

  if(isset($_GET['page'])) { /*Hvis siden URL indeholder "?page=blablabla"*/
  $page = $_GET['page']; /*Læg blablabla i variablen $page*/  

  switch ($page) {

     case 'forsiden':
      updateWelcomeText($mysqli);
      break;   

     case 'OmOs':
      updateOmOs($mysqli);
      break;  

     case 'Rejser':
      destinations($mysqli);
      break;

     case 'addDestinations':
      addDest($mysqli);
      break;

    case 'updateDest':
         if(isset($_GET['id'])) {
              $id = $_GET['id'];
          updateDest($mysqli,$id);
        } else {
          echo"Der er ikke angivet ID!";
        }
        break;

    case 'deleteDest':
        if(isset($_GET['id'])) {
          $id = $_GET['id'];
          deleteDest($mysqli,$id);
        } else {
          echo"Der er ikke angivet ID!";
        }
        break;
      





     case 'errors':
      displayErrors();
      break;   

     case 'login':
      login($mysqli);
      break;  

     case 'logout':
      logout();
      break;      
           
       
    
  }
 } 
?>



</div>

<footer>
    <div class="footer_box">
    	<h3>Adresse</h3>
    	<p>
    	  WaterWorld Rejsebureau <br>
    	  Nørregade 6, 4. <br>
    	  1165  København K.    		
    	</p>
    </div>

    <div class="footer_box">
    	<h3>Telefon</h3>
    	<p>
    	 33 32 32 65  		
    	</p>
      </div>

    <div class="footer_box">
    	  		
     	<h3>Email</h3>
    	<p>
    	 info@waterworldrejsebureau.dk
    	</p>
    </div>

    <div class="footer_box">
    	<h3>Åbeningstider</h3>
    	<p>
    	Mandag – Fredag: 9.00-17.30 <br>
        Lørdag: 9.30-13.30
 
    	</p>
    </div>

</footer>


</div>
</body>
</html>
