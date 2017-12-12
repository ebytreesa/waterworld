<?php

function login($mysqli) {
	if(isset($_POST['loginsubmit'])) {
		if(!empty($_POST['username']) && !empty($_POST['password'])) {
			$username 	= $mysqli->real_escape_string($_POST['username']);
			$password 	= md5($_POST['password']);

			$query = "SELECT * FROM `eby_users` WHERE `username` = '$username' AND `password`= '$password'";

			$result = $mysqli->query($query) or die ('Fejl!');

			if($result->num_rows > 0) { 
				while($row=$result->fetch_assoc()) {
					if(empty($row['activation'])){
					$_SESSION['username'] = $row['username'];
					header('location:index.php?page=updateWelcomeText');	

					} else{
						echo "brugerkotntor er ikke aktiveret";
						$error = true;
					}
					
				}
			} else {
				$error = true;
				echo "Der fandtes ingen brugere med det indtastede brugernavn og password!";
			}
		} else {
			$error = true;
			echo "Både brugernavn og password skal udfyldes";
		}
	}

	if(!isset($_POST['loginsubmit']) || isset($error)) { /*Hvis der endnu ikke er trykket på submit knappen, ELLER der er trykket og det har resulteret i at $error er defineret*/

?>

		<!-- Selve log ind formularen -->
		<form action="" method="post">
			<fieldset>
				<legend>Log ind</legend>

				<label for="username">Dit navn:</label>
				<br>
				<input type="text" name="username"></input>
				<br>

				<label for="password">Password:</label>
				<br>
				<input type="password" name="password">
				<br>

				<input type="submit" name="loginsubmit" value="log ind">
			</fieldset>
		</form>


<?php

	}
}

function logout() {
	session_destroy(); /*Sletter alle sessions*/

	header('location: index.php'); /*Sender os tilbage til index.php*/
}
/*Funktion til redigering af forside*/
function updateWelcomeText($mysqli) { /*$mysqli skal med som parameter, så databaseforbindelse er tilgængelig*/
	/*I login proceduren satte vi en SESSION ved navn "username" såfremt det lykkedes at logge ind*/
	/*Vi har bestemt os for, at man skal være logget ind for at kunne rette forsidetekst*/

	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		if(isset($_POST['submit'])) { /*Hvis der er trykket på knappen med name = "frontpagesubmit"*/
			if(!empty($_POST['title']) && !empty($_POST['content'])) { /*Hvis begge felter i formularen er udfyldt*/
				$title 		= $mysqli->real_escape_string($_POST['title']);
				$content 	= $mysqli->real_escape_string($_POST['content']);
				$imageOld1 	= $_POST['imageOld1'];
				$imageOld2 	= $_POST['imageOld2'];
				$imageOld3 	= $_POST['imageOld3'];

				if(!empty($_FILES['image1']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image1 	= $_FILES['image1'];

					/*Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image1 	= imageUpload($image1);

					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/'.$imageOld1);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/thumbs/small_'.$imageOld1);
				} else {
					$image1 = $imageOld1;
				}

				if(!empty($_FILES['image2']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image2 	= $_FILES['image2'];

					/*Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image2 	= imageUpload($image2);

					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/'.$imageOld2);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/thumbs/small_'.$imageOld2);
				} else {
					$image2 = $imageOld2;
				}

				if(!empty($_FILES['image3']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image3 	= $_FILES['image3'];

					/*Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image3 	= imageUpload($image3);

					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/'.$imageOld3);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/thumbs/small_'.$imageOld3);
				} else {
					$image3 = $imageOld3;
				}

				$query = "UPDATE `eby_welcometext` SET `title` = '$title', `content` = '$content', `image1` = '$image1', `image2` = '$image2', `image3` = '$image3' WHERE `id` = 1";   
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');

				header('location: index.php');
			} else { /*Hvis et (eller begge) felter i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde begge felter!<br><br>";
			}
		} /*Slut på det der skal ske når der er trykket på knappen med name = "frontpagesubmit"*/

		if (!isset($_POST['submit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
			$query = "SELECT * FROM `eby_welcometext` WHERE `id` = 1";
			$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!');

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$title 		= $row['title'];
					$content 	= $row['content'];
					$imageOld1 	= $row['image1'];
					$imageOld2 	= $row['image2'];
					$imageOld3 	= $row['image3'];
				}
		
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Rediger forside</legend>

					<input type="hidden" name = "imageOld1" value ="<?php echo $imageOld1; ?>">
					<input type="hidden" name = "imageOld2" value ="<?php echo $imageOld2; ?>">
					<input type="hidden" name = "imageOld3" value ="<?php echo $imageOld3; ?>">

					<label for "image1">Billede1</label>
					<img class='adminImgForsiden' src="/uploads/img/<?php echo $imageOld1; ?>" class="stretch100">
					<input type="file" name="image1" id="image1">
					<br>
					<br>


					<label for "image2">Billede1</label>
					<img class='adminImgForsiden' src="/uploads/img/<?php echo $imageOld2; ?>" class="stretch100">
					<input type="file" name="image2" id="image2">
					<br>
					<br>


					<label for "image3">Billede1</label>
					<img class='adminImgForsiden' src="/uploads/img/<?php echo $imageOld3; ?>" class="stretch100">
					<input type="file" name="image3" id="image3">
					<br>
					<br>


					<label for="title">Overskrift:</label>
					<br>
					<input type="text" class="stretch100" name="title" value="<?php echo $title; ?>"></input>
					<br>
					<br>

					<label for="content">Brødtekst:</label>
					<br>
					<textarea name="content" class="stretch100" rows=10 cols=80><?php echo $content; ?></textarea>
					<br>



					<input type="submit" name="submit" value="Rediger">
				</fieldset>
			</form>

<?php
			} else {
				echo"Der fandtes intet i databasen";
			}
		}

	}
}


function updateOmOs($mysqli){ 

	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		if(isset($_POST['submit'])) { /*Hvis der er trykket på knappen med name = "frontpagesubmit"*/

			if(!empty($_POST['title']) && !empty($_POST['content'])) { /*Hvis begge felter i formularen er udfyldt*/
				$title 		= $mysqli->real_escape_string($_POST['title']);
				$content 	= $mysqli->real_escape_string($_POST['content']);
				$imageOld 	= $_POST['imageOld'];


				if(!empty($_FILES['image']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image 	= $_FILES['image'];

					/*Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image 	= imageUpload($image);

					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/'.$imageOld);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/thumbs/small_'.$imageOld);
				} else {
					$image = $imageOld;
				}

				$query = "UPDATE `eby_omos` SET `title` = '$title',  `image` = '$image' WHERE `id` = 1";   
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');

				header('location: index.php?page=OmOs');
			} else { /*Hvis et (eller begge) felter i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde alle felte!<br><br>";
		    }
	    } /*Slut på det der skal ske når der er trykket på knappen med name = "frontpagesubmit"*/
    
	    if (!isset($_POST['submit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
			$query = "SELECT * FROM `eby_omos` ";
			$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!');

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$title 		= $row['title'];
					$content	= $row['content'];					
					$imageOld 	= $row['image'];
				
		        }
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Rediger Om Os siden</legend>

					<input type="hidden" name = "imageOld" value ="<?php echo $imageOld; ?>">

					<label for "image">Billede</label>
					<img src="uploads/img/<?php echo $imageOld; ?>" class="stretch100">
					<input type="file" name="image" id="image">
					<br>
					<br>


					<label for="title">Overskrift:</label>
					<br>
					<input type="text" class="stretch100" name="title" value="<?php echo $title; ?>"></input>
					<br>
					<br>

					<label for="content">Brødtekst:</label>
					<br>
					<textarea name="content" class="stretch100" rows=10 cols=80><?php echo $content; ?></textarea>
					<br>

					

					<input type="submit" name="submit" value="Rediger">
				</fieldset>
			</form>
            
<?php
			} else {
				echo"Der fandtes intet i databasen";
			}
		}

	}
} 


function welcomeText($mysqli){

	$query = "SELECT * FROM `eby_welcometext` "; /*Definer DB forespørgsel: Vælg alt fra tabellen "" i rækker der har id = 1*/
	$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!'); /*Udfør forespørgsel og læg resultat i $result*/

	if($result->num_rows > 0) { /*Hvis der fandtes 1 eller flere resultater*/
		while($row = $result->fetch_assoc()) { /*Looper igennem alle rækker i resultatet og lægger hver række i $row*/

			
			$title 		= $row['title']; /*Tager værdien fra kolonnen "title" og lægger det i en variabel*/
			$content 	= $mysqli->real_escape_string(nl2br($row['content'])); /*Tager værdien fra kolonnen "content" og erstatter /n og /l med <br> og lægger det i en variabel*/
			$image1 	= $row['image1'];
			$image2 	= $row['image2'];
			$image3 	= $row['image3'];
			
?>
<div class="imgSlider">

<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active">
      <img src="/uploads/img/<?php echo $image1; ?>" alt="...">
    </div>
    <div class="item">
      <img src="/uploads/img/<?php echo $image2; ?>" alt="...">
    </div>
    <div class="item">
      <img src="/uploads/img/<?php echo $image3; ?>" alt="...">
    </div>
    ...
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
</div>

		    <div class='welcomeBox'>
			   		
		       <h2><?php echo $title; ?></h2>
		       
		   </div>
<?php
			
		}

	} else { /*Hvis der ikke fandtes resultater i DB forespørgsel*/
		echo"Der fandtes intet i databasen";
	} /*Slut på else*/
}

function frontpageDest($mysqli) {
	$query = "SELECT * FROM `eby_destinations` LIMIT 3"; /*Definer DB forespørgsel: Vælg alt fra tabellen "" i rækker der har id = 1*/
	$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!'); /*Udfør forespørgsel og læg resultat i $result*/

	if($result->num_rows > 0) { /*Hvis der fandtes 1 eller flere resultater*/
		while($row = $result->fetch_assoc()) { /*Looper igennem alle rækker i resultatet og lægger hver række i $row*/

			
			$title 		= $row['title']; /*Tager værdien fra kolonnen "title" og lægger det i en variabel*/
			$content 	= $mysqli->real_escape_string(nl2br($row['content'])); /*Tager værdien fra kolonnen "content" og erstatter /n og /l med <br> og lægger det i en variabel*/
			$image 		= $row['image']; /*Tager værdien fra kolonnen "image" og lægger det i en variabel*/
			$image 		= "uploads/img/".$image;
?>

		    <div class='destBox' id='destBox'>
			   <img class='imageBox' id='imgBox' src='<?php echo $image;?>'>			   		
		       <h2><?php echo $title;?></h2>		       
		   </div>
<?php
			
		}

	} else { /*Hvis der ikke fandtes resultater i DB forespørgsel*/
		echo"Der fandtes intet i databasen";
	} /*Slut på else*/
}


function omOs($mysqli) {
	$query = "SELECT * FROM `eby_omos` "; /*Definer DB forespørgsel: Vælg alt fra tabellen "" i rækker der har id = 1*/
	$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!'); /*Udfør forespørgsel og læg resultat i $result*/

	if($result->num_rows > 0) { /*Hvis der fandtes 1 eller flere resultater*/
		while($row = $result->fetch_assoc()) { /*Looper igennem alle rækker i resultatet og lægger hver række i $row*/

			
			$title 		= $row['title']; /*Tager værdien fra kolonnen "title" og lægger det i en variabel*/
			$content 	= $mysqli->real_escape_string(nl2br($row['content'])); /*Tager værdien fra kolonnen "content" og erstatter /n og /l med <br> og lægger det i en variabel*/
			$image 		= $row['image']; /*Tager værdien fra kolonnen "image" og lægger det i en variabel*/
			$image 		= "uploads/img/".$image;
?>

		    <div class='omOsBox'>
			   <img class="omOsImg" src="<?php echo $image; ?>">
		
		       <h2><?php echo $title; ?></h2>
		       <p><?php echo $content; ?></p>
		    </div>
		   
		    <div class="googleMap">
		          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2249.5823237025193!2d12.570020616169392!3d55.67886298053377!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46525311cb9a52a3%3A0x6aacee5a8a03492c!2sN%C3%B8rregade+6%2C+1165+K%C3%B8benhavn!5e0!3m2!1sen!2sdk!4v1464867203010" width="400" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>
		    </div>
		    
<?php
			
		}

	} else { /*Hvis der ikke fandtes resultater i DB forespørgsel*/
		echo"Der fandtes intet i databasen";
	} /*Slut på else*/
}

function destinations($mysqli) {
	if(userAccess($mysqli,2,true)) {
		$query		= "SELECT * FROM `eby_destinations`";
		$result		= $mysqli->query($query) or die ('Fejl!');

		if($result->num_rows > 0) {
			echo "
			<table style='width:100%'>
			  <tr>
			    <td>REJSER</td>
			    <td>Ret</td> 
			    <td>Slet</td>
			  </tr>
			";

			while($row = $result->fetch_assoc()) {
				echo"
				  <tr>
				    <td>".$row['title']."</td>";
				    
			    		echo"
						    <td><a href='index.php?page=updateDest&id=".$row['id']."'>Ret</a></td> 
						    <td><a href='index.php?page=deleteDest&id=".$row['id']."'>Slet</a></td> 
						  </tr>
						";
					
			}

			echo "
				</table>
				<br><br>
				<a href='index.php?page=addDestinations'>Tilføj Rejser</a>
			";
		} else {
			echo"<h3>Der blev ikke fundet destinations</h3>";
			echo"<p><a href='index.php'>Gå til forside</a></p>";
		}
	}

}

function addDest($mysqli){
	if(isset($_POST['createsubmit'])) {
		if(!empty($_POST['title']) && !empty($_POST['content']) && !empty($_POST['price']) && !empty($_POST['date'])){
			$title 	= $mysqli->real_escape_string($_POST['title']);
			$content 	= $mysqli->real_escape_string($_POST['content']);
			$price 		= $_POST['price'];
			$date 		= $_POST['date'];

			if(!empty($_FILES['image']['name'])) { /*Hvis der er valgt en billedfil til upload*/
				$image 	= $_FILES['image'];

				/*Kalder upload funktionen. $this fordi det er en funktion i denne klasse.
				Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
				$image 	= imageUpload($image);
			} else {
				$image = ""; /*Hvis der ikke er uploadet et billede, definerer vi $image med en tom værdi, for ikke at få fejl når vi gemmer i DB*/
			}

			$query 		= "SELECT * FROM `eby_destinations` WHERE `title` = '$title' AND `content` = '$content'";
			$result 	= $mysqli->query($query);

			if($result->num_rows > 0) {
				echo"<h3>Rejser er taget!</h3>";
				$error 	= true;
			}


			if(!isset($error)) {
				$query	= "INSERT INTO `eby_destinations` (`title`, `content`, `price`, `date`, `image`) VALUES ('$title','$content',$price,'$date','$image')";
				$result = $mysqli->query($query) or die('ØV - det virker ikke!');

			}
		} else {
			echo"<h3>Du skal udfylde alle felter!</h3>";
			$error 		= true;
		}
	}


	if(!isset($_POST['createsubmit']) || isset($error)) { /*Hvis der endnu ikke er trykket på submit knappen, ELLER der er trykket og det har resulteret i at $error er defineret*/

		 
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Add Destinations</legend>

					
					<label for "image">Billede</label>
					<br>
					<input type="file" name="image" id="image">
					<br>
					<br>


					<label for="title">Overskrift:</label>
					
					<input type="text" class="stretch100" name="title" ></input>
					<br>
					<br>

					<label for="content">Brødtekst:</label>
					<br>
					<textarea name="content" class="stretch100" rows=10 cols=80></textarea>
					<br>


					<label for="price">Pris:</label>
					<br>
					<input type="text" class="stretch100" name="price" ></input>
					<br>
					<br>


					<label for="date">Dato:</label>
					<br>
					<input type="text" class="stretch100" name="date"></input>
					<br>
					<br>

					

					<input type="submit" name="createsubmit" value="Submit">
				</fieldset>
			</form>
            

<?php	
	}

}

	


function updateDest($mysqli,$id){
	if(userAccess($mysqli,2,true)) { 
	
	/*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		if(isset($_POST['submit'])) { /*Hvis der er trykket på knappen med name = "frontpagesubmit"*/

			if(!empty($_POST['title']) && !empty($_POST['content'])&& !empty($_POST['price']) && !empty($_POST['date'])) { /*Hvis alle felter i formularen er udfyldt*/
				$title 		= $_POST['title'];
				$content 	= $_POST['content'];
				$imageOld 	= $_POST['image'];
				$price  	= $_POST['price'];
				$date  	    = $_POST['date'];

				if(!empty($_FILES['image']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image 	= $_FILES['image'];

					/*Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image 	= imageUpload($image);

					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/'.$imageOld);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/img/thumbs/small_'.$imageOld);
				} else {
					$image = $imageOld;
				}

				$query = "UPDATE `eby_destinations` SET `title` = '$title',`content` = '$content',`price` = '$price',`date` = '$date',  `image` = '$image' WHERE `id` = $id";   
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');

				header('location: index.php?page=Rejser');
			} else { /*Hvis et (eller begge) felter i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde alle felte!<br><br>";
		    }
	    } /*Slut på det der skal ske når der er trykket på knappen med name = "frontpagesubmit"*/
    
	    if (!isset($_POST['submit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
			$query = "SELECT * FROM `eby_destinations` WHERE `id` = $id";
			$result = $mysqli->query($query) or die('DATABASEFEJL - Der blev ikke hentet fra DB!');

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$title 		= $row['title'];
					$content 	= $row['content'];
					$imageOld 	= $row['image'];
					$price  	= $row['price'];
					$date  	    = $row['date'];

				}
			} else {
				echo "Rejse fandtes ikke i databasen";
			}
		}
?>
		<form action="" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>Rediger Rejser</legend>

				<input type="hidden" name = "imageOld" value ="<?php echo $imageOld; ?>">

				<label for "image">Billede</label>
				<img src="uploads/img/<?php echo $imageOld; ?>" class="stretch100">
				<input type="file" name="image" id="image">
				<br>
				<br>


				<label for="title">Overskrift:</label>
				<br>
				<input type="text" class="stretch100" name="title" value="<?php echo $title; ?>"></input>
				<br>
				<br>

				<label for="content">Brødtekst:</label>
				<br>
				<textarea name="content" class="stretch100" rows=10 cols=80  value ="<?php echo $content; ?>"></textarea>
				<br>


				<label for="price">Pris:</label>
				<br>
				<input type="text" class="stretch100" name="price" value ="<?php echo $price; ?>"></input>
				<br>
				<br>


				<label for="date">Dato:</label>
				<br>
				<input type="text" class="stretch100" name="date" value="<?php echo $date; ?>"></input>
				<br>
				<br>

				

				<input type="submit" name="submit" value="Rediger">
			</fieldset>
		</form>
            
<?php

	}

}


function deleteDest($mysqli,$id) {
	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		$query 	= "SELECT * FROM `eby_destinations` WHERE `id` = $id";
		$result = $mysqli->query($query);

		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$image 	= $row['image'];
					@unlink('uploads/img/'.$image);
					@unlink('uploads/img/thumbs/small_'.$image);
			}

			$query 	= "DELETE FROM `eby_destinations` WHERE `id` = $id";
			$result = $mysqli->query($query) or die('Det blev ikke slettet!"');

			header('location:index.php?page=Rejser');

		} else {
			echo"Rejse fandtes ikke i databasen!";
		}
	}

}


function destNav($mysqli) {
	$query 	= "SELECT * FROM `eby_destinations` ";
	$result = $mysqli->query($query);

	if($result->num_rows > 0) {
		echo "<div class='destNavWrapper'>";
		echo "<ul>";
		echo "<h3 class ='destNavHeader'>REJSER</h3>";
		while ($row = $result->fetch_assoc()) {
			$id 			= $row['id'];
			$title 			= $row['title'];
			

			echo"<li><a href ='index.php?page=rejser&id=".$id."'>";
			echo $title;
			echo"</li></a>";
		}
		echo"</ul>";
		echo"</div>";

	} else {
		echo"Ingen nyheder i databasen!";
	}
}


function latestDest($mysqli) {
	if(isset($_GET['id'])) {
		$id 		= $mysqli->real_escape_string($_GET['id']);
		$query 		= "SELECT * FROM `eby_destinations` WHERE `id` = $id";
	} else {
		$query 		= "SELECT * FROM `eby_destinations` ORDER BY `id` ASC LIMIT 1 ";
	
    }
	$result = $mysqli->query($query) or die('DATABASEFEJL ved hentning af destinations!');

	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id 			= $row['id'];
			$title 			= $row['title'];
			$content 		= nl2br($row['content']);
			$image 			= $row['image'];
			$price 		    = $row['price'];
			$date 		    = $row['date'];

?>

			<div class='destWrapper'>				
				<h2><?php echo $title;?></h2>
				<p class="content"><?php echo $content;?></p>
				<img class='destIMG' src='/uploads/img/<?php echo $image; ?>'>                
				
				<div class ='priceBox'>&nbsp&nbsp Pris &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $price;?>
				&nbsp kr.<br><span>Afrejser Fra </span> &nbsp&nbsp&nbsp&nbsp<?php echo $date;?> </div>
            </div>

<?php             
			
		}

	} else {
		echo "Der blev ikke fundet en nyhed i databasen";
	}

}



function contact() {
	if(isset($_POST['submit'])) {
		if(!empty($_POST['senderMail']) && !empty($_POST['senderName']) && !empty($_POST['subject']) && !empty($_POST['content'])) {
			/*Mail til dig selv*/
			$recieverMail 		= 'ebysanthome@gmail.com';
			$recieverName 		= 'eby';
			$subject 			= $_POST['subject'];
		    $content 			= nl2br($_POST['content']);
		    $senderMail 		= $_POST['senderMail'];
		    $senderName 		= $_POST['senderName'];

			/*Hvis det lykkedes at sende mail til dig selv*/
			if(sendViaPHPmailer($recieverMail,$recieverName,$subject,$content,$senderMail,$senderName)) { 
				/*Så send en kopi til brugeren*/
				$recieverMail 		= $_POST['senderMail'];
				$recieverName 		= $_POST['senderName'];
				$subject 			= "KOPI af din mail til MERCANTEC HOT";
			    $content 			= "<h1>Emne: ".$_POST['subject']."</h1><p>".nl2br($_POST['content'])."</p>";
			    $senderMail 		= 'ebysanthome@gmail.com';
			    $senderName 		= 'Eby';

			    /*Hvis det lykkedes at sende kopien*/
			    if(sendViaPHPmailer($recieverMail,$recieverName,$subject,$content,$senderMail,$senderName)) {
			    	echo "<h1>Tak for din henvendelse!</h1> Vi har sendt dig en kopi af din mail, og behandler din henvendelse hurtigst muligt.";
			    } else {
			    	echo "Der blev ikke sendt en kopi mail <br>";
			    	exit(); /*Hvis det ikke lykkedes at sende kopien, stop funktionen*/
			    }
			} else {
			    echo "Der blev ikke sendt en mail <br>";
				exit(); /*Hvis det ikke lykkedes at sende mail til dig selv, stop funktionen*/
			}
		} else {
			echo "Du skal udfylde alle felter";
		}
	} else {
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Send os en mail:</legend>

					<label for="senderName">Navn</label>
					<br>
					<input type="text" class="stretch100" name="senderName"  required="required"></input>
					<br>
					<br>

					<label for="senderMail">Email</label>
					<br>
					<input type="email" class="stretch100" name="senderMail" required="required"></input>
					<br>
					<br>

					<label for="subject">Emne</label>
					<br>
					<input type="text" class="stretch100" name="subject" required="required"></input>
					<br>
					<br>

					<label for="content">Besked</label>
					<br>
					<textarea name="content" class="stretch100" rows=10 cols=50 required="required"></textarea>
					<br>

					<input type="submit" name="submit" value="Send">
				</fieldset>
			</form>
<?php
	}
}
