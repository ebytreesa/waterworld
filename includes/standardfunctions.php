<?php


/*******   FUNCTION: userAccess   ************************************************************************************/
/*******   																										******/
/*******   Funktion som kan bruges i if() statements. 															******/
/*******   $mysqli er med for at gøre databasen tilgængelig inden i funktionen 									******/
/*******   $level er det parameter der sættes som minimum (bruger)level for at funktionen returnerer true 		******/
/*******   $warnings skal defineres som enten true eller false, 												******/
/*******   true betyder at fejlmeddelelser bliver udskrevet, 													******/
/*******   false betyder at de ikke bliver udskrevet															******/
/*******   																										******/
/*********************************************************************************************************************/
function userAccess($mysqli,$level,$warnings) {
	if(isset($_SESSION['username'])) { /*Hvis denne session er sat (defineret) betyder det at vi er logget ind*/
		$username = $_SESSION['username']; /*Vi lægger indholdet af session i en variabel*/

		$query = "SELECT * FROM `eby_users` WHERE `username` = '$username' LIMIT 1"; /*Forepørgsel, find den bruger hvor username er lig med det username der er logget ind (begræns for en sikkerheds skyld til max at finde 1)*/
		$result = $mysqli->query($query) or die('UPS'); /*Udfør forespørges og læg resultatet i en variabel*/

		if($result->num_rows > 0) { /*Hvis der fandtes et resultat*/
			while($row = $result->fetch_assoc()) { /*Loop igennem resultatet en række ad gangen*/
				$userlevel = $row['level']; /*Læg værdien af kolonnen 'level' i en variabel OBS vi kalder '$userlevel' for ikke at overskrive '$level' */
			}

			if($userlevel >= $level) { /*Hvis den bruger der er logget ind har en level der er magen til eller større end den level vi har sat som krav*/
				return true;
			} else {
				if($warnings) { /*Hvis $warnings er sat til true*/
					echo"Du har ikke rettigheder til dette!";
					echo"<p><a href='index.php'>Gå til forside</a></p>";
				}
				return false; /*stop funktionen og returner false*/
			}

		} else {
			if($warnings) {/*Hvis $warnings er sat til true*/
				echo "Brugeren blev ikke fundet!";
				echo"<p><a href='index.php'>Gå til forside</a></p>";
			}
			return false; /*stop funktionen og returner false*/
		}

	} else {
		if($warnings) {/*Hvis $warnings er sat til true*/
			echo "Du er ikke logget ind!";
			echo"<p><a href='index.php'>Gå til forside</a></p>";
		}
		return false; /*stop funktionen og returner false*/
	}
}



/*******   FUNCTION: displayErrors   *********************************************************************************/
/*******   																										******/
/*******   Funktion som kan bruges til at udskrive fejlmeddelelser. 											******/
/*******   KRAV: Der skal være sat en $_SESSION['errormessage'] = "Blabla" 										******/
/*******   - udskift selv Blabla med meddelelsen																******/
/*******   																										******/
/*******   Typisk vil der være en side: index.php?page=errors, som kalder denne funktion						******/
/*******   																										******/
/*******   OBS! 																								******/
/*******   Funktionen imageUpload($image) i denne fil, gør bruge af denne funktion								******/
/*******   																										******/
/*********************************************************************************************************************/
function displayErrors() {
	if(isset($_SESSION['errormessage'])) { /*Hvis der er sat en session ved navn 'errormessage'*/
		$errormessage = $_SESSION['errormessage'];/*Læg indholdet af den session i variablen $errormessage*/
		unset($_SESSION['errormessage']); /*Tøm session ved navn 'errormessage' igen*/

		echo $errormessage; /*Udskriv fejlmeddelelsen på skærmen*/
	} else { /*Hvis der IKKE er sat en session ved navn 'errormessage'*/
		echo "Der er ingen fejl - det er da en fejl?"; /*Udskriv en fejlmeddelelse om at der ikke var en fejlmeddelelse*/
	}
}




/*******   FUNCTION: imageUpload   ***********************************************************************************/
/*******   																										******/
/*******   Funktion som udfører upload af billedfiler og returner det færdigt gemte filnavn.					******/
/*******   																										******/
/*******   KRAV: Der skal være et input felt i den formular hvor billedet skal kunne uploades:					******/
/*******   <input type="file" name="image" id="image">															******/
/*******   																										******/
/*******   KRAV: Der skal i POST koden der hører til formularen være:											******/
/*******   if(!empty($_FILES['image']['name'])) {																******/
/*******   		$image 	= $_FILES['image'];																		******/
/*******   		$image 	= imageUpload($image);																	******/
/*******   }																									******/
/*******   Herefter er billedet uploadet til serveren og filens navn er lagt i variablen $image.				******/
/*******   																										******/
/*********************************************************************************************************************/
function imageUpload($image) {
	$returnUrl = $_SERVER['REQUEST_URI']; /*Her samler vi URL op, så vi kan lade det indgå i et "tilbage" link i fejlmeddelelser*/

    /*Fil information*/
    /*Note: Vi tilføjer tidsstempel til det ønskede filnavn for at undgå problemer med navnesammenfald.
    Hvis vi prøver at uploade en fil med et navn der alerede ligger i destinationsmappen,
    bliver den eksisterende fil nemlig bare overskrevet*/
    $name 		= time()."-".$image['name'];
    $tmpname 	= $image['tmp_name'];
    $type 		= $image['type'];
    $size 		= $image['size'];

    if($size > 5*1024*1024) { /*Hvis filen er større end 5MB (skal angives i bytes, 1 MB = 1024*1024 bytes)*/
    	$_SESSION['errormessage'] = "Filen må ikke overstige 5 MB! <a href='index.php?page=createNews'>Prøv igen</a>"; /*Vi lægger en fejlmeddelelse i en SESSION variabel*/
		header("Location: index.php?page=errors"); /*Redirecter til udskrift af fejlmeddelelser hvor SESSION variablen bliver udskrevet*/
		exit(); /*For at være sikker på at vores header location virker*/
    }

    /*destinationsmapper*/
    $destthumb = $_SERVER['DOCUMENT_ROOT']."/uploads/img/thumbs/";
    $desthigh = $_SERVER['DOCUMENT_ROOT']."/uploads/img/";

    if(is_dir($desthigh) && is_dir($destthumb)) { /*Hvis begge destinationsmapper findes*/
        $allowedExts = array("gif", "jpeg", "jpg", "png", "JPG"); /*Defination af tilladte filtyper*/
        $temp = explode(".", $image['name']); /*Laver et array af tekststrenge, påbegynder ny tekststreng for hvert "."*/
        $extension = end($temp); /*end returnerer det sidste element i et array. I dette tilfælde bliver det så vores fils extension*/
        if (in_array($extension, $allowedExts)){  /*Hvis vores fils extension findes i det array vi lavede med tilladte extensions*/
            
            #Move file
            if(move_uploaded_file($tmpname, $desthigh.$name)) { /*Hvis det lykkedes at uploade råfilen*/
                	                
                list($width, $height) = getimagesize($desthigh.$name); /*Lægger bredde og højde for billedfilen i variabler*/
                $difference = $height/$width; /*Udregner bredde/højde ratio*/
                $nwidth = 200; /*Definerer ny bredde (for thumbnail i dette tilfælde) i en variabel*/
                $nheight = round($nwidth*$difference); /*Definerer ny højde ud fra ratio og afrunder til helt antal pixels*/
                $nwidth_high = 1170; /*Definerer ny bredde (for highres i dette tilfælde) i en variabel*/
                $nheight_high = round($nwidth_high*$difference); /*Definerer ny højde ud fra ratio og afrunder til helt antal pixels*/
                
                list($image, $imagetype) = explode('/', $type); /*$type kommer jo fra $_FILES['type'] og for et jpg returnerer den "image/jpeg"*/
                
                /*Henter til server*/
                switch($imagetype) {
                    case 'png':
                        $sourcehigh = imagecreatefrompng($desthigh.$name); /*Henter billedfilen hvis extension er png*/
                        break;
                    
                    case 'jpg':
                    case 'jpeg':
                    case 'JPG':
                    case 'JPEG':
                        $sourcehigh = imagecreatefromjpeg($desthigh.$name); /*Henter billedfilen hvis extension er jpg, jpeg, JPG eller JPEG*/
                        break;
    
                    case'gif':
                    case'GIF':
                        $sourcehigh = imagecreatefromgif($desthigh.$name); /*Henter billedfilen hvis extension er gif eller GIF*/
                        break;
                }
                
                /*laver en sort billedfil med den ønskede str til højopløst version, 
                første linje i højopløst,anden linje i thumb*/
                $high = imagecreatetruecolor($nwidth_high, $nheight_high); 
                $thumb = imagecreatetruecolor($nwidth, $nheight);

               /* Skalerer upload filen til den ønskede størrelse og kopierer det ind i den sorte billedfil, 
                første linje i højopløst, anden linje i thumb*/
                imagecopyresized($high, $sourcehigh, 0, 0, 0, 0, $nwidth_high, $nheight_high, $width, $height); 
                imagecopyresized($thumb, $sourcehigh, 0, 0, 0, 0, $nwidth, $nheight, $width, $height);
                
                /*Gemmer den færdige fil. Hvilken php funktion der skal bruges afhænger af filtypen, derfor bruges en switch*/
                switch($imagetype) {
                    case 'png':
                        imagepng($high, $desthigh.$name); /*Gemmer highres billedfilen hvis extension er png*/
                        imagepng($thumb, $destthumb."small_".$name); /*Gemmer thumb billedfilen hvis extension er png*/
                        break;
                    
                    case 'jpg':
                    case 'jpeg':
                    case 'JPG':
                    case 'JPEG':
                        imagejpeg($high, $desthigh.$name); /*Gemmer highres billedfilen hvis extension er jpg, jpeg, JPG eller JPEG*/
                        imagejpeg($thumb, $destthumb."small_".$name); /*Gemmer thumb billedfilen hvis extension er jpg, jpeg, JPG eller JPEG*/
                        break;
    
                    case'gif':
                    case'GIF':
                        imagegif($high, $desthigh.$name); /*Gemmer highres billedfilen hvis extension er gif eller GIF*/
                        imagegif($thumb, $destthumb."small_".$name); /*Gemmer thumb billedfilen hvis extension er gif eller GIF*/
                        break;
                }
                return $name;
            }   
        } else { /*Hvis filtypen ikke er blandt de filtyper vi tillader*/
	    	$_SESSION['errormessage'] = "Filen blev ikke uploaded. Du kan kun uploade filer af typen jpg, png eller gif!<br><a href='".$returnUrl."'>Prøv igen</a>"; /*Vi lægger en fejlmeddelelse i en SESSION variabel. OBS Vi indsætter den rtuer URL vi har defineret i den første linje i denne funktion*/
			header("Location: index.php?page=errors"); /*Redirecter til udskrift af fejlmeddelelser hvor SESSION variablen bliver udskrevet*/
			exit(); /*For at være sikker på at vores header location virker*/
        }
    } else { /*Hvis en af destinationsmapperne ikke findes*/
    	$_SESSION['errormessage'] = "Der er ikke oprettet de nødvendige destinationsmapper!<br>Kontakt webmaster ...<br><a href='".$returnUrl."'>Prøv igen</a>"; /*Vi lægger en fejlmeddelelse i en SESSION variabel*/
		header("Location: index.php?page=errors"); /*Redirecter til udskrift af fejlmeddelelser hvor SESSION variablen bliver udskrevet*/
		exit(); /*For at være sikker på at vores header location virker*/
    }
}

/******* FUNCTION: sendViaPHPmailer ******************************************************************************/
/******* ******/
/******* Funktion som sender en mail via PHP mailer. ******/
/******* ******/
/******* KRAV: PHPmailer skal være installeret - mere info på github.com/PHPmailer/PHPMailer ******/
/******* ******/
/******* Funktionen skal altid tilrettes med de rigtige oplysninger vedrørende host, login, port osv. ******/
/******* ******/
/*********************************************************************************************************************/
function sendViaPHPmailer($recieverMail,$recieverName,$subject,$content,$senderMail,$senderName) {
require_once('/PHPmailer/class.phpmailer.php');     // Henter objekt class'en
include_once("/PHPmailer/class.smtp.php");               // Muligvis ikk nødvendig
$mail = new PHPMailer();                            // Opretter $mail som et PHPMailer objekt (  Objektorienteret PHP)
$mail->IsSMTP();                                    // Fortæl at der bruges SMTP
$mail->SMTPDebug = 0;                               // Indstilling for debug
                                                    // 0 = Ingen fejl eller beskeder (når det er testet og virker)
                                                    // 1 = errors and messages (Under test: Udskriv fejl og beskeder)
                                                    // 2 = messages only (Under test: Udskriv kun beskeder)
$mail->SMTPAuth = true;                             // sæt SMTP authentication til
$mail->SMTPSecure = "ssl";                          // Sæt prefix til server
$mail->Host = "smtp.gmail.com";                     // Sæt GMAIL som SMTP server
$mail->Port = 465;                                  // Sæt SMTP porten for GMAIL server
$mail->Username = "ebysanthome@gmail.com";          // GMAIL username
$mail->Password = "eby686513";                      // GMAIL password
$mail->CharSet = 'UTF-8';                           // Sæt mail charset (UTF-8 passer til dansk)
$mail->SetFrom($senderMail,$senderName);            // Modtager af mail ser denne som afsender (Overrides dog af gmail)
$mail->AddReplyTo($senderMail,$senderName);         // Modtager af mail sender til denne ved reply funktion
$mail->Subject = $subject;                          // Mailens emne
$mail->MsgHTML($content);                           // Mailens indhold tages fra $content
$mail->AddAddress($recieverMail,$recieverName);     // Definer modtager mail adresse
/*$mail->AddAttachment("img/frontpage.jpg");*/      // Attachment - deaktiveret her


if(!$mail->Send()) {                                // Hvis afsending af mail mislykkes
   echo "Mailer Error: " . $mail->ErrorInfo;
   return false;         // Returner fejl meddelelese
} else {                                            // Ellers (det lykkedes altså)
return "Beskeden blev sendt!";                      // Returner success meddelelse
}
}