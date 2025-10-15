<?php
// --------------------
// CONFIGURATION
// --------------------
$admin_email = "alex24.bonnard@gmail.com";  // 🔸 Remplace par ton adresse réelle
$site_name = "La Bella Forchetta";        // 🔸 Remplace par le nom de ton restaurant

// --------------------
// RÉCUPÉRATION DES DONNÉES
// --------------------
$name = htmlspecialchars($_POST['name'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$date = htmlspecialchars($_POST['date'] ?? '');
$time = htmlspecialchars($_POST['time'] ?? '');
$guests = htmlspecialchars($_POST['guests'] ?? '');
$notes = htmlspecialchars($_POST['notes'] ?? '');

// --------------------
// VALIDATION DES CHAMPS
// --------------------
if (!$name || !$email || !$date || !$time) {
    die("<h2>❌ Erreur</h2><p>Veuillez remplir tous les champs requis.</p>");
}

// --------------------
// VALIDATION DU JOUR ET DE L’HEURE
// --------------------
$day_of_week = date('N', strtotime($date)); // 1 = lundi, 7 = dimanche
$hour = (int)substr($time, 0, 2);
$minute = (int)substr($time, 3, 2);

if ($day_of_week == 1) {
    die("<h2>❌ Désolé !</h2><p>Le restaurant est <strong>fermé le lundi</strong>. Merci de choisir un autre jour.</p>");
}

if ($hour < 19 || ($hour >= 23 && $minute > 0)) {
    die("<h2>⏰ Erreur d’horaire</h2><p>Le restaurant est ouvert <strong>de 19h00 à 23h00</strong>. Merci de choisir une heure valide.</p>");
}

// --------------------
// ENVOI DES EMAILS
// --------------------
$subject_admin = "Nouvelle réservation - $site_name";
$message_admin = "
Nouvelle réservation :

Nom : $name
Email : $email
Date : $date
Heure : $time
Personnes : $guests
Remarques : $notes
";

$subject_user = "Confirmation de réservation - $site_name";
$message_user = "
Bonjour $name,

Merci pour votre réservation au $site_name !
Voici le récapitulatif :

📅 Date : $date
🕓 Heure : $time
👥 Nombre de personnes : $guests

Nous vous accueillerons avec plaisir !
—
L’équipe du $site_name
";

// En-têtes des mails
$headers = "From: $site_name <$admin_email>\r\nReply-To: $admin_email\r\n";

// Envoi
$ok_admin = mail($admin_email, $subject_admin, $message_admin, $headers);
$ok_user = mail($email, $subject_user, $message_user, $headers);

// --------------------
// RÉPONSE À L’UTILISATEUR
// --------------------
if ($ok_admin && $ok_user) {
    echo "<h2>✅ Merci $name !</h2>";
    echo "<p>Votre réservation pour le <strong>$date</strong> à <strong>$time</strong> a été enregistrée.</p>";
    echo "<p>Un email de confirmation a été envoyé à <strong>$email</strong>.</p>";
    echo "<a href='index.html'>← Retour au site</a>";
} else {
    echo "<h2>❌ Erreur</h2><p>Une erreur est survenue lors de l’envoi de l’email. Veuillez réessayer plus tard.</p>";

// faire un système mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // nécessite Composer

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ton_email@gmail.com';
    $mail->Password = 'ton_mot_de_passe_app'; // mot de passe d’application Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('ton_email@gmail.com', 'Ton Restaurant');
    $mail->addAddress($emailClient);

    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de réservation';
    $mail->Body = "Bonjour $nom,<br>Votre réservation a bien été confirmée.";

    $mail->send();
    echo 'Email envoyé avec succès !';
} catch (Exception $e) {
    echo "Erreur lors de l’envoi : {$mail->ErrorInfo}";
}
}
?>
