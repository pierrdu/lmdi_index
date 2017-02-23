<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\index\controller;

class main
{
	protected $index;
	protected $baledit;
	protected $aiguillage;
	protected $balises;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\controller\helper */
	protected $helper;
	/** @var string phpBB root path */
	protected $phpbb_root_path;
	/** @var string phpEx */
	protected $phpEx;

	/**
	* Constructor
	*
	*/
	public function __construct(
		\lmdi\index\core\index $index,
		\lmdi\index\core\baledit $baledit,
		\lmdi\index\core\aiguillage $aiguillage,
		\lmdi\index\core\balises $balises,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\request\request $request,
		\phpbb\controller\helper $helper,
		$phpbb_root_path,
		$phpEx)
	{
		$this->index		 	= $index;
		$this->baledit		 	= $baledit;
		$this->aiguillage		= $aiguillage;
		$this->balises		 	= $balises;
		$this->template 		= $template;
		$this->user 			= $user;
		$this->request 		= $request;
		$this->helper 			= $helper;
		$this->phpbb_root_path 	= $phpbb_root_path;
		$this->phpEx 			= $phpEx;
	}


	public function handle_index()
	{
		include($this->phpbb_root_path . 'includes/functions_user.' . $this->phpEx);
		include($this->phpbb_root_path . 'includes/functions_module.' . $this->phpEx);
		include($this->phpbb_root_path . 'includes/functions_display.' . $this->phpEx);

		// Exclude Bots
		if ($this->user->data['is_bot'])
		{
			redirect(append_sid($this->phpbb_root_path . 'index.' . $this->phpEx));
		}

		// Variables
		$mode   = $this->request->variable('mode', '');
		$action = $this->request->variable('action', '');
		$code   = $this->request->variable('code', '-1');
		$cap    = $this->request->variable('cap', 'A');
		$fam    = $this->request->variable('fam', '');
		$id     = (int) $this->request->variable('id', 0);

		// String loading
		$this->user->add_lang_ext('lmdi/index', 'index');

		// Add the base entry into the breadcrump at top
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $this->helper->route('lmdi_index_controller'),
			'FORUM_NAME'	=> $this->user->lang['LBALISAGE'],
		));

		switch ($mode)
		{
			case 'ed':
			case 'save':
			case 'del':
			case 'destroy':
				$this->baledit->main ($id, $mode);
			break;
			case 'display' :
				$this->balises->main ($fam);
			break;
			case 'index':
				$this->index->main ($cap);
			break;
			default:
				$this->aiguillage->main ();
			break;
		}
	}
}
