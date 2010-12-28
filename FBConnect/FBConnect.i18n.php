<?php
/*
 * Copyright © 2008-2010 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * FBConnect.i18n.php
 * 
 * Internationalization file for FBConnect.
 */

$messages = array();

/** English */
$messages['en'] = array(
	// Extension name
	'fbconnect'   => 'Facebook Connect',
	'fbconnect-desc'     => 'Enables users to [[Special:Connect|Connect]] with their [http://www.facebook.com Facebook] accounts.
Offers authentification based on Facebook groups and the use of FBML in wiki text',
	// Group containing Facebook Connect users
	'group-fb-user'           => 'Facebook Connect users',
	'group-fb-user-member'    => 'Facebook Connect user',
	'grouppage-fb-user'       => '{{ns:project}}:Facebook Connect users',
	// Group for Facebook Connect users beloning to the group specified by $fbUserRightsFromGroup
	'group-fb-groupie'        => 'Group members',
	'group-fb-groupie-member' => 'Group member',
	'grouppage-fb-groupie'    => '{{ns:project}}:Group members',
	// Officers of the Facebook group
	'group-fb-officer'        => 'Group officers',
	'group-fb-officer-member' => 'Group officer',
	'grouppage-fb-officer'    => '{{ns:project}}:Group officers',
	// Admins of the Facebook group
	'group-fb-admin'          => 'Group admins',
	'group-fb-admin-member'   => 'Group administrator',
	'grouppage-fb-admin'      => '{{ns:project}}:Group admins',
	// Personal toolbar
	'fbconnect-connect'  => 'Log in with Facebook Connect',
	'fbconnect-connect-simple'  => 'Connect',
	'fbconnect-convert'  => 'Connect this account with Facebook',
	'fbconnect-logout'   => 'Logout of Facebook',
	'fbconnect-link'     => 'Back to facebook.com',
	'fbconnect-or'       => 'OR',

	// Special:Connect
	'fbconnect-title'    => 'Connect account with Facebook',
	'fbconnect-intro'    => 'This wiki is enabled with Facebook Connect, the next evolution of Facebook Platform.
This means that when you are Connected, in addition to the normal [[Wikipedia:Help:Logging in#Why log in?|benefits]] you see when logging in, you will be able to take advantage of some extra features...',
	'fbconnect-click-to-login' => 'Click to login to this site via Facebook',
	'fbconnect-click-to-connect-existing' => 'Click to connect your Facebook account to $1',
	'fbconnect-conv'     => 'Convenience',
	'fbconnect-convdesc' => 'Connected users are automatically logged you in.
If permission is given, then this wiki can even use Facebook as an email proxy so you can continue to receive important notifications without revealing your e-mail address.',
	'fbconnect-fbml'     => 'Facebook markup language',
	'fbconnect-fbmldesc' => 'Facebook has provided a bunch of built-in tags that will render dynamic data.
Many of these tags can be included in wiki text, and will be rendered differently depending on which Connected user they are being viewed by.',
	'fbconnect-comm'     => 'Communication',
	'fbconnect-commdesc' => 'Facebook Connect ushers in a whole new level of networking.
See which of your friends are using the wiki, and optionally share your actions with your friends through the Facebook news feed.',
	'fbconnect-welcome'  => 'Welcome, Facebook Connect user!',
	'fbconnect-loginbox' => "Or '''login''' with Facebook:

$1",
	'fbconnect-merge'    => 'Merge your wiki account with your Facebook ID',
/*
	'fbconnect-mergebox' => 'This feature has not yet been implemented.
Accounts can be [[Special:Renameuser|merged manually]] if the [http://mediawiki.org/wiki/Extension:Renameuser|Rename user extension] has been installed.

$1

Note: This can be undone by a sysop.',
*/
	'fbconnect-logoutbox' => '$1

This will also log you out of Facebook and all connected sites, including this wiki.',
	'fbconnect-listusers-header' => '$1 and $2 privileges are automatically transfered from the officer and admin titles of the Facebook group $3.

For more info, please contact the group creator $4.',
	// Prefix to use for automatically-generated usernames
	'fbconnect-usernameprefix' => 'FacebookUser',
	// Special:Connect
	'fbconnect-error' => 'Verification error',
	'fbconnect-errortext' => 'An error occured during verification with Facebook Connect.',
	'fbconnect-cancel' => 'Action cancelled',
	'fbconnect-canceltext' => 'The previous action was cancelled by the user.',
	'fbconnect-invalid' => 'Invalid option',
	'fbconnect-invalidtext' => 'The selection made on the previous page was invalid.',
	'fbconnect-success' => 'Facebook verification succeeded',
	'fbconnect-successtext' => 'You have been successfully logged in with Facebook Connect.',
	'fbconnect-success-connecting-existing-account' => 'Your facebook account has been connected. To change which events get pushed to your facebook news feed, please visit your <a href="$1">preferences</a> page.',
	#'fbconnect-optional' => 'Optional',
	#'fbconnect-required' => 'Required',
	'fbconnect-nickname' => 'Nickname',
	'fbconnect-fullname' => 'Fullname',
	'fbconnect-gender' => 'Gender',
	'fbconnect-email' => 'E-mail address',
	'fbconnect-language' => 'Language',
	'fbconnect-timecorrection' => 'Time zone correction (hours)',
	'fbconnect-chooselegend' => 'Username choice',
	'fbconnect-chooseinstructions' => 'All users need a nickname; you can choose one from the options below.',
	'fbconnect-invalidname' => 'The nickname you chose is already taken or not a valid nickname.
Please chose a different one.',
	'fbconnect-choosenick' => 'Your Facebook profile name ($1)',
	'fbconnect-choosefirst' => 'Your first name ($1)',
	'fbconnect-choosefull' => 'Your full name ($1)',
	'fbconnect-chooseauto' => 'An auto-generated name ($1)',
	'fbconnect-choosemanual' => 'A name of your choice:',
	'fbconnect-chooseexisting' => 'An existing account on this wiki',
	'fbconnect-chooseusername' => 'Username:',
	'fbconnect-choosepassword' => 'Password:',
	'fbconnect-updateuserinfo' => 'Update the following personal information:',
	'fbconnect-alreadyloggedin-title' => 'Already connected',
	'fbconnect-alreadyloggedin' => "'''You are already logged in, $1!'''

If you want to use Facebook Connect to log in in the future, you can [[Special:Connect/Convert|convert your account to use Facebook Connect]].",
	/*
	'fbconnect-convertinstructions' => 'This form lets you change your user account to use an OpenID URL or add more OpenID URLs',
	'fbconnect-convertoraddmoreids' => 'Convert to OpenID or add another OpenID URL',
	'fbconnect-convertsuccess' => 'Successfully converted to OpenID',
	'fbconnect-convertsuccesstext' => 'You have successfully converted your OpenID to $1.',
	'fbconnect-convertyourstext' => 'That is already your OpenID.',
	'fbconnect-convertothertext' => 'That is someone else\'s OpenID.',
	*/
	'fbconnect-logged-in-now-connect' => "You have been logged in to your account, please click the login button to connect it with Facebook.",
	'fbconnect-logged-in-now-connect-title' =>  "Almost done!",
	'fbconnect-modal-title' => 'Finish your account setup',
    'fbconnect-modal-headmsg' => 'Almost done!',
	'fbconnect-error-creating-user' => 'Error creating the user in the local database.',
	'fbconnect-error-user-creation-hook-aborted' => 'A hook (extension) aborted the account creation with the message: $1',
	'fbconnect-prefstext' => 'Facebook Connect',
	'fbconnect-link-to-profile' => 'Facebook profile',
	'fbconnect-prefsheader' => 'By default, some events will push items to your Facebook feed. You can customise these now, or later at any time in your preferences.',
	// From MediaWiki Translatewiki
	#'fbconnect-prefsheader' => "To control which events will push an item to your Facebook news feed, <a id='fbConnectPushEventBar_show' href='#'>show preferences</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>hide preferences</a>",
	'fbconnect-prefs-can-be-updated' => 'You can update these any time by visiting the "$1" tab of your preferences page.',
	'fbconnect-prefs-show' => 'Show feed preferences >>',
    'fbconnect-prefs-hide' => 'Hide feed preferences >>',
	'fbconnect-prefs-post' => 'Post to my Facebook News Feed when I:',
    'fbconnect-connect-msg' => "Congratulations! Your Wikia and Facebook accounts are now connected. <br/> Check your <a href='$1'>preferences</a> to control which events appear in Facebook feed.",
    'fbconnect-connect-error-msg' => "We're sorry, we couldn't complete your connection without permission to post to your Facebook account. After setup you have [[w:c:help:Help:Facebook_Connect#Sharing_with_your_Facebook_activity_feed|full control]] of what's posted to your news feed. Please try again.",
	'fbconnect-disconnect-link' => "You can also <a id='fbConnectDisconnect' href='#'> disconnect your Wikia account from Facebook.</a> You will able continue using your Wikia account as normal, with your history (edits, points, achievements ) intact.",
	'fbconnect-disconnect-done' => "Disconnecting <span id='fbConnectDisconnectDone'>... done! </span>",
	'fbconnect-disconnect-info' => 'We have emailed a new password to use with your account - you can log in with the same username as before.',
	'tog-fbconnect-push-allow-never' => 'Never send anything to my news feed (overrides other options)',
	'fbconnect-reclamation-title' => 'Disconnecting from Facebook',
	'fbconnect-reclamation-body' => 'Your account is now disconnected from Facebook! <br/><br/>  We have emailed a new password to use with you account - you can log in with the same username as before. Hooray!
											<br/><br/> To login go to: $1',
    'fbconnect-reclamation-title-error' => 'Disconnecting from Facebook',
	'fbconnect-reclamation-body-error' => 'There was some error during disconnecting from Facebook or you account is already disconnected. 
											<br/><br/> To login go to: $1',
    'fbconnect-unknown-error' => 'Unknown error, try again or contact with us.',          
	'fbconnect-passwordremindertitle'      	=> 'Your Wikia account is now disconnected from Facebook!',
    'fbconnect-passwordremindertitle-exist' => 'Your Wikia account is now disconnected from Facebook!',
	'fbconnect-passwordremindertext'       => 'Hi,
It looks like you\'ve just disconnected your Wikia account from Facebook. We\'ve kept all of your history, edit points and achievements intact, so don\'t worry!

You can use the same username as before, and we\'ve generated a new password for you to use. Here are your details:

Username: $2
Password: $3

The replacement password has been sent only to you at this email address.

Thanks,

The Wikia Community Team',
	'fbconnect-passwordremindertext-exist'	=> 'Hi,
It looks like you\'ve just disconnected your Wikia account from Facebook. We\'ve kept all of your history, edit points and achievements intact, so don\'t worry!

You can use the same username and password as you did before you connected.

Thanks,

The Wikia Community Team',

	'fbconnect-msg-for-existing-users' => "<p>Already a Wikia user?</p><br/><br/>If you would like to connect this facebook account to an existing Wikia account, please <a class='loginAndConnect' href='$1'>login</a> first.",
	
	'fbconnect-invalid-email' => "Please provide a valid email address.",
	'fbconnect-wikia-login-w-facebook' => 'Log in / Sign Up with Facebook Connect',
	'fbconnect-wikia-login-or-create' => 'Log in / Create an account',
	'fbconnect-wikia-login-bullets' => '<ul><li>Sign up in just a few clicks</li><li>You have control of what goes to your feed</li></ul>',
	
	'fbconnect-fbid-is-already-connected-title' => 'Facebook account is already in use',
	'fbconnect-fbid-is-already-connected' => 'The Facebook account you are attempting to connect to your Wikia account is already connected to a different Wikia account. If you would like to connect your current Wikia account to that Facebook id, please disconnect the Facebook account from your other username first by visiting the "Facebook Connect" tab of your Preferences page.',
	'fbconnect-fbid-connected-to' => 'The Wikia username that is currently connected to this Facebook id is <strong>$1</strong>.',
    'fbconnect-connect-next' => 'Next >>' ,
);

/**
 * Message documentation (Message documentation)
 * This is shown to translators to help them know what the string is for.
 * @author Garrett Brown
 */
$messages['qqq'] = array(
	'fbconnect-desc' => 'Short description of the FBConnect extension, shown in [[Special:Version]]. Do not translate or change links.',
	'fbconnect-listusers-header' => '$1 is the name of the Bureaucrat group, $2 is the name of the Sysop group.',
	'fbconnect-or' => 'This is just the word "OR" in English, used to separate the Facebook Connect login option from the normal Wikia login options on the ajaxed login dialog box.',
	'fbconnect-email' => '{{Identical|E-mail address}}',
	'fbconnect-language' => '{{Identical|Language}}',
	'fbconnect-choosepassword' => '{{Identical|Password}}',
	'fbconnect-alreadyloggedin' => '$1 is a user name.',
	'fbconnect-logged-in-now-connect' => 'This message is shown in a modal dialog along with an fbconnect button when the user is trying to login and connect. This is a workaround for popup blockers.',
	'fbconnect-prefstext' => 'FBConnect preferences tab text above the list of preferences',
	'fbconnect-link-to-profile' => 'Appears next to the user\'s name in their Preferences page and this text is made into link to the profile of that user if they are connected.',
	'fbconnect-msg-for-existing-users' => 'This is displayed next to the username field in the choose-name form.
If a user comes to the site and facebook connects, the purpose of this message is to let them know how to procede if they are actually trying to connect their facebook account to an existing account.',
	'fbconnect-connect-next' => 'This text appears on the button in the login-and-connect dialog.
After a user enters their username/password, this will slide them over to the next screen which is the Facebook Connect button.'
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'fbconnect-link' => 'Terug na facebook.com',
	'fbconnect-comm' => 'Kommunikasie',
	'fbconnect-error' => 'Verifikasiefout',
	'fbconnect-invalid' => 'Ongeldige opsie',
	'fbconnect-nickname' => 'Bynaam',
	'fbconnect-fullname' => 'Volle naam',
	'fbconnect-email' => 'E-posadres',
	'fbconnect-language' => 'Taal',
	'fbconnect-choosefirst' => 'U eerste naam ($1)',
	'fbconnect-choosefull' => 'U volledige naam ($1)',
	'fbconnect-chooseauto' => "'n Outomaties gegenereerde naam ($1)",
	'fbconnect-choosemanual' => "'n Naam van u keuse:",
	'fbconnect-chooseexisting' => "'n Bestaande gebruiker op hierdie wiki:",
	'fbconnect-chooseusername' => 'Gebruikersnaam:',
	'fbconnect-choosepassword' => 'Wagwoord:',
	'fbconnect-link-to-profile' => 'Facebook-profiel',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'fbconnect' => 'Злучэньне Facebook',
	'fbconnect-desc' => 'Дае магчымасьць удзельнікам [[Special:Connect|злучыцца]] з іх рахункам на [http://www.facebook.com Facebook].
Прапануе аўтэнтыфікацыю заснаваную на групах Facebook і выкарыстаньні FBML у вікі-тэксьце',
	'group-fb-user' => 'Карыстальнікі злучэньня Facebook',
	'group-fb-user-member' => 'Карыстальнік злучэньня Facebook',
	'grouppage-fb-user' => '{{ns:project}}:Карыстальнікі злучэньня Facebook',
	'group-fb-groupie' => 'Удзельнікі групы',
	'group-fb-groupie-member' => 'Удзельнік групы',
	'grouppage-fb-groupie' => '{{ns:project}}:Удзельнікі групы',
	'group-fb-officer' => 'Кіраўнікі групы',
	'group-fb-officer-member' => 'Кіраўнік групы',
	'grouppage-fb-officer' => '{{ns:project}}:Кіраўнікі групы',
	'group-fb-admin' => 'Адміністратары групы',
	'group-fb-admin-member' => 'Адміністратар групы',
	'grouppage-fb-admin' => '{{ns:project}}:Адміністратары групы',
	'fbconnect-connect' => 'Увайсьці ў сыстэму праз злучэньне Facebook',
	'fbconnect-convert' => 'Злучыць гэты рахунак і Facebook',
	'fbconnect-logout' => 'Выйсьці з Facebook',
	'fbconnect-link' => 'Вярнуцца на facebook.com',
	'fbconnect-title' => 'Злучыць рахунак з Facebook',
);

/** Breton (Brezhoneg)
 * @author Gwendal
 */
$messages['br'] = array(
	'fbconnect' => 'Facebook Connect',
	'group-fb-user' => 'Implijerien Facebook Connect',
	'group-fb-user-member' => 'Implijer Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}: Implijerien Facebook Connect',
	'group-fb-groupie' => 'Izili ar strollad',
	'group-fb-groupie-member' => 'Ezel ar strollad',
	'grouppage-fb-groupie' => '{{ns:project}}: Izili ar strollad',
	'group-fb-admin' => 'Merourien ar strollad',
	'group-fb-admin-member' => 'Merour ar strollad',
	'grouppage-fb-admin' => '{{ns:project}}: Merourien ar strollad',
	'fbconnect-connect' => 'Kevreañ gant Facebook Connect',
	'fbconnect-convert' => "Kevreañ ar c'hont-mañ gant Facebook",
	'fbconnect-logout' => 'Digrevreañ eus Facebook',
	'fbconnect-link' => 'E facebook.com en-dro',
	'fbconnect-title' => 'Kont kevreañ gant Facebook',
	'fbconnect-click-to-login' => "Klikit evit kevreañ el lec'hienn-mañ dre Facebook",
	'fbconnect-click-to-connect-existing' => 'Klikit evit kevreañ ho kont Facebook da $1',
	'fbconnect-comm' => 'Daremprederezh',
	'fbconnect-loginbox' => "Pe '''kevreañ''' gant Facebook:

$1",
	'fbconnect-error' => 'Fazi gwiriañ',
	'fbconnect-invalid' => "N'haller ket dibab an dra-se",
	'fbconnect-nickname' => 'Lesanv',
	'fbconnect-fullname' => 'Anv klok',
	'fbconnect-email' => "Chomlec'h postel",
	'fbconnect-language' => 'Yezh',
	'fbconnect-chooselegend' => 'Dibab an anv implijer',
	'fbconnect-chooseinstructions' => "An holl implijerien o deus ezhomm ul lesanv; gallout a rit dibab unan eus ar c'hinnigoù a-is.",
	'fbconnect-invalidname' => 'Al lezanv ho peus dibabet a zo direizh pe implijet dija.
Trugarez da zibab unan all.',
	'fbconnect-choosefull' => "Hoc'h anv klok ($1)",
	'fbconnect-chooseauto' => 'Un anv krouet emgefre ($1)',
	'fbconnect-choosemanual' => "Un anv dibabet ganeoc'h :",
	'fbconnect-chooseexisting' => 'Ur gont zo anezhi war ar wiki-mañ',
	'fbconnect-chooseusername' => 'Anv implijer :',
	'fbconnect-choosepassword' => 'Ger-tremen :',
	'fbconnect-alreadyloggedin' => "'''Kevreet oc'h dija, $1!'''

Ma fell deoc'h implijout Facebook Connect da gevreañ diwezhatoc'h, e c'hallit [[Special:Connect/Convert|amdreiñ ho kont evit implijout Facebook Connect]].",
	'fbconnect-prefstext' => 'Facebook Connect',
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Stellt eine [[Special:Connect|Spezialseite]] bereit mit der Benutzer eine Verbindung mit ihrem [http://de-de.facebook.com/ Facebook-Konten] herstellen können.
Zudem wird die Authentifizierung basierend auf Facebook-Gruppen und der Einsatz von FBML in Wikitext ermöglicht.',
	'group-fb-user' => 'Facebook-Connect-Benutzer',
	'group-fb-user-member' => 'Facebook-Connect-Benutzer',
	'grouppage-fb-user' => '{{ns:project}}:Facebook-Connect-Benutzer',
	'group-fb-groupie' => 'Gruppenmitglieder',
	'group-fb-groupie-member' => 'Gruppenmitglied',
	'grouppage-fb-groupie' => '{{ns:project}}:Gruppenmitglieder',
	'group-fb-officer' => 'Gruppenrechteverwalter',
	'group-fb-officer-member' => 'Gruppenrechteverwalter',
	'grouppage-fb-officer' => '{{ns:project}}:Gruppenrechteverwalter',
	'group-fb-admin' => 'Gruppenadministratoren',
	'group-fb-admin-member' => 'Gruppenadministrator',
	'grouppage-fb-admin' => '{{ns:project}}:Gruppenadministratoren',
	'fbconnect-connect' => 'Anmelden mit Facebook Connect',
	'fbconnect-convert' => 'Dieses Konto mit Facebook verknüpfen',
	'fbconnect-logout' => 'Aus Facebook abmelden',
	'fbconnect-link' => 'Zurück zu de-de.facebook.com',
	'fbconnect-title' => 'Konto mit Facebook verknüpfen',
	'fbconnect-intro' => 'Dieses Wiki hat Facebook Connect, die nächsten Weiterentwicklung der Plattform Facebook, aktiviert.
Dies bedeutet, dass man, sofern man angemeldet ist, zusätzlich zu den herkömmlichen [[Wikipedia:Help:Logging in#Why log in?|Vorteilen]] einer Anmeldung, weitere zusätzliche Funktionen nutzen kann...',
	'fbconnect-click-to-login' => 'Auf diese Schaltfläche klicken, um sich auf diesem Wiki via Facebook anzumelden',
	'fbconnect-click-to-connect-existing' => 'Auf diese Schaltfläche klicken, um das Facebook-Konto mit $1 zu verknüpfen',
	'fbconnect-conv' => 'Bequemlichkeit',
	'fbconnect-convdesc' => 'Verknüpfte Benutzer werden automatisch angemeldet.
Sofern die Erlaubnis vorliegt, kann dieses Wiki sogar Facebook als Kommunikationsschnittstelle für E-Mails nutzen, so dass man weiterhin wichtige Nachrichten erhalten kann, ohne hierzu die E-Mail-Adresse offenlegen zu müssen.',
	'fbconnect-fbml' => 'Facebook Auszeichnungssprache',
	'fbconnect-fbmldesc' => 'Facebook stellt ein Bündel integrierter Tags bereit, die dynamisch erzeugte Daten verarbeiten können.
Viele dieser Tags können in Wikitext einbezogen werden. Sie werden, je nach auf dem Wiki angemeldeten Benutzer, individuell mit Daten versehen und ausgegeben.',
	'fbconnect-comm' => 'Kommunikation',
	'fbconnect-commdesc' => 'Facebook Connect ermöglicht eine vollkommen neuartige Möglichkeit des Netzwerkens.
Man kann sehen welche der eigenen Freunde das Wiki nutzen und, sofern gewünscht, ihnen die eigenen Aktionen über den eigenen Facebook-Newsfeed ausgeben lassen.',
	'fbconnect-welcome' => 'Willkommen, Facebook-Connect-Benutzer!',
	'fbconnect-loginbox' => "Oder via Facebook '''anmelden''':

$1",
	'fbconnect-merge' => 'Das Wikikonto mit der Facebook-ID verknüpfen',
	'fbconnect-logoutbox' => '$1

Dies führt zu einer Abmeldung von Facebook und allen verknüpften Websites, einschließlich dieses Wikis.',
	'fbconnect-listusers-header' => 'Die Privilegien $1 und $2 werden automatisch von denen des Gruppenrechteverwalters und Gruppenadministrators der Facebook-Gruppe $3 übertragen.

Für weitere Informationen kann man den Gruppenersteller $4 kontaktieren.',
	'fbconnect-usernameprefix' => 'Facebook-Benutzer',
	'fbconnect-error' => 'Überprüfungsfehler',
	'fbconnect-errortext' => 'Ein Fehler trat während der Überprüfung mit Facebook Connect auf.',
	'fbconnect-cancel' => 'Aktion abgebrochen',
	'fbconnect-canceltext' => 'Die vorherige Aktion wurde vom Benutzer abgebrochen.',
	'fbconnect-invalid' => 'Ungültige Option',
	'fbconnect-invalidtext' => 'Die Auswahl, die auf der vorherigen Seite getroffen wurde, ist ungültig.',
	'fbconnect-success' => 'Facebook Connect-Überprüfung erfolgreich',
	'fbconnect-successtext' => 'Die Anmeldung via Facebook Connect war erfolgreich.',
	'fbconnect-nickname' => 'Benutzername',
	'fbconnect-fullname' => 'Vollständiger Name',
	'fbconnect-email' => 'E-Mail-Adresse',
	'fbconnect-language' => 'Sprache',
	'fbconnect-timecorrection' => 'Zeitzonenkorrektur (Stunden)',
	'fbconnect-chooselegend' => 'Wahl des Benutzernamens',
	'fbconnect-chooseinstructions' => 'Alle Benutzer benötigen einen Benutzernamen. Es kann einer aus der untenstehenden Liste ausgewählt werden.',
	'fbconnect-invalidname' => 'Der ausgewählte Benutzername wurde bereits vergeben oder ist nicht zulässig.
Bitte einen anderen auswählen.',
	'fbconnect-choosenick' => 'Der Profilname auf Facebook ($1)',
	'fbconnect-choosefirst' => 'Vorname ($1)',
	'fbconnect-choosefull' => 'Vollständiger Name ($1)',
	'fbconnect-chooseauto' => 'Ein automatisch erzeugter Name ($1)',
	'fbconnect-choosemanual' => 'Ein Name der Wahl:',
	'fbconnect-chooseexisting' => 'Ein bestehendes Benutzerkonto in diesem Wiki',
	'fbconnect-chooseusername' => 'Benutzername:',
	'fbconnect-choosepassword' => 'Passwort:',
	'fbconnect-updateuserinfo' => 'Die folgenden persönlichen Angaben müssen aktualisiert werden:',
	'fbconnect-alreadyloggedin' => "'''Du bist bereits angemeldet, $1!'''

Sofern OpenID für künftige Anmeldevorgänge genutzt werden soll, kann das [[Special:Connect/Convert|Benutzerkonto für die Nutzung durch Facebook Connect eingerichtet werden]].",
	'fbconnect-error-creating-user' => 'Fehler beim Erstellen des Benutzers in der lokalen Datenbank.',
	'fbconnect-error-user-creation-hook-aborted' => 'Die Schnittstelle einer Softwareerweiterung hat die Benutzerkontoerstellung mit folgender Nachricht abgebrochen: $1',
	'fbconnect-prefstext' => 'Facebook Connect',
	'fbconnect-link-to-profile' => 'Facebook-Profil',
	'fbconnect-prefsheader' => "Einstellungen zu den Aktionen, die über den eigenen Facebook-Newsfeed ausgegeben werden sollen: <a id='fbConnectPushEventBar_show' href='#'>Einstellungen anzeigen</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>Einstellungen ausblenden</a>",
	'fbconnect-prefs-can-be-updated' => 'Sie können jederzeit aktualisiert werden, indem man sie unter der Registerkarte „$1“ auf der Seite Einstellungen ändert.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Kghbln
 */
$messages['de-formal'] = array(
	'fbconnect-alreadyloggedin' => "'''Sie sind bereits angemeldet, $1!'''

Sofern OpenID für künftige Anmeldevorgänge genutzt werden soll, kann das [[Special:Connect/Convert|Benutzerkonto für die Nutzung durch Facebook Connect eingerichtet werden]].",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'group-fb-groupie' => 'Membros do grupo',
	'group-fb-groupie-member' => 'Membro do grupo',
	'grouppage-fb-groupie' => '{{ns:project}}:Membros do grupo',
	'group-fb-officer' => 'Directores do grupo',
	'group-fb-officer-member' => 'Director do grupo',
	'grouppage-fb-officer' => '{{ns:project}}:Directores do grupo',
	'group-fb-admin' => 'Administradores do grupo',
	'group-fb-admin-member' => 'Administrador do grupo',
	'grouppage-fb-admin' => '{{ns:project}}:Administradores do grupo',
	'fbconnect-convert' => 'Conectar esta conta co Facebook',
	'fbconnect-logout' => 'Desconectarse do Facebook',
	'fbconnect-link' => 'Volver a facebook.com',
	'fbconnect-title' => 'Conectar a conta co Facebook',
	'fbconnect-conv' => 'Comodidade',
	'fbconnect-comm' => 'Comunicación',
	'fbconnect-error' => 'Erro de verificación',
	'fbconnect-cancel' => 'Acción cancelada',
	'fbconnect-invalid' => 'Opción incorrecta',
	'fbconnect-nickname' => 'Alcume',
	'fbconnect-fullname' => 'Nome completo',
	'fbconnect-email' => 'Enderezo de correo electrónico',
	'fbconnect-language' => 'Lingua',
	'fbconnect-timecorrection' => 'Corrección da zona horaria (horas)',
	'fbconnect-chooselegend' => 'Elección do nome de usuario',
	'fbconnect-chooseinstructions' => 'Todos os usuarios precisan un alcume; pode escoller un de entre as opcións de embaixo.',
	'fbconnect-choosefirst' => 'O seu nome ($1)',
	'fbconnect-choosefull' => 'O seu nome completo ($1)',
	'fbconnect-chooseauto' => 'Un nome xerado automaticamente ($1)',
	'fbconnect-choosemanual' => 'Un nome da súa escolla:',
	'fbconnect-chooseexisting' => 'Unha conta existente neste wiki',
	'fbconnect-chooseusername' => 'Nome de usuario:',
	'fbconnect-choosepassword' => 'Contrasinal:',
	'fbconnect-link-to-profile' => 'Perfil no Facebook',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Permitte al usatores de [[Special:Connect|connecter se]] con lor contos de [http://www.facebook.com Facebook].
Offere authentication a base de gruppos de Facebook e le uso de FBML in texto wiki.',
	'group-fb-user' => 'Usatores de Facebook Connect',
	'group-fb-user-member' => 'Usator de Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Usatores de Facebook Connect',
	'group-fb-groupie' => 'Membros del gruppo',
	'group-fb-groupie-member' => 'Membro del gruppo',
	'grouppage-fb-groupie' => '{{ns:project}}:Membros del gruppo',
	'group-fb-officer' => 'Directores del gruppo',
	'group-fb-officer-member' => 'Director del gruppo',
	'grouppage-fb-officer' => '{{ns:project}}:Directores del gruppo',
	'group-fb-admin' => 'Administratores del gruppo',
	'group-fb-admin-member' => 'Administrator del gruppo',
	'grouppage-fb-admin' => '{{ns:project}}:Administratores del gruppo',
	'fbconnect-connect' => 'Aperir session con Facebook Connect',
	'fbconnect-convert' => 'Connecter iste conto con Facebook',
	'fbconnect-logout' => 'Clauder session de Facebook',
	'fbconnect-link' => 'Retornar a facebook.com',
	'fbconnect-title' => 'Connecter le conto con Facebook',
	'fbconnect-intro' => 'Iste wiki dispone de Facebook Connect, le proxime evolution del platteforma Facebook.
Isto significa que, si tu es connectite, in addition al normal [[Wikipedia:Help:Logging in#Why log in?|beneficios]] que tu vide quando aperir un session, tu potera traher avantage de alcun functionalitate extra...',
	'fbconnect-click-to-login' => 'Clicca pro authenticar te a iste sito via Facebook',
	'fbconnect-click-to-connect-existing' => 'Clicca pro connecter tu conto de Facebook a $1',
	'fbconnect-conv' => 'Commoditate',
	'fbconnect-convdesc' => 'Le usatores connectite es automaticamente authenticate.
Si permission es date, alora iste wiki pote mesmo usar Facebook como proxy de e-mail de sorta que tu pote continuar a reciper importante notificationes sin revelar tu adresse de e-mail.',
	'fbconnect-fbml' => 'Linguage de marcation de Facebook',
	'fbconnect-fbmldesc' => 'Facebook ha providite un collection de etiquettas integrate pro tractamento dynamic de datos.
Multes de iste etiquettas pote esser includite in texto wiki, e essera tractate differentemente dependente de qual usator connectite los visualisa.',
	'fbconnect-comm' => 'Communication',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'fbconnect' => 'Facebook Connect',
	'group-fb-user' => 'Facebook Connect Benotzer',
	'group-fb-user-member' => 'Facebook-Connect-Benotzer',
	'grouppage-fb-user' => '{{ns:project}}:Facebook-Connect-Benotzer',
	'group-fb-groupie' => 'Membere vum Grupp',
	'group-fb-groupie-member' => 'Member vum Grupp',
	'grouppage-fb-groupie' => '{{ns:project}}: Gruppememberen',
	'fbconnect-convert' => 'Dëse Kont mat Facebook verbannen',
	'fbconnect-logout' => 'Ofmellen op Facebook',
	'fbconnect-link' => 'Zréck op facebook.com',
	'fbconnect-title' => 'Kont mat Facebook verbannen',
	'fbconnect-click-to-connect-existing' => 'Klickt fir Äre Facebook-Kont mat $1 ze verbannen',
	'fbconnect-conv' => 'Bequemlechkeet',
	'fbconnect-comm' => 'Kommunikatioun',
	'fbconnect-welcome' => 'Wëllkomm, Facebook-Connect-Benotzer!',
	'fbconnect-merge' => 'Verbannt Äre Wiki-Kont mat Ärer Facebook-ID',
	'fbconnect-usernameprefix' => 'Facebook-Benotzer',
	'fbconnect-nickname' => 'Spëtznumm',
	'fbconnect-fullname' => 'Ganzen Numm',
	'fbconnect-email' => 'E-Mailadress',
	'fbconnect-language' => 'Sprooch',
	'fbconnect-choosefirst' => 'Äre Virnumm ($1)',
	'fbconnect-choosefull' => 'Äre ganzen Numm ($1)',
	'fbconnect-choosemanual' => 'En Numm vun Ärer Wiel:',
	'fbconnect-chooseusername' => 'Benotzernumm:',
	'fbconnect-choosepassword' => 'Passwuert:',
	'fbconnect-prefstext' => 'Facebook-Connect',
	'fbconnect-link-to-profile' => 'Facebook-Profil',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Им овозможува на корисниците да се [[Special:Connect|поврзат]] со нивните сметки на [http://mk-mk.facebook.com Facebook].
Нуди потврдување на корисник врз основа на  групи на Facebook и употреба на FBML во викитекст.',
	'group-fb-user' => 'Корисници на Facebook Connect',
	'group-fb-user-member' => 'Корисник на Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Корисници на Facebook Connect',
	'group-fb-groupie' => 'Членови на група',
	'group-fb-groupie-member' => 'Член на група',
	'grouppage-fb-groupie' => '{{ns:project}}:Членови на група',
	'group-fb-officer' => 'Раководители на група',
	'group-fb-officer-member' => 'Раководител на група',
	'grouppage-fb-officer' => '{{ns:project}}:Раководители на група',
	'group-fb-admin' => 'Администратори на група',
	'group-fb-admin-member' => 'Администратор на група',
	'grouppage-fb-admin' => '{{ns:project}}:Администратори на група',
	'fbconnect-connect' => 'Најава со Facebook Connect',
	'fbconnect-convert' => 'Поврзи ја оваа сметка со Facebook',
	'fbconnect-logout' => 'Одјава од Facebook',
	'fbconnect-link' => 'Назад кон страницата на Facebook',
	'fbconnect-title' => 'Поврзи ја сметката преку Facebook',
	'fbconnect-intro' => 'Ова вики има овозможено Facebook Connect, следниот развоен стадиум на платформата Facebook.
Ова значи дека кога сте поврзани, покрај нормалните [[Wikipedia:Help:Logging in#Why log in?|погодности]] при најавување, ќе можете да ги користите предностите на некои дополнителни фунции...',
	'fbconnect-click-to-login' => 'Кликнете го копчево за да се најавите на ова мреж. место преку Facebook',
	'fbconnect-click-to-connect-existing' => 'Кликнете го копчево за да ја поврзете Вашата сметка на Facebook со $1',
	'fbconnect-conv' => 'Погодност',
	'fbconnect-convdesc' => 'Поврзаните корисници се автоматски најавени.
Ако дадете дозвола, ова вики може дури и да го користи Facebook како застапник за е-пошта, така што продолжувате да добивате важни известувања без да ја разоткриете вашата е-поштенска адреса.',
	'fbconnect-fbml' => 'Facebook-ов јазик за означување',
	'fbconnect-fbmldesc' => 'Facebook ни дава низа вградени ознаки со чија помош се прикажуваат динамичките податоци.
Многу вакви ознаки можат да се вклучат во викитест, и ќе се прикажуваат различно, во зависност од тоа кој поврзан корисник ги разгледува.',
	'fbconnect-comm' => 'Комуникација',
	'fbconnect-commdesc' => 'Facebook Connect воведува еден нов начин на мрежно општење.
Погледајте кои од Вашите пријатели го користат викито, а по желба можете и да го споделувате она што го правите со Вашите пријатели преку каналот за новости на Facebook.',
	'fbconnect-welcome' => 'Добредојде, кориснику на Facebook Connect!',
	'fbconnect-loginbox' => "Или пак '''најавете се''' со Facebook:

$1",
	'fbconnect-merge' => 'Спојте ја Вашата вики-сметка со Вашата назнака (ID) на Facebook',
	'fbconnect-logoutbox' => '$1

Ова исто така ќе ве одјави од Facebook и сите поврзани мрежни места, вклучувајќи го ова вики.',
	'fbconnect-listusers-header' => 'Привилегиите $1 и $2 автоматски преоѓаат од звањата на раководителот и администраторот на Facebook-групата  $3.

За повеќе информации, обратете се кај создавачот на групата - $4.',
	'fbconnect-usernameprefix' => 'FacebookUser',
	'fbconnect-error' => 'Грешка при потврдувањето',
	'fbconnect-errortext' => 'Се појави грешка при потврдувањето во однос на Facebook Connect.',
	'fbconnect-cancel' => 'Дејството е откажано',
	'fbconnect-canceltext' => 'Претходното дејство е откажано од корисникот.',
	'fbconnect-invalid' => 'Неважечка можност',
	'fbconnect-invalidtext' => 'Направениот избор на претходната страница е неважечки.',
	'fbconnect-success' => 'Потврдата на Facebook успеа',
	'fbconnect-successtext' => 'Успешно сте најавени со Facebook Connect.',
	'fbconnect-nickname' => 'Прекар',
	'fbconnect-fullname' => 'Име и презиме',
	'fbconnect-email' => 'Е-пошта',
	'fbconnect-language' => 'Јазик',
	'fbconnect-timecorrection' => 'Исправка на часовната зона (часови)',
	'fbconnect-chooselegend' => 'Избор на корисничко име',
	'fbconnect-chooseinstructions' => 'Сите корисници треба да имаат прекар; можете да одберете од долунаведените можности.',
	'fbconnect-invalidname' => 'Прекарот што го избравте е зафатен или не претставува важечки прекар.
Изберете друг.',
	'fbconnect-choosenick' => 'Името на Вашиот профил на Facebook ($1)',
	'fbconnect-choosefirst' => 'Вашето име ($1)',
	'fbconnect-choosefull' => 'Вашето име и презиме ($1)',
	'fbconnect-chooseauto' => 'Автоматски-создадено име ($1)',
	'fbconnect-choosemanual' => 'Име по ваш избор:',
	'fbconnect-chooseexisting' => 'Постоечка сметка на ова вики',
	'fbconnect-chooseusername' => 'Корисничко име:',
	'fbconnect-choosepassword' => 'Лозинка:',
	'fbconnect-updateuserinfo' => 'Поднови ги следниве лични податоци:',
	'fbconnect-alreadyloggedin' => "'''Веќе сте најавени, $1!'''

Ако во иднина сакате да користите Facebook Connect за најава, можете да [[Special:Connect/Convert|ја претворите сметката во таква што користи Facebook Connect]].",
	'fbconnect-error-creating-user' => 'Грешка при создавањето на корисникот во локалната база на податоци.',
	'fbconnect-error-user-creation-hook-aborted' => 'создавањето на сметката беше прекинато од кука (додаток), со пораката: $1',
	'fbconnect-prefstext' => 'Facebook Connect',
	'fbconnect-link-to-profile' => 'Профил на Facebook',
	'fbconnect-prefsheader' => "Контролирање кои настани ќе истакнат некоја ставка на Вашето емитување на новости на Facebook: <a id='fbConnectPushEventBar_show' href='#'>прикажи нагодувања</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>сокриј нагодувања</a>",
	'fbconnect-prefs-can-be-updated' => 'Овие можете да ги подновите во секое време во јазичето „$1“ во Вашата страница за нагодувања.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'fbconnect' => 'Verbinden met Facebook',
	'fbconnect-desc' => 'Stelt gebruikers in staat een [[Special:Connect|verbinding te maken]] met hun [http://www.facebook.com Facebookgebruiker].
Maakt het mogelijk om aan te melden met de Facebookgebruiker en FBML te gebruiken in wikitekst',
	'group-fb-user' => 'Facebook Connectgebruikers',
	'group-fb-user-member' => 'Facebook Connectgebruiker',
	'grouppage-fb-user' => '{{ns:project}}:Facebook Connectgebruikers',
	'group-fb-groupie' => 'groepsleden',
	'group-fb-groupie-member' => 'groepslid',
	'grouppage-fb-groupie' => '{{ns:project}}:Groepsleden',
	'group-fb-officer' => 'groepshoofden',
	'group-fb-officer-member' => 'groepshoofd',
	'grouppage-fb-officer' => '{{ns:project}}:Groepshoofden',
	'group-fb-admin' => 'groepsbeheerders',
	'group-fb-admin-member' => 'groepsbeheerder',
	'grouppage-fb-admin' => '{{ns:project}}:Groepsbeheerders',
	'fbconnect-connect' => 'Aanmelden via Facebook Connect',
	'fbconnect-convert' => 'Deze gebruiker met Facebook verbinden',
	'fbconnect-logout' => 'Afmelden van Facebook',
	'fbconnect-link' => 'Terug naar facebookcom',
	'fbconnect-title' => 'Gebruiker verbinden met Facebook',
	'fbconnect-click-to-login' => 'Klik om bij deze site aan te melden via Facebook',
	'fbconnect-click-to-connect-existing' => 'Klik om uw Facebookgebruiker te verbinden met $1',
	'fbconnect-conv' => 'Gemak',
	'fbconnect-fbml' => 'Facebookopmaaktaal',
	'fbconnect-fbmldesc' => 'Facebook heeft een aantal labels beschikbaar gemaakt die het mogelijk maken gegevens dynamisch weer te geven.
Veel van deze labels kunnen opgenomen worden in wikitekst en worden anders weergegeven afhankelijk van door welke gebruiker ze worden bekeken.',
	'fbconnect-comm' => 'Communicatie',
	'fbconnect-welcome' => 'Welkom, Facebook Connectgebruiker!',
	'fbconnect-loginbox' => "Of '''meld aan'''via Facebook:

$1",
	'fbconnect-merge' => 'Voeg uw wikigebruiker samen met uw Facebookgebruiker',
	'fbconnect-logoutbox' => '$1

Hierdoor wordt u ook afgemeld van Facebook en alle gekoppelde sites, inclusief deze wiki.',
	'fbconnect-error' => 'Controlefout',
	'fbconnect-errortext' => 'Er is een fout opgetreden tijdens de verificatie via Facebook Connect.',
	'fbconnect-cancel' => 'Handeling geannuleerd',
	'fbconnect-canceltext' => 'De vorige handeling is geannuleerd door de gebruiker.',
	'fbconnect-invalid' => 'Ongeldige optie',
	'fbconnect-invalidtext' => 'De gemaakte selectie op de vorige pagina is ongeldig.',
	'fbconnect-success' => 'Aangemeld via Facebook',
	'fbconnect-successtext' => 'U bent aangemeld via Facebook Connect.',
	'fbconnect-nickname' => 'Gebruikersnaam',
	'fbconnect-fullname' => 'Volledige naam',
	'fbconnect-email' => 'E-mailadres',
	'fbconnect-language' => 'Taal',
	'fbconnect-timecorrection' => 'Tijdzonecorrectie (uren)',
	'fbconnect-chooselegend' => 'Gebruikersnaamkeuze',
	'fbconnect-chooseinstructions' => 'Alle gebruikers hebben een gebruikersnaam nodig. U kunt er een kiezen uit de onderstaande mogelijkheden.',
	'fbconnect-invalidname' => 'De gebruikersnaam van uw keuze is al in gebruik of ongeldig.
Kies een andere.',
	'fbconnect-choosenick' => 'Uw profielnaam bij Facebook ($1)',
	'fbconnect-choosefirst' => 'Uw voornaam ($1)',
	'fbconnect-choosefull' => 'Uw volledig naam ($1)',
	'fbconnect-chooseauto' => 'Een automatisch aangemaakte naam ($1)',
	'fbconnect-choosemanual' => 'Een voorkeursnaam:',
	'fbconnect-chooseexisting' => 'Een bestaande gebruiker op deze wiki',
	'fbconnect-chooseusername' => 'Gebruikersnaam:',
	'fbconnect-choosepassword' => 'Wachtwoord:',
	'fbconnect-updateuserinfo' => 'De volgende persoonlijke informatie bijwerken:',
	'fbconnect-alreadyloggedin' => "'''U bent al aangemeld, $1!'''

Als u in de toekomst uw Facebook Connect wilt gebruiken om aan te melden, [[Special:Connect/Convert|zet uw gebruiker dan om naar Facebook Connect]].",
	'fbconnect-error-creating-user' => 'Er is een fout opgetreden tijdens het aanmaken van de gebruiker in de lokale database.',
	'fbconnect-error-user-creation-hook-aborted' => 'Een uitbreiding heeft het aanmaken van de gebruiker beëindigd met het volgende bericht: $1',
	'fbconnect-prefstext' => 'Verbinden met Facebook',
	'fbconnect-link-to-profile' => 'Facebookprofiel',
	'fbconnect-prefs-can-be-updated' => 'U kunt deze te allen tijde bijwerken door naar het tabblad "$1" in uw voorkeuren te gaan.',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Gjør det mulig for brukere å [[Special:Connect|koble til]] med sine [http://www.facebook.com Facebook]-kontoer.
Tilbyr autentisering basert på Facebookgrupper og bruken av FBML i wikitekst',
	'group-fb-user' => 'Facebook Connect-brukere',
	'group-fb-user-member' => 'Facebook Connect-bruker',
	'grouppage-fb-user' => '{{ns:project}}:Facebook Connect-brukere',
	'group-fb-groupie' => 'Gruppemedlemmer',
	'group-fb-groupie-member' => 'Gruppemedlem',
	'grouppage-fb-groupie' => '{{ns:project}}:Gruppemedlemmer',
	'group-fb-officer' => 'Gruppeoffiserer',
	'group-fb-officer-member' => 'Gruppeoffiser',
	'grouppage-fb-officer' => '{{ns:project}}:Gruppeoffiserer',
	'group-fb-admin' => 'Gruppeadministratorer',
	'group-fb-admin-member' => 'Gruppeadministrator',
	'grouppage-fb-admin' => '{{ns:project}}:Gruppeadministratorer',
	'fbconnect-connect' => 'Logg inn med Facebook Connect',
	'fbconnect-convert' => 'Koble til denne kontoen med Facebook',
	'fbconnect-logout' => 'Logg ut av Facebook',
	'fbconnect-link' => 'Tilbake til facebook.com',
	'fbconnect-title' => 'Koble til konto med Facebook',
	'fbconnect-intro' => 'Denne wikien er aktivert med Facebook Connect, den neste evolusjonen av Facebook-plattformen.
Dette betyr at når du er koblet til, i tillegg til de vanlige [[Wikipedia:Help:Logging in#Why log in?|fordelene]] du ser når du logger inn, vil du kunne dra nytte av noen ekstra funksjoner...',
	'fbconnect-click-to-login' => 'Klikk for å logge inn på dette nettstedet via Facebook',
	'fbconnect-click-to-connect-existing' => 'Klikk for å koble din Facebook-konto til $1',
	'fbconnect-conv' => 'Bekvemmelighet',
	'fbconnect-convdesc' => 'Tilkoblede brukere er automatisk logget inn.
Om tillatelse er gitt kan denne wikien til og med bruke Facebook som en e-postmellomtjener slik at du kan fortsette å motta viktige varsler uten å avsløre e-postadressen din.',
	'fbconnect-fbml' => 'Facebook-markeringsspråk (markup language)',
	'fbconnect-fbmldesc' => 'Facebook har levert en haug med innebygde elementer som vil gjengi dynamiske data.
Mange av disse elementene kan inkluderes i wikitekst og vil gjengis forskjellig avhengig av hvilken tilkoblet bruker som de blir sett av.',
	'fbconnect-comm' => 'Kommunikasjon',
	'fbconnect-commdesc' => 'Facebook kobler sammen brukere på et helt nytt nivå av nettverksbygging.
Se hvilke av dine venner som bruker denne wikien og eventuelt del handlingene dine med vennene dine gjennom Facebooks nyhetsstrøm.',
	'fbconnect-welcome' => 'Velkommen, Facebook Connect-bruker!',
	'fbconnect-loginbox' => "Eller '''logg inn''' med Facebook:

$1",
	'fbconnect-merge' => 'Slå sammen wikikontoen din med din Facebook-ID',
	'fbconnect-logoutbox' => '$1

Dette vil også logge deg ut av Facebook og alle tilkoblede nettsteder, inkludert denne wikien.',
	'fbconnect-listusers-header' => '$1- og $2-privilegier blir automatisk overført fra offiser- og admintitler i Facebook-gruppen $3.

For mer info, kontakt en gruppeoppretteren $4.',
	'fbconnect-usernameprefix' => 'FacebookBruker',
	'fbconnect-error' => 'Bekreftelsesfeil',
	'fbconnect-errortext' => 'En feil oppstod under bekreftelse med Facebook Connect.',
	'fbconnect-cancel' => 'Handling avbrutt',
	'fbconnect-canceltext' => 'Den forrige handlingen ble avbrutt av brukeren.',
	'fbconnect-invalid' => 'Ugyldig valg',
	'fbconnect-invalidtext' => 'Valget gjort på den forrige siden var ugyldig.',
	'fbconnect-success' => 'Facebookbekreftelsen var vellykket',
	'fbconnect-successtext' => 'Du har blitt logget inn med Facebook Connect.',
	'fbconnect-nickname' => 'Kallenavn',
	'fbconnect-fullname' => 'Fullt navn',
	'fbconnect-email' => 'E-postadresse',
	'fbconnect-language' => 'Språk',
	'fbconnect-timecorrection' => 'Tidssonekorreksjon (timer)',
	'fbconnect-chooselegend' => 'Brukernavnvalg',
	'fbconnect-chooseinstructions' => 'Alle brukere trenger et kallenavn; du kan velge et fra alternativene under.',
	'fbconnect-invalidname' => 'Kallenavnet du valgte er allerede tatt eller er ikke et gyldig kallenavn.
Velg et annet.',
	'fbconnect-choosenick' => 'Ditt Facebook-profilnavn ($1)',
	'fbconnect-choosefirst' => 'Ditt fornavn ($1)',
	'fbconnect-choosefull' => 'Ditt fulle navn ($1)',
	'fbconnect-chooseauto' => 'Et automatisk generert navn ($1)',
	'fbconnect-choosemanual' => 'Et valgfritt navn:',
	'fbconnect-chooseexisting' => 'En eksisterende konto på denne wikien',
	'fbconnect-chooseusername' => 'Brukernavn:',
	'fbconnect-choosepassword' => 'Passord:',
	'fbconnect-updateuserinfo' => 'Oppdater følgende personlige informasjon:',
	'fbconnect-alreadyloggedin' => "'''Du er allerede logget inn, $1'''

Om du ønsker å bruke Facebook Connect for å logge inn i fremtiden, kan du [[Special:Connect/Convert|konvertere kontoen din til å bruke Facebook Connect]].",
	'fbconnect-error-creating-user' => 'Feil ved opprettelse av brukeren i den lokale databasen.',
	'fbconnect-error-user-creation-hook-aborted' => 'En krok (utvidelse) avbrøt kontoopprettelsen med meldingen: $1',
	'fbconnect-prefstext' => 'Facebook Connect',
	'fbconnect-link-to-profile' => 'Facebook-profil',
	'fbconnect-prefsheader' => "For å kontrollere hvilke hendelser som vil dytte et element til Facebooks nyhetsstrøm, <a id='fbConnectPushEventBar_show' href='#'>vis innstillinger</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>skjul innstillinger</a>",
	'fbconnect-prefs-can-be-updated' => 'Du kan oppdatere disse når som helst ved å gå til «$1»-fanen på innstillingssiden din.',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Pozwala użytkownikom na [[Special:Connect|połączenie]] ze swoim [http://www.facebook.com kontem na Facebooku].
Umożliwia uwierzytelnianie w oparciu o grupy Facebooka i wykorzystanie FBML w tekście wiki',
	'group-fb-user' => 'Użytkownicy Facebook Connect',
	'group-fb-user-member' => 'Użytkownik Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Użytkownicy Facebook Connect',
	'group-fb-groupie' => 'Członkowie grupy',
	'group-fb-groupie-member' => 'Członek grupy',
	'grouppage-fb-groupie' => '{{ns:project}}:Członkowie grupy',
	'group-fb-officer' => 'Przywódcy grupy',
	'group-fb-officer-member' => 'Przywódca grupy',
	'grouppage-fb-officer' => '{{ns:project}}:Przywódcy grupy',
	'group-fb-admin' => 'Administratorzy grupy',
	'group-fb-admin-member' => 'Administrator grupy',
	'grouppage-fb-admin' => '{{ns:project}}:Administratorzy grupy',
	'fbconnect-connect' => 'Zaloguj przy pomocy Facebook Connect',
	'fbconnect-convert' => 'Połącz to konto z Facebookiem',
	'fbconnect-logout' => 'Wyloguj się z Facebooka',
	'fbconnect-link' => 'Powrót na facebook.com',
	'fbconnect-title' => 'Połącz konto z Facebookiem',
	'fbconnect-click-to-login' => 'Kliknij, aby zalogować się do tej witryny logując się na Facebooku',
	'fbconnect-click-to-connect-existing' => 'Kliknij, aby przyłączyć swoje konto na Facebooku do $1',
	'fbconnect-conv' => 'Wygoda',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'fbconnect' => 'Conession Facebook',
	'fbconnect-desc' => "A abìlita j'utent a [[Special:Connect|Conëttse]] con ij sò cont [http://www.facebook.com Facebook].
A eufr autenticassion basà an sle partìe Facebook e ël dovragi ëd FBML an test wiki",
	'group-fb-user' => 'Utent ëd Facebook Connect',
	'group-fb-user-member' => 'Utent ëd Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Utent ëd Facebook Connect',
	'group-fb-groupie' => 'Mèmber ëd la partìa',
	'group-fb-groupie-member' => 'Mèmber ëd la partìa',
	'grouppage-fb-groupie' => '{{ns:project}}:Mèmber ëd la partìa',
	'group-fb-officer' => 'Ufissiaj dla partìa',
	'group-fb-officer-member' => 'Ufissiaj dla partìa',
	'grouppage-fb-officer' => '{{ns:project}}:Ufissiaj dla partìa',
	'group-fb-admin' => 'Aministrador ëd la partìa',
	'group-fb-admin-member' => 'Aministrador ëd la partìa',
	'grouppage-fb-admin' => '{{ns:project}}:Aministrador ëd la partìa',
	'fbconnect-connect' => 'Intra con Facebook Connect',
	'fbconnect-convert' => 'Colega sto cont con Facebook',
	'fbconnect-logout' => 'Seurt da Facebook',
	'fbconnect-link' => 'André a Facebook.com',
	'fbconnect-title' => 'Colega cont con Facebook',
);

/** Russian (Русский)
 * @author Eleferen
 */
$messages['ru'] = array(
	'group-fb-groupie-member' => 'Группа участников',
	'fbconnect-cancel' => 'Действие отменено',
	'fbconnect-invalid' => 'Неверный параметр',
	'fbconnect-success' => 'Проверка через Facebook закончилась успешно',
	'fbconnect-nickname' => 'Псевдоним',
	'fbconnect-fullname' => 'Полное имя',
	'fbconnect-email' => 'Адрес электронной почты',
	'fbconnect-language' => 'Язык',
	'fbconnect-timecorrection' => 'Часовой пояс (в часах)',
	'fbconnect-chooselegend' => 'Выбор имени пользователя',
	'fbconnect-chooseinstructions' => 'У каждого участника должен быть псевдоним; вы можете выбрать один из представленных ниже.',
	'fbconnect-choosenick' => 'Имя вашего профиля в Facebook ($1)',
	'fbconnect-choosefull' => 'Ваше полное имя ($1)',
	'fbconnect-chooseauto' => 'Автоматически созданное имя ($1)',
	'fbconnect-choosemanual' => 'Имя на ваш выбор:',
	'fbconnect-chooseexisting' => 'Существующая учётная запись в этой вики',
	'fbconnect-chooseusername' => 'Имя участника:',
	'fbconnect-choosepassword' => 'Пароль:',
	'fbconnect-updateuserinfo' => 'Обновите следующую персональную информацию:',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'fbconnect' => 'Ugnay sa Facebook',
	'fbconnect-desc' => 'Nagpapahintulot sa mga tagagamit na [[Special:Connect|Umugnay]] sa kanilang mga akawnt sa [http://www.facebook.com Facebook].
Nagbibigay ng pagpapatunay batay sa mga pangkat sa Facebook at ang paggamit ng FBML sa teksto ng wiki',
	'group-fb-user' => 'Mga tagagamit ng Ugnay sa Facebook',
	'group-fb-user-member' => 'Tagagamit ng Ugnay sa Facebook',
	'grouppage-fb-user' => '{{ns:project}}:Mga tagagamit ng Ugnay sa Facebook',
	'group-fb-groupie' => 'Mga kasapi sa pangkat',
	'group-fb-groupie-member' => 'Kasapi sa pangkat',
	'grouppage-fb-groupie' => '{{ns:project}}:Mga kasapi sa pangkat',
	'group-fb-officer' => 'Mga opisyal ng pangkat',
	'group-fb-officer-member' => 'Opisyal ng pangkat',
	'grouppage-fb-officer' => '{{ns:project}}:Mga opisyal ng pangkat',
	'group-fb-admin' => 'Mga tagapangasiwa ng pangkat',
	'group-fb-admin-member' => 'Tagapangasiwa ng pangkat',
	'grouppage-fb-admin' => '{{ns:project}}:Mga tagapangasiwa ng pangkat',
	'fbconnect-connect' => 'Lumagdang may Ugnay sa Facebook',
	'fbconnect-convert' => 'Iugnay ang akawnt na ito sa Facebook',
	'fbconnect-logout' => 'Lumabas mula sa Facebook',
	'fbconnect-link' => 'Bumalik sa facebook.com',
	'fbconnect-title' => 'Iugnay ang akawnt sa Facebook',
	'fbconnect-intro' => 'Ang wiking ito ay pinaganang may Ugnay sa Facebook, ang susunod na ebolusyon ng plataporma ng Facebook.
Nangangahulugan itong kapag nakakunekta ka, bilang dagdag sa normal na [[Wikipedia:Help:Logging in#Why log in?|mga benepisyong]] nakikita mo kapag lumalagdang papasok, maaari kang makinabang sa ilang karagdagang mga tampok...',
	'fbconnect-click-to-login' => 'Pindutin upang lumagda sa sityong ito sa pamamagitan ng Facebook',
	'fbconnect-click-to-connect-existing' => 'Pindutin upang umugnay sa iyong akawnt sa Facebook sa $1',
	'fbconnect-conv' => 'Kaginhawahan',
	'fbconnect-convdesc' => 'Ang nakaugnay na mga tagagamit ay kusang naglalagda sa iyong papasok.
Kapag ibinigay ang pahintulot, kung gayon ang wiki ay maaari ring gamitin ang Facebook bilang isang kahaliling e-liham upang makapagpatuloy kang makatanggap ng mahahalagang mga pabatid na hindi ibinubunyag ang iyong tirahan ng e-liham.',
	'fbconnect-fbml' => 'Wikang pangmarka ng Facebook',
	'fbconnect-fbmldesc' => 'Nagbigay ang Facebook ng isang bungkos ng nakapaloob nang mga tatak na gagawa ng masisiglang dato.  Marami sa mga tatak na ito ang maidaragdag sa teksto ng wiki, at ipapakitang kaiba depende sa aling nakaugnay na tagagamit ang tumitingin sa kanila.',
	'fbconnect-comm' => 'Pakikipag-ugnayan',
	'fbconnect-commdesc' => 'Ang Ugnay sa Facebook ay naghahatid ng isang buong bagong antas ng pagnenetwork.
Tingnan kung sino sa inyong mga kaibigan ang gumagamit ng wiki, at opsyonal na ipamahagi ang iyong mga galaw sa mga kaibigan mo sa pamamagitan ng pakain ng balita ng Facebook.',
	'fbconnect-welcome' => 'Maligayang pagdating, tagagamit ng Ugnay sa Facebook!',
	'fbconnect-loginbox' => "O '''lumagda''' sa pamamagitan ng Facebook:

$1",
	'fbconnect-merge' => 'Isanib ang iyong akawnt na pangwiki sa iyong ID na pang-Facebook',
	'fbconnect-logoutbox' => '$1

Ilalabas ka rin nito mula sa Facebook at lahat ng nakaugnay na mga sityo, kabilang ang wiking ito.',
	'fbconnect-listusers-header' => 'Ang mga pribilehiyong $1 at $2 ay kusang nalilipat mula sa mga pamagat ng opisyal at tagapangasiwa ng pangkat na $3 ng Facebook.

Para sa mas maraming mga kabatiran, mangyaring makipag-ugnayan sa tagapaglikha ng pangkat na $4.',
	'fbconnect-usernameprefix' => 'Tagagamit ng Facebook',
	'fbconnect-error' => 'Kamalian sa pagpapatunay',
	'fbconnect-errortext' => 'Naganap ang isang kamalian habang nagpapatunay sa pamamagitan ng Ugnay sa Facebook.',
	'fbconnect-cancel' => 'Hindi itinuloy ang galaw',
	'fbconnect-canceltext' => 'Ang nakaraang kilos ay hindi itinuloy ng tagagamit.',
	'fbconnect-invalid' => 'Hindi tanggap na opsyon',
	'fbconnect-invalidtext' => 'Ang ginawang pagpili sa nakaraang pahina ay hindi tanggap.',
	'fbconnect-success' => 'Nagtagumpay ang pagpapatibay ng Facebook',
	'fbconnect-successtext' => 'Matagumpay kang nailagdang papasok sa pamamagitan ng Ugnay sa Facebook.',
	'fbconnect-nickname' => 'Palayaw',
	'fbconnect-fullname' => 'Buong pangalan',
	'fbconnect-email' => 'Tirahan ng e-liham',
	'fbconnect-language' => 'Wika',
	'fbconnect-timecorrection' => 'Pagtatama sa sona ng oras (mga oras)',
	'fbconnect-chooselegend' => 'Pagpili ng pangalan ng tagagamit',
	'fbconnect-chooseinstructions' => 'Ang lahat ng mga tagagamit ay nangangailangan ng palayaw; maaari kang pumili mula sa mga mapagpipiliang nasa ibaba.',
	'fbconnect-invalidname' => 'May nakakuha na ng napiling mong palayaw o hindi isang tanggap na palayaw.
Mangyaring pumili ng isang naiiba.',
	'fbconnect-choosenick' => 'Ang iyong pangalan ng balangkas sa Facebook ($1)',
	'fbconnect-choosefirst' => 'Ang unang pangalan mo ($1)',
	'fbconnect-choosefull' => 'Ang buong pangalan mo ($1)',
	'fbconnect-chooseauto' => 'Isang kusang nalikhang pangalan ($1)',
	'fbconnect-choosemanual' => 'Isang pangalang napili mo:',
	'fbconnect-chooseexisting' => 'Isang umiiral na akawnt sa wiking ito',
	'fbconnect-chooseusername' => 'Pangalan ng tagagamit:',
	'fbconnect-choosepassword' => 'Hudyat:',
	'fbconnect-updateuserinfo' => 'Isapanahon ang sumusunod na kabatirang pangsarili:',
	'fbconnect-alreadyloggedin' => "'''Nakalagda ka na, $1!'''

Kung nais mong gamitin ang Ugnay sa Facebook upang makalagda ka sa hinaharap, maaari mong [[Special:Connect/Convert|palitan ang iyong akawnt upang gamitin ang Ugnay sa Facebook]].",
	'fbconnect-error-creating-user' => 'Kamalian sa paglikha ng tagagamit sa katutubong kalipunan ng dato.',
	'fbconnect-error-user-creation-hook-aborted' => 'Isang kawit (dugtong) ang pumigil sa paglikha ng akawnt na may mensaheng: $1',
	'fbconnect-prefstext' => 'Ugnay sa Facebook',
	'fbconnect-link-to-profile' => 'Balangkas sa Facebook',
	'fbconnect-prefsheader' => "Upang matabanan ang kung aling mga kaganapan ang tutulak sa isang bagay papunta sa iyong pakain ng balita sa Facebook, <a id='fbConnectPushEventBar_show' href='#'>ipakita ang mga nais</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>itago ang mga nais</a>",
	'fbconnect-prefs-can-be-updated' => 'Maisasapanahon mo ang mga ito anumang oras sa pamamagitan ng pagdalaw sa panglaylay na "$1" ng iyong pahina ng mga nais.',
);
