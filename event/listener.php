<?php
/**
*
* @package phpBB Extension - LMDI Balisage
* @copyright (c) 2016 LMDI - Pierre Duhem
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
	/** @var \phpbb\cache\service */
	protected $cache;
	/* @var \phpbb\user */
	protected $user;
	/* @var \phpbb\db\driver\driver_interface */
	protected $db;
	/* @var \phpbb\template\template */
	protected $template;
	/* @var \phpbb\config\config */
	protected $config;
	/* @var \phpbb\controller\helper */
	protected $helper;
	protected $glossary_table;

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\template\template $template,
		\phpbb\cache\service $cache,
		\phpbb\user $user,
		$glossary_table
		)
	{
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->cache = $cache;
		$this->user = $user;
		$this->glossary_table = $glossary_table;
	}

	static public function getSubscribedEvents ()
	{
		return array(
		'core.user_setup'				=> 'load_language_on_setup',
		'core.page_header'				=> 'build_url',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'lmdi/index',
			'lang_set' => 'index',
			);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function build_url ($event)
	{
		if (version_compare ($this->config['version'], '3.2.x', '<'))
		{
			$s320 = 0;
		}
		else
		{
			$s320 = 1;
		}
		$this->template->assign_vars(array(
			'U_BALISAGE'	=> $this->helper->route('lmdi_index_controller', array('mode' => 'aiguillage')),
			'L_BALISAGE'	=> $this->user->lang['LBALISAGE'],
			'T_BALISAGE'	=> $this->user->lang['TBALISAGE'],
			'S_320'		=> $s320,
		));
	}

}
