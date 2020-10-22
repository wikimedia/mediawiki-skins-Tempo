<?php

// If Echo extension is installed, we use its messages
// If not installed, we only use it for new talk page notifications

class NotificationsMenuTemplate extends BaseTemplate {

	private $menuItems;
	private $output;
	const MAX_NOTES = 5;

	private function isEchoInstalled() {
		return class_exists( 'ApiEchoNotifications' ) && class_exists( 'MWEchoNotifUser' );
	}

	private function addEchoNotifications() {
		$user = $this->data['user'];
		$attributeManager = EchoAttributeManager::newFromGlobalVars();
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
		}

		$this->output .= $output;
	}

	public function hasNotifications() {
		if ( $this->isEchoInstalled() ) {
			if ( $this->data['user']->isLoggedIn() ) {
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
			$ribbonMessage = $this->data['skin']->msg( 'tempo-ribbon-newmessages' )->escaped();
			$ribbonClass .= ' unread';
		} else {
			$ribbonMessage = $this->data['skin']->msg( 'tempo-ribbon-nonewmessages' )->escaped();
			$ribbonClass .= ' read';
		}

		$this->output .= Html::openElement( 'li' ) .
						Linker::link( $this->data['user']->getTalkPage(), $ribbonMessage, [ 'class' => $ribbonClass ] );
					Html::closeElement( 'li' );
	}

	public function execute() {
		if ( $this->isEchoInstalled() ) {
			$this->addEchoNotifications();
		} else {
			$this->addRibbon();
		}

?>
<?php

	if ( $this->hasNotifications() ) {
		$linkClass = 'notif';
	} else {
		$linkClass = '';
	}

	if ( $this->isEchoInstalled() ) {
		echo Linker::link( SpecialPage::getTitleFor( 'Notifications' ), $this->data['skin']->msg( 'tempo-notifications' )->escaped(), [ 'class' => $linkClass ] );
	} else {
		echo Html::rawElement( 'a', [ 'href' => '#', 'class' => $linkClass ], $this->data['skin']->msg( 'tempo-notifications' )->escaped() );
	}
?>

<ul<?php if ( !$this->data['user']->isLoggedIn() ) {?> class="anonymous-user"<?php } ?>>
	<?php
		echo $this->output;
	?>
</ul>
<?php
	}

}
