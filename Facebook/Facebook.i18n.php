<?php
/*
 * Copyright � 2008-2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * Facebook.i18n.php
 * 
 * Internationalization file for the Facebook extension.
 */

$messages = array();

/** English */
$messages['en'] = array(
	// Extension name
	'facebook' => 'Facebook Open Graph for MediaWiki',
	'facebook-desc' => '\'\'\'Facebook Open Graph\'\'\' for MediaWiki allows users to log in with their Facebook account and integrates your wiki into Facebook\'s Open Graph. Open Graph social plugins can be used on wiki pages, and you can even manage user rights from within Facebook using Facebook Groups.',
	// Group containing Facebook Connect users
	'group-fb-user' => 'Facebook users',
	'group-fb-user-member' => 'Facebook user',
	'grouppage-fb-user' => '{{ns:project}}:Facebook users',
	// Group for Facebook users belonging to the group specified by $fbUserRightsFromGroup
	'group-fb-groupie' => 'Group members',
	'group-fb-groupie-member' => 'Group member',
	'grouppage-fb-groupie' => '{{ns:project}}:Group members',
	// Admins of the Facebook group
	'group-fb-admin' => 'Group admins',
	'group-fb-admin-member' => 'Group administrator',
	'grouppage-fb-admin' => '{{ns:project}}:Group admins',
	// Personal toolbar
	'facebook-connect' => 'Log in with Facebook',
	'facebook-convert' => 'Connect this account with Facebook',
	'facebook-log-in' => 'Log In',
	// Prefix to use for automatically-generated usernames
	'facebook-usernameprefix' => 'FacebookUser',
	// Special:Connect
	'facebook-title' => 'Log in with Facebook',
	'facebook-welcome-name' => 'Welcome, $1.',
	'facebook-continue-text' => 'Your Facebook account is connected to the username $1. Would you like to log out and continue as this user?',
	'facebook-continue-button' => 'Continue',
	'facebook-merge-title' => 'Merge accounts',
//	'facebook-merge'    => 'Merge your wiki account with your Facebook ID.', // Deprecated
	'facebook-merge-text' => 'Merging your username with your Facebook account lets you log in to $1 with your Facebook ID.',
	'facebook-connect-text' => 'Facebook is enabled for your username. Click the button below to log in to Facebook.',
	'facebook-only-text' => 'Logging in allows you to explore $1 using your Facebook identity.',
	'facebook-logout-and-continue' => 'Log out and continue',
	'facebook-error' => 'Verification error',
	'facebook-errortext' => 'An error occurred during verification with Facebook.',
	'facebook-cancel' => 'Action cancelled',
	'facebook-canceltext' => 'The previous action was cancelled by the user.',
	'facebook-invalid' => 'Invalid option',
	'facebook-invalidtext' => 'The selection made on the previous page was invalid.',
	'facebook-success' => 'Facebook verification succeeded',
	'facebook-successtext' => 'You have been successfully logged in with Facebook.',
	//'facebook-success-connecting-existing-account' => 'Your facebook account has been connected. To change which events get pushed to your Facebook news feed, please visit your <a href="$1">preferences</a> page.',
	//'facebook-optional' => 'Optional',
	//'facebook-required' => 'Required',
	'facebook-nickname' => 'Nickname',
	'facebook-fullname' => 'Full name',
	'facebook-gender' => 'Gender',
	'facebook-email' => 'E-mail address',
	'facebook-language' => 'Language',
	'facebook-timecorrection' => 'Time zone correction (hours)',
	'facebook-chooselegend' => 'Username choice',
	'facebook-updatelegend' => 'Update options',
	'facebook-chooseinstructions' => 'All users need a nickname; you can choose one from the options below.',
	'facebook-invalidname' => 'The nickname you chose is already taken or not a valid nickname. Please chose a different one.',
	'facebook-choosenick' => 'Your Facebook profile name ($1)',
	'facebook-choosefirst' => 'Your first name ($1)',
	'facebook-choosefull' => 'Your full name ($1)',
	'facebook-chooseauto' => 'An auto-generated name ($1)',
	'facebook-choosemanual' => 'A name of your choice:',
	'facebook-chooseexisting' => 'An existing account on this wiki',
	'facebook-chooseusername' => 'Username:',
	'facebook-choosepassword' => 'Password:',
	'facebook-updateuserinfo' => 'Update the following information:',
	'facebook-alreadyloggedin-title' => 'Already connected',
	/*
	'facebook-convertinstructions' => 'This form lets you change your user account to use an OpenID URL or add more OpenID URLs',
	'facebook-convertoraddmoreids' => 'Convert to OpenID or add another OpenID URL',
	'facebook-convertsuccess' => 'Successfully converted to OpenID',
	'facebook-convertsuccesstext' => 'You have successfully converted your OpenID to $1.',
	'facebook-convertyourstext' => 'That is already your OpenID.',
	'facebook-convertothertext' => 'That is someone else\'s OpenID.',
	*/
	'facebook-error-creating-user' => 'Error creating the user in the local database.',
	'facebook-error-user-creation-hook-aborted' => 'A hook (extension) aborted the account creation with the message: $1',
	'facebook-error-wrong-id' => 'Your username, $1, is connected to a different Facebook account. If you would like to continue as $2, you must first log out of {{SITENAME}}.',
	// Special:Connect/Debug
	'facebook-debug-title' => 'Facebook Application Debugger',
	'facebook-debug-msg' => 'If a problem with your Facebook application\'s settings is indicated, click the icon to update that setting on Facebook.

To change the default value, follow the link to the interface message on the left.',
	'facebook-termsofservicepage' => 'Project:Terms of service',
	'facebook-auth-dialog-headline' => '{{SITENAME}}',
	'facebook-auth-dialog-description' => 'Welcome to the social experience on {{SITENAME}}. On the next screen, you will be able to choose a username for yourself.',
	'facebook-auth-dialog-explanation' => 'Your email address is tied to your account and allows you to change your password. Your email address is kept private.',
	'facebook-object-debug-title' => 'Open Graph Object Debugger',
	'facebook-object-debug' => 'Input a page name to view the page in the Open Graph',
	'facebook-debug' => 'Debug',
	'facebook-error-application' => 'You must be listed as an administrator or a developer for your application. Visit the roles tab in your app\'s Facebook settings.',
	// ajaxLoginMerge.tmpl.php
	'facebook-logged-in-now-connect' => 'You have been logged in to your account, please click the login button to connect it with Facebook.',
	// ChooseNameTemplate.class.php
	'facebook-modal-title' => 'Finish your account setup',
	'facebook-signup-mail' => 'E-mail:',
	'facebook-msg-for-existing-users' => '<p>Already a Wikia user?</p><br/><br/>
If you would like to connect this facebook account to an existing Wikia account, please <a class="loginAndConnect" href="$1">login</a> first.',
	// facebook_customizations.php
	'facebook-invalid-email' => 'Please provide a valid email address.',
	// facebook-prefstext
	'prefs-facebook-prefstext' => 'Facebook',
	'facebook-link-to-profile' => 'Facebook profile',
	'facebook-prefsheader' => 'By default, some events will push items to your Facebook feed. You can customize these now, or later at any time in your preferences.',
	// From MediaWiki Translatewiki
	#'facebook-prefsheader' => "To control which events will push an item to your Facebook news feed, <a id='facebookPushEventBar_show' href='#'>show preferences</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>hide preferences</a>",
	'facebook-prefs-can-be-updated' => 'You can update these any time by visiting the "$1" tab of your preferences page.',
	'facebook-prefs-show' => 'Show feed preferences >>',
	'facebook-prefs-hide' => 'Hide feed preferences >>',
	'facebook-prefs-post' => 'Post to my Facebook News Feed when I:',
	'prefs-facebook-event-prefstext' => 'News Feeds',
	'prefs-facebook-status-prefstext' => 'Status',
	'facebook-connect-msg' => "Congratulations! Your Wikia and Facebook accounts are now connected.<br/>
Check your <a href='$1'>preferences</a> to control which events appear in your Facebook feed.",
	'facebook-connect-msg-sync-profile' => "Congratulations! Your Wikia and Facebook accounts are now connected.<br/>
Check your <a href='$1'>preferences</a> to control which events appear in your Facebook feed. Sync your <a href='$2'>Wikia profile</a> with Facebook.",
	'facebook-connect-error-msg' => "We're sorry, we couldn't complete your connection without permission to post to your Facebook account. After setup you have [[w:c:help:Help:Facebook_Connect#Sharing_with_your_Facebook_activity_feed|full control]] of what's posted to your news feed. Please try again.",
	'facebook-disconnect-link' => 'You can also <a id="facebookDisconnect" href="#"> disconnect your account from Facebook.</a>
You will able continue using your account as normal, with your history (edits, points, achievements) intact.',
	'facebook-disconnect-done' => 'Disconnecting <span id="facebookDisconnectDone">... done!</span>',
	'facebook-disconnect-info' => 'We have emailed a new password to use with your account - you can log in with the same username as before.',
	'tog-facebook-push-allow-never' => 'Never send anything to my news feed (overrides other options)',
	'facebook-reclamation-title' => 'Disconnecting from Facebook',
	'facebook-reclamation-body' => 'Your account is now disconnected from Facebook!

We have emailed a new password to use with you account - you can log in with the same username as before. Hooray!


To login go to: $1',
	'facebook-reclamation-title-error' => 'Disconnecting from Facebook',
	'facebook-reclamation-body-error' => 'There was an error disconnecting your account from Facebook or your account is already disconnected.',
	'facebook-unknown-error' => 'Unknown error, please try again or contact with us.',
	'facebook-passwordremindertitle' => 'Your Wikia account is now disconnected from Facebook!',
	'facebook-passwordremindertitle-exist' => 'Your account is now disconnected from Facebook!',
	'facebook-passwordremindertext' => 'Hi,
It looks like you\'ve just disconnected your account from Facebook. We\'ve kept all of your history, edit points and achievements intact, so don\'t worry!

You can use the same username as before, and we\'ve generated a new password for you to use. Here are your details:

Username: $2
Password: $3

The replacement password has been sent only to you at this email address.',
	'facebook-passwordremindertext-exist' => 'Hi,
It looks like you\'ve just disconnected your account from Facebook.
We\'ve kept all of your history, edit points and achievements intact, so don\'t worry!

You can use the same username and password as you did before you connected.',
);

/**
 * Message documentation (Message documentation)
 * This is shown to translators to help them know what the string is for.
 * @author Garrett Brown
 */
$messages['qqq'] = array(
	'facebook-desc' => 'Short description of the Facebook extension, shown in [[Special:Version]]. Do not translate or change links.',
	'facebook-email' => '{{Identical|E-mail address}}',
	'facebook-language' => '{{Identical|Language}}',
	'facebook-choosepassword' => '{{Identical|Password}}',
	'facebook-logged-in-now-connect' => 'This message is shown in a modal dialog along with a Facebook button when the user is trying to login and connect. This is a workaround for popup blockers.',
	'facebook-prefstext' => 'Facebook preferences tab text above the list of preferences',
	'facebook-link-to-profile' => 'Appears next to the user\'s name in their Preferences page and this text is made into link to the profile of that user if they are connected.',
	'facebook-connect-next' => 'This text appears on the button in the login-and-connect dialog.
After a user enters their username/password, this will slide them over to the next screen which is the Facebook login button.'
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'facebook-comm' => 'Kommunikasie',
	'facebook-error' => 'Verifikasiefout',
	'facebook-invalid' => 'Ongeldige opsie',
	'facebook-nickname' => 'Bynaam',
	'facebook-fullname' => 'Volle naam',
	'facebook-email' => 'E-posadres',
	'facebook-language' => 'Taal',
	'facebook-choosefirst' => 'U eerste naam ($1)',
	'facebook-choosefull' => 'U volledige naam ($1)',
	'facebook-chooseauto' => "'n Outomaties gegenereerde naam ($1)",
	'facebook-choosemanual' => "'n Naam van u keuse:",
	'facebook-chooseexisting' => "'n Bestaande gebruiker op hierdie wiki:",
	'facebook-chooseusername' => 'Gebruikersnaam:',
	'facebook-choosepassword' => 'Wagwoord:',
	'facebook-link-to-profile' => 'Facebook-profiel',
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'group-fb-groupie' => 'ܗܕ̈ܡܐ ܕܟܢܘܫܬܐ',
	'group-fb-groupie-member' => 'ܗܕܡܐ ܕܟܢܘܫܬܐ',
	'grouppage-fb-groupie' => '{{ns:project}}:ܗܕ̈ܡܐ ܕܟܢܘܫܬܐ',
	'facebook-invalid' => 'ܓܒܝܬܐ ܠܐ ܬܪܝܨܬܐ',
	'facebook-fullname' => 'ܫܡܐ ܓܡܝܪܐ',
	'facebook-email' => 'ܦܪܫܓܢܐ ܕܒܝܠܕܪܐ ܐܠܩܛܪܘܢܝܐ',
	'facebook-language' => 'ܠܫܢܐ',
	'facebook-choosefirst' => 'ܫܡܐ ܩܕܡܝܐ ܕܝܠܟ($1)',
	'facebook-choosefull' => 'ܫܡܐ ܓܡܝܪܐ ܕܝܠܟ($1)',
	'facebook-chooseusername' => 'ܫܡܐ ܕܡܦܠܚܢܐ:',
	'facebook-choosepassword' => 'ܡܠܬܐ ܕܥܠܠܐ:',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'facebook' => 'Злучэньне Facebook',
	'facebook-desc' => 'Дае магчымасьць удзельнікам [[Special:Connect|злучыцца]] з іх рахункам на [http://www.facebook.com Facebook].
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
	'facebook-connect' => 'Увайсьці ў сыстэму праз злучэньне Facebook',
	'facebook-convert' => 'Злучыць гэты рахунак і Facebook',
	'facebook-title' => 'Злучыць рахунак з Facebook',
	'facebook-merge' => 'Аб’яднаць Ваш вікі-рахунак з Вашым ідэнтыфікатарам Facebook',
	'facebook-error' => 'Памылка праверкі',
	'facebook-errortext' => 'Узьнікла памылка падчас праверкі са злучэньнем Facebook.',
	'facebook-cancel' => 'Дзеяньне адмененае',
	'facebook-canceltext' => 'Папярэдняе дзеяньне было адмененае ўдзельнікам.',
	'facebook-invalid' => 'Няслушная ўстаноўка',
	'facebook-invalidtext' => 'Выбар, зроблены на папярэдняй старонцы, быў няслушны.',
	'facebook-success' => 'Праверка Facebook адбылася пасьпяхова',
	'facebook-successtext' => 'Вы пасьпяхова ўвайшлі ў сыстэму праз злучэньне Facebook.',
	'facebook-nickname' => 'Мянушка',
	'facebook-fullname' => 'Поўнае імя',
	'facebook-email' => 'Адрас электроннай пошты',
	'facebook-language' => 'Мова',
	'facebook-timecorrection' => 'Карэкцыя часавага пасу (гадзінаў)',
	'facebook-chooselegend' => 'Выбар імя карыстальніка',
	'facebook-chooseinstructions' => 'Кожны ўдзельнік павінен мець мянушку;
Вы можаце выбраць адну з пададзеных ніжэй.',
	'facebook-invalidname' => 'Мянушка якую Вы выбралі, ужо выкарыстоўваецца ці зьяўляецца няслушнай.
Калі ласка, выберыце іншую.',
	'facebook-choosenick' => 'Назва Вашага профілю ў Facebook ($1)',
	'facebook-choosefirst' => 'Вашае імя ($1)',
	'facebook-choosefull' => 'Вашае поўнае імя ($1)',
	'facebook-chooseauto' => 'Аўтаматычна створанае імя ($1)',
	'facebook-choosemanual' => 'Імя на Ваш выбар:',
	'facebook-chooseexisting' => 'Існуючы рахунак у {{GRAMMAR:месны|{{SITENAME}}}}',
	'facebook-chooseusername' => 'Імя ўдзельніка:',
	'facebook-choosepassword' => 'Пароль:',
	'facebook-updateuserinfo' => 'Зьмяніце наступную асабістую інфармацыю:',
	'facebook-error-creating-user' => 'Адбылася памылка падчас стварэньня рахунку карыстальніка ў лякальнай базе зьвестак.',
	'facebook-error-user-creation-hook-aborted' => 'Перахопнік (пашырэньне) адмяніў стварэньне рахунку з паведамленьнем: $1',
	'facebook-prefstext' => 'Злучэньне Facebook',
	'facebook-link-to-profile' => 'Профіль у Facebook',
	'facebook-prefsheader' => "Для кантролю таго, якія падзеі будуць дадавацца у стужку навінаў Facebook, <a id='facebookPushEventBar_show' href='#'>паказаць устаноўкі</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>схаваць устаноўкі</a>",
	'facebook-prefs-can-be-updated' => 'Вы можаце зьмяніць гэта ў любы момант, наведаўшы закладку «$1» на Вашай старонцы ўстановак.',
);

/** Breton (Brezhoneg)
 * @author Fulup
 * @author Gwendal
 * @author Y-M D
 */
$messages['br'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => "Talvezout a ra d'an implijerien da [[Special:Connect|gevreañ]] dre o c'hontoù [http://www.facebook.com Facebook].
Kinnig a ra ur gwiriekadur diazezet war strolladoù Facebook hag implij FBML en destenn wiki",
	'group-fb-user' => 'Implijerien Facebook Connect',
	'group-fb-user-member' => 'Implijer Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}: Implijerien Facebook Connect',
	'group-fb-groupie' => 'Izili ar strollad',
	'group-fb-groupie-member' => 'Ezel ar strollad',
	'grouppage-fb-groupie' => '{{ns:project}}: Izili ar strollad',
	'group-fb-officer' => 'Tud karget a strolladoù',
	'group-fb-officer-member' => 'Den karget a strolladoù',
	'grouppage-fb-officer' => '{{ns:project}}:Tud karget a strolladoù',
	'group-fb-admin' => 'Merourien ar strollad',
	'group-fb-admin-member' => 'Merour ar strollad',
	'grouppage-fb-admin' => '{{ns:project}}: Merourien ar strollad',
	'facebook-connect' => 'Kevreañ gant Facebook Connect',
	'facebook-convert' => "Kevreañ ar c'hont-mañ gant Facebook",
	'facebook-title' => 'Kont kevreañ gant Facebook',
	'facebook-merge' => 'Kendeuziñ ho kont wiki gant ho ker-anaout Facebook.',
	'facebook-error' => 'Fazi gwiriañ',
	'facebook-errortext' => "C'hoarvezet ez eus ur fazi e-ser gwiriañ gant Facebook connect",
	'facebook-cancel' => 'Ober nullet',
	'facebook-canceltext' => 'Nullet eo bet an oberiadenn gent gant an implijer.',
	'facebook-invalid' => "N'haller ket dibab an dra-se",
	'facebook-invalidtext' => 'Direizh e oa an dibab graet war ar bajenn gent.',
	'facebook-success' => 'Gwiriekadur gant Facebook aet da benn vat',
	'facebook-successtext' => "Kevreet oc'h ervat gant Facebook Connect.",
	'facebook-nickname' => 'Lesanv',
	'facebook-fullname' => 'Anv klok',
	'facebook-email' => "Chomlec'h postel",
	'facebook-language' => 'Yezh',
	'facebook-timecorrection' => 'Reizhañ ar werzhid eur (en eurioù)',
	'facebook-chooselegend' => 'Dibab an anv implijer',
	'facebook-chooseinstructions' => "An holl implijerien o deus ezhomm ul lesanv; gallout a rit dibab unan eus ar c'hinnigoù a-is.",
	'facebook-invalidname' => 'Al lezanv ho peus dibabet a zo direizh pe implijet dija.
Trugarez da zibab unan all.',
	'facebook-choosenick' => 'Anv ho profil Facebook ($1)',
	'facebook-choosefirst' => "Hoc'h anv-bihan ($1)",
	'facebook-choosefull' => "Hoc'h anv klok ($1)",
	'facebook-chooseauto' => 'Un anv krouet emgefre ($1)',
	'facebook-choosemanual' => "Un anv dibabet ganeoc'h :",
	'facebook-chooseexisting' => 'Ur gont zo anezhi war ar wiki-mañ',
	'facebook-chooseusername' => 'Anv implijer :',
	'facebook-choosepassword' => 'Ger-tremen :',
	'facebook-updateuserinfo' => 'Hizivaat an titouroù personel da-heul :',
	'facebook-error-creating-user' => "C'hoarvezet ez eus ur fazi e-ser krouiñ an implijer en diaz roadennoù lec'hel.",
	'facebook-error-user-creation-hook-aborted' => "Ur c'hroc'hed (astenn) en deus distaolet ar c'hrouiñ kontoù gant ar c'hemenn : $1",
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil Facebook',
	'facebook-prefsheader' => "Evit chom mestr war an darvoudoù a gaso un elfenn en ho lanvad keleier Facebook, <a id='facebookPushEventBar_show' href='#'>diskouez ar penndibaboù</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>kuzhat ar penndibaboù</a>",
	'facebook-prefs-can-be-updated' => 'Gallout a rit hizivaat an elfennoù-se pa fell deoc\'h en ur implijout an ivinell "$1" en ho pajenn penndibaboù.',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'facebook-desc' => 'Omogućuje korisnicima [[Special:Connect|spajanje]] sa svojim [http://www.facebook.com Facebook] računima.
Nudi autentifikaciju zasnovanu na Facebook grupama i korištenju FBML u wiki tekstu',
	'group-fb-groupie' => 'Članovi grupe',
	'group-fb-groupie-member' => 'Član grupe',
	'group-fb-admin' => 'Administratori grupe',
	'group-fb-admin-member' => 'Administrator grupe',
	'facebook-connect' => 'Prijavite se sa Facebook Connect',
	'facebook-title' => 'Spajanje računa sa Facebook',
	'facebook-click-to-login' => 'Kliknite da se prijavite na ovu stranicu preko Facebooka',
	'facebook-click-to-connect-existing' => 'Kliknite da spojite vaš Facebook račun na $1',
	'facebook-comm' => 'Komunikacija',
	'facebook-merge' => 'Spoji svoj wiki račun sa svojim Facebook ID',
	'facebook-cancel' => 'Akcija obustavljena',
	'facebook-invalid' => 'Nevaljana opcija',
	'facebook-nickname' => 'Nadimak',
	'facebook-fullname' => 'Puno ime',
	'facebook-email' => 'E-mail adresa',
	'facebook-language' => 'Jezik',
);

/** Czech (Česky)
 * @author Jkjk
 */
$messages['cs'] = array(
	'facebook-fbml' => 'Soubor značek Facebook',
	'facebook-error' => 'Chyba ověření',
	'facebook-errortext' => 'Vyskytla se chyba během ověření s Facebook Connect.',
	'facebook-cancel' => 'Akce zrušena',
	'facebook-canceltext' => 'Přechozí akce byla uživatelem zrušena.',
	'facebook-invalid' => 'Nesprávná možnost',
	'facebook-invalidtext' => 'Výběr na minulé stránce byl nesprávný.',
	'facebook-success' => 'Ověření Facebooku bylo úspěšné',
	'facebook-successtext' => 'Jste zalogován s Facebook Connect',
	'facebook-nickname' => 'Přezdívka',
	'facebook-fullname' => 'Plné jméno',
	'facebook-email' => 'E-mailová adresa',
	'facebook-language' => 'Jazyk',
	'facebook-timecorrection' => 'Úprava časové zóny (hodiny)',
	'facebook-chooselegend' => 'Výběr uživatelského jména',
	'facebook-chooseinstructions' => 'Všichni uživatelé musí mít přezdívku; můžete si jí vybrat z možností níže.',
	'facebook-choosenick' => 'Jméno vašeho Facebook profilu ($1)',
	'facebook-choosefirst' => 'Vaše křestní jméno ($1)',
	'facebook-choosefull' => 'Vaše plné jméno ($1)',
	'facebook-chooseauto' => 'Automaticky generované jméno ($1)',
	'facebook-choosemanual' => 'Jméno dle vašeho výběru:',
	'facebook-chooseexisting' => 'Existující účet na této wiki',
	'facebook-chooseusername' => 'Uživatelské jméno:',
	'facebook-choosepassword' => 'Heslo:',
	'facebook-updateuserinfo' => 'Aktualizovat následující osobní údaje:',
	'facebook-error-creating-user' => 'Chyba při vytváření uživatele v lokální databázi',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook profil',
	'facebook-prefs-can-be-updated' => 'Toto můžete aktualizovat kdykoliv navštívením "$1" tabu ve vaší stránce nastavení.',
);

/** German (Deutsch)
 * @author Als-Holder
 * @author Kghbln
 * @author The Evil IP address
 */
$messages['de'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Stellt eine [[Special:Connect|Spezialseite]] bereit mit der Benutzer eine Verbindung mit ihrem [http://de-de.facebook.com/ Facebook-Konten] herstellen können.
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
	'facebook-connect' => 'Anmelden mit Facebook Connect',
	'facebook-convert' => 'Dieses Konto mit Facebook verknüpfen',
	'facebook-title' => 'Konto mit Facebook verknüpfen',
	'facebook-merge' => 'Das Wikikonto mit der Facebook-ID verknüpfen',
	'facebook-usernameprefix' => 'Facebook-Benutzer',
	'facebook-error' => 'Überprüfungsfehler',
	'facebook-errortext' => 'Ein Fehler trat während der Überprüfung mit Facebook Connect auf.',
	'facebook-cancel' => 'Aktion abgebrochen',
	'facebook-canceltext' => 'Die vorherige Aktion wurde vom Benutzer abgebrochen.',
	'facebook-invalid' => 'Ungültige Option',
	'facebook-invalidtext' => 'Die Auswahl, die auf der vorherigen Seite getroffen wurde, ist ungültig.',
	'facebook-success' => 'Facebook Connect-Überprüfung erfolgreich',
	'facebook-successtext' => 'Die Anmeldung via Facebook Connect war erfolgreich.',
	'facebook-nickname' => 'Benutzername',
	'facebook-fullname' => 'Bürgerlicher Name',
	'facebook-email' => 'E-Mail-Adresse',
	'facebook-language' => 'Sprache',
	'facebook-timecorrection' => 'Zeitzonenkorrektur (Stunden)',
	'facebook-chooselegend' => 'Wahl des Benutzernamens',
	'facebook-chooseinstructions' => 'Alle Benutzer benötigen einen Benutzernamen. Es kann einer aus der untenstehenden Liste ausgewählt werden.',
	'facebook-invalidname' => 'Der ausgewählte Benutzername wurde bereits vergeben oder ist nicht zulässig.
Bitte einen anderen auswählen.',
	'facebook-choosenick' => 'Der Profilname auf Facebook ($1)',
	'facebook-choosefirst' => 'Vorname ($1)',
	'facebook-choosefull' => 'Dein bürgerlicher Name ($1)',
	'facebook-chooseauto' => 'Ein automatisch erzeugter Name ($1)',
	'facebook-choosemanual' => 'Ein Name der Wahl:',
	'facebook-chooseexisting' => 'Ein bestehendes Benutzerkonto in diesem Wiki',
	'facebook-chooseusername' => 'Benutzername:',
	'facebook-choosepassword' => 'Passwort:',
	'facebook-updateuserinfo' => 'Die folgenden persönlichen Angaben müssen aktualisiert werden:',
	'facebook-error-creating-user' => 'Fehler beim Erstellen des Benutzers in der lokalen Datenbank.',
	'facebook-error-user-creation-hook-aborted' => 'Die Schnittstelle einer Softwareerweiterung hat die Benutzerkontoerstellung mit folgender Nachricht abgebrochen: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook-Profil',
	'facebook-prefsheader' => "Einstellungen zu den Aktionen, die über den eigenen Facebook-Newsfeed ausgegeben werden sollen: <a id='facebookPushEventBar_show' href='#'>Einstellungen anzeigen</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>Einstellungen ausblenden</a>",
	'facebook-prefs-can-be-updated' => 'Sie können jederzeit aktualisiert werden, indem man sie unter der Registerkarte „$1“ auf der Seite Einstellungen ändert.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Kghbln
 */
$messages['de-formal'] = array(
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'facebook' => 'Facebook Connect',
	'group-fb-user' => 'Wužywarje Facebook Connect',
	'group-fb-user-member' => 'Wužywaŕ Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Wužywarje Facebook Connect',
	'group-fb-groupie' => 'Kupkowe cłonki',
	'group-fb-groupie-member' => 'Kupkowy cłonk',
	'grouppage-fb-groupie' => '{{ns:project}}:Kupkowe cłonki',
	'group-fb-officer' => 'Kupkowe zastojniki',
	'group-fb-officer-member' => 'Kupkowy zastojnik',
	'grouppage-fb-officer' => '{{ns:project}}:Kupkowe zastojniki',
	'group-fb-admin' => 'Kupkowe administratory',
	'group-fb-admin-member' => 'Kupkowy administrator',
	'grouppage-fb-admin' => '{{ns:project}}:Kupkowe administratory',
	'facebook-conv' => 'Komfortabelnosć',
	'facebook-fbml' => 'Wobznamjeńska rěc Facebook',
	'facebook-comm' => 'Komunikacija',
	'facebook-cancel' => 'akcija pśetergnjona',
	'facebook-invalid' => 'Njepłaśiwa akcija',
	'facebook-nickname' => 'Pśimě',
	'facebook-fullname' => 'Dopołne mě',
	'facebook-email' => 'E-mailowa adresa',
	'facebook-language' => 'Rěc',
	'facebook-timecorrection' => 'Korektura casoweje cony (góźiny)',
	'facebook-choosenick' => 'Mě profila na Facebooku ($1)',
	'facebook-choosefirst' => 'Twójo pśedmě ($1)',
	'facebook-choosefull' => 'Twójo dopołne mě ($1)',
	'facebook-choosemanual' => 'Mě twójogo žycenja:',
	'facebook-chooseexisting' => 'Eksistěrujuce konto w toś tom wikiju',
	'facebook-chooseusername' => 'Wužywarske mě:',
	'facebook-choosepassword' => 'Gronidło:',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil Facebook',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'facebook' => 'Facebook-Konekto',
	'facebook-desc' => 'Ebligas al uzantoj [[Special:Connect|Konekti]] kun iliaj [http://www.facebook.com Facebook]-kontoj.',
	'group-fb-user' => 'Uzantoj de Facebook-Konekto',
	'group-fb-user-member' => 'Uzanto de Facebook-Konekto',
	'grouppage-fb-user' => '{{ns:project}}:Uzantoj de Facebook-Konekto',
	'group-fb-groupie' => 'Membroj de grupo',
	'group-fb-groupie-member' => 'Membro de grupo',
	'grouppage-fb-groupie' => '{{ns:project}}:Membroj de grupo',
	'group-fb-officer' => 'Oficiroj de grupo',
	'group-fb-officer-member' => 'Oficiro de grupo',
	'grouppage-fb-officer' => '{{ns:project}}:Oficiroj de grupo',
	'group-fb-admin' => 'Administrantoj de grupo',
	'group-fb-admin-member' => 'Administranto de grupo',
	'grouppage-fb-admin' => '{{ns:project}}:Administrantoj de grupo',
	'facebook-connect' => 'Ensaluti kun Facebook-Konekto',
	'facebook-convert' => 'Konekti ĉi tiun konton kun Facebook',
	'facebook-title' => 'Konekti konton kun Facebook',
	'facebook-comm' => 'Komunikado',
	'facebook-merge' => 'Kunigi vian vikikonton kun via Facebook-identigo',
	'facebook-invalid' => 'Malvalida elekto',
	'facebook-nickname' => 'Kromnomo',
	'facebook-fullname' => 'Plena nomo',
	'facebook-email' => 'Retadreso',
	'facebook-language' => 'Lingvo',
	'facebook-timecorrection' => 'Horzona diferenco (horoj)',
	'facebook-chooselegend' => 'Elekto de salutnomo',
	'facebook-chooseinstructions' => 'Ĉiuj uzantoj bezonas kromnomo; vi povas elekti unu el la jenaj elektoj.',
	'facebook-invalidname' => 'La kromnomo elektita estas jam uzita aŭ estas malvalida kromnomo.
Bonvolu elekti malsame.',
	'facebook-choosenick' => 'Via profilnomo en Facebook ($1)',
	'facebook-choosefirst' => 'Via unua nomo ($1)',
	'facebook-choosefull' => 'Via plena nomo ($1)',
	'facebook-chooseauto' => 'Aŭtomate generita nomo ($1)',
	'facebook-choosemanual' => 'Nomo de via elekto:',
	'facebook-chooseexisting' => 'Ekzistanta konto en ĉi tiu vikio',
	'facebook-chooseusername' => 'Salutnomo:',
	'facebook-choosepassword' => 'Pasvorto:',
	'facebook-updateuserinfo' => 'Ĝisdatigi la jenan propran informon:',
);

/** Spanish (Español)
 * @author Translationista
 */
$messages['es'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Permite a los usuarios [[Special:Connect|conectarse]] con sus cuentas [http://www.facebook.com Facebook].
Ofrece autenticación basada en grupos de Facebook y usa FBML en texto wiki',
	'group-fb-user' => 'Facebook Conecta usuarios',
	'group-fb-user-member' => 'usuario de Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:usuarios de Facebook Connect',
	'group-fb-groupie' => 'Miembros del grupo',
	'group-fb-groupie-member' => 'Miembro del grupo',
	'grouppage-fb-groupie' => '{{ns:project}}:Miembros del grupo',
	'group-fb-officer' => 'Agentes de grupo',
	'group-fb-officer-member' => 'Agente de grupo',
	'grouppage-fb-officer' => '{{ns:project}}:agentes de grupo',
	'group-fb-admin' => 'Administradores de grupo',
	'group-fb-admin-member' => 'Administrador de grupo',
	'grouppage-fb-admin' => '{{ns:project}}:Administradores de grupo',
	'facebook-connect' => 'Inicia sesión con Facebook Connect',
	'facebook-convert' => 'Conectar esta cuenta con Facebook',
	'facebook-title' => 'Conectar esta cuenta con Facebook',
	'facebook-merge' => 'Fusiona tu wiki cuenta con tu identificador de Facebook',
	'facebook-error' => 'Error de verificación',
	'facebook-errortext' => 'Se produjo un error durante la verificación con Facebook Connect.',
	'facebook-cancel' => 'Acción cancelada',
	'facebook-canceltext' => 'La acción anterior fue cancelada por el usuario.',
	'facebook-invalid' => 'Opción no válida',
	'facebook-invalidtext' => 'La selección hecha en la página anterior era inválida.',
	'facebook-success' => 'Verificación de Facebook realizada con éxito',
	'facebook-successtext' => 'Has iniciado sesión exitosamente con Facebook Connect.',
	'facebook-nickname' => 'Usuario',
	'facebook-fullname' => 'Nombre completo',
	'facebook-email' => 'Dirección de correo electrónico',
	'facebook-language' => 'Idioma',
	'facebook-timecorrection' => 'Corrección de huso horario (horas)',
	'facebook-chooselegend' => 'Elección del nombre de usuario',
	'facebook-chooseinstructions' => 'Todos los usuarios necesitan un nombre de usuario;
puedes escoger uno de entre las siguientes opciones.',
	'facebook-invalidname' => 'El nombre de usuario que eligió ya está siendo usado o no es un nombre de usuario válido. 
 Por favor elija uno diferente.',
	'facebook-choosenick' => 'Tu nombre de perfil en Facebook ($1)',
	'facebook-choosefirst' => 'Tu primer nombre ($1)',
	'facebook-choosefull' => 'Tu nombre completo ($1)',
	'facebook-chooseauto' => 'Un nombre generado automáticamente ($1)',
	'facebook-choosemanual' => 'El nombre que elijas:',
	'facebook-chooseexisting' => 'Una cuenta existente en este wiki',
	'facebook-chooseusername' => 'Nombre de usuario:',
	'facebook-choosepassword' => 'Contraseña:',
	'facebook-updateuserinfo' => 'Actualiza la siguiente información personal:',
	'facebook-error-creating-user' => 'Ocurrió un error al crear el usuario en la base de datos local.',
	'facebook-error-user-creation-hook-aborted' => 'Un gancho (extensión) abortó la creación de la cuenta con el mensaje: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Perfil de Facebook',
	'facebook-prefsheader' => "Para controlar qué eventos incluirán un elemento al canal de noticias, <a id='facebookPushEventBar_show' href='#'>mostrar preferencias</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>ocultar preferencias</a>",
	'facebook-prefs-can-be-updated' => 'Puedes actualizar estos elementos en cualquier momento mediante la pestaña "$1" en tu página de preferencias.',
);

/** Basque (Euskara)
 * @author An13sa
 */
$messages['eu'] = array(
	'facebook' => 'Facebook Konexioa',
	'facebook-connect' => 'Facebook Konexioarekin saioa hasi',
	'facebook-convert' => 'Kontu hau Facebookekin lotu',
	'facebook-title' => 'Kontua Facebookekin  lotu',
	'facebook-comm' => 'Komunikazioa',
	'facebook-nickname' => 'Ezizena',
	'facebook-fullname' => 'Izen osoa',
	'facebook-email' => 'E-posta helbidea',
	'facebook-language' => 'Hizkuntza',
	'facebook-chooseusername' => 'Erabiltzaile izena:',
	'facebook-choosepassword' => 'Pasahitza:',
	'facebook-prefstext' => 'Facebook Konexioa',
	'facebook-link-to-profile' => 'Facebook profila',
);

/** Finnish (Suomi)
 * @author Nike
 * @author Olli
 */
$messages['fi'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Antaa käyttäjien [[Special:Connect|yhdistää]] [http://www.facebook.com Facebook]-tilinsä sivustolle.
Tarjoaa tunnistautumisen Facebook-ryhmiin pohjautuen ja antaa käyttää FBML-koodia wikitekstissä.',
	'group-fb-user' => 'Facebook Connect -käyttäjät',
	'group-fb-user-member' => 'Facebook Connect -käyttäjä',
	'grouppage-fb-user' => '{{ns:project}}:Facebook Connect -käyttäjät',
	'group-fb-groupie' => 'ryhmän jäsenet',
	'group-fb-groupie-member' => 'ryhmän jäsen',
	'grouppage-fb-groupie' => '{{ns:project}}:Ryhmän jäsenet',
	'group-fb-officer' => 'ryhmän virkailijat',
	'group-fb-officer-member' => 'ryhmän virkailija',
	'grouppage-fb-officer' => '{{ns:project}}:Ryhmän virkailijat',
	'group-fb-admin' => 'ryhmän ylläpitäjät',
	'group-fb-admin-member' => 'ryhmän ylläpitäjä',
	'grouppage-fb-admin' => '{{ns:project}}:Ryhmän ylläpitäjät',
	'facebook-connect' => 'Kirjaudu sisään Facebook Connectin avulla',
	'facebook-convert' => 'Yhdistä tämä tili Facebookiin',
	'facebook-title' => 'Yhdistä tili Facebookin kanssa',
	'facebook-click-to-login' => 'Kirjaudu tälle sivustolle Facebookin avulla napsauttamalla',
	'facebook-choosenick' => 'Facebook-profiilisi nimi ($1)',
	'facebook-choosefirst' => 'Etunimesi ($1)',
	'facebook-choosefull' => 'Koko nimesi ($1)',
	'facebook-chooseauto' => 'Automaattisesti luotu nimi ($1)',
	'facebook-choosemanual' => 'Omavalintainen nimi:',
	'facebook-chooseexisting' => 'Olemassa oleva tunnus tässä wikissä',
	'facebook-chooseusername' => 'Käyttäjätunnus',
	'facebook-choosepassword' => 'Salasana:',
	'facebook-updateuserinfo' => 'Päivitä seuraavat henkilökohtaiset tiedot:',
	'facebook-error-creating-user' => 'Virhe luotaessa käyttäjää paikalliseen tietokantaan.',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook-profiili',
	'facebook-prefsheader' => "Hallitse, mitkä tapahtumat tulevat näkyviin Facebook-uutissyötteeseen: <a id='facebookPushEventBar_show' href='#'>näytä asetukset</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>piilota asetukset</a>",
	'facebook-prefs-can-be-updated' => 'Voit päivittää näitä milloin tahansa siirtymällä välilehdelle »$1» asetussivullasi.',
);

/** French (Français)
 * @author Verdy p
 */
$messages['fr'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Permet aux utilisateurs de [[Special:Connect|se connecter]] avec leurs comptes [http://www.facebook.com Facebook]. Offre une authentification basée sur les groupes Facebook et l’utilisation de FBML dans le texte wiki',
	'group-fb-user' => 'Utilisateurs de Facebook Connect',
	'group-fb-user-member' => 'Utilisateur de Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Utilisateurs de Facebook Connect',
	'group-fb-groupie' => 'Membres de groupe',
	'group-fb-groupie-member' => 'Membre de groupe',
	'grouppage-fb-groupie' => '{{ns:project}}:Membres de groupe',
	'group-fb-officer' => 'Responsables de groupe',
	'group-fb-officer-member' => 'Responsable de groupe',
	'grouppage-fb-officer' => '{{ns:project}}:Responsables de groupe',
	'group-fb-admin' => 'Administrateurs de groupe',
	'group-fb-admin-member' => 'Administrateur de groupe',
	'grouppage-fb-admin' => '{{ns:project}}:Administrateurs de groupe',
	'facebook-connect' => 'Se connecter avec Facebook Connect',
	'facebook-convert' => 'Connecter ce compte avec Facebook',
	'facebook-title' => 'Connecter un compte avec Facebook',
	'facebook-merge' => 'Fusionner votre compte wiki avec votre identifiant Facebook',
	'facebook-error' => 'Erreur de vérification',
	'facebook-errortext' => 'Une erreur s’est produite lors de la vérification avec Facebook Connect.',
	'facebook-cancel' => 'Action annulée',
	'facebook-canceltext' => 'L’action précédente a été annulée par l’utilisateur.',
	'facebook-invalid' => 'Option invalide',
	'facebook-invalidtext' => 'La sélection faite à la page précédente était invalide.',
	'facebook-success' => 'Vérification Facebook réussie',
	'facebook-successtext' => 'Vous avez été connecté avec succès avec Facebook Connect.',
	'facebook-nickname' => 'Pseudonyme',
	'facebook-fullname' => 'Nom complet',
	'facebook-email' => 'Adresse courriel',
	'facebook-language' => 'Langue',
	'facebook-timecorrection' => 'Ajustement de fuseau horaire (en heures)',
	'facebook-chooselegend' => 'Choix du nom d’utilisateur',
	'facebook-chooseinstructions' => 'Tous les utilisateurs ont besoin d’un pseudonyme ; vous pouvez en choisir un à partir des choix ci-dessous.',
	'facebook-invalidname' => 'Le pseudonyme que vous avez choisi est déjà pris ou n’est pas un pseudonyme valide.
Veuillez en choisir un autre.',
	'facebook-choosenick' => 'Votre nom de profil Facebook ($1)',
	'facebook-choosefirst' => 'Votre prénom ($1)',
	'facebook-choosefull' => 'Votre nom complet ($1)',
	'facebook-chooseauto' => 'Un nom créé automatiquement ($1)',
	'facebook-choosemanual' => 'Un nom de votre choix :',
	'facebook-chooseexisting' => 'Un compte existant sur ce wiki',
	'facebook-chooseusername' => 'Nom d’utilisateur :',
	'facebook-choosepassword' => 'Mot de passe :',
	'facebook-updateuserinfo' => 'Mettre à jour les renseignements personnels suivants :',
	'facebook-error-creating-user' => 'Erreur de création de l’utilisateur dans la base de données locale.',
	'facebook-error-user-creation-hook-aborted' => 'Un crochet (extension) a abandonné la création de compte avec le message : $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil Facebook',
	'facebook-prefsheader' => 'Pour contrôler quels évènements vont générer un élément inclus dans votre flux de nouvelles Facebook, <a id="facebookPushEventBar_show" href="#">montrer les préférences</a> <a id="facebookPushEventBar_hide" href="#" style="display:none">cacher les préférences</a>',
	'facebook-prefs-can-be-updated' => 'Vous pouvez mettre à jour ces éléments à tout moment en visitant l’onglet « $1 » de votre page de préférences.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Permite aos usuarios [[Special:Connect|conectarse]] coas súas contas do [http://www.facebook.com Facebook].
Ofrece unha autenticación baseada en grupos do Facebook e o uso de FBML no texto wiki',
	'group-fb-user' => 'Usuarios do Facebook Connect',
	'group-fb-user-member' => 'Usuario do Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Usuarios do Facebook Connect',
	'group-fb-groupie' => 'Membros do grupo',
	'group-fb-groupie-member' => 'Membro do grupo',
	'grouppage-fb-groupie' => '{{ns:project}}:Membros do grupo',
	'group-fb-officer' => 'Directores do grupo',
	'group-fb-officer-member' => 'Director do grupo',
	'grouppage-fb-officer' => '{{ns:project}}:Directores do grupo',
	'group-fb-admin' => 'Administradores do grupo',
	'group-fb-admin-member' => 'Administrador do grupo',
	'grouppage-fb-admin' => '{{ns:project}}:Administradores do grupo',
	'facebook-connect' => 'Identificarse co Facebook Connect',
	'facebook-convert' => 'Conectar esta conta co Facebook',
	'facebook-title' => 'Conectar a conta co Facebook',
	'facebook-merge' => 'Fusionar a súa conta wiki co ID do Facebook',
	'facebook-error' => 'Erro de verificación',
	'facebook-errortext' => 'Houbo un erro durante a comprobación co Facebook Connect.',
	'facebook-cancel' => 'Acción cancelada',
	'facebook-canceltext' => 'O usuario cancelou a acción anterior.',
	'facebook-invalid' => 'Opción incorrecta',
	'facebook-invalidtext' => 'A selección feita na páxina anterior era incorrecta.',
	'facebook-success' => 'Verificación do Facebook correcta',
	'facebook-successtext' => 'Accedeu ao sistema correctamente co Facebook Connect.',
	'facebook-nickname' => 'Alcume',
	'facebook-fullname' => 'Nome completo',
	'facebook-email' => 'Enderezo de correo electrónico',
	'facebook-language' => 'Lingua',
	'facebook-timecorrection' => 'Corrección da zona horaria (horas)',
	'facebook-chooselegend' => 'Elección do nome de usuario',
	'facebook-chooseinstructions' => 'Todos os usuarios precisan un alcume; pode escoller un de entre as opcións de embaixo.',
	'facebook-invalidname' => 'O alcume elixido xa está tomado ou non é válido.
Escolla un diferente.',
	'facebook-choosenick' => 'O nome do seu perfil no Facebook ($1)',
	'facebook-choosefirst' => 'O seu nome ($1)',
	'facebook-choosefull' => 'O seu nome completo ($1)',
	'facebook-chooseauto' => 'Un nome xerado automaticamente ($1)',
	'facebook-choosemanual' => 'Un nome da súa escolla:',
	'facebook-chooseexisting' => 'Unha conta existente neste wiki',
	'facebook-chooseusername' => 'Nome de usuario:',
	'facebook-choosepassword' => 'Contrasinal:',
	'facebook-updateuserinfo' => 'Actualice a seguinte información persoal:',
	'facebook-error-creating-user' => 'Erro ao crear o usuario na base de datos local.',
	'facebook-error-user-creation-hook-aborted' => 'Un hook (extensión) abortou a creación da conta con esta mensaxe: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Perfil no Facebook',
	'facebook-prefsheader' => "Para controlar aqueles acontecementos que xerarán un elemento na súa fonte de novas do Facebook, <a id='facebookPushEventBar_show' href='#'>mostrar as preferencias</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>agochar as preferencias</a>",
	'facebook-prefs-can-be-updated' => 'Pode actualizar estes elementos en calquera momento visitando a lapela "$1" da súa páxina de preferencias.',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Stellt e [[Special:Connect|Spezialsyte]] z Verfiegig, wu Benutzer dermit e Verbindig mit ihre [http://de-de.facebook.com/ Facebook-Konte] chenne härstelle.
Macht e Authentifizierig megli, wu uf Facebook-Gruppe basiert, un dr Yysatz vu FBML in Wikitext.',
	'group-fb-user' => 'Facebook-Connect-Benutzer',
	'group-fb-user-member' => 'Facebook-Connect-Benutzer',
	'grouppage-fb-user' => '{{ns:project}}:Facebook-Connect-Benutzer',
	'group-fb-groupie' => 'Gruppemitglider',
	'group-fb-groupie-member' => 'Gruppemitglid',
	'grouppage-fb-groupie' => '{{ns:project}}:Gruppemitglider',
	'group-fb-officer' => 'Gruppeverwalter',
	'group-fb-officer-member' => 'Gruppeverwalter',
	'grouppage-fb-officer' => '{{ns:project}}:Gruppeverwalter',
	'group-fb-admin' => 'Gruppeammanne',
	'group-fb-admin-member' => 'Gruppeammann',
	'grouppage-fb-admin' => '{{ns:project}}:Gruppeammanne',
	'facebook-connect' => 'Aamälde iber Facebook Connect',
	'facebook-convert' => 'Des Konto mit Facebook verbinde',
	'facebook-title' => 'Konto mit Facebook verbinde',
	'facebook-merge' => 'S Wikikonto mit dr Facebook-ID zämmefiere',
	'facebook-error' => 'Iberpriefigsfähler',
	'facebook-errortext' => 'E Fähler isch bi dr Iberpriefig mit Facebook Connect ufträtte.',
	'facebook-cancel' => 'Aktion abbroche',
	'facebook-canceltext' => 'Di vorig Aktion isch vum Benutzer abbroche wore.',
	'facebook-invalid' => 'Nit giltigi Option',
	'facebook-invalidtext' => 'D Uuswahl, wu uf dr vorige Syte troffe woren isch, isch nit giltig.',
	'facebook-success' => 'Facebook Connect-Iberpriefig erfolgryych',
	'facebook-successtext' => 'D Aamäldig iber Facebook Connect isch erfolgryych gsi.',
	'facebook-nickname' => 'Benutzername',
	'facebook-fullname' => 'Vollständige Name',
	'facebook-email' => 'E-Mail-Adräss',
	'facebook-language' => 'Sproch',
	'facebook-timecorrection' => 'Zytzonekorrektur (Stunde)',
	'facebook-chooselegend' => 'Benutzernameuuswahl',
	'facebook-chooseinstructions' => 'Alli Benutzer bruuche ne Benutzername; Du chasch us däre Lischt ein uussueche.',
	'facebook-invalidname' => 'Dr uusgwehlt Benutzername isch scho vergee oder er isch nit zuelässig.
Bitte wehl e andere.',
	'facebook-choosenick' => 'Dr Profilname uf Facebook ($1)',
	'facebook-choosefirst' => 'Dyy Vorname ($1)',
	'facebook-choosefull' => 'Dyy vollständige Name ($1)',
	'facebook-chooseauto' => 'E automatisch aagleite Name ($1)',
	'facebook-choosemanual' => 'E vu Dir gwehlte Name:',
	'facebook-chooseexisting' => 'E Benutzerkonto, wu s in däm Wiki git',
	'facebook-chooseusername' => 'Benutzername:',
	'facebook-choosepassword' => 'Passwort:',
	'facebook-updateuserinfo' => 'Die persenlige Aagabe mien aktualisiert wäre:',
	'facebook-error-creating-user' => 'Fähler bim Aalege vum Benutzer in dr lokale Datebank.',
	'facebook-error-user-creation-hook-aborted' => 'D Schnittstell vun ere Softwareerwyterig het s Aalege vum Benutzerkonto abbroche mit däre Nochricht: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook-Profil',
	'facebook-prefsheader' => "Yystellige zue dr Aktione, wu iber dr eige Facebook-Newsfeed uusgee wäre solle: <a id='facebookPushEventBar_show' href='#'>Yystellige aazeige</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>Yystellige uusblände</a>",
	'facebook-prefs-can-be-updated' => 'Du chasch si jederzyt aktualisiere uf dr Regischtercharte „$1“ in Dyyne Yystellige.',
);

/** Hebrew (עברית)
 * @author YaronSh
 */
$messages['he'] = array(
	'group-fb-user' => 'משתמשי Facebook Connect',
	'group-fb-user-member' => 'משתמש Facebook Connect',
	'facebook-connect' => 'כניסה עם Facebook Connect',
	'facebook-title' => 'קישור חשבון ל־Facebook',
	'facebook-click-to-login' => 'נא ללחוץ כדי להיכנס לאתר זה דרך Facebook',
	'facebook-conv' => 'נוחות',
	'facebook-fbml' => 'שפת הסימון של Facebook',
	'facebook-comm' => 'תקשורת',
	'facebook-error' => 'שגיאת אימות',
	'facebook-errortext' => 'אירעה שגיאה במהלך האימות מול Facebook Connect.',
	'facebook-cancel' => 'הפעולה בוטלה',
	'facebook-canceltext' => 'הפעולה הקודמת בוטלה על ידי המשתמש.',
	'facebook-invalid' => 'אפשרות שגויה',
	'facebook-successtext' => 'נכנסת בהצלחה באמצעות Facebook Connect.',
	'facebook-nickname' => 'כינוי',
	'facebook-fullname' => 'שם מלא',
	'facebook-email' => 'כתובת דוא״ל',
	'facebook-language' => 'שפה',
	'facebook-timecorrection' => 'תיקון לאזור הזמן (שעות)',
	'facebook-chooselegend' => 'בחירת שם המשתמש',
	'facebook-chooseinstructions' => 'כל המשתמשים זקוקים לכינוי; ניתן לבחור באחד מהאפשרויות שלהלן.',
	'facebook-invalidname' => 'הכינוי שבחרת כבר תפוס או שאינו תקני.
נא לנסות לבחור באחד אחר.',
	'facebook-choosefirst' => 'שמך הפרטי ($1)',
	'facebook-choosefull' => 'שמך המלא ($1)',
	'facebook-chooseauto' => 'שם שנוצר אוטומטית ($1)',
	'facebook-choosemanual' => 'שם לבחירתך:',
	'facebook-chooseexisting' => 'חשבון קיים בוויקי זה',
	'facebook-chooseusername' => 'שם משתמש:',
	'facebook-choosepassword' => 'ססמה:',
	'facebook-updateuserinfo' => 'עדכון הפרטים האישיים הבאים:',
	'facebook-error-creating-user' => 'שגיאה ביצירת המשתמש בבסיס הנתונים המקומי.',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook פרופיל',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Zmóžnja wužiwarjam so z jich kontami na [http://www,facebook.com Facebook] [[Special:Connect|zwjazać]].',
	'group-fb-user' => 'Wužiwarjo Facebook Connect',
	'group-fb-user-member' => 'Wužiwar Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Wužiwarjo Facebook Connect',
	'group-fb-groupie' => 'Skupinscy čłonojo',
	'group-fb-groupie-member' => 'Skupinski čłon',
	'grouppage-fb-groupie' => '{{ns:project}}:Skupinscy čłonojo',
	'group-fb-officer' => 'Skupinscy zarjadnicy',
	'group-fb-officer-member' => 'Skupinski zarjadnik',
	'grouppage-fb-officer' => '{{ns:project}}:Skupinscy zarjadnicy',
	'group-fb-admin' => 'Skupinscy administratorojo',
	'group-fb-admin-member' => 'Skupinski administrator',
	'grouppage-fb-admin' => '{{ns:project}}:Skupinscy administratorojo',
	'facebook-connect' => 'Přez Facebook Connect přizjewić',
	'facebook-convert' => 'Tute konto z Facebookom zwjazać',
	'facebook-title' => 'Konto z Facebookom zwjazać',
	'facebook-merge' => 'Twoje wikikonto z twojim Facebook-ID zwjazać',
	'facebook-error' => 'Pruwowanski zmylk',
	'facebook-errortext' => 'Při přepruwowanju přez Facebook Connect je zmylk wustupił.',
	'facebook-cancel' => 'Akcija přetorhnjena',
	'facebook-canceltext' => 'Předchadna akcija bu wot wužiwarja přetorhnjena.',
	'facebook-invalid' => 'Njepłaćiwa opcija',
	'facebook-invalidtext' => 'Wuběr na předchadnej stronje bě njepłaćiwy.',
	'facebook-success' => 'Přepruwowanje Facebook je so poradźiło',
	'facebook-successtext' => 'Sy so wuspěšnje přez Facebook Connect přizjewił.',
	'facebook-nickname' => 'Přimjeno',
	'facebook-fullname' => 'Dospołne mjeno',
	'facebook-email' => 'E-mejlowa adresa',
	'facebook-language' => 'Rěč',
	'facebook-timecorrection' => 'Korektura časoweho pasma (hodźiny)',
	'facebook-chooselegend' => 'Wuběranje wužiwarskeho mjena',
	'facebook-chooseinstructions' => 'Wšitcy wužiwarjo trjebaja přimjeno; móžěs jedne z opcijow deleka wubrać.',
	'facebook-invalidname' => 'Wubrane přimjeno so hižo wužiqwa abo njeje płaćiwe.
Prošu wubjer druhe přimjeno.',
	'facebook-choosenick' => 'Mjeno profila na Facebooku ($1)',
	'facebook-choosefirst' => 'Twoje předmjeno ($1)',
	'facebook-choosefull' => 'Twoje dospołne mjeno ($1)',
	'facebook-chooseauto' => 'Awtomatisce wutworjene mjeno ($1)',
	'facebook-choosemanual' => 'Mjeno twojeje wólby:',
	'facebook-chooseexisting' => 'Eksistowace konto na tutym wikiju',
	'facebook-chooseusername' => 'Wužiwarske mjeno:',
	'facebook-choosepassword' => 'Hesło:',
	'facebook-updateuserinfo' => 'Zaktualizuj slědowace wosobinske informacije:',
	'facebook-error-creating-user' => 'Zmylk při wutworjenju wužiwarja w lokalnej datowej bance.',
	'facebook-error-user-creation-hook-aborted' => 'Rozšěrjenje přetorhny załoženje konta ze slědowacej zdźělenku: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil Facebook',
	'facebook-prefsheader' => "Zo by wodźił, kotre podawki pósćelu element do kanala nowinkow Facebook, <a id='facebookPushEventBar_show' href='#'>nastajenja pokazać</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>nastajenja schować</a>",
	'facebook-prefs-can-be-updated' => 'Móžeš je kóždy čas aktualizować, hdyž je na rajtarku "$1" na stronje twojich nastajenjow změniš.',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'facebook' => 'Facebook Connect',
	'group-fb-user' => 'Facebook Connect felhasználók',
	'group-fb-user-member' => 'Facebook Connect felhasználó',
	'grouppage-fb-user' => '{{ns:project}}:Facebook Connect felhasználók',
	'group-fb-groupie' => 'csoporttagok',
	'group-fb-groupie-member' => 'csoporttag',
	'grouppage-fb-groupie' => '{{ns:project}}:Csoporttagok',
	'group-fb-officer' => 'csoporttisztek',
	'group-fb-officer-member' => 'csoporttiszt',
	'grouppage-fb-officer' => '{{ns:project}}:Csoporttisztek',
	'group-fb-admin' => 'csoportadminisztrátorok',
	'group-fb-admin-member' => 'csoportadminisztrátor',
	'grouppage-fb-admin' => '{{ns:project}}:Csoportadminisztrátorok',
	'facebook-connect' => 'Bejelentkezés a Facebook Connecttel',
	'facebook-convert' => 'Fiók összekötése a Facebookkal',
	'facebook-title' => 'Fiók összekötése a Facebookkal',
	'facebook-click-to-login' => 'Bejelentkezés az oldalra Facebookon keresztül',
	'facebook-conv' => 'Kényelem',
	'facebook-fbml' => 'Facebook-jelölőnyelv',
	'facebook-comm' => 'Kommunikáció',
	'facebook-merge' => 'Wikis fiók összekötése facebookos azonosítóval',
	'facebook-error' => 'Hiba az ellenőrzés során',
	'facebook-errortext' => 'Hiba történt a Facebook Connect ellenőrzése közben.',
	'facebook-cancel' => 'Művelet megszakítva',
	'facebook-canceltext' => 'A felhasználó megszakította az előző műveletet.',
	'facebook-invalid' => 'Érvénytelen beállítás',
	'facebook-invalidtext' => 'Az előző oldalon kiválasztott beállítás érvénytelen.',
	'facebook-success' => 'Facebook-ellenőrzés sikerült',
	'facebook-successtext' => 'Sikeresen bejelentkeztél a Facebook Connect használatával.',
	'facebook-nickname' => 'Becenév',
	'facebook-fullname' => 'Teljes név',
	'facebook-email' => 'E-mail cím',
	'facebook-language' => 'Nyelv',
	'facebook-timecorrection' => 'Időzóna-korrekció (órában)',
	'facebook-chooselegend' => 'Felhasználónév választása',
	'facebook-chooseinstructions' => 'Minden felhasználónak kell egy becenév; válassz egyet az alábbi lehetőségek közül.',
	'facebook-choosenick' => 'Facebook-profilod neve ($1)',
	'facebook-choosefirst' => 'A keresztneved ($1)',
	'facebook-choosefull' => 'A teljes neved ($1)',
	'facebook-chooseauto' => 'Automatikusan generált név ($1)',
	'facebook-choosemanual' => 'A választott név:',
	'facebook-chooseexisting' => 'Már létező fiók ezen a wikin',
	'facebook-chooseusername' => 'Felhasználónév:',
	'facebook-choosepassword' => 'Jelszó:',
	'facebook-updateuserinfo' => 'A következő személyes adatok frissítése:',
	'facebook-error-creating-user' => 'Nem sikerült létrehozni a felhasználót a helyi adatbázisban.',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook-profil',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Permitte al usatores de [[Special:Connect|connecter se]] con lor contos de [http://www.facebook.com Facebook].
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
	'facebook-connect' => 'Aperir session con Facebook Connect',
	'facebook-convert' => 'Connecter iste conto con Facebook',
	'facebook-title' => 'Connecter le conto con Facebook',
	'facebook-merge' => 'Fusionar tu conto wiki con tu ID de Facebook',
	'facebook-error' => 'Error de verification',
	'facebook-errortext' => 'Un error occurreva durante le verification con Facebook Connect.',
	'facebook-cancel' => 'Action cancellate',
	'facebook-canceltext' => 'Le previe action esseva cancellate per le usator.',
	'facebook-invalid' => 'Option invalide',
	'facebook-invalidtext' => 'Le modo de selection in le previe pagina es invalide.',
	'facebook-success' => 'Verification de Facebook succedite',
	'facebook-successtext' => 'Le apertura de session con Facebook Connect ha succedite.',
	'facebook-nickname' => 'Pseudonymo',
	'facebook-fullname' => 'Nomine complete',
	'facebook-email' => 'Adresse de e-mail',
	'facebook-language' => 'Lingua',
	'facebook-timecorrection' => 'Correction de fuso horari (horas)',
	'facebook-chooselegend' => 'Selection del nomine de usator',
	'facebook-chooseinstructions' => 'Tote le usatores require un pseudonymo;
tu pote seliger un del optiones in basso.',
	'facebook-invalidname' => 'Le pseudonymo que tu seligeva es jam in uso o non es un pseudonymo valide.
Per favor selige un altere.',
	'facebook-choosenick' => 'Le nomine de tu profilo de Facebook ($1)',
	'facebook-choosefirst' => 'Tu prenomine ($1)',
	'facebook-choosefull' => 'Tu nomine complete ($1)',
	'facebook-chooseauto' => 'Un nomine automaticamente generate ($1)',
	'facebook-choosemanual' => 'Un nomine de tu preferentia:',
	'facebook-chooseexisting' => 'Un conto existente in iste wiki',
	'facebook-chooseusername' => 'Nomine de usator:',
	'facebook-choosepassword' => 'Contrasigno:',
	'facebook-updateuserinfo' => 'Actualisar le sequente informationes personal:',
	'facebook-error-creating-user' => 'Error durante le creation del usator in le base de datos local.',
	'facebook-error-user-creation-hook-aborted' => 'Le interfacie de un extension ha abortate le creation del conto con le message: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profilo de Facebook',
	'facebook-prefsheader' => "Pro determinar le eventos que pote inserer un entrata in tu lista de novas a Facebook, <a id='facebookPushEventBar_show' href='#'>monstra preferentias</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>cela preferentias</a>",
	'facebook-prefs-can-be-updated' => 'Tu pote sempre actualisar istes per visitar le scheda "$1" de tu pagina de preferentias.',
);

/** Indonesian (Bahasa Indonesia)
 * @author IvanLanin
 */
$messages['id'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Memungkinkan pengguna untuk [[Special:Connect|tersambung]] dengan akun [http://www.facebook.com Facebook].
Memberikan autentikasi berdasarkan grup Facebook dan penggunaan FBML dalam teks wiki.',
	'group-fb-user' => 'Pengguna Facebook Connect',
	'group-fb-user-member' => 'Pengguna Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Pengguna Facebook Connect',
	'group-fb-groupie' => 'Anggota grup',
	'group-fb-groupie-member' => 'Anggota grup',
	'grouppage-fb-groupie' => '{{ns:project}}:Anggota grup',
	'group-fb-officer' => 'Pejabat grup',
	'group-fb-officer-member' => 'Pejabat grup',
	'grouppage-fb-officer' => '{{ns:project}}:Pejabat grup',
	'group-fb-admin' => 'Admin grup',
	'group-fb-admin-member' => 'Administrator grup',
	'grouppage-fb-admin' => '{{ns:project}}:Admin grup',
	'facebook-connect' => 'Masuk dengan Facebook Connect',
	'facebook-convert' => 'Hubungkan akun ini dengan Facebook',
	'facebook-title' => 'Hubungkan akun dengan Facebook',
	'facebook-merge' => 'Gabungkan akun wiki dengan ID Facebook Anda',
	'facebook-error' => 'Kesalahan verifikasi',
	'facebook-errortext' => 'Kesalahan terjadi sewaktu verifikasi dengan Facebook Connect.',
	'facebook-cancel' => 'Tindakan dibatalkan',
	'facebook-canceltext' => 'Tindakan sebelumnya dibatalkan oleh pengguna.',
	'facebook-invalid' => 'Pilihan tidak sah',
	'facebook-invalidtext' => 'Pilihan yang dibuat pada halaman sebelumnya tidak sah.',
	'facebook-success' => 'Verifikasi Facebook berhasil',
	'facebook-successtext' => 'Anda berhasil masuk dengan Facebook Connect.',
	'facebook-nickname' => 'Nama panggilan',
	'facebook-fullname' => 'Nama lengkap',
	'facebook-email' => 'Alamat surel',
	'facebook-language' => 'Bahasa',
	'facebook-timecorrection' => 'Koreksi zona waktu (jam)',
	'facebook-chooselegend' => 'Pilihan nama pengguna',
	'facebook-chooseinstructions' => 'Semua pengguna memerlukan nama panggilan; Anda dapat memilih dari salah satu opsi berikut.',
	'facebook-invalidname' => 'Nama panggilan yang Anda pilih sudah diambil atau tidak sah.
Silakan memilih yang berbeda.',
	'facebook-choosenick' => 'Nama profil Facebook Anda ($1)',
	'facebook-choosefirst' => 'Nama depan Anda ($1)',
	'facebook-choosefull' => 'Nama lengkap Anda ($1)',
	'facebook-chooseauto' => 'Nama yang dibuat secara otomatis ($1)',
	'facebook-choosemanual' => 'Nama pilihan Anda:',
	'facebook-chooseexisting' => 'Akun yang telah ada di wiki ini',
	'facebook-chooseusername' => 'Nama pengguna:',
	'facebook-choosepassword' => 'Sandi:',
	'facebook-updateuserinfo' => 'Perbarui informasi pribadi berikut:',
	'facebook-error-creating-user' => 'Kesalahan saat membuat pengguna dalam basis data lokal.',
	'facebook-error-user-creation-hook-aborted' => 'Suatu pengait (ekstensi) menggagalkan pembuatan akun dengan pesan: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil Facebook',
	'facebook-prefsheader' => 'Untuk mengendalikan peristiwa yang akan mendorong butir ke kabar berita Facebook, <a id="facebookPushEventBar_show" href="#">tampilkan preferensi</a> untuk <a id="facebookPushEventBar_hide" href="#" style="display:none">sembunyikan preferensi</a>',
	'facebook-prefs-can-be-updated' => 'Anda dapat memperbaruinya kapan saja dengan mengunjungi tab "$1" pada halaman preferensi Anda.',
);

/** Italian (Italiano)
 * @author Beta16
 * @author Ric
 */
$messages['it'] = array(
	'group-fb-groupie' => 'Membri del gruppo',
	'group-fb-groupie-member' => 'Membri del gruppo',
	'facebook-click-to-login' => 'Fare clic per accedere a questo sito tramite Facebook',
	'facebook-click-to-connect-existing' => 'Clicca per collegare il tuo account Facebook a $1',
	'facebook-comm' => 'Comunicazione',
	'facebook-error' => 'Errore di verifica',
	'facebook-nickname' => 'Nickname',
	'facebook-fullname' => 'Nome completo',
	'facebook-email' => 'Indirizzo e-mail',
	'facebook-language' => 'Lingua',
	'facebook-chooselegend' => 'Scelta del nome utente',
	'facebook-chooseinstructions' => 'Tutti gli utenti hanno bisogno di un nickname; puoi sceglierne uno dalle opzioni seguenti.',
	'facebook-choosefirst' => 'Il tuo nome ($1)',
	'facebook-choosefull' => 'Il tuo nome completo ($1)',
	'facebook-chooseauto' => 'Un nome auto-generato ($1)',
	'facebook-choosemanual' => 'Un nome di tua scelta:',
	'facebook-chooseusername' => 'Nome utente:',
	'facebook-choosepassword' => 'Password:',
);

/** Japanese (日本語)
 * @author 青子守歌
 */
$messages['ja'] = array(
	'facebook' => 'Facebook接続',
	'facebook-desc' => '利用者が、[http://www.facebook.com Facebook]アカウントで[[Special:Connect|接続]]できるようにする。
ウィキ文中のFBMLとFacebookグループに基づく申し込み認証',
	'group-fb-user' => 'Facebook接続利用者',
	'group-fb-user-member' => 'Facebook接続利用者',
	'grouppage-fb-user' => '{{ns:project}}:Facebook接続利用者',
	'group-fb-groupie' => 'グループメンバー',
	'group-fb-groupie-member' => 'グループメンバー',
	'grouppage-fb-groupie' => '{{ns:project}}:グループメンバー',
	'group-fb-officer' => 'グループの役員',
	'group-fb-officer-member' => 'グループの役員',
	'grouppage-fb-officer' => '{{ns:project}}:グループの役員',
	'group-fb-admin' => 'グループの管理者',
	'group-fb-admin-member' => 'グループの管理者',
	'grouppage-fb-admin' => '{{ns:project}}:グループの管理者',
	'facebook-connect' => 'Facebook接続でログイン',
	'facebook-convert' => 'このアカウントにFacebookで接続',
	'facebook-title' => 'Facebookでアカウントに接続',
	'facebook-merge' => 'FacebookのIDを使用して、ウィキ上の自分のアカウントを統合',
	'facebook-error' => '検証エラー',
	'facebook-errortext' => 'Facebook接続で認証中にエラーが発生しました。',
	'facebook-cancel' => '操作がキャンセルされました',
	'facebook-canceltext' => '1つまえの操作が利用者によって取り消されました。',
	'facebook-invalid' => '不正なオプション',
	'facebook-invalidtext' => '前のページで選択された形式は不正です。',
	'facebook-success' => 'Facebook検証は成功しました',
	'facebook-successtext' => 'Facebook接続でのログインに成功しました',
	'facebook-nickname' => 'ニックネーム',
	'facebook-fullname' => 'フルネーム',
	'facebook-email' => '電子メールアドレス',
	'facebook-language' => '言語',
	'facebook-timecorrection' => 'タイムゾーンの補正（時間）',
	'facebook-chooselegend' => '利用者名の選択',
	'facebook-chooseinstructions' => 'すべての利用者はニックネームが必要です。以下の選択肢から1つを選ぶことができます。',
	'facebook-invalidname' => '選択されたニックネームは既に使用されているか、有効でないニックネームです。
別の選択肢を選んでください。',
	'facebook-choosenick' => '自分のFacebookプロフィール名（$1）',
	'facebook-choosefirst' => '名（$1）',
	'facebook-choosefull' => '氏名（$1）',
	'facebook-chooseauto' => '自動生成された名前（$1）',
	'facebook-choosemanual' => '選択された名前：',
	'facebook-chooseexisting' => 'このウィキに存在するアカウント',
	'facebook-chooseusername' => '利用者名：',
	'facebook-choosepassword' => 'パスワード：',
	'facebook-updateuserinfo' => '以下の個人情報を更新する：',
	'facebook-error-creating-user' => 'ローカルのデータベースに利用者を作成する時にエラーが発生しました。',
	'facebook-error-user-creation-hook-aborted' => 'フック（拡張機能）は、次のメッセージと共にアカウントの作成を中断しました：$1',
	'facebook-prefstext' => 'Facebook接続',
	'facebook-link-to-profile' => 'Facebookのプロフィール',
	'facebook-prefsheader' => "どのイベントがFacebookニュースフィードに項目を挿入するかを制御するために、<a id='facebookPushEventBar_show' href='#'>設定を表示</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>設定を非表示</a>",
	'facebook-prefs-can-be-updated' => 'これらは、いつでも、設定ページの「$1」タブから更新することができます。',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Erlaabt et Benotzer hir sech mat hire [http://www.facebook.com Facebook] Konten ze [[Special:Connect|connectéieren]].',
	'group-fb-user' => 'Facebook-Connect-Benotzer',
	'group-fb-user-member' => 'Facebook-Connect-Benotzer',
	'grouppage-fb-user' => '{{ns:project}}:Facebook-Connect-Benotzer',
	'group-fb-groupie' => 'Membere vum Grupp',
	'group-fb-groupie-member' => 'Member vum Grupp',
	'grouppage-fb-groupie' => '{{ns:project}}: Gruppememberen',
	'group-fb-officer' => 'Responsabel vum Grupp',
	'group-fb-officer-member' => 'Responsabel vum Grupp',
	'grouppage-fb-officer' => '{{ns:project}}:Responsabel vum Grupp',
	'group-fb-admin' => 'Administrateure vum Grupp',
	'group-fb-admin-member' => 'Administrateur vum Grupp',
	'grouppage-fb-admin' => '{{ns:project}}:Administrateure vum Grupp',
	'facebook-connect' => 'Mat Facebook-Connect aloggen',
	'facebook-convert' => 'Dëse Kont mat Facebook verbannen',
	'facebook-title' => 'Kont mat Facebook verbannen',
	'facebook-click-to-login' => 'Klickt fir Iech iwwer Facebook op dësem Site anzeloggen',
	'facebook-click-to-connect-existing' => 'Klickt fir Äre Facebook-Kont mat $1 ze verbannen',
	'facebook-conv' => 'Bequemlechkeet',
	'facebook-convdesc' => "Verbonne Benotzer ginn automatesch ageloggt.
Wann d'Autorisatioun virläit kann dës Wiki esouguer Facebook als E-Mail-Proxy benotzen esou datt Dir weiderhi wichteg Matdeelunge kréie kënnt ouni Är E-Mailadress präiszeginn.",
	'facebook-fbml' => 'Facebook-Markup Sprooch',
	'facebook-comm' => 'Kommunikatioun',
	'facebook-commdesc' => "Facebook-Connect erlaabt e ganz neien Niveau vu Networking.
Kuckt wie vun Äre Frënn d'Wiki benotzt, a virun allem deelt Är Aktioune mat Äre Frënn iwwer d'Facebook-Newsfeed.",
	'facebook-merge' => 'Verbannt Äre Wiki-Kont mat Ärer Facebook-ID',
	'facebook-usernameprefix' => 'Facebook-Benotzer',
	'facebook-error' => 'Feeler bei der Iwwerpréifung',
	'facebook-errortext' => 'Bäi der Iwwerpréifung mat Facebook Connect ass e Feeler geschitt.',
	'facebook-cancel' => 'Aktioun ofgebrach',
	'facebook-canceltext' => 'Déi Aktioun virdru gouf vum Benotzer ofgebrach.',
	'facebook-invalid' => 'Net-valabel Optioun',
	'facebook-invalidtext' => 'Dat wat Dir op der Säit virdrun erausgesicht hutt ass net valabel.',
	'facebook-success' => 'Facebook Iwwerpréifung mat Succès',
	'facebook-successtext' => 'Dir sidd elo mat Facebook Connect ageloggt.',
	'facebook-nickname' => 'Spëtznumm',
	'facebook-fullname' => 'Ganzen Numm',
	'facebook-email' => 'E-Mailadress',
	'facebook-language' => 'Sprooch',
	'facebook-timecorrection' => "Verbesserung fir d'Zäitzon (Stonnen)",
	'facebook-chooselegend' => 'Eraussiche vum Benotzernumm',
	'facebook-chooseinstructions' => 'All Benotzer brauchen e Spëtznumm; Dir kënnt Iech een aus den Optiounen hei drënner eraussichen.',
	'facebook-invalidname' => 'De Spëtznumm deen Dir erausgesicht hutt ass scho verginn oder et ass kee valabele Spëtznumm.
Sicht Iech w.e.g. een Aneren.',
	'facebook-choosenick' => 'Äre Facbook-Profilnumm ($1)',
	'facebook-choosefirst' => 'Äre Virnumm ($1)',
	'facebook-choosefull' => 'Äre ganzen Numm ($1)',
	'facebook-chooseauto' => 'En Numm deen automatesch generéiert gouf ($1)',
	'facebook-choosemanual' => 'En Numm vun Ärer Wiel:',
	'facebook-chooseexisting' => 'E Benotzerkont deen et op dëser Wiki gëtt',
	'facebook-chooseusername' => 'Benotzernumm:',
	'facebook-choosepassword' => 'Passwuert:',
	'facebook-updateuserinfo' => 'Dës perséinlech Informatioun aktualiséieren:',
	'facebook-error-creating-user' => 'Feeler beim Uleeë vum Benotzer an der lokaler Datebank.',
	'facebook-error-user-creation-hook-aborted' => "Eng Erweiderung huet d'Uleeë vum Kont ofgebrach mam Message: $1",
	'facebook-prefstext' => 'Facebook-Connect',
	'facebook-link-to-profile' => 'Facebook-Profil',
	'facebook-prefs-can-be-updated' => 'Dir kënnt dës zu all Moment aktualiséieren an deem Dir op den Tab "$1" op der Säit vun Ären Astellunge gitt.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Им овозможува на корисниците да се [[Special:Connect|поврзат]] со нивните сметки на [http://mk-mk.facebook.com Facebook].
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
	'facebook-connect' => 'Најава со Facebook Connect',
	'facebook-convert' => 'Поврзи ја оваа сметка со Facebook',
	'facebook-title' => 'Поврзи ја сметката преку Facebook',
	'facebook-merge' => 'Спојте ја Вашата вики-сметка со Вашата назнака (ID) на Facebook',
	'facebook-usernameprefix' => 'FacebookUser',
	'facebook-error' => 'Грешка при потврдувањето',
	'facebook-errortext' => 'Се појави грешка при потврдувањето во однос на Facebook Connect.',
	'facebook-cancel' => 'Дејството е откажано',
	'facebook-canceltext' => 'Претходното дејство е откажано од корисникот.',
	'facebook-invalid' => 'Неважечка можност',
	'facebook-invalidtext' => 'Направениот избор на претходната страница е неважечки.',
	'facebook-success' => 'Потврдата на Facebook успеа',
	'facebook-successtext' => 'Успешно сте најавени со Facebook Connect.',
	'facebook-nickname' => 'Прекар',
	'facebook-fullname' => 'Име и презиме',
	'facebook-email' => 'Е-пошта',
	'facebook-language' => 'Јазик',
	'facebook-timecorrection' => 'Исправка на часовната зона (часови)',
	'facebook-chooselegend' => 'Избор на корисничко име',
	'facebook-chooseinstructions' => 'Сите корисници треба да имаат прекар; можете да одберете од долунаведените можности.',
	'facebook-invalidname' => 'Прекарот што го избравте е зафатен или не претставува важечки прекар.
Изберете друг.',
	'facebook-choosenick' => 'Името на Вашиот профил на Facebook ($1)',
	'facebook-choosefirst' => 'Вашето име ($1)',
	'facebook-choosefull' => 'Вашето име и презиме ($1)',
	'facebook-chooseauto' => 'Автоматски-создадено име ($1)',
	'facebook-choosemanual' => 'Име по ваш избор:',
	'facebook-chooseexisting' => 'Постоечка сметка на ова вики',
	'facebook-chooseusername' => 'Корисничко име:',
	'facebook-choosepassword' => 'Лозинка:',
	'facebook-updateuserinfo' => 'Поднови ги следниве лични податоци:',
	'facebook-error-creating-user' => 'Грешка при создавањето на корисникот во локалната база на податоци.',
	'facebook-error-user-creation-hook-aborted' => 'создавањето на сметката беше прекинато од кука (додаток), со пораката: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Профил на Facebook',
	'facebook-prefsheader' => "Контролирање кои настани ќе истакнат некоја ставка на Вашето емитување на новости на Facebook: <a id='facebookPushEventBar_show' href='#'>прикажи нагодувања</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>сокриј нагодувања</a>",
	'facebook-prefs-can-be-updated' => 'Овие можете да ги подновите во секое време во јазичето „$1“ во Вашата страница за нагодувања.',
);

/** Mongolian (Монгол)
 * @author Chinneeb
 */
$messages['mn'] = array(
	'facebook-language' => 'Хэл',
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'group-fb-groupie-member' => 'Куронь ломань',
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'facebook' => 'Verbinden met Facebook',
	'facebook-desc' => 'Stelt gebruikers in staat een [[Special:Connect|verbinding te maken]] met hun [http://www.facebook.com Facebookgebruiker].
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
	'facebook-connect' => 'Aanmelden via Facebook Connect',
	'facebook-convert' => 'Deze gebruiker met Facebook verbinden',
	'facebook-title' => 'Gebruiker verbinden met Facebook',
	'facebook-merge' => 'Voeg uw wikigebruiker samen met uw Facebookgebruiker',
	'facebook-error' => 'Controlefout',
	'facebook-errortext' => 'Er is een fout opgetreden tijdens de verificatie via Facebook Connect.',
	'facebook-cancel' => 'Handeling geannuleerd',
	'facebook-canceltext' => 'De vorige handeling is geannuleerd door de gebruiker.',
	'facebook-invalid' => 'Ongeldige optie',
	'facebook-invalidtext' => 'De gemaakte selectie op de vorige pagina is ongeldig.',
	'facebook-success' => 'Aangemeld via Facebook',
	'facebook-successtext' => 'U bent aangemeld via Facebook Connect.',
	'facebook-nickname' => 'Gebruikersnaam',
	'facebook-fullname' => 'Volledige naam',
	'facebook-email' => 'E-mailadres',
	'facebook-language' => 'Taal',
	'facebook-timecorrection' => 'Tijdzonecorrectie (uren)',
	'facebook-chooselegend' => 'Gebruikersnaamkeuze',
	'facebook-chooseinstructions' => 'Alle gebruikers hebben een gebruikersnaam nodig. U kunt er een kiezen uit de onderstaande mogelijkheden.',
	'facebook-invalidname' => 'De gebruikersnaam van uw keuze is al in gebruik of ongeldig.
Kies een andere.',
	'facebook-choosenick' => 'Uw profielnaam bij Facebook ($1)',
	'facebook-choosefirst' => 'Uw voornaam ($1)',
	'facebook-choosefull' => 'Uw volledig naam ($1)',
	'facebook-chooseauto' => 'Een automatisch aangemaakte naam ($1)',
	'facebook-choosemanual' => 'Een voorkeursnaam:',
	'facebook-chooseexisting' => 'Een bestaande gebruiker op deze wiki',
	'facebook-chooseusername' => 'Gebruikersnaam:',
	'facebook-choosepassword' => 'Wachtwoord:',
	'facebook-updateuserinfo' => 'De volgende persoonlijke informatie bijwerken:',
	'facebook-error-creating-user' => 'Er is een fout opgetreden tijdens het aanmaken van de gebruiker in de lokale database.',
	'facebook-error-user-creation-hook-aborted' => 'Een uitbreiding heeft het aanmaken van de gebruiker beëindigd met het volgende bericht: $1',
	'facebook-prefstext' => 'Verbinden met Facebook',
	'facebook-link-to-profile' => 'Facebookprofiel',
	'facebook-prefsheader' => "Bepalen welke handelingen worden toegevoegd aan uw nieuwsfeed in Facebook. <a id='facebookPushEventBar_show' href='#'>Voorkeuren weergeven</a><a id='facebookPushEventBar_hide' href='#' style='display:none'>Voorkeuren verbergen</a>.",
	'facebook-prefs-can-be-updated' => 'U kunt deze te allen tijde bijwerken door naar het tabblad "$1" in uw voorkeuren te gaan.',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Nghtwlkr
 */
$messages['nn'] = array(
	'facebook-language' => 'Språk',
	'facebook-choosefirst' => 'Førenamnet ditt ($1)',
	'facebook-choosefull' => 'Det fulle namnet ditt ($1)',
	'facebook-chooseusername' => 'Brukarnamn:',
	'facebook-choosepassword' => 'Passord:',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Gjør det mulig for brukere å [[Special:Connect|koble til]] med sine [http://www.facebook.com Facebook]-kontoer.
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
	'facebook-connect' => 'Logg inn med Facebook Connect',
	'facebook-convert' => 'Koble til denne kontoen med Facebook',
	'facebook-title' => 'Koble til konto med Facebook',
	'facebook-merge' => 'Slå sammen wikikontoen din med din Facebook-ID',
	'facebook-usernameprefix' => 'FacebookBruker',
	'facebook-error' => 'Bekreftelsesfeil',
	'facebook-errortext' => 'En feil oppstod under bekreftelse med Facebook Connect.',
	'facebook-cancel' => 'Handling avbrutt',
	'facebook-canceltext' => 'Den forrige handlingen ble avbrutt av brukeren.',
	'facebook-invalid' => 'Ugyldig valg',
	'facebook-invalidtext' => 'Valget gjort på den forrige siden var ugyldig.',
	'facebook-success' => 'Facebookbekreftelsen var vellykket',
	'facebook-successtext' => 'Du har blitt logget inn med Facebook Connect.',
	'facebook-nickname' => 'Kallenavn',
	'facebook-fullname' => 'Fullt navn',
	'facebook-email' => 'E-postadresse',
	'facebook-language' => 'Språk',
	'facebook-timecorrection' => 'Tidssonekorreksjon (timer)',
	'facebook-chooselegend' => 'Brukernavnvalg',
	'facebook-chooseinstructions' => 'Alle brukere trenger et kallenavn; du kan velge et fra alternativene under.',
	'facebook-invalidname' => 'Kallenavnet du valgte er allerede tatt eller er ikke et gyldig kallenavn.
Velg et annet.',
	'facebook-choosenick' => 'Ditt Facebook-profilnavn ($1)',
	'facebook-choosefirst' => 'Ditt fornavn ($1)',
	'facebook-choosefull' => 'Ditt fulle navn ($1)',
	'facebook-chooseauto' => 'Et automatisk generert navn ($1)',
	'facebook-choosemanual' => 'Et valgfritt navn:',
	'facebook-chooseexisting' => 'En eksisterende konto på denne wikien',
	'facebook-chooseusername' => 'Brukernavn:',
	'facebook-choosepassword' => 'Passord:',
	'facebook-updateuserinfo' => 'Oppdater følgende personlige informasjon:',
	'facebook-error-creating-user' => 'Feil ved opprettelse av brukeren i den lokale databasen.',
	'facebook-error-user-creation-hook-aborted' => 'En krok (utvidelse) avbrøt kontoopprettelsen med meldingen: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook-profil',
	'facebook-prefsheader' => "For å kontrollere hvilke hendelser som vil dytte et element til Facebooks nyhetsstrøm, <a id='facebookPushEventBar_show' href='#'>vis innstillinger</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>skjul innstillinger</a>",
	'facebook-prefs-can-be-updated' => 'Du kan oppdatere disse når som helst ved å gå til «$1»-fanen på innstillingssiden din.',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Pozwala użytkownikom na [[Special:Connect|połączenie]] ze swoim [http://www.facebook.com kontem na Facebooku].
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
	'facebook-connect' => 'Zaloguj przy pomocy Facebook Connect',
	'facebook-convert' => 'Połącz to konto z Facebookiem',
	'facebook-title' => 'Połącz konto z Facebookiem',
	'facebook-merge' => 'Połącz swoje konto wiki ze swoim identyfikatorem w Facebooku.',
	'facebook-error' => 'Błąd weryfikacji',
	'facebook-errortext' => 'Wystąpił błąd podczas weryfikacji przez Facebook Connect.',
	'facebook-cancel' => 'Akcja anulowana',
	'facebook-canceltext' => 'Poprzednia akcja została anulowana przez użytkownika.',
	'facebook-invalid' => 'Nieprawidłowa opcja',
	'facebook-invalidtext' => 'Wybór wykonany na poprzedniej stronie był nieprawidłowy.',
	'facebook-success' => 'Facebook zweryfikował',
	'facebook-successtext' => 'Zostałeś zalogowany poprzez Facebook Connect.',
	'facebook-nickname' => 'Nazwa użytkownika',
	'facebook-fullname' => 'Imię i nazwisko',
	'facebook-email' => 'Adres e‐mail',
	'facebook-language' => 'Język',
	'facebook-timecorrection' => 'Strefa czasowa (liczba godzin)',
	'facebook-chooselegend' => 'Wybór nazwy użytkownika',
	'facebook-chooseinstructions' => 'Każdy musi mieć przypisaną nazwę użytkownika. Możesz wybrać jedną z poniższych.',
	'facebook-invalidname' => 'Nazwa użytkownika, którą wybrałeś jest już wykorzystywana lub jest nieprawidłowa.
Wybierz inną nazwę użytkownika.',
	'facebook-choosenick' => 'Nazwa Twojego profilu na Facebooku ($1)',
	'facebook-choosefirst' => 'Twoje imię ($1)',
	'facebook-choosefull' => 'Imię i nazwisko ($1)',
	'facebook-chooseauto' => 'Automatycznie wygenerowana nazwa ($1)',
	'facebook-choosemanual' => 'Nazwa do wyboru:',
	'facebook-chooseexisting' => 'Istniejące konto na tej wiki',
	'facebook-chooseusername' => 'Nazwa użytkownika',
	'facebook-choosepassword' => 'Hasło',
	'facebook-updateuserinfo' => 'Aktualizacja następujących danych o użytkowniku',
	'facebook-error-creating-user' => 'Wystąpił błąd podczas tworzenia konta użytkownika w lokalnej bazie danych.',
	'facebook-error-user-creation-hook-aborted' => 'Hak (rozszerzenie) przerwał tworzenie konta z komunikatem – $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil na Facebooku',
	'facebook-prefsheader' => "Kontrola, które zdarzenia spowodują dodanie nowej aktualności do Facebooka – <a id='facebookPushEventBar_show' href='#'>pokaż preferencje</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>ukryj preferencje</a>",
	'facebook-prefs-can-be-updated' => 'Możesz aktualizować informacje w dowolnym momencie odwiedzając zakładkę „$1” na stronie preferencji.',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'facebook' => 'Conession Facebook',
	'facebook-desc' => "A abìlita j'utent a [[Special:Connect|intré]] ant ij sò cont [http://www.facebook.com Facebook].
A eufr n'autenticassion basà an sle partìe Facebook e l'utilisassion ëd FBML ant ël test wiki",
	'group-fb-user' => 'Utent ëd Facebook Connect',
	'group-fb-user-member' => 'Utent ëd Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Utent ëd Facebook Connect',
	'group-fb-groupie' => 'Mèmber ëd la partìa',
	'group-fb-groupie-member' => 'Mèmber ëd la partìa',
	'grouppage-fb-groupie' => '{{ns:project}}:Mèmber ëd la partìa',
	'group-fb-officer' => 'Ufissiaj dla partìa',
	'group-fb-officer-member' => 'Ufissiaj dla partìa',
	'grouppage-fb-officer' => '{{ns:project}}:Ufissiaj dla partìa',
	'group-fb-admin' => 'Aministrator ëd la partìa',
	'group-fb-admin-member' => 'Aministrator ëd la partìa',
	'grouppage-fb-admin' => '{{ns:project}}:Aministrator ëd la partìa',
	'facebook-connect' => 'Intré an Facebook Connect',
	'facebook-convert' => 'Colega sto cont con Facebook',
	'facebook-title' => 'Coleghé un cont con Facebook',
	'facebook-merge' => 'Mës-cé sò cont wiki con sò identificativ Facebook',
	'facebook-error' => 'Eror ëd verìfica',
	'facebook-errortext' => "A l'é capitaje n'eror durant la verìfica con Facebook Connect.",
	'facebook-cancel' => 'Assion anulà',
	'facebook-canceltext' => "L'assion ëd prima a l'é stàita anulà da l'utent.",
	'facebook-invalid' => 'Opsion pa bon-a.',
	'facebook-invalidtext' => "La selession fàita an sla pàgina ëd prima a l'era pa bon-a.",
	'facebook-success' => "La verìfica ëd Facebook a l'é andàita bin",
	'facebook-successtext' => "A l'é intrà ant ël sistema për da bin con Facebook Connect.",
	'facebook-nickname' => 'Stranòm',
	'facebook-fullname' => 'Nòm complet',
	'facebook-email' => 'Adrëssa ëd pòsta eletrònica',
	'facebook-language' => 'Lenga',
	'facebook-timecorrection' => 'Coression dël fus orari (ore)',
	'facebook-chooselegend' => "Sernia ëd lë stranòm d'utent",
	'facebook-chooseinstructions' => "Tùit j'utent a l'han dabzògn ëd në stranòm,
a peul sern-ne un da j'opsion sì-sota.",
	'facebook-invalidname' => "Lë stranòm ch'a l'ha sernù a l'é già pijà o a l'é pa në stranòm bon.
Për piasì ch'a na serna n'àutr.",
	'facebook-choosenick' => 'Tò nòm ëd profil Facebook ($1)',
	'facebook-choosefirst' => 'Tò nòm ($1)',
	'facebook-choosefull' => 'Tò nòm complet ($1)',
	'facebook-chooseauto' => 'Un nòm generà da sol ($1)',
	'facebook-choosemanual' => "Në stranòm ch'a veul chiel:",
	'facebook-chooseexisting' => 'Un cont esistent an sta wiki-sì',
	'facebook-chooseusername' => "Stranòm d'utent:",
	'facebook-choosepassword' => 'Ciav:',
	'facebook-updateuserinfo' => "Modìfica j'anformassion përsonaj ch'a ven-o:",
	'facebook-error-creating-user' => "Eror ant la creassion ëd l'utent ant la base ëd dàit local.",
	'facebook-error-user-creation-hook-aborted' => "Un gancio (estension) a l'ha fàit abortì la creassion dël cont con ël mëssagi:$1",
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Profil ëd Facebook',
	'facebook-prefsheader' => "Për controlé che event a mandran n'element a sò fluss ëd neuve ëd Facebook, <a id='facebookPushEventBar_show' href='#'>smon-e ij sò gust</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>stërmé ij sò gust</a>",
	'facebook-prefs-can-be-updated' => 'A peul modifiché sòn quand ch\'a veul an visitand la tichëtta "$1" ëd la pàgina dij sò gust.',
);

/** Portuguese (Português)
 * @author Giro720
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Permite que os utilizadores se [[Special:Connect|autentiquem]] com as suas contas do [http://www.facebook.com Facebook]. Oferece autenticação baseada nos grupos do Facebook e o uso de FBML no texto wiki',
	'group-fb-user' => 'Utilizadores do Facebook Connect',
	'group-fb-user-member' => 'Utilizador do Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Utilizadores do Facebook Connect',
	'group-fb-groupie' => 'Membros de grupos',
	'group-fb-groupie-member' => 'Membro de grupos',
	'grouppage-fb-groupie' => '{{ns:project}}:Membros de grupos',
	'group-fb-officer' => 'Oficiais de grupos',
	'group-fb-officer-member' => 'Oficial de grupos',
	'grouppage-fb-officer' => '{{ns:project}}:Oficiais de grupos',
	'group-fb-admin' => 'Administradores de grupos',
	'group-fb-admin-member' => 'Administrador de grupos',
	'grouppage-fb-admin' => '{{ns:project}}:Administradores de grupos',
	'facebook-connect' => 'Autenticação com o Facebook Connect',
	'facebook-convert' => 'Ligar esta conta ao Facebook',
	'facebook-title' => 'Ligar conta ao Facebook',
	'facebook-merge' => 'Fundir a conta wiki com o seu ID no Facebook',
	'facebook-error' => 'Erro de verificação',
	'facebook-errortext' => 'Ocorreu um erro durante a verificação com o Facebook Connect.',
	'facebook-cancel' => 'Operação cancelada',
	'facebook-canceltext' => 'A operação anterior foi cencelada pelo utilizador.',
	'facebook-invalid' => 'Opção inválida',
	'facebook-invalidtext' => 'A escolha feita na página anterior era inválida.',
	'facebook-success' => 'A verificação Facebook ocorreu com sucesso',
	'facebook-successtext' => 'Foi autenticado com o Facebook Connect.',
	'facebook-nickname' => 'Nick',
	'facebook-fullname' => 'Nome completo',
	'facebook-email' => 'Correio electrónico',
	'facebook-language' => 'Língua',
	'facebook-timecorrection' => 'Correcção do fuso horário (horas)',
	'facebook-chooselegend' => 'Escolha do nome de utilizador',
	'facebook-chooseinstructions' => 'Todos os utilizadores precisam de um nick; pode escolher uma das opções abaixo.',
	'facebook-invalidname' => 'O nick que escolheu já foi usado ou não é válido.
Escolha um diferente, por favor.',
	'facebook-choosenick' => 'O nome do seu perfil no Facebook ($1)',
	'facebook-choosefirst' => 'O seu primeiro nome ($1)',
	'facebook-choosefull' => 'O seu nome completo ($1)',
	'facebook-chooseauto' => 'Um nome gerado automaticamente ($1)',
	'facebook-choosemanual' => 'Um nome à sua escolha:',
	'facebook-chooseexisting' => 'Uma conta existente nesta wiki',
	'facebook-chooseusername' => 'Nome de utilizador:',
	'facebook-choosepassword' => 'Palavra-chave:',
	'facebook-updateuserinfo' => 'Actualize as seguintes informações pessoais:',
	'facebook-error-creating-user' => 'Ocorreu um erro ao criar o utilizador na base de dados local.',
	'facebook-error-user-creation-hook-aborted' => "Um ''hook'' (extensão) abortou a criação da conta, com a mensagem: $1",
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Perfil no Facebook',
	'facebook-prefsheader' => "Para controlar que operações geram uma entrada no feed de notícias do Facebook, <a id='facebookPushEventBar_show' href='#'>mostrar preferências</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>esconder preferências</a>",
	'facebook-prefs-can-be-updated' => 'Pode actualizar estes elementos a qualquer altura, no separador "$1" das suas preferências.',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Giro720
 * @author Luckas Blade
 */
$messages['pt-br'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Permite que os usuários se [[Special:Connect|autentiquem]] com as suas contas do [http://www.facebook.com Facebook]. Oferece autenticação baseada nos grupos do Facebook e o uso de FBML no texto wiki',
	'group-fb-user' => 'Usuários do Facebook Connect',
	'group-fb-user-member' => 'Usuários do Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Usuários do Facebook Connect',
	'group-fb-groupie' => 'Membros de grupos',
	'group-fb-groupie-member' => 'Membro de grupos',
	'grouppage-fb-groupie' => '{{ns:project}}:Membros de grupos',
	'group-fb-officer' => 'Responsáveis de grupos',
	'group-fb-officer-member' => 'Responsável de grupos',
	'grouppage-fb-officer' => '{{ns:project}}:Responsáveis de grupos',
	'group-fb-admin' => 'Administradores de grupos',
	'group-fb-admin-member' => 'Administrador de grupos',
	'grouppage-fb-admin' => '{{ns:project}}:Administradores de grupos',
	'facebook-connect' => 'Autenticação com o Facebook Connect',
	'facebook-convert' => 'Ligar esta conta ao Facebook',
	'facebook-title' => 'Ligar conta ao Facebook',
	'facebook-merge' => 'Fundir a conta wiki com o seu ID no Facebook',
	'facebook-error' => 'Erro de verificação',
	'facebook-errortext' => 'Ocorreu um erro durante a verificação com o Facebook Connect.',
	'facebook-cancel' => 'Operação cancelada',
	'facebook-canceltext' => 'A operação anterior foi cencelada pelo usuário.',
	'facebook-invalid' => 'Opção inválida',
	'facebook-invalidtext' => 'A escolha feita na página anterior era inválida.',
	'facebook-success' => 'A verificação Facebook ocorreu com sucesso',
	'facebook-successtext' => 'Você foi autenticado com o Facebook Connect.',
	'facebook-nickname' => 'Apelido',
	'facebook-fullname' => 'Nome completo',
	'facebook-email' => 'Endereço de e-mail',
	'facebook-language' => 'Língua',
	'facebook-timecorrection' => 'Correção do fuso horário (horas)',
	'facebook-chooselegend' => 'Escolha do nome de usuário',
	'facebook-chooseinstructions' => 'Todos os usuários precisam de um apelido; você pode escolher uma das opções abaixo.',
	'facebook-invalidname' => 'O apelido que você escolheu já foi usado ou não é válido.
Escolha um diferente, por favor.',
	'facebook-choosenick' => 'O nome do seu perfil no Facebook ($1)',
	'facebook-choosefirst' => 'Seu primeiro nome ($1)',
	'facebook-choosefull' => 'Seu nome completo ($1)',
	'facebook-chooseauto' => 'Um nome gerado automaticamente ($1)',
	'facebook-choosemanual' => 'Um nome de sua escolha:',
	'facebook-chooseexisting' => 'Uma conta existente nesta wiki',
	'facebook-chooseusername' => 'Nome de usuário:',
	'facebook-choosepassword' => 'Senha:',
	'facebook-updateuserinfo' => 'Atualize as seguintes informações pessoais:',
	'facebook-error-creating-user' => 'Ocorreu um erro ao criar o usuário na base de dados local.',
	'facebook-error-user-creation-hook-aborted' => "Um ''hook'' (extensão) abortou a criação da conta, com a mensagem: $1",
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Perfil no Facebook',
	'facebook-prefsheader' => "Para controlar que operações geram uma entrada no feed de notícias do Facebook, <a id='facebookPushEventBar_show' href='#'>mostrar preferências</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>esconder preferências</a>",
	'facebook-prefs-can-be-updated' => 'Você pode atualizar estes elementos quando quiser através da aba "$1" das suas preferências.',
);

/** Romanian (Română)
 * @author Minisarm
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'facebook' => 'Conectare Facebook',
	'group-fb-user' => 'Utilizatori Facebook Connect',
	'group-fb-user-member' => 'Utilizator Facebook Connect',
	'group-fb-groupie' => 'Membrii grup',
	'group-fb-admin' => 'Administratori de grup',
	'group-fb-admin-member' => 'Administrator grup',
	'facebook-connect' => 'Conectare cu Facebook Connect',
	'facebook-click-to-login' => 'Apasă pentru a vă autentifica pe acest site prin Facebook',
	'facebook-click-to-connect-existing' => 'Apăsaţi pentru a vă conecta contul dvs. Facebook la $1',
	'facebook-comm' => 'Comunicaţie',
	'facebook-error' => 'Eroare la verificare',
	'facebook-cancel' => 'Acţiune anulată',
	'facebook-invalid' => 'Opţiune invalidă',
	'facebook-nickname' => 'Pseudonim',
	'facebook-fullname' => 'Numele complet',
	'facebook-email' => 'Adresa de e-mail',
	'facebook-language' => 'Limba',
	'facebook-chooselegend' => 'Alegerea utilizatorului',
	'facebook-choosefirst' => 'Prenumele tău ($1)',
	'facebook-choosefull' => 'Numele tău complet ($1)',
	'facebook-chooseauto' => 'Un nume generat automat ($1)',
	'facebook-choosemanual' => 'Un nume la alegere:',
	'facebook-chooseexisting' => 'Un cont existent pe acest wiki',
	'facebook-chooseusername' => 'Utilizator:',
	'facebook-choosepassword' => 'Parolă:',
	'facebook-updateuserinfo' => 'Actualiează următoarele informaţii personale:',
	'facebook-error-creating-user' => 'Eroare la crearea utilizatorului în baza de date locală.',
	'facebook-link-to-profile' => 'Profil Facebook',
);

/** Russian (Русский)
 * @author Eleferen
 * @author MaxSem
 * @author Sergey kudryavtsev
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'facebook' => 'Подключение Facebook',
	'facebook-desc' => 'Позволяет участникам [[Special:Connect|подключаться]] с помощью своих учётных записей на [http://www.facebook.com Facebook]. 
Предлагает аутентификацию на основе групп Facebook и использование FBML в викитексте.',
	'group-fb-user' => 'Участники, подключенные через Facebook',
	'group-fb-user-member' => 'Участник, подключенный через Facebook',
	'grouppage-fb-user' => '{{ns:project}}:Участники, подключенные через Facebook',
	'group-fb-groupie' => 'Члены группы',
	'group-fb-groupie-member' => 'Член группы',
	'grouppage-fb-groupie' => '{{ns:project}}:Члены группы',
	'group-fb-officer' => 'Сотрудники группы',
	'group-fb-officer-member' => 'Сотрудник группы',
	'grouppage-fb-officer' => '{{ns:project}}:Сотрудники группы',
	'group-fb-admin' => 'Группа администраторов',
	'group-fb-admin-member' => 'Администратор группы',
	'grouppage-fb-admin' => '{{ns:project}}:Администраторы группы',
	'facebook-connect' => 'Войти с помощью Facebook Connect',
	'facebook-convert' => 'Подключить эту учётную запись к Facebook',
	'facebook-title' => 'Подключение учётной записи к Facebook',
	'facebook-cancel' => 'Действие отменено',
	'facebook-invalid' => 'Неверный параметр',
	'facebook-success' => 'Проверка через Facebook закончилась успешно',
	'facebook-nickname' => 'Псевдоним',
	'facebook-fullname' => 'Полное имя',
	'facebook-email' => 'Адрес электронной почты',
	'facebook-language' => 'Язык',
	'facebook-timecorrection' => 'Часовой пояс (в часах)',
	'facebook-chooselegend' => 'Выбор имени пользователя',
	'facebook-chooseinstructions' => 'У каждого участника должен быть псевдоним. Вы можете выбрать один из представленных ниже.',
	'facebook-choosenick' => 'Имя вашего профиля в Facebook ($1)',
	'facebook-choosefirst' => 'Ваше имя ($1)',
	'facebook-choosefull' => 'Ваше полное имя ($1)',
	'facebook-chooseauto' => 'Автоматически созданное имя ($1)',
	'facebook-choosemanual' => 'Имя на ваш выбор:',
	'facebook-chooseexisting' => 'Существующая учётная запись в этой вики',
	'facebook-chooseusername' => 'Имя участника:',
	'facebook-choosepassword' => 'Пароль:',
	'facebook-updateuserinfo' => 'Обновите следующие личные сведения:',
	'facebook-error-creating-user' => 'Ошибка при создании пользователя в локальной базе данных.',
	'facebook-prefstext' => 'Facebook Connect',
);

/** Rusyn (Русиньскый)
 * @author Gazeb
 */
$messages['rue'] = array(
	'facebook-fullname' => 'Повне мено',
	'facebook-email' => 'Адреса електронічной пошты',
	'facebook-language' => 'Язык',
);

/** Swedish (Svenska)
 * @author Intima
 * @author MaxSem
 */
$messages['sv'] = array(
	'facebook' => 'Facebook Connect',
	'facebook-desc' => 'Möjliggör för användare att [[Special:Connect|ansluta]] med sitt [http://www.facebook.com Facebook] konto. 
 Erbjudanden autentisering baserad på Facebook grupper och användningen av FBML i wiki text',
	'group-fb-user' => 'Facebook Connect-användare',
	'group-fb-user-member' => 'Facebook Connect-användare',
	'grouppage-fb-user' => '{{ns:project}}:Facebook Connect användare',
	'group-fb-groupie' => 'Gruppmedlemmar',
	'group-fb-groupie-member' => 'Gruppmedlemmar',
	'grouppage-fb-groupie' => '{{ns:project}}:Gruppmedlemmar',
	'group-fb-officer' => 'Grupp officerare',
	'group-fb-officer-member' => 'Grupp officerare',
	'grouppage-fb-officer' => '{{ns:project}}:Grupp officerare',
	'group-fb-admin' => 'Grupp Administratörer',
	'group-fb-admin-member' => 'Grupp administratör',
	'grouppage-fb-admin' => '{{ns:project}}:Grupp administratörer',
	'facebook-connect' => 'Logga in med Facebook Connect',
	'facebook-convert' => 'Anslut detta konto med Facebook',
	'facebook-title' => 'Anslut kontot med Facebook',
	'facebook-merge' => 'Koppla din wiki konto med ditt Facebook-ID',
	'facebook-error' => 'Verifieringsfel',
	'facebook-errortext' => 'Ett fel uppstod under kontroll med Facebook Connect.',
	'facebook-cancel' => 'Åtgärden avbröts',
	'facebook-canceltext' => 'Den tidigare åtgärden avbröts av användaren.',
	'facebook-invalid' => 'Ogiltigt alternativ',
	'facebook-invalidtext' => 'De val du gjort på föregående sida var ogiltig',
	'facebook-success' => 'Facebook verifieringen lyckades',
	'facebook-successtext' => 'Du har varit framgångsrikt inloggad med Facebook Connect.',
	'facebook-nickname' => 'Smeknamn',
	'facebook-fullname' => 'Fullständigt namn',
	'facebook-email' => 'E-postadress',
	'facebook-language' => 'Språk',
	'facebook-timecorrection' => 'Tidszon korrigering (timmar)',
	'facebook-chooselegend' => 'Välj användarnamn',
	'facebook-chooseinstructions' => 'Alla användare behöver ett smeknamn, du kan välja ett av alternativen nedan.',
	'facebook-invalidname' => 'Smeknamnet du valde är redan upptaget eller inte ett giltigt smeknamn. 
 Vänligen välj ett annat.',
	'facebook-choosenick' => 'Ditt Facebook-profil namn ($1)',
	'facebook-choosefirst' => 'Ditt förnamn ($1)',
	'facebook-choosefull' => 'Ditt fullständiga namn ($1)',
	'facebook-chooseauto' => 'Ett automatiskt genererat namn ($1)',
	'facebook-choosemanual' => 'Ett valfritt namn:',
	'facebook-chooseexisting' => 'Ett befintligt konto på denna wiki',
	'facebook-chooseusername' => 'Användarnamn:',
	'facebook-choosepassword' => 'Lösenord:',
	'facebook-updateuserinfo' => 'Uppdatera följande personliga uppgifter:',
	'facebook-error-creating-user' => 'Fel vid skapandet av användaren i den lokala databasen.',
	'facebook-error-user-creation-hook-aborted' => 'En krok (förlängning) aborterade att skapa konto med meddelandet: $1',
	'facebook-prefstext' => 'Facebook Connect',
	'facebook-link-to-profile' => 'Facebook-profil',
	'facebook-prefsheader' => "För att styra vilka händelser som kommer att pressa ett objekt till ditt nyhetsflöde på Facebook, <a id='facebookPushEventBar_show' href='#'>visa preferenser</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>dölja inställningar</a>",
	'facebook-prefs-can-be-updated' => ' Du kan uppdatera dessa när som helst genom att gå till "$1"-fliken i dina inställningar.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'group-fb-groupie' => 'గుంపు సభ్యులు',
	'group-fb-groupie-member' => 'గుంపు సభ్యుడు',
	'grouppage-fb-groupie' => '{{ns:project}}:గుంపు సభ్యులు',
	'facebook-fullname' => 'పూర్తిపేరు',
	'facebook-email' => 'ఈ-మెయిల్ చిరునామా',
	'facebook-language' => 'భాష',
	'facebook-timecorrection' => 'కాల మండలపు సర్దుబాటు (గంటలు)',
	'facebook-chooseexisting' => 'ఈ వికీలో ఇప్పటికే ఉన్న ఖాతా',
	'facebook-chooseusername' => 'వాడుకరిపేరు:',
	'facebook-choosepassword' => 'సంకేతపదం:',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'facebook' => 'Ugnay sa Facebook',
	'facebook-desc' => 'Nagpapahintulot sa mga tagagamit na [[Special:Connect|Umugnay]] sa kanilang mga akawnt sa [http://www.facebook.com Facebook].
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
	'facebook-connect' => 'Lumagdang may Ugnay sa Facebook',
	'facebook-convert' => 'Iugnay ang akawnt na ito sa Facebook',
	'facebook-title' => 'Iugnay ang akawnt sa Facebook',
	'facebook-merge' => 'Isanib ang iyong akawnt na pangwiki sa iyong ID na pang-Facebook',
	'facebook-usernameprefix' => 'Tagagamit ng Facebook',
	'facebook-error' => 'Kamalian sa pagpapatunay',
	'facebook-errortext' => 'Naganap ang isang kamalian habang nagpapatunay sa pamamagitan ng Ugnay sa Facebook.',
	'facebook-cancel' => 'Hindi itinuloy ang galaw',
	'facebook-canceltext' => 'Ang nakaraang kilos ay hindi itinuloy ng tagagamit.',
	'facebook-invalid' => 'Hindi tanggap na opsyon',
	'facebook-invalidtext' => 'Ang ginawang pagpili sa nakaraang pahina ay hindi tanggap.',
	'facebook-success' => 'Nagtagumpay ang pagpapatibay ng Facebook',
	'facebook-successtext' => 'Matagumpay kang nailagdang papasok sa pamamagitan ng Ugnay sa Facebook.',
	'facebook-nickname' => 'Palayaw',
	'facebook-fullname' => 'Buong pangalan',
	'facebook-email' => 'Tirahan ng e-liham',
	'facebook-language' => 'Wika',
	'facebook-timecorrection' => 'Pagtatama sa sona ng oras (mga oras)',
	'facebook-chooselegend' => 'Pagpili ng pangalan ng tagagamit',
	'facebook-chooseinstructions' => 'Ang lahat ng mga tagagamit ay nangangailangan ng palayaw; maaari kang pumili mula sa mga mapagpipiliang nasa ibaba.',
	'facebook-invalidname' => 'May nakakuha na ng napiling mong palayaw o hindi isang tanggap na palayaw.
Mangyaring pumili ng isang naiiba.',
	'facebook-choosenick' => 'Ang iyong pangalan ng balangkas sa Facebook ($1)',
	'facebook-choosefirst' => 'Ang unang pangalan mo ($1)',
	'facebook-choosefull' => 'Ang buong pangalan mo ($1)',
	'facebook-chooseauto' => 'Isang kusang nalikhang pangalan ($1)',
	'facebook-choosemanual' => 'Isang pangalang napili mo:',
	'facebook-chooseexisting' => 'Isang umiiral na akawnt sa wiking ito',
	'facebook-chooseusername' => 'Pangalan ng tagagamit:',
	'facebook-choosepassword' => 'Hudyat:',
	'facebook-updateuserinfo' => 'Isapanahon ang sumusunod na kabatirang pangsarili:',
	'facebook-error-creating-user' => 'Kamalian sa paglikha ng tagagamit sa katutubong kalipunan ng dato.',
	'facebook-error-user-creation-hook-aborted' => 'Isang kawit (dugtong) ang pumigil sa paglikha ng akawnt na may mensaheng: $1',
	'facebook-prefstext' => 'Ugnay sa Facebook',
	'facebook-link-to-profile' => 'Balangkas sa Facebook',
	'facebook-prefsheader' => "Upang matabanan ang kung aling mga kaganapan ang tutulak sa isang bagay papunta sa iyong pakain ng balita sa Facebook, <a id='facebookPushEventBar_show' href='#'>ipakita ang mga nais</a> <a id='facebookPushEventBar_hide' href='#' style='display:none'>itago ang mga nais</a>",
	'facebook-prefs-can-be-updated' => 'Maisasapanahon mo ang mga ito anumang oras sa pamamagitan ng pagdalaw sa panglaylay na "$1" ng iyong pahina ng mga nais.',
);

/** Ukrainian (Українська)
 * @author Тест
 */
$messages['uk'] = array(
	'facebook-email' => 'Адреса електронної пошти',
	'facebook-language' => 'Мова',
	'facebook-choosepassword' => 'Пароль:',
);
