<?php

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

	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->loadSidebarSections();
	}

	public function setupSkinUserCss( OutputPage $out ) {

		parent::setupSkinUserCss( $out );
		$baseModules = array( 'skins.tempo' );

		$out->addModuleStyles( $baseModules );
		$out->addModuleScripts( $baseModules );

	}

	public function loadSidebarSections() {
		wfRunHooks( 'LoadSidebarSectionsBefore' );
		$this->addDefaultSidebarItems();
		wfRunHooks( 'LoadSidebarSectionsAfter' );
	}


	public function getNotifications() {
		return '<li><p>' . $this->msg( 'tempo-nonotifications' ) . '</p></li>';
	}

	public function getHeadNavigation() {

		$menu = array();
		$linkAttributes = array();

		if ( $this->getUser()->getNewtalk() ) {
			$linkAttributes = array( 'class' => 'notif' );
		}

		if ( $this->getUser()->isAnon() ) {
			$personalMenu = Linker::linkKnown( SpecialPage::getTitleFor( 'UserLogin'), $this->msg( 'pt-login' )->plain() );
		} else {
			$personalMenu = Linker::linkKnown( $this->getUser()->getUserPage(), $this->msg( 'tempo-myprofile' ) ) .
							Html::openElement( 'ul' ) . $this->getPersonalToolsList() . Html::closeElement( 'ul' );
		}

		$menu[] = $personalMenu;

		$notifications = new NotificationsMenuTemplate( $this->getConfig() );
		$notifications->set( 'user', $this->getUser() );
		$notifications->set( 'newtalk', $this->getUser()->getNewtalk() );

		$menu[] = $notifications->getHTML();

		$userlinks = Html::openElement( 'ul' );
		foreach ( $menu as $menuItem ) {
			$userlinks .= Html::openElement( 'li' ) . $menuItem . Html::closeElement( 'li' );
		}

		return $userlinks;

	}

	public function getTabs( $side = 'left' ) {
		$content_navigation = $this->buildContentNavigationUrls();

		$namespaces = $content_navigation['namespaces'];
		$actions = $content_navigation['actions'];
		$views = $content_navigation['views'];

		unset($actions['watch']);
		unset($actions['unwatch']);
		unset($views['view']);

		if ( $side === 'left' ) {
			return array_merge( $namespaces, $views );
		} else {
			return $actions;
		}
	}

	public function addDefaultSidebarItems() {

	}
	public function addSidebarItem($title, $editable = false, $editlink = '', $content) {
		array_push( $this->mSidebarSections, 
						array(
							'title' => $title,
							'content' => $content 
						)
				);
	}

	public function getSearchForm() {
		$searchTitle = SpecialPage::getTitleFor( 'Search' );
        $top_search = '
                        <form id="bubble_search" name="search_site" action="' . $searchTitle->getFullURL() . '" method="get">
                                <input type="text" id="searchInput" class="search_box" name="search" x-webkit-speech />
                                <input type="submit" id="searchButton" class="search_button mw-ui-button" value="' . $this->msg( 'searchbutton' ) . '"/>
                        </form>';

        return $top_search;
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

	public function getNameForUser() {
		$user = $this->getUser();

		if ( $user->isAnon() ) {
			return $this->msg( 'tempo-guest' );
		} 
		
		$realName = $user->getRealName();

		if ( !empty( $realName ) ) {
				return $realName;
		}

		return $user->getName();
	}
	
}

class TempoTemplate extends BaseTemplate {

	public function execute() {
		$skin = $this->getSkin();

?>
<!DOCTYPE html>
	<?php echo $skin->getOutput()->headElement( $skin ); ?>
	<body>
		<div id="container">
			<div id="top">
				<div id="topnav">
					<div id="logo"><img src="<?php $this->text( 'logopath' ) ?>" width="66" alt="<?php $this->text( 'sitename' ) ?>"/></div>
					<div id="topSearch"><?php echo $skin->getSearchForm() ?></div>
					<div class="userlinks-wrapper">
						<div id="hello"><p><?php echo $skin->msg( 'tempo-hello', $skin->getNameForUser() )->plain() ?></p></div>
						<div id="userlinks"><?php echo $skin->getHeadNavigation() ?></div>
					</div>
				</div>
			</div>

				<div id="rail">
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
					<?php
						if ( $this->data['sitenotice'] ) {
					?>
							<div id="sitenotice"><?php echo $this->html( 'sitenotice' ) ?></div>
					<?php
						}
					?>
					<div id="navtabs"><ul class="tabsleft">
											<?php foreach( $skin->getTabs( 'left' ) as $key => $item ) {
														echo $this->makeListItem( $key, $item );
												}
											?>

										</ul>
									   	<ul class="tabsright">
									   		<?php foreach( $skin->getTabs( 'right' ) as $key => $item ) {
									   					echo $this->makeListItem( $key, $item );
									   			}
									   		?>
									   	</ul>
					</div>
					<div id="content">
						<h1><?php $this->html('title') ?></h1>
						<article>
							<?php $this->html( 'bodytext' ) ?>
						</article>
					</div>
				</div>
				<div id="bottom">
					<footer>
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
						<ul>
							<?php
								foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) { ?>
									<li>
										<?php
												foreach ( $footerIcons as $icon ) {
													echo $this->getSkin()->makeFooterIcon( $icon );
												}
										?>
									</li>
							<?php
								} ?>
						</ul>
					</footer>
				</div>
			</div>
		</div>
	</body>
</html>
<?php

	}
}


