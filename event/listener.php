<?php
/**
*
* @package phpBB Extension - LMDI Balisage
* @copyright (c) 2016-2021 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\index\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{

	protected $db;
	protected $config;
	protected $language;
	protected $template;
	protected $helper;
	protected $glossary_table;

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\template\template $template,
		\phpbb\language\language $language
		)
	{
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->language = $language;
	}


	static public function getSubscribedEvents ()
	{
		return array(
		'core.user_setup_after'			=> 'load_language_on_setup',
		'core.page_header'				=> 'build_url',
		);
	}


	public function load_language_on_setup()
	{
		$this->language->add_lang('index', 'lmdi/index');
	}


	public function build_url ()
	{
		$this->template->assign_vars(array(
			'U_BALISAGE'	=> $this->helper->route('lmdi_index_controller', array('mode' => 'aiguillage')),
			'L_BALISAGE'	=> $this->language->lang('LBALISAGE'),
			'T_BALISAGE'	=> $this->language->lang('TBALISAGE'),
		));
	}

}
