<?php

    namespace WebanUg\NwtuRegistration\Controller;

    use Doctrine\DBAL\DBALException;
    use Doctrine\DBAL\Driver\Exception;
    use Symfony\Component\Mime\Address;
    use TYPO3\CMS\Core\Context\Context;
    use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
    use TYPO3\CMS\Core\Mail\MailMessage;
    use TYPO3\CMS\Core\Utility\GeneralUtility;
    use TYPO3\CMS\Core\Database\ConnectionPool;
    use TYPO3\CMS\Core\Utility\MailUtility;
    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

    class RegistrationController extends ActionController
    {
        public function __construct()
        {
        }

        function registerAction(): string
        {
            $html = "";
            if ( $_POST['action'] == "send_anmeldung" )
            {
                // Eingaben prüfen
                $err = 0;
                $errFlds = array();
                foreach ( $_POST['req'] as $name )
                {
                    if ( !$_POST[ $name ] )
                    {
                        $err++;
                        $errFlds[] = $name;
                    }
                }
                if ( $err > 0 && is_array( $errFlds ) )
                {
                    $html .= "<p>FEHLER: Sie haben nicht alle Pflichtfelder ausgefüllt: ";
                    foreach ( $errFlds as $name )
                    {
                        $html .= "<br>" . $name;
                    }
                    $html .= "<br><br><a href='/?id=77'>Zur Anmeldung</a>";
                }
                else
                {
                    // anmeldung verschicken
                    $to = "office@nwtu.de";
                    $to1 = "j.igras@nwtu.de";

                    $subject = "Anmeldung Lehrgang";
                    $from = "anmeldung@nwtu.de";
                    $msg = "";
                    foreach ( $_POST as $key => $value )
                    {
                        if ( $key == "req" )
                        {
                            continue;
                        }
                        if ( $value )
                        {
                            $msg .= $key . ": " . utf8_decode( $value ) . "\n";
                        }
                        if ( stristr( $key, 'Kup_Dan' ) )
                        {
                            $msg .= "\r\n";
                        }
                    }
                    //                $header = "MIME-Version: 1.0\r\n";
                    //$header .= "From: Anmeldungsformular <$from>\r\n";
                    //                $header .= "Content-type: text/html; charset=utf-8\r\n";
                    // $header .= "Reply-To: $from\r\n";
                    // $header .= "Cc: $cc\r\n";  // falls an CC gesendet werden soll
                    //                $header .= "X-Mailer: PHP " . phpversion();
                    $mail = GeneralUtility::makeInstance( MailMessage::class );
                    $mail->setFrom( MailUtility::getSystemFrom() );
                    $mail->to(
                        new Address( $to ),
                        new Address( $to1 )
                    );
                    $mail->setReplyTo( $from );
                    $mail->subject( $subject );
                    $mail->html( $msg );;
                    $res = $mail->send();
                    if ( $res )
                    {
                        $html .= "<h1>Vielen Dank f&uuml;r Ihre Anmeldung</h1><p>Wir werden uns schnellstm&ouml;glich mit Ihnen in Verbindung setzen.</p>";
                    }
                }
            }
            else
            {
                $html .= "
				
				<style>
#user_confirm {
	float:left;
	margin:0 10px 0 0;
}
.form_row {
  display: flex !important;
  gap: 10px;
  padding: 1% 0;
}
.anmeldung_breitensport {
  padding:2%;
}
label {
	width:40%;
}
				</style>
            <form class=\"anmeldung_breitensport\" method=\"post\" action=\"" . $_SERVER['REQUEST_URI'] . "\">
                <fieldset>
                    <legend><h2>Anmeldungsformular</h2></legend>
                    <div class=\"form_row\">
                        <label>Lehrgang am (Datum): </label> <input type=\"date\" name=\"Datum\" value=\"\" required=\"1\">
                    </div>
                    <div class=\"form_row\">
                        <label>Lehrgang in (Ort): </label> <input type=\"text\" placeholder=\"Ort\" name=\"Ort\" value=\"\" required=\"1\">
                    </div>
                    <div class=\"form_row\">
                        <label>Name des teilnehmenden Vereins: </label> <input type=\"text\" placeholder=\"Vereinsname\" name=\"Verein\" value=\"\" required=\"1\">
                    </div>
                    <div class=\"form_row\">
                        <label>Name des verantwortlichen Vereinsvertreters: </label> <input type=\"text\" placeholder=\"Name\" name=\"Name_Anmeldung\" value=\"\"  required=\"1\">
                    </div>
                    <div class=\"form_row\">
                        <label>E-Mail des verantwortlichen Vereinsvertreters: </label> <input type=\"text\" placeholder=\"E-Mail Adresse\" name=\"E-Mail_Anmeldung\" value=\"\"  required=\"0\">
                    </div>
                    <div class=\"form_row\">
                        <label>Telefonnummer des verantwortlichen Vereinsvertreters: </label> <input type=\"text\" placeholder=\"Telefonnummer\" name=\"Telefonnummer\" value=\"\" required=\"1\">
                    </div>
                    <div class=\"form_row\">
                        <label>Name des verantwortlichen Vereinsvertreters am <i>Lehrgangstag vor Ort</i>: </label> <input type=\"text\" placeholder=\"Name\" name=\"Name_vor_Ort\" value=\"\"  required=\"1\">
                    </div>
                    
                    <div class=\"formtabelle\">
                        <table cellspacing=\"4\">
                            <thead>
                                <td>Lfd.Nr.</td>
                                <td>Name</td>
                                <td>DTU Pass-Nr.</td>
                                <td>Geb.-Datum</td>
                                <td>Kup/Dan</td>
                            </thead>\n";

                for ( $i = 1; $i <= 20; $i++ )
                {
                    $html .= "  <tr>\n
                                            <td style=\"text-align:center;\">" . $i . ".</td>\n
                                            <td><input type='text' name='" . $i . "_Name' value=''></td>\n
                                            <td><input type='text' name='" . $i . "_DTU_PassNr' value=''></td>\n
                                            <td><input type='text' name='" . $i . "_Geb_Datum' value=''></td>\n
                                            <td><input type='text' name='" . $i . "_Kup_Dan' value=''></td>\n
                                        </tr>\n";
                }
                $html .= "  

				<tr>
					<td></td>
					<td colspan=\"3\" style=\"padding-top:20px;\">
						<input type=\"checkbox\" name=\"Datenschutzbestätigung\" id=\"user_confirm\" class=\"req yellow\" required=\"1\" />
						<input type=\"hidden\" name=\"req[]\" value=\"Datenschutzbestätigung\" />
						<p><label style=\"width:90%;\" for=\"user_confirm\">Ich stimme zu, dass meine Angaben aus dem Formular zur Beantwortung
			meiner Anfrage erhoben und verarbeitet und gespeichert werden.
			<br>
			Hinweis: Sie können Ihre Einwilligung jederzeit für die Zukunft per E-Mail an office@nwtu.de widerrufen.
			Detaillierte Informationen zum Umgang mit Nutzerdaten finden Sie in unserer
			Datenschutzerklärung</label></p>
					</td>
				</tr>

				<tr>
					<td></td>
					<td colspan=\"4\">
						<input type=\"hidden\" name=\"action\" value=\"send_anmeldung\" />
						<input style=\"padding:10px;margin-top:20px;\" type=\"submit\" value=\"Anmeldung abschicken\" />
					</td>
				</tr>          
                        </table>
                        
                    </div>
                    
                </fieldset>
            </form>";
            }
            return $html;
        }
    }
