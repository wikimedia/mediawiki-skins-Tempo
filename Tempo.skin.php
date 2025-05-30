<?php

use MediaWiki\Html\Html;
use MediaWiki\Linker\Linker;

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 *
 * @ingroup Skins
 */
class SkinTempo extends SkinTemplate {

	public $skinname = 'tempo', $stylename = 'tempo',
		$template = 'TempoTemplate';

	public $mSidebarSections = [];

	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$jsModules = [ 'skins.tempo.js' ];
		$out->addModules( $jsModules );
	}

	public function getHeadNavigation() {
		$menu = [];
		$linkAttributes = [];

		$services = MediaWiki\MediaWikiServices::getInstance();
		$linkRenderer = $services->getLinkRenderer();
		$userHasNewMessages = $services->getTalkPageNotificationManager()->userHasNewMessages( $this->getUser() );

		if ( $userHasNewMessages ) {
			$linkAttributes = [ 'class' => 'notif' ];
		}

		if ( $this->getUser()->isAnon() ) {
			$personalMenu = $linkRenderer->makeKnownLink(
				SpecialPage::getTitleFor( 'Userlogin' ),
				$this->msg( 'pt-login' )->text()
			);
		} else {
			// $personalTools = $this->getPersonalToolsList();
			$personalTools = $this->getStructuredPersonalTools();
			// If we have Echo icons, remove them from the "My profile" menu
			if ( isset( $personalTools['notifications-alert'] ) && $personalTools['notifications-alert'] ) {
				unset( $personalTools['notifications-alert'] );
			}
			if ( isset( $personalTools['notifications-notice'] ) && $personalTools['notifications-notice'] ) {
				unset( $personalTools['notifications-notice'] );
			}
			$personalToolsHTML = $this->makePersonalToolsList( $personalTools );
			$personalMenu = $linkRenderer->makeKnownLink( $this->getUser()->getUserPage(), $this->msg( 'tempo-myprofile' )->text() ) .
							Html::openElement( 'ul' ) . $personalToolsHTML . Html::closeElement( 'ul' );
		}

		$menu[] = $personalMenu;

		$notifications = new NotificationsMenuTemplate( $this->getConfig() );
		$notifications->set( 'skin', $this->getSkin() );
		$notifications->set( 'user', $this->getUser() );
		$notifications->set( 'newtalk', $userHasNewMessages );

		$menu[] = $notifications->getHTML();

		$userlinks = Html::openElement( 'ul' );
		foreach ( $menu as $menuItem ) {
			$userlinks .= Html::openElement( 'li' ) . $menuItem . Html::closeElement( 'li' );
		}

		return $userlinks;
	}

	public function getSearchForm() {
		$searchTitle = SpecialPage::getTitleFor( 'Search' );
		$top_search = Html::openElement( 'form', [
			'name' => 'search_site',
			'action' => $searchTitle->getFullURL(),
			'method' => 'get',
			'role' => 'search' ] ) .
				Html::label( $this->msg( 'search' )->text(), 'searchInput' ) .
				Html::openElement( 'input', [ 'type' => 'text', 'id' => 'searchInput', 'class' => 'search_box', 'name' => 'search' ] ) .
				Html::openElement( 'input', [ 'type' => 'submit', 'id' => 'searchButton', 'value' => $this->msg( 'searchbutton' )->text() ] ) .
			Html::closeElement( 'form' );

		return $top_search;
	}

	public function getSidebarItems() {
		$sidebar_html = '';

		foreach ( $this->mSidebarSections as $sidebarItem ) {
			$sidebar_html .= Html::openElement( 'section' );
			$sidebar_html .= Html::openElement( 'div', [ 'class' => 'top' ] );
			$sidebar_html .= Html::openElement( 'h3' ) . $sidebarItem['title'] . Html::closeElement( 'h3' );
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

	public function getTabs( $side = 'left' ) {
		$content_navigation = $this->get( 'content_navigation' );

		$namespaces = $content_navigation['namespaces'];
		$actions = $content_navigation['actions'];
		$views = $content_navigation['views'];

		unset( $actions['watch'] );
		unset( $actions['unwatch'] );
		unset( $views['view'] );

		// left side
		if ( $side === 'left' ) {
			return array_merge( $namespaces, $views );
		}

		// right side
		return $actions;
	}

	public function execute() {
		$skin = $this->getSkin();
?>
		<div id="container">
			<div id="top" class="noprint">
				<div id="topnav">
					<div id="logo" role="banner"><img src="<?php $this->text( 'logopath' ) ?>" width="66" alt="<?php $this->text( 'sitename' ) ?>"/></div>
					<div id="topSearch"><?php echo $skin->getSearchForm() ?></div>
					<div class="userlinks-wrapper">
						<div id="hello"><?php echo $skin->msg( 'tempo-hello', $skin->getNameForUser() )->parseAsBlock() ?></div>
						<div id="userlinks"><?php echo $skin->getHeadNavigation() ?></div>
					</div>
				</div>
			</div>

				<div id="rail" class="noprint">
					<div id="sidebar" role="navigation">
						<?php
							foreach ( $this->getSidebar() as $boxName => $box ) { ?>
										<section id="<?php echo htmlspecialchars( Sanitizer::escapeIdForAttribute( $box['id'] ), ENT_QUOTES ) ?>"<?php echo Linker::tooltip( $box['id'] ) ?>>
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
					<div id="navtabs" class="noprint" role="navigation">
						<ul class="tabsleft">
						<?php
							foreach ( $this->getTabs( 'left' ) as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
						?>
						</ul>
						<ul class="tabsright">
						<?php
							foreach ( $this->getTabs( 'right' ) as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
						?>
						</ul>
					</div>
					<div id="content" role="main">
						<?php echo $this->getIndicators(); ?>
						<h1><?php $this->html( 'title' ) ?></h1>
						<article>
							<?php $this->html( 'bodytext' ) ?>
						</article>
					</div>
				</div>
				<div id="bottom" class="noprint" role="contentinfo" lang="<?php echo $this->get( 'userlang' ) ?>" dir="<?php echo $this->get( 'dir' ) ?>">
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
								foreach ( $this->get( 'footericons' ) as $blockName => &$footerIcons ) { ?>
									<li>
										<?php
										foreach ( $footerIcons as $footerIconKey => $icon ) {
											if ( !isset( $icon['src'] ) ) {
												unset( $footerIcons[$footerIconKey] );
											}
											echo $skin->makeFooterIcon( $icon );
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
<?php
	}
}
