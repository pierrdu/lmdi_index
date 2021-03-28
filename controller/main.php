<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2021 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\index\controller;

class main
{
	protected $edition;
	protected $baledit;
	protected $aiguillage;
	protected $balises;
	protected $balremp;
	protected $ordre;
	protected $famille;
	protected $template;
	protected $user;
	protected $language;
	protected $request;
	protected $helper;
	protected $phpbb_root_path;
	protected $phpEx;

	/**
	* Constructor
	*
	*/
	public function __construct(
		\lmdi\index\core\edition $edition,
		\lmdi\index\core\baledit $baledit,
		\lmdi\index\core\aiguillage $aiguillage,
		\lmdi\index\core\balises $balises,
		\lmdi\index\core\balremp $balremp,
		\lmdi\index\core\ordre $ordre,
		\lmdi\index\core\famille $famille,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\controller\helper $helper,
		$phpbb_root_path,
		$phpEx)
	{
		$this->edition		 	= $edition;
		$this->baledit		 	= $baledit;
		$this->aiguillage		= $aiguillage;
		$this->balises		 	= $balises;
		$this->balremp			= $balremp;
		$this->ordre			= $ordre;
		$this->famille			= $famille;
		$this->template 		= $template;
		$this->user 			= $user;
		$this->language		= $language;
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
		
		// String loading
		$this->language->add_lang('index', 'lmdi/index');

		// Add the base entry into the breadcrump at top
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $this->helper->route('lmdi_index_controller'),
			'FORUM_NAME'	=> $this->language->lang('LBALISAGE'),
		));

		switch ($mode)
		{
			// Partie d'édition
			case 'ed':
			case 'save':
			case 'del':
			case 'destroy':
				$id = (int) $this->request->variable('id', 0);
				$this->baledit->main ($id, $mode);
				break;
			case 'edition':
				$cap = $this->request->variable('cap', 'A');
				$this->edition->main ($cap);
				break;
			case 'remp':
				$this->balremp->main ($mode);
				break;
			// Partie d'affichage
			case 'famille' :
				$fam = $this->request->variable('fam', '', true);
				$this->famille->main ($fam);
				break;
			case 'ordre':
				$ord = $this->request->variable('ord', '', true);
				$this->ordre->main ($ord);
				break;
			default:
				$this->aiguillage->main ();
				break;
		}
	}
}
