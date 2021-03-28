<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2019 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* Code commandant l'affichage d'une famille
*
*/
namespace lmdi\index\core;

class balises
{
	protected $template;
	protected $language;
	protected $db;
	protected $auth;
	protected $ext_manager;
	protected $path_helper;
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
		\phpbb\language\language $language,
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
		$this->language 		= $language;
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
		// Les fichiers sont produits par le script balfamiilles.php.
		$fichier = $this->phpbb_root_path . "/store/lmdi/index/" . $fam . '.data';
		$titre = $this->language->lang('TBALISAGE');
		$data = $this->lecture ($fichier);
		$abc_links = "Dans la table ci-dessous, cliquez sur les liens pour afficher la liste des sujets possédant la balise sélectionnée.";
		if (!$data)
		{
			$titre = "Pas de balises de rang générique ou spécifique pour la famille <a href=\"../app.php/tag/$fam\">$fam</a>.";
			$abc_links = "";
			$data = "";
		}

		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'index.html',
		));
		$this->template->assign_vars(array(
			'U_CANONICAL'	=> generate_board_url() . '/' . append_sid("app.{$this->phpEx}/index", "mode=display&amp;fam=$fam"),
			'TITLE'		=> $titre,
			'ABC'		=> $abc_links,
			'CORPS'		=> $data,
		));

		page_footer();
	}	// main


	private function lecture ($fichier)
	{
		if (file_exists ($fichier))
		{
			$f = fopen ($fichier, "r");
			if ($f)
			{
				$data = fread ($f, filesize($fichier));
				fclose ($f);
				return $data;
			}
			else
				return false;
		}
		else
			return false;
	}	// lecture

}
