<?php

function login($mysqli) {
	if(isset($_POST['loginsubmit'])) {
		if(!empty($_POST['username']) && !empty($_POST['password'])) {
			$username 	= $mysqli->real_escape_string($_POST['username']);
			$password 	= md5($_POST['password']);

			$query = "SELECT * FROM `users` WHERE `username` = '$username' AND `password`= '$password'";

			$result = $mysqli->query($query) or die ('Fejl!');

			if($result->num_rows > 0) { 
				while($row=$result->fetch_assoc()) {
					if(empty($row['activation'])){
					$_SESSION['username'] = $row['username'];
					header('location:index.php');	

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




function createUser($mysqli) {
	if(isset($_POST['createsubmit'])) {
		if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['password2'])  && !empty($_POST['email'])) {
			$username 	= $mysqli->real_escape_string($_POST['username']);
			$email 	= $mysqli->real_escape_string($_POST['email']);
			$password 	= md5($_POST['password']);
			$password2 	= md5($_POST['password2']);
			$level 		= $mysqli->real_escape_string($_POST['level']);
			$activation = substr(md5(uniqid(mt_rand(),true)),0,16);

			$query 		= "SELECT * FROM `users` WHERE `username` = '$username'";
			$result 	= $mysqli->query($query);

			if($result->num_rows > 0) {
				echo"<h3>Brugernavnet er taget!</h3>";
				$error 	= true;
			}

			$query 		= "SELECT * FROM `users` WHERE `email` = '$email'";
			$result 	= $mysqli->query($query);

			if($result->num_rows > 0) {
				echo"<h3>email er allerede tilmeldt</h3>";
				$error 	= true;
			}

			if($password !== $password2) {
				echo"<h3>Du har ikke tastet det samme i PASSWORD og GENTAG PASSWORD!</h3>";
				$error 	= true;
			}

			if(!isset($error)) {
				$query	= "INSERT INTO `users` (`username`, `password`, `level`, `email`, `activation`) VALUES ('$username','$password', $level, '$email', '$activation')";
				$result = $mysqli->query($query) or die('ØV - det virker ikke!');

				$id 	= $mysqli->insert_id;

				$link 	= "http://procedural_php/index.php?page=activate&id=".$id."&key=".$activation;

				if(userAccess($mysqli,2,false)) {
					header('location: index.php?page=useradmin');
				} else {

					$content = "

					         <h1>Tak for din tilmelding til MercantecHot!</h1>
					         <p>Du mangler nu kun at activere din bruger kontor</p>
					         <p><a href = '".$link."'>ACTIVER</a></p>
					         <p>du kan altenative kopier nedstående URL og sætte det ind i din browsers adresselinje:<br>".$link."</p>
					         <h4>Med venlig hilsen</h4>
					         <h3>Mercantec Hot Teamet</h3>
					";

					$recieverMail = $email;
					$recieverName = $username;
					$subject	  =	"Aktivere din bruger på MercantecHot.dk";		
					$senderMail   = "ebysanthome@gmail.com";
					$senderName   = "Mercantec Hot Teamet";

			        if (sendViaPHPmailer($recieverMail,$recieverName,$subject,$content,$senderMail,$senderName)){
			        	echo "<h3>Du skal udfylde alle felter!</h3>
					      <p>Du vil modtage en mail som inholder et link til aktivering af din bruger kontor</p><a href= 'index.php'>Gå til forsiden</a></p>";

			        } else{
			            echo "UPS! DEr gik noget galt og din bruger blev ikke gemt";

			            $query = "DELETE FROM `users` WHERE `id` = $id";
			            $mysqli()->query($query)or die('Nå?');

			        }
					          /*if(!isset($_SESSION['username'])) {
						                 $_SESSION['username'] = $username;
					            }*/


				}
			}
		} else {
			echo"<h3>Du skal udfylde alle felter!</h3>";
			$error 		= true;
		}
	}


	if(!isset($_POST['createsubmit']) || isset($error)) { /*Hvis der endnu ikke er trykket på submit knappen, ELLER der er trykket og det har resulteret i at $error er defineret*/
?>
		<form action="" method="post">
			<fieldset>
				<legend>Opret ny bruger</legend>
				<br>
				<label for="username">Brugernavn:</label>
				<br>
				<input type="text" name="username" required="required" class="strech100">
				<br>
				<br>

				<label for="email">Email:</label>	<br>
				<input type="email" name="email" required="required"  class="strech100">	<br>	<br>

				<label for="password">Password:</label>
				<br>
				<input type="password" name="password" required="required"  class="strech100">
				<br>
				<label for="password2">Gentag password:</label>
				<br>
				<input type="password" name="password2" required="required"  class="strech100">
				<br>
<?php
				if(userAccess($mysqli,2,false)) {
					echo"
					<br>
					<label for 'level'>Level: </label><br>
					<select name='level'>
						<option value='1'>Almindelig bruger</option>
						<option value='2' selected='selected'>Administrator</option>
					</select>
					";

				} else {
					echo"<input type='hidden' name='level' value=1>";
				}
?>
				<br><br>
				<input type="submit" name="createsubmit" value="Opret">
			</fieldset>
		</form>

<?php	
	}

}




function login($mysqli) {
	if(isset($_POST['loginsubmit'])) {
		if(!empty($_POST['username']) && !empty($_POST['password'])) {
			$username 	= $mysqli->real_escape_string($_POST['username']);
			$password 	= md5($_POST['password']);

			$query = "SELECT * FROM `users` WHERE `username` = '$username' AND `password`= '$password'";

			$result = $mysqli->query($query) or die ('Fejl!');

			if($result->num_rows > 0) { 
				while($row=$result->fetch_assoc()) {
					if(empty($row['activation'])){
					$_SESSION['username'] = $row['username'];
					header('location:index.php');	

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

function deleteUser($mysqli) {
	if(userAccess($mysqli,2,true)) {
		if(isset($_GET['id'])) {
			$id = $mysqli->real_escape_string($_GET['id']);

			$query="SELECT * FROM `users` WHERE `id` = $id";
			$result = $mysqli->query($query);

			if($result->num_rows > 0) {
				if($id != 1) {
					$query = "DELETE FROM `users` WHERE `id` = $id";
					$result = $mysqli->query($query) or die('Det blev squtte slettet!');
					header('location: index.php?page=useradmin');
				} else {
					echo"<h3>Du må ikke slette admin<br><br></h3>";
					echo"<p><a href='index.php'>Gå til forside</a></p>";
				}
			} else {
				echo"<h3>ID fandtes ikke<br><br></h3>";
				echo"<p><a href='index.php'>Gå til forside</a></p>";
			}

		} else {
			echo"<h3>ID er ikke sat<br><br></h3>";
			echo"<p><a href='index.php'>Gå til forside</a></p>";
		}
	}
}


function updateUser($mysqli) {
	if(userAccess($mysqli,2,true)) {

		if(isset($_GET['id'])) {
			$id = $mysqli->real_escape_string($_GET['id']);
			$query = "SELECT * FROM `users` WHERE `id` = $id";
			$result = $mysqli->query($query) or die('WTF???');

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$username = $row['username'];
					$password = $row['password'];
				}

				if(isset($_POST['updatesubmit'])) {

					if(!empty($_POST['username'])) {
						$username = $mysqli->real_escape_string($_POST['username']);
					}

					if(!empty($_POST['password'])) {
						$password = md5($_POST['password']);
					}

					$query = "UPDATE `users` SET `username`= '$username' ,`password`= '$password' WHERE `id` = $id";
					$result = $mysqli->query($query) or die('Det blev squtte gemt!');

					header('location:index.php?page=useradmin');

				}

				if(!isset($_POST['updatesubmit']) || isset($error)) {

?>
					<form action="" method="post">
						<fieldset>
							<legend>Rediger bruger</legend>

							<label for="username">Brugernavn:</label>
							<br>
							<input type="text" name="username" value="<?php echo $username; ?>"></input>
							<br>

							<label for="password">Password:</label>
							<br>
							<input type="password" name="password">
							<br>

							<input type="submit" name="updatesubmit" value="Rediger">
						</fieldset>
					</form>
<?php
				}
			} else {
				echo "WTF? ID findes ikke";
			}

		} else {
			echo"ID er ikke sat!";
		}

	}

}

function userAdmin($mysqli) {
	if(userAccess($mysqli,2,true)) {
		$query		= "SELECT * FROM `users`";
		$result		= $mysqli->query($query) or die ('Fejl!');

		if($result->num_rows > 0) {
			echo "
			<table style='width:100%'>
			  <tr>
			    <td>Brugernavn</td>
			    <td>Ret</td> 
			    <td>Slet</td>
			  </tr>
			";

			while($row = $result->fetch_assoc()) {
				echo"
				  <tr>
				    <td>".$row['username']."</td>";
				    if($row['id'] != 1) {
			    		echo"
						    <td><a href='index.php?page=updateuser&id=".$row['id']."'>Ret</a></td> 
						    <td><a href='index.php?page=deleteuser&id=".$row['id']."'>Slet</a></td> 
						  </tr>
						";
					}
			}

			echo "
				</table>
				<br><br>
				<a href='index.php?page=createuser'>Tilføj bruger</a>
			";
		} else {
			echo"<h3>Der blev ikke fundet brugere</h3>";
			echo"<p><a href='index.php'>Gå til forside</a></p>";
		}
	}

}

function frontpage($mysqli) {
	$query = "SELECT * FROM `welcometext` WHERE `id` = 1"; /*Definer DB forespørgsel: Vælg alt fra tabellen "frontpage" i rækker der har id = 1*/
	$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!'); /*Udfør forespørgsel og læg resultat i $result*/

	if($result->num_rows > 0) { /*Hvis der fandtes 1 eller flere resultater*/
		while($row = $result->fetch_assoc()) { /*Looper igennem alle rækker i resultatet og lægger hver række i $row*/
			$title 		= $row['title']; /*Tager værdien fra kolonnen "title" og lægger det i en variabel*/
			$content 	= nl2br($row['content']); /*Tager værdien fra kolonnen "content" og erstatter /n og /l med <br> og lægger det i en variabel*/
			$image 		= $row['image']; /*Tager værdien fra kolonnen "image" og lægger det i en variabel*/
			$image 		= "uploads/img/".$image;
		}

?>
		<!-- Herunder udskrives html, hvor vi indlægger variablerne -->
		<div class="frontpageIMG">
			<img src="<?php echo $image; ?>">
		</div>
		<h1><?php echo $title; ?></h1>
		<p><?php echo $content; ?></p>

<?php


	} else { /*Hvis der ikke fandtes resultater i DB forespørgsel*/
		echo"Der fandtes intet i databasen";
	} /*Slut på else*/
}

/*Funktion til redigering af forside*/
function updateFrontpage($mysqli) { /*$mysqli skal med som parameter, så databaseforbindelse er tilgængelig*/
	/*I login proceduren satte vi en SESSION ved navn "username" såfremt det lykkedes at logge ind*/
	/*Vi har bestemt os for, at man skal være logget ind for at kunne rette forsidetekst*/

	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		if(isset($_POST['frontpagesubmit'])) { /*Hvis der er trykket på knappen med name = "frontpagesubmit"*/

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

				$query = "UPDATE `frontpage` SET `title` = '$title', `content` = '$content', `image` = '$image' WHERE `id` = 1";   
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');

				header('location: index.php');
			} else { /*Hvis et (eller begge) felter i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde begge felter!<br><br>";
			}
		} /*Slut på det der skal ske når der er trykket på knappen med name = "frontpagesubmit"*/

		if (!isset($_POST['frontpagesubmit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
			$query = "SELECT * FROM `frontpage` WHERE `id` = 1";
			$result = $mysqli->query($query) or die('Der blev ikke hentet fra DB!');

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$title 		= $row['title'];
					$content 	= $row['content'];
					$imageOld 	= $row['image'];
				}
		
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Rediger forside</legend>

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
					<textarea name="content" class="stretch100" rows=10><?php echo $content; ?></textarea>
					<br>

					<input type="submit" name="frontpagesubmit" value="Rediger">
				</fieldset>
			</form>

<?php
			} else {
				echo"Der fandtes intet i databasen";
			}
		}

	}
}

function newsNav($mysqli) {
	$query 	= "SELECT * FROM `news` ORDER BY `created_at` DESC";
	$result = $mysqli->query($query);

	if($result->num_rows > 0) {
		echo "<div class='newsNavWrapper'>";
		echo "<ul>";
		echo "<li class = 'newsNavTop'>NYHEDER</li>";
		while ($row = $result->fetch_assoc()) {
			$id 			= $row['id'];
			$title 			= $row['title'];
			$created_at 	= $row['created_at'];
			$created_at 	= date("d/m Y", strtotime($created_at));

			echo"<a href ='index.php?page=news&id=".$id."'><li>";
			echo $created_at."<br>".$title;
			echo"</li></a>";
		}
		echo"</ul>";
		echo"</div>";

	} else {
		echo"Ingen nyheder i databasen!";
	}
}

function News($mysqli) {
	if(isset($_GET['id'])) {
		$id 		= $mysqli->real_escape_string($_GET['id']);
		$query 		= "SELECT * FROM `news` WHERE `id` = $id";
	} else {
		$query 		= "SELECT * FROM `news` ORDER BY `created_at` DESC LIMIT 1";
	}

	$result = $mysqli->query($query) or die('DATABASEFEJL ved hentning af nyhed!');

	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id 			= $row['id'];
			$title 			= $row['title'];
			$content 		= nl2br($row['content']);
			$image 			= $row['image'];
			$created_at 	= $row['created_at'];
			$created_at 	= date("d/m Y", strtotime($created_at));
			$users_id 		= $row['users_id'];

			$query = "SELECT `username` FROM `users` WHERE `id` = $users_id";
			$resultUsername = $mysqli->query($query);

			if($resultUsername->num_rows > 0) {
				while($rowUsername = $resultUsername->fetch_assoc()) {
					$username = $rowUsername['username'];
				}

			} else {
				$username = "Ukendt bruger";
			}
?>

			<div class='newsWrapper'>
				<h4>Skrevet <?php echo $created_at; ?> af <?php echo $username; ?></h4>
				<h1><?php echo $title; ?></h1>
<?php
				if(!empty($image)) {
					echo "<img class='newsIMG' src='/uploads/img/".$image."'>";
				}
?>
				<p><?php echo $content; ?></p>
				<form action="" method="post">
			<h2>Kommentarer</h2>
			<?php comment($mysqli,$id); ?>
				



				
			<?php createComment($mysqli,$id); ?>

			</div>


<?php 
            newsNav($mysqli);
			
		}

	} else {
		echo "Der blev ikke fundet en nyhed i databasen";
	}

}

function adminNews($mysqli) {
	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		$query		= "SELECT * FROM `news`";
		$result 	= $mysqli->query($query);

		if($result->num_rows > 0) {
			echo "
			<table style='width:100%'>
			  <tr>
			    <td>Nyhed</td>
			    <td>Ret</td> 
			    <td>Slet</td>
			  </tr>
			";

			while($row = $result->fetch_assoc()) {
				$title 	= $row['title'];
				$id 	= $row['id'];

				echo"
				  <tr>
				    <td>".$title."</td>
		    		<td><a href='index.php?page=updateNews&id=".$id."'>Ret</a></td> 
					    <td><a href='index.php?page=deleteNews&id=".$id."'>Slet</a></td> 
					  </tr>
					";
			}

			echo "
				</table>
				<br><br>
			";
		} else {
			echo"Der er ingen nyheder!<br><br>";
		}
		
		echo"<a href='index.php?page=createNews'>Tilføj nyhed</a>";

	}
}

function createNews($mysqli) {
	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget indsom den bruger der har lavet nyheden ELLER er logget ind som minimum level 2*/
		$username 	= $_SESSION['username'];
		$query 		= "SELECT `id` FROM `users` WHERE `username` = '$username'";
		$result  	= $mysqli->query($query) or die('Database fejl bruger');

		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$users_id = $row['id'];
			}
		} else {
			echo "Bruger id kune ikke findes?";
			exit();
		}

		if(isset($_POST['submit'])) { /*Hvis der er trykket på knappen med name = "submit"*/
			if(!empty($_POST['title']) && !empty($_POST['content'])) { /*Hvis begge felter i formularen er udfyldt*/
				$title 		= $_POST['title'];
				$content 	= $_POST['content'];

				if(!empty($_FILES['image']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image 	= $_FILES['image'];

					/*Kalder upload funktionen. $this fordi det er en funktion i denne klasse.
					Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image 	= imageUpload($image);
				} else {
					$image = ""; /*Hvis der ikke er uploadet et billede, definerer vi $image med en tom værdi, for ikke at få fejl når vi gemmer i DB*/
				}
				$query = "INSERT INTO `news` (`title`, `content`, `image`, `users_id`) VALUES ('$title', '$content', '$image', $users_id)";
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');
				
				header('location: index.php?page=news&id='.$mysqli->insert_id); /*$mysqli->insert_id giver det sidste id der blev sat ind i databasen*/

			} else { /*Hvis et (eller begge) felter i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde begge felter!<br><br>";
			}
		} /*Slut på det der skal ske når der er trykket på knappen med name = "submit"*/

		if (!isset($_POST['submit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
		
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Opret nyhed</legend>
					
					<label for "image">Upload billede;</label>
					<input type="file" name="image" id="image">
					<br>
					<br>


					<label for="title">Overskrift:</label>
					<br>
					<input type="text" class="stretch100" name="title"></input>
					<br>
					<br>

					<label for="content">Brødtekst:</label>
					<br>
					<textarea name="content" class="stretch100" rows=10></textarea>
					<br>

					<input type="submit" name="submit" value="Opret">
				</fieldset>
			</form>

<?php
		}
	}

}

function updateNews($mysqli,$id) {
	if (!isset($_POST['submit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
		$query = "SELECT * FROM `news` WHERE `id` = $id";
		$result = $mysqli->query($query) or die('DATABASEFEJL - Der blev ikke hentet fra DB!');

		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$title 		= $row['title'];
				$content 	= $row['content'];
				$imageOld 	= $row['image'];
				$users_id 	= $row['users_id'];
			}
		} else {
			echo "Nyheden fandtes ikke i databasen";
		}
	}


	if((isset($_SESSION['users_id']) && $_SESSION['users_id'] == $users_id) || userAccess($mysqli,2,true)) { /*Hvis vi er logget indsom den bruger der har lavet nyheden ELLER er logget ind som minimum level 2*/
		if(isset($_POST['submit'])) { /*Hvis der er trykket på knappen med name = "submit"*/
			if(!empty($_POST['title']) && !empty($_POST['content'])) { /*Hvis begge felter i formularen er udfyldt*/
				$title 		= $_POST['title'];
				$content 	= $_POST['content'];
				$imageOld 	= $_POST['imageOld'];

				if(!empty($_FILES['image']['name'])) { /*Hvis der er valgt en billedfil til upload*/
					$image 	= $_FILES['image'];

					/*Kalder upload funktionen. $this fordi det er en funktion i denne klasse.
					Husk at funktionen returner filnavnet, samtidig med at billedet bliver uploadet*/
					$image 	= imageUpload($image);

					@unlink('uploads/img/'.$imageOld);
					@unlink('uploads/img/thumbs/small_'.$imageOld);
				} else {
					$image = $imageOld;
				}
				$query = "UPDATE `news` SET `title` = '$title', `content` = '$content', `image` = '$image' WHERE `id` = $id";
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');
				
				header('location: index.php?page=news&id='.$id);

			} else { /*Hvis et (eller begge) felter i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde  felte!<br><br>";
			}
		} /*Slut på det der skal ske når der er trykket på knappen med name = "submit"*/

		if (!isset($_POST['submit']) || isset($error)) { /*Hvis der ikke er trykket på submit, ELLER hvis der ER trykket på submit, men valideringen er fejlet (så har vi jo sat $error = true")*/
		
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Rediger Nyhed med id: <?php echo $id; ?></legend>

					<input type="hidden" name = "imageOld" value ="<?php echo $imageOld; ?>">
<?php
					if(!empty($imageOld)) {
?>
						<label for "image">Eksisterende billede</label>
						<img src="/uploads/img/<?php echo $imageOld; ?>" class="stretch100">
						<br>
						<br>

						<label for "image">Upload nyt billede;</label>

<?php
					} else {
?>
						<label for "image">Upload billede;</label>
<?php
					} 
?>


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
					<textarea name="content" class="stretch100" rows=10><?php echo $content; ?></textarea>
					<br>

					<input type="submit" name="submit" value="Rediger">
				</fieldset>
			</form>

<?php
		}
	}

}

function deleteNews($mysqli,$id) {
	if(userAccess($mysqli,2,true)) { /*Hvis vi er logget ind, og userlevel er 2 eller højere (defineret i function*/
		$query 	= "SELECT `image` FROM `news` WHERE `id` = $id";
		$result = $mysqli->query($query);

		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$image 	= $row['image'];
					@unlink('uploads/img/'.$image);
					@unlink('uploads/img/thumbs/small_'.$image);
			}

			$query 	= "DELETE FROM `news` WHERE `id` = $id";
			$result = $mysqli->query($query) or die('Det blev ikke slettet!"');

			header('location: index.php?page=adminNews');

		} else {
			echo"Nyheden fandtes ikke i databasen!";
		}
	}

}



function createComment($mysqli,$id) {
	if(isset($_SESSION['username'])) { /*Hvis denne session er sat (defineret) betyder det at vi er logget ind*/
		$username = $_SESSION['username']; /*Vi lægger indholdet af session i en variabel*/
		$query 		= "SELECT `id` FROM `users` WHERE `username` = '$username'";
		$result  	= $mysqli->query($query) or die('Database fejl bruger');

	
			
		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$users_id = $row['id'];
			}
		} else {
			echo "Bruger id kune ikke findes?";
			exit();
		}

		if(isset($_POST['commentsubmit'])) { /*Hvis der er trykket på knappen med name = "submit"*/
			if(!empty($_POST['comment']) ) { /*Hvis  felte i formularen er udfyldt*/
				$comment 		= $_POST['comment'];	
			   					
				$query = "INSERT INTO `comments` (`username`, `comment`, `users_id`, `news_id`) VALUES ('$username', '$comment', $users_id, $id)";
				$result = $mysqli->query($query) or die('Der blev ikke gemt i databasen');
				
				header("Refresh:0");
			} else { /*Hvis felte i formularen IKKE er udfyldt*/
				$error = true; /*Sætter variablen $error, som senere bruges til kontrol*/
				echo "Du skal udfylde  felte!<br><br>";
			}
		} else {
?>
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Opret kommentar</legend>
					
					<label for="content">Skriv kommentar:</label>
					<br>
					<textarea name="comment" class="stretch100" rows=10></textarea>
					<br>

					<input type="submit" name="commentsubmit" value="Opret">
				</fieldset>
			</form>
<?php		
		}
		
	} else {
		echo "Du er ikke logget ind!";

	}
}



function comment($mysqli,$id) {
	$query 	= "SELECT * FROM `comments` WHERE `news_id` = $id ORDER BY `time` DESC";
	$result = $mysqli->query($query);

	if($result->num_rows > 0) {
		echo "<div class='commentWrapper'>";
		
		
		while ($row = $result->fetch_assoc()) {
			$commentid 		= $row['id'];
			$users_id 		= $row['users_id'];
			$comment 		= $row['comment'];
			$created_at 	= $row['time'];
			$created_at 	= date("d/m Y", strtotime($created_at));

			$query = "SELECT * FROM `users` WHERE `id` = $users_id";
			$result2 = $mysqli->query($query);

			if($result2->num_rows > 0) {
				while ($row2 = $result2->fetch_assoc()) {
					$username 	= $row2['username'];
				}
			} else {
				$username = "Ukendt bruger";
			}

			if(userAccess($mysqli,1,false)) {
				if($_SESSION['username'] == $username) {
					echo"ret/slet";  /*Udskriv link til ret og slet*/
				}
			}


			
            echo $created_at."<br>".$username."<br>";
			echo $comment;
			echo "<hr>";
			
			
		}
		
		echo"</div>";

	} else {
		echo"Ingen kommentater i databasen!";
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
					<input type="text" class="stretch100" name="senderName" required="required"></input>
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
					<textarea name="content" class="stretch100" rows=10 required="required"></textarea>
					<br>

					<input type="submit" name="submit" value="Send">
				</fieldset>
			</form>
<?php
	}
}













