<?php
/**
 * Creation du formulaire Contact Form 7 pour la page Contact
 * Usage unique : wp eval-file wp-content/themes/zougzoug/inc/admin/setup-cf7-form.php
 *
 * Structure HTML identique a la maquette pour que le CSS existant s'applique.
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

// Verifier que CF7 est actif
if (!class_exists('WPCF7_ContactForm')) {
	echo "ERREUR: Contact Form 7 n'est pas actif.\n";
	exit;
}

// Verifier s'il existe deja un formulaire "Contact ZougZoug"
$existing = get_posts([
	'post_type'      => 'wpcf7_contact_form',
	'post_status'    => 'publish',
	'posts_per_page' => 1,
	'title'          => 'Contact ZougZoug',
]);

if ($existing) {
	echo "Le formulaire existe deja : ID #{$existing[0]->ID}\n";
	echo "Shortcode : [contact-form-7 id=\"{$existing[0]->ID}\" title=\"Contact ZougZoug\" html_class=\"contact-form\"]\n";
	exit;
}

// Template du formulaire — structure HTML identique a la maquette
$form_body = '<div class="form-row">
  <div class="form-group">
    <label>Nom</label>
    [text* nom autocomplete:family-name]
  </div>
  <div class="form-group">
    <label>Prénom</label>
    [text* prenom autocomplete:given-name]
  </div>
</div>

<div class="form-group">
  <label>Email</label>
  [email* email autocomplete:email]
</div>

<div class="form-group">
  <label>Sujet</label>
  [select sujet "Choisir un sujet|" "Projet sur mesure|sur-mesure" "Luminaires & architecture|luminaires" "Vaisselle & art de la table|vaisselle" "Cours de céramique|cours" "Autre|autre"]
</div>

<div class="form-group">
  <label>Message</label>
  [textarea* message x6]
</div>

[submit class:cta-button "Envoyer"]';

// Configuration mail
$mail = [
	'subject'            => '[sujet] — Nouveau message via le site',
	'sender'             => 'Atelier ZougZoug <wordpress@zougzoug.lan>',
	'body'               => "Nouveau message depuis le formulaire de contact\n\n"
	                        . "Nom : [nom]\n"
	                        . "Prénom : [prenom]\n"
	                        . "Email : [email]\n"
	                        . "Sujet : [sujet]\n\n"
	                        . "Message :\n[message]\n\n"
	                        . "---\n"
	                        . "Envoyé depuis [_site_title] ([_site_url])",
	'recipient'          => 'atelierzougzoug@gmail.com',
	'additional_headers' => "Reply-To: [email]",
	'attachments'        => '',
	'use_html'           => 0,
	'exclude_blank'      => 0,
];

// Mail 2 (confirmation a l'expediteur) — desactive par defaut
$mail_2 = [
	'active'             => false,
	'subject'            => 'Votre message a bien été envoyé — Atelier ZougZoug',
	'sender'             => 'Atelier ZougZoug <wordpress@zougzoug.lan>',
	'body'               => "Bonjour [prenom],\n\n"
	                        . "Merci pour votre message. Charlotte vous répondra dans les meilleurs délais.\n\n"
	                        . "Récapitulatif :\n"
	                        . "Sujet : [sujet]\n"
	                        . "Message : [message]\n\n"
	                        . "À bientôt,\n"
	                        . "Atelier ZougZoug",
	'recipient'          => '[email]',
	'additional_headers' => '',
	'attachments'        => '',
	'use_html'           => 0,
	'exclude_blank'      => 0,
];

// Messages personnalises en francais
$messages = [
	'mail_sent_ok'     => 'Votre message a bien été envoyé. Merci !',
	'mail_sent_ng'     => 'Une erreur est survenue. Veuillez réessayer plus tard.',
	'validation_error' => 'Veuillez vérifier les champs en erreur.',
	'spam'             => 'Une erreur est survenue. Veuillez réessayer plus tard.',
	'accept_terms'     => 'Vous devez accepter les conditions avant d\'envoyer votre message.',
	'invalid_required' => 'Ce champ est requis.',
	'invalid_too_long' => 'Ce champ est trop long.',
	'invalid_too_short'=> 'Ce champ est trop court.',
];

// Creer le formulaire
$contact_form = WPCF7_ContactForm::get_template([
	'title'  => 'Contact ZougZoug',
	'locale' => 'fr_FR',
]);

$contact_form->set_properties([
	'form'     => $form_body,
	'mail'     => $mail,
	'mail_2'   => $mail_2,
	'messages' => $messages,
]);

$post_id = $contact_form->save();

if ($post_id) {
	echo "Formulaire cree avec succes !\n";
	echo "  ID : #{$post_id}\n";
	echo "  Shortcode : [contact-form-7 id=\"{$post_id}\" title=\"Contact ZougZoug\" html_class=\"contact-form\"]\n";
	echo "\nMettez a jour page-contact.php avec cet ID.\n";
} else {
	echo "ERREUR: impossible de creer le formulaire.\n";
}
