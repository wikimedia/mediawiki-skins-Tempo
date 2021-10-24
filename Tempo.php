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

if ( function_exists( 'wfLoadSkin' ) ) {
	wfLoadSkin( 'Tempo' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Tempo'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for Tempo skin. Please use wfLoadSkin instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the Tempo skin requires MediaWiki 1.25+' );
}
