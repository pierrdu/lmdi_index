<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace lmdi\index\core;

class balises
{
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\extension\manager "Extension Manager" */
	protected $ext_manager;
	/** @var \phpbb\path_helper */
	protected $path_helper;
	/** @var \lmdi\gloss\core\helper */
	protected $gloss_helper;
	// Strings
	protected $phpEx;
	protected $phpbb_root_path;
	protected $table_rh_tt;
	protected $table_rh_t;
	protected $ext_path;
	protected $ext_path_web;

	/**
	* Constructor
	*
	*/
	public function __construct(
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\auth\auth $auth,
		\phpbb\extension\manager $ext_manager,
		\phpbb\path_helper $path_helper,
		$phpEx,
		$phpbb_root_path,
		$table_rh_tt,
		$table_rh_t)
	{
		$this->template 		= $template;
		$this->user 			= $user;
		$this->db 			= $db;
		$this->config 			= $config;
		$this->helper			= $helper;
		$this->auth			= $auth;
		$this->ext_manager	 	= $ext_manager;
		$this->path_helper	 	= $path_helper;
		$this->phpEx 			= $phpEx;
		$this->phpbb_root_path 	= $phpbb_root_path;
		$this->table_rh_tt		= $table_rh_tt;
		$this->table_rh_t		= $table_rh_t;

		$this->ext_path = $this->ext_manager->get_extension_path('lmdi/index', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}

	public $u_action;

	function main($fam)
	{
		$fichier = $pict = $this->ext_path . "data/" . $fam . '.data';
		$titre = $this->user->lang['TBALISAGE'];
		$data = $this->lecture ($fichier);
		$abc_links = "Dans la table ci-dessous, cliquez sur les liens pour afficher la liste des sujets possédant la balise sélectionnée.";

		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'index.html',
		));
		$this->template->assign_vars(array(
			'TITLE'		=> $titre,
			'ABC'		=> $abc_links,
			'CORPS'		=> $data,
		));

		page_footer();
	}

	private function lecture ($fichier)
	{
		$f = fopen ($fichier, "r");
		if ($f)
		{
			$data = fread ($f, filesize($fichier));
			fclose ($f);
			return $data;
		}
		return false;
	}

}
