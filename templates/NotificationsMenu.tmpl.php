<?php

// If Echo extension is installed, we use its messages
// If not installed, we only use it for new talk page notifications

class NotificationMenuTemplate extends BaseTemplate {

	private function echoInstalled() {
			return class_exists( 'ApiEchoNotifications' ) && class_exists( 'MWEchoNotifUser' );
	}

	public function getNotificationCount() {
		if ( $this->echoInstalled() ) {
			$notifUser = MWEchoNotifUser::newFromUser( $this->getUser() );
			return $notifUser->getNotificationCount( false );
		}

		if ( $this->getUser()->getNewtalk() ) {
			return 1;
		}

		return 0;
	}

	public function execute() {

	}

}


