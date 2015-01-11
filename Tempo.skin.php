<?php
/**
 * BlueSky skin
 *
 * @file
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 *
 * @ingroup Skins
 */
class SkinTempo extends SkinTemplate {

	public $skinname = 'tempo', $stylename = 'tempo',
		$template = 'TempoTemplate', $useHeadElement = true;

	public $mSidebarSections = array();

	/**
	 * Basically just loads the skin's JavaScript via ResourceLoader.
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->loadSidebarSections();
	}

	/**
	 * Load the skin's CSS via ResourceLoader.
	 *
	 * @param OutputPage $out
	 */
	public function setupSkinUserCss( OutputPage $out ) {

		parent::setupSkinUserCss( $out );
		$baseModules = array( 'skins.tempo' );
		$out->addModuleStyles( $baseModules );

	}

	public function loadSidebarSections() {
		wfRunHooks( 'LoadSidebarSectionsBefore' );
		$this->addPersonalLinksBox();
		wfRunHooks( 'LoadSidebarSectionsAfter' );
	}

	public function addPersonalLinksBox() {
		// Wrap it in UL so it formats properly
		$personalTools = Html::openElement( 'ul' ) . $this->getPersonalToolsList() . Html::closeElement( 'ul' );

		if ( $this->getUser()->isAnon() ) {
			$heading = 'Join Us!';
		} else {
			$heading = 'My Links';
		}
		$this->addSidebarItem( $heading, false, '', $personalTools );
	}
	public function getHeadElement() {
		return $this->getOutput()->buildCssLinks();
	}

	public function getNavmenu() {
		return array( 'home' => array( 'text' => 'Home', 'href' => Skin::makeMainPageUrl() ) );
	}

	public function getTabs() {
		$content_navigation = $this->buildContentNavigationUrls();

		$namespaces = $content_navigation['namespaces'];
		$actions = $content_navigation['actions'];
		$views = $content_navigation['views'];

		unset($actions['watch']);
		unset($actions['unwatch']);

		if ( $this->getUser()->isAnon() ) {
			unset($views['history']);
		}

		unset($views['view']);
		return array_merge( $namespaces, $views, $actions );
	}

	public function addSidebarItem($title, $editable = false, $editlink = '', $content) {
		array_push( $this->mSidebarSections, 
						array(
							'title' => $title,
							'content' => $content 
						)
				);
	}

	public function getSidebarItems() {
		$sidebar_html = '';

		foreach( $this->mSidebarSections as $sidebarItem ) {
			$sidebar_html .= Html::openElement( 'section' );
			$sidebar_html .= Html::openElement( 'div', array( 'class' => 'top' ) );
			$sidebar_html .= Html::openElement( 'h3' ) . $sidebarItem['title'] . Html::closeElement('h3');
			$sidebar_html .= Html::closeElement( 'div' );
			$sidebar_html .= $sidebarItem['content'];
			$sidebar_html .= Html::closeElement( 'section' );

		}
		
		return $sidebar_html;
	}
	
}

class TempoTemplate extends BaseTemplate {

	public function makeList($items) {
		$html = "";
		foreach($items as $key => $value){
			$html .= $this->makeListItem($key,$value);
		}
		return $html;
	}	

	public function execute() {
		$skin = $this->getSkin();

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php $this->html( 'title' ) ?></title>
		<?= $skin->getHeadElement() ?>
	</head>
	<body>
		<div id="container">
			<div id="rail">
				<div id="logo"><img src="<?php $this->text( 'logopath' ) ?>" alt="<?php $this->text( 'sitename' ) ?>"/></div>
				<div id="sidebar">
					<?php
						foreach ( $this->getSidebar() as $boxName => $box ) { ?>
									<section id="<?php echo Sanitizer::escapeId( $box['id'] ) ?>"<?php echo Linker::tooltip( $box['id'] ) ?>>
									<div class="top"><h3><?php echo htmlspecialchars( $box['header'] ); ?></h3></div>

									<?php if ( is_array( $box['content'] ) ) { ?>
										<ul>
											<?php
													foreach ( $box['content'] as $key => $item ) {
														echo $this->makeListItem( $key, $item );
													}
											?>
												</ul>
											<?php
												} else {
													echo $box['content'];
												}

										?> 
									</section> 
									<?php
											} 
					?>
				</div>
			</div>
			<div id="main">
				<div id="userlinks"><ul><?= $skin->getPersonalToolsList() ?></ul></div>
				<div id="navtabs"><ul>
										<?php foreach( $skin->getTabs() as $key => $item ) {
													echo $this->makeListItem( $key, $item );
											}
										?>
								</ul>
				</div>
				<div id="content">
					<div id="header"><h1><?php $this->html('title') ?></h1></div>
					<article>
						<?php $this->html( 'bodytext' ) ?>
					</article>
				</div>
			</div>
			<div id="bottom">
				<div id="footer">
					<?php
						foreach ( $this->getFooterLinks() as $category => $links ) { ?>
									<ul>
										<?php
											foreach ( $links as $key ) { ?>
												<li><?php $this->html( $key ) ?></li>
										<?php
											} 
										?>
									</ul>
					<?php
								} 
					?>
				</div>
			</div>
		</div>
	</body>
</html>
<?php

	}
}


