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
 * require_once "$IP/skins/Tempo/Tempo.php";
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

// Skin credits that will show up on Special:Version
$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'Tempo',
	'version' => '1.6rc1',
	'author' => array( 'Lojjik Braughler' ),
	'descriptionmsg' => 'tempo-desc',
	'license-name' => 'CC0 1.0'
);

$wgValidSkinNames['tempo'] = 'Tempo';

$wgAutoloadClasses['SkinTempo'] = __DIR__ . '/Tempo.skin.php';
$wgAutoloadClasses['NotificationsMenuTemplate'] = __DIR__ . '/templates/NotificationsMenu.tmpl.php';
$wgMessagesDirs['SkinTempo'] = __DIR__ . '/i18n';

// Main CSS ResourceLoader module
$wgResourceModules['skins.tempo'] = array(
	'styles' => array(
		'skins/Tempo/resources/tempo.css' => array( 'media' => 'screen' ),
		'skins/Tempo/resources/normalize.css' => array( 'media' => 'screen' )
	),
	'position' => 'top'
);

$wgResourceModules['skins.tempo.js'] = array(
	'scripts' => array(
		'skins/Tempo/resources/js/tempo.js'
	)
);