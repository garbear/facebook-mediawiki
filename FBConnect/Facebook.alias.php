<?php
/**
 * Aliases for special pages.
 *
 * @file
 * @ingroup Extensions
 */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'Connect' => array( 'Connect', 'ConnectAccount' ),
);

/** Spanish (Español) */
$specialPageAliases['es'] = array(
	'Connect' => array( 'Conectar', 'ConectarCuenta' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'Connect' => array( '接続' ),
);

/** Malayalam (മലയാളം) */
$specialPageAliases['ml'] = array(
	'Connect' => array( 'ബന്ധിപ്പിക്കുക', 'അംഗത്വംബന്ധിപ്പിക്കുക' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'Connect' => array( 'Verbinden', 'GebruikerVerbinden' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;
