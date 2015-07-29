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
		$notifications = ApiEchoNotifications::getNotifications( $this->data['user'], 'html', self::MAX_NOTES );
		$output = '';

		if ( $notifications ) {
			foreach ( $notifications as $note ) {
				$output .= $note['*'];
			}
		}

		$this->output .= $output;
	}

	public function hasNotifications() {
		if ( $this->isEchoInstalled() ) {
			$notifUser = MWEchoNotifUser::newFromUser( $this->data['user'] );
			return $notifUser->getNotificationCount( false ) >= 1;
		}

		return $this->data['newtalk'];
	}

	public function addRibbon() {
		$ribbonClass = 'ribbon';

		if ( $this->data['newtalk'] ) {
			$ribbonMessage = wfMessage( 'tempo-ribbon-newmessages' );
			$ribbonClass .= ' unread';
		} else {
			$ribbonMessage = wfMessage( 'tempo-ribbon-nonewmessages' );
			$ribbonClass .= ' read';
		}

		$this->output .= Html::openElement( 'li' ) .
						Linker::link( $this->data['user']->getTalkPage(), $ribbonMessage, array( 'class' => $ribbonClass ) );
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
		echo Linker::link( SpecialPage::getTitleFor( 'Notifications' ), wfMessage( 'tempo-notifications' )->plain(), array( 'class' => $linkClass ) );
	} else {
		echo Html::rawElement( 'a', array( 'href' => '#', 'class' => $linkClass ), wfMessage( 'tempo-notifications' )->plain() );
	}
?>

<ul>
	<?php
		echo $this->output;
	?>
</ul>
<?php
	}

}
