<?php

/**
 * REMARK: This is a generated file, please do not alter it by editing directly.
 * You can change errors, labels, messages in Fork.
 *
 * Locale (dutch)
 *
 * @package		Backend
 * @subpackage	locale
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */

/**
 * Errors
 *
 * An error is a sentence that represents a full error-message.
 *
 * REMARK: HTML is allowed, caps are needed. When using in a template you should *NEVER* use modifiers that can't handle HTML
 * Please do use htmlentities for special chars, this will prevent possible bugs in the rendering.
 *
 * An entry will look like: $err['<module>']['<key>'] = '<value>';
 * ex: $err['core']['LoginFailed'] = 'Login failed.';
 */
$err = array();
$err['core'] = array();
// a
// b
// c
$err['core']['ContentIsRequired'] = 'Gelieve inhoud in te geven.';
// d
// e
$err['core']['EmailIsInvalid'] = 'Gelieve een geldig emailadres in te geven.';
// f
// g
$err['core']['GeneralFormError'] = 'Er ging iets mis. Kijk de gemarkeerde velden na.';
// h
// i
$err['core']['InvalidParameters'] = 'Ongeldige parameters.';
$err['core']['InvalidUsernamePasswordCombination'] = 'De combinatie van gebruikersnaam en wachtwoord is niet correct.';
// j
// k
// l
// m
// n
$err['core']['NameIsRequired'] = 'Gelieve een naam in te geven.';
$err['core']['NonExisting'] = 'Dit item bestaat niet.';
$err['users']['NonExisting'] = 'De gebruiker bestaat niet.';
// o
$err['core']['OnlyJPGAndGifAreAllowed'] = 'Enkel jpg, jpeg en gif zijn toegelaten.';
// p
$err['core']['PasswordIsRequired'] = 'Gelieve een wachtwoord in te geven.';
// q
// r
// s
$err['core']['SomethingWentWrong'] = 'Er ging iets mis. Probeer later opnieuw.';
$err['core']['SurnameIsRequired'] = 'Gelieve een achternaam in te geven.';
// t
$err['core']['TitleIsRequired'] = 'Gelieve een titel in te geven.';
// u
$err['core']['UsernameIsRequired'] = 'Gelieve een gebruikersnaam in te geven.';
// v
// w
// x
// y
// z


/**
 * Labels
 *
 * A label is a translation for one to three words. No sentences. A sentence can be placed in the messages-area.
 *
 * REMARK: HTML is *NOT* allowed, everything should be lowercase, use modifiers to alter string
 * Please do use htmlentities for special chars, this will prevent possible bugs in the rendering.
 *
 * An entry will look like: $lbl['<module>']['<key>'] = '<value>';
 * ex: $lbl['core']['Login'] = 'login';
 */
$lbl = array();
$lbl['core'] = array();
// a
$lbl['core']['Add'] = 'toevoegen';
$lbl['pages']['Add'] = 'pagina toevoegen';
$lbl['core']['Avatar'] = 'avatar';
// b
$lbl['core']['Blog'] = 'blog';
// c
$lbl['core']['Cancel'] = 'annuleer';
$lbl['core']['Content'] = 'inhoud';
// d
$lbl['core']['Date'] = 'datum';
$lbl['core']['Delete'] = 'verwijderen';
$lbl['core']['Dutch'] = 'nederlands';
// e
$lbl['core']['Edit'] = 'bewerken';
$lbl['core']['Email'] = 'email';
// f
// g
// h
// i
$lbl['core']['InterfaceLanguage'] = 'interface-taal';
// j
// k
// l
$lbl['core']['LastEditedOn'] = 'laats aangepast op';
$lbl['core']['Login'] = 'login';
$lbl['core']['Logout'] = 'afmelden';
// m
// n
$lbl['core']['Name'] = 'naam';
$lbl['core']['Next'] = 'volgende';
$lbl['core']['Nickname'] = 'nickname';
// o
$lbl['core']['OK'] = 'ok';
// p
$lbl['core']['Pages'] = 'pagina\'s';
$lbl['core']['Password'] = 'wachtwoord';
$lbl['core']['Previous'] = 'vorige';
// q
// r
$lbl['core']['Revisions'] = 'versies';
// s
$lbl['core']['Submit'] = 'verzenden';
$lbl['core']['Surname'] = 'achternaam';
// t
$lbl['core']['Time'] = 'tijd';
$lbl['core']['Title'] = 'titel';
// u
$lbl['core']['Update'] = 'wijzig';
$lbl['core']['Username'] = 'gebruikersnaam';
$lbl['core']['UseThisVersion'] = 'gebruik deze versie';
// v
// w
// x
// y
// z


/**
 * Messages
 *
 * A message is a sentence.
 *
 * REMARK: HTML is allowed, caps are needed. When using in a template you should *NEVER* use modifiers that can't handle HTML
 * Please do use htmlentities for special chars, this will prevent possible bugs in the rendering.
 *
 * An entry will look like: $err['<module>']['<key>'] = '<value>';
 * ex: $msg['users']['UserAdded'] = 'The user is added.';
 */
$msg = array();
// a
$msg['core']['Added'] = 'item toegevoegd.';
$msg['spotlight']['Added'] = 'Item <em>%s</em> toegevoegd.';
$msg['users']['Added'] = 'Gebruiker <em>%s</em> toegevoegd.';
// b
// c
$msg['core']['ConfirmDelete'] = 'Ben je zeker dat je dit item wil verwijderen?';
$msg['spotlight']['ConfirmDelete'] = 'Ben je zeker dat je <em>%s</em> wil verwijderen?';
$msg['users']['ConfirmDelete'] = 'Ben je zeker dat je de gebruiker <em>%s</em> wil verwijderen?';
// d
$msg['core']['Deleted'] = 'Het item is verwijderd.';
$msg['spotlight']['Deleted'] = 'Het item <em>%s</em> is verwijderd.';
$msg['users']['Deleted'] = 'De gebruiker <em>%s</em> is verwijderd.';
// e
$msg['core']['Edited'] = 'Wijzigingen opgeslagen.';
$msg['spotlight']['Edited'] = 'Wijzigingen voor <em>%s</em> opgeslagen.';
$msg['users']['Edited'] = 'Wijzigingen voor gebruiker <em>%s</em> opgeslagen.';
// f
// g
// h
$msg['core']['HeaderEdit'] = '<em>%s</em> bewerken';
$msg['users']['HeaderEdit'] = 'Gebruiker <em>%s</em> bewerken';
$msg['core']['HeaderIndex'] = 'Overzicht';
$msg['users']['HeaderIndex'] = 'Gebruikers overzicht';
// i
// j
// k
// l
$msg['core']['LoggedInAs'] = 'aangemeld als';
// m
// n
$msg['core']['NoDrafts'] = 'Er zijn geen drafts.';
$msg['core']['NoItems'] = 'Er zijn geen items aanwezig.';
$msg['core']['NoItemsPublished'] = 'Er zijn geen items gepubliceerd.';
$msg['core']['NoItemsScheduled'] = 'Er zijn geen items gepland.';
$msg['core']['NoRevisions'] = 'Er zijn nog geen versies.';
$msg['core']['NotAllowedActionTitle'] = 'Verboden';
$msg['core']['NotAllowedActionMessage'] = 'Deze actie is niet toegestaan.';
$msg['core']['NotAllowedModuleTitle'] = 'Verboden';
$msg['core']['NotAllowedModuleMessage'] = 'Deze module is niet toegestaan.';
// o
// p
// q
// r
$msg['core']['RevisionsExplanation'] = 'De 5 laatst opgeslagen versies worden bijgehouden. <strong>De huidige versie wordt pas overschreven als je het item opslaat.</strong>';
// s
$msg['core']['SequenceChanged'] = 'De volgorde is aangepast.';
// t
// u
$msg['core']['UsingARevision'] = 'Je gebruikt een oudere versie!';
// v
$msg['core']['VisibleOnSite'] = 'Zichtbaar op de website?';
// w
// x
// y
// z

?>