<?php

require_once 'Swat/SwatMessage.php';
require_once 'Site/pages/SiteEditPage.php';
require_once 'Deliverance/DeliveranceListFactory.php';

/**
 * @package   Deliverance
 * @copyright 2009-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class DeliveranceSignupPage extends SiteEditPage
{
	// {{{ protected properties

	/**
	 * @var boolean
	 */
	protected $send_welcome = true;

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Deliverance/pages/signup.xml';
	}

	// }}}

	// process phase
	// {{{ protected function save()

	protected function save(SwatForm $form)
	{
		$list = $this->getList();
		$this->subscribe($list);
	}

	// }}}
	// {{{ protected function getList()

	protected function getList()
	{
		return DeliveranceListFactory::get($this->app, 'default');
	}

	// }}}
	// {{{ protected function subscribe()

	protected function subscribe(DeliveranceList $list)
	{
		$default_info = $list->getDefaultSubscriberInfo();

		$email     = $this->getEmail();
		$info      = $this->getSubscriberInfo($list);
		$array_map = $this->getArrayMap();

		$this->checkMember($list, $email);

		$response = $list->subscribe($email, $info, $this->send_welcome,
			$array_map);

		$this->handleSubscribeResponse($list, $response);

		if ($response === DeliveranceList::SUCCESS ||
			$response === DeliveranceList::QUEUED) {
			$this->sendNotification($list);
		}
	}

	// }}}
	// {{{ protected function handleSubscribeResponse()

	protected function handleSubscribeResponse(DeliveranceList $list, $response)
	{
		$message = $list->handleSubscribeResponse($response);

		if ($message instanceof SwatMessage) {
			$this->ui->getWidget('message_display')->add($message);
		}
	}

	// }}}
	// {{{ protected function getEmail()

	protected function getEmail()
	{
		return $this->ui->getWidget('email')->value;
	}

	// }}}
	// {{{ abstract protected function getSubscriberInfo();

	abstract protected function getSubscriberInfo(DeliveranceList $list);

	// }}}
	// {{{ protected function getArrayMap()

	protected function getArrayMap()
	{
		return array();
	}

	// }}}
	// {{{ protected function checkMember()

	protected function checkMember(DeliveranceList $list, $email)
	{
		if ($list->isMember($email)) {
			$this->send_welcome = false;
			$message = $this->getExistingMemberMessage($list, $email);
			if ($message != null) {
				$this->addAppMessage($message);
			}
		}
	}

	// }}}
	// {{{ protected function getExistingMemberMessage()

	protected function getExistingMemberMessage(DeliveranceList $list, $email)
	{
		// TODO: rewrite.
		$message = new SwatMessage(
			Deliverance::_(
				'Thank you. Your email address was already subscribed to '.
				'our newsletter.'
			),
			'notice'
		);

		$message->secondary_content = Deliverance::_(
			'Your subscriber information has been updated, and you will '.
			'continue to receive mailings at this address.'
		);

		return $message;
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate(SwatForm $form)
	{
		if ($this->canRelocate($form)) {
			$this->app->relocate($this->source.'/thankyou');
		}
	}

	// }}}
	// {{{ protected function canRelocate()

	protected function canRelocate(SwatForm $form)
	{
		$relocate = true;

		if ($this->ui->hasWidget('message_display')) {
			$message_display = $this->ui->getWidget('message_display');
			$relocate = ($message_display->getMessageCount() == 0);
		}

		return $relocate;
	}

	// }}}
	// {{{ protected function addAppMessage()

	protected function addAppMessage(SwatMessage $message)
	{
		$this->app->messages->add($message);
	}

	// }}}
	// {{{ protected function sendNotification()

	protected function sendNotification(DeliveranceList $list)
	{
		if (isset($this->app->notifier)) {
			$interests = $list->getInterests()->getDefaultShortnames();

			$this->app->notifier->send(
				'newsletter_signup',
				array(
					'site'      => $this->app->config->notifier->site,
					'list'      => $list->getShortname(),
					'interests' => $interests,
				)
			);

		}
	}

	// }}}
}

?>
