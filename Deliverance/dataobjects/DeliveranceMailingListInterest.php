<?php

/**
 * @package   Delieverance
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DeliveranceMailingListInterest extends SwatDBDataObject
{


	/**
	 * Unique identifier
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $shortname;

	/**
	 * @var string
	 */
	public $group_shortname;

	/**
	 * User visible title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Order of display
	 *
	 * @var integer
	 */
	public $displayorder;

	/**
	 * @var boolean
	 */
	public $visible;

	/**
	 * Whether or not new subscribers should be added by default
	 *
	 * @var boolean
	 */
	public $is_default;




	protected function init()
	{
		parent::init();

		$this->table = 'MailingListInterest';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'instance',
			SwatDBClassMap::get('SiteInstance')
		);
	}


}

?>
