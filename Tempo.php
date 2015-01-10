<?php
/*
 *
 * @file
 * @ingroup Skins
 * @author Lojjik Braughler
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 * To install place the Tempo folder (the folder containing this file!) into
 * skins/ and add this line to your wiki's LocalSettings.php:
 * require_once("$IP/skins/Tempo/Tempo.php");
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

// Skin credits that will show up on Special:Version
$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'Tempo',
	'version' => '1.0',
	'author' => array( 'Lojjik Braughler' ),
	'description' => 'A delightfully simple skin',
);

$wgValidSkinNames['tempo'] = 'Tempo';

$wgAutoloadClasses['SkinTempo'] = __DIR__ . '/Tempo.skin.php';
$wgMessagesDirs['SkinTempo'] = __DIR__ . '/i18n';

// Main CSS ResourceLoader module
$wgResourceModules['skins.tempo'] = array(
	'styles' => array(
		'skins/Tempo/resources/tempo.css' => array( 'media' => 'screen' ),
	),
	'position' => 'top'
);

// Main JS module for this skin
$wgResourceModules['skins.tempo.js'] = array(
	'scripts' => array(
		'skins/Tempo/resources/js/tempo.js',
	),
	'dependencies' => array(
		'jquery.client'
	)
);

$wgHooks['BeforePageDisplay'][] = function( OutputPage &$out, &$skin ) {

	if ( get_class( $skin ) !== 'SkinTempo' ) {
		return true;
	}

	$out->addMeta( 'http:content-type', 'text/html; charset=UTF-8' );

	return true;
};