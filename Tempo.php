<?php

/**
 * Tempo skin by Lojjik Braughler
 *
 * To the extent possible under law, the person who associated CC0 with
 * Tempo has waived all copyright and related or neighboring rights
 * to Tempo.
 *
 * You should have received a copy of the CC0 legalcode along with this
 * work.  If not, see <http://creativecommons.org/publicdomain/zero/1.0/>
 *
 * @file
 * @ingroup Skins
 * @author Lojjik Braughler
 *
 * To install place the Tempo folder (the folder containing this file!) into
 * skins/ and add this line to your wiki's LocalSettings.php:
 * require_once("$IP/skins/Tempo/Tempo.php");
 *
 */


if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

// Skin credits that will show up on Special:Version
$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'Tempo',
	'version' => '1.5',
	'author' => array( 'Lojjik Braughler' ),
	'descriptionmsg' => 'tempo-desc',
	'license-name' => 'CC0 1.0'
);

$wgValidSkinNames['tempo'] = 'Tempo';

$wgAutoloadClasses['SkinTempo'] = __DIR__ . '/Tempo.skin.php';
$wgMessagesDirs['SkinTempo'] = __DIR__ . '/i18n';

// Main CSS ResourceLoader module
$wgResourceModules['skins.tempo'] = array(
	'styles' => array(
		'skins/Tempo/resources/tempo.css' => array( 'media' => 'screen' ),
		'skins/Tempo/resources/printable.css' => array( 'media' => 'print' )
	),
	'position' => 'top'
);

// Main JS module for this skin
$wgResourceModules['skins.tempo.js'] = array(
	'scripts' => array(
		'skins/Tempo/resources/js/tempo.js',
	),
	'dependencies' => array(
		'jquery.client',
		'jquery.ui'
	)
);

$wgHooks['BeforePageDisplay'][] = function( OutputPage &$out, &$skin ) {

	if ( get_class( $skin ) !== 'SkinTempo' ) {
		return true;
	}

	$out->addMeta( 'http:content-type', 'text/html; charset=UTF-8' );

	return true;
};