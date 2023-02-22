<?php

// If Echo extension is installed, we use its messages
// If not installed, we only use it for new talk page notifications

use MediaWiki\MediaWikiServices;

class NotificationsMenuTemplate extends BaseTemplate {

	private $menuItems;
	private $output;
	const MAX_NOTES = 5;

	private function isEchoInstalled() {
		return ExtensionRegistry::getInstance()->isLoaded( 'Echo' );
	}

	private function addEchoNotifications() {
		$user = $this->data['user'];

		// [[phab:T301381]] avoid fataling with Echo installed.
		if ( method_exists( 'EchoAttributeManager', 'newFromGlobalVars' ) ) {
			$attributeManager = EchoAttributeManager::newFromGlobalVars();
		} else {
			// @see https://github.com/wikimedia/mediawiki-extensions-Echo/commit/596729d852ae5fe8c6b3f43c582982f15ac349f3
			$attributeManager = EchoServices::getInstance()->getAttributeManager();
		}

		$eventTypes = $attributeManager->getUserEnabledEvents( $user, 'web' );
		$mapper = new EchoNotificationMapper();
		$notifications = $mapper->fetchByUser( $user, self::MAX_NOTES, 0, $eventTypes );
		$output = '';

		if ( $notifications ) {
			$language = $this->data['skin']->getLanguage();

			foreach ( $notifications as $note ) {
				$formatted = EchoDataOutputFormatter::formatOutput( $note, 'html', $user, $language );
				$output .= $formatted['*'];
			}
		} else {
			// Show an informative message letting the user know there's nothing to see here.
			// Otherwise we'd render an empty menu with no contents if the user
			// has no notifications, and that'd be just silly.
			$output .= $this->data['skin']->msg( 'tempo-no-notifications' )->escaped();
		}

		$this->output .= $output;
	}

	public function hasNotifications() {
		if ( $this->isEchoInstalled() ) {
			if ( $this->data['user']->isRegistered() ) {
				$notifUser = MWEchoNotifUser::newFromUser( $this->data['user'] );
				return $notifUser->getNotificationCount() >= 1;
			} else {
				$this->output = $this->data['skin']->msg( 'tempo-messages-login' )->parse();
			}
		}

		return $this->data['newtalk'];
	}

	public function addRibbon() {
		$ribbonClass = 'ribbon';

		if ( $this->data['newtalk'] ) {
			$ribbonMessage = $this->data['skin']->msg( 'tempo-ribbon-newmessages' )->text();
			$ribbonClass .= ' unread';
		} else {
			$ribbonMessage = $this->data['skin']->msg( 'tempo-ribbon-nonewmessages' )->text();
			$ribbonClass .= ' read';
		}

		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();

		$this->output .= Html::openElement( 'li' ) .
						$linkRenderer->makeLink(
							$this->data['user']->getTalkPage(),
							$ribbonMessage,
							[ 'class' => $ribbonClass ]
						) .
					Html::closeElement( 'li' );
	}

	public function execute() {
		if ( $this->isEchoInstalled() ) {
			$this->addEchoNotifications();
		} else {
			$this->addRibbon();
		}

		if ( $this->hasNotifications() ) {
			$containerClass = '';
			$linkClass = 'notif';
		} else {
			$containerClass = 'no-notifications';
			$linkClass = '';
		}

		if ( $this->isEchoInstalled() ) {
			$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
			echo $linkRenderer->makeLink(
				SpecialPage::getTitleFor( 'Notifications' ),
				$this->data['skin']->msg( 'tempo-notifications' )->text(),
				[ 'class' => $linkClass ]
			);
		} else {
			echo Html::rawElement( 'a', [ 'href' => '#', 'class' => $linkClass ], $this->data['skin']->msg( 'tempo-notifications' )->escaped() );
		}

		echo Html::openElement( 'ul', [
			'class' => [ ( !$this->data['user']->isRegistered() ? 'anonymous-user' : '' ), $containerClass ]
		] );

		echo $this->output;

		echo Html::closeElement( 'ul' );
	}

}
