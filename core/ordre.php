<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2021 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*	Module traitant les ordres (appelé depuis la page des classes et des
	ordres).
	*/

namespace lmdi\index\core;

class ordre
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
		$this->language		= $language;
		$this->db				= $db;
		$this->config			= $config;
		$this->helper			= $helper;
		$this->auth			= $auth;
		$this->ext_manager		= $ext_manager;
		$this->path_helper		= $path_helper;
		$this->phpEx			= $phpEx;
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->table_rh_tt		= $table_rh_tt;
		$this->table_rh_t		= $table_rh_t;

		$this->ext_path = $this->ext_manager->get_extension_path('lmdi/index', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}

	public $u_action;

	function main($ordre)
	{
		// Changement de base pour utiliser galerieins
		if ($this->config['server_name'] == "localhost")
		{
			$jeton = mysqli_connect ("localhost", "root", "pierred", "galerieins");
		}
		else
		{
			$jeton = mysqli_connect ("galerieins.mysql.db", "galerieins", "Pi47Duhem", "galerieins");
		}
		$sql = "SELECT insect_classe.nom as classe, insect_ordre.id_ordre as id_ordre 
		FROM insect_photo p 
		LEFT JOIN insect_classe ON (p.id_classe = insect_classe.id_classe) 
		LEFT JOIN insect_ordre ON (p.id_ordre = insect_ordre.id_ordre) 
		WHERE insect_ordre.nom = '$ordre' LIMIT 1";
		$resultat = mysqli_query ($jeton, $sql);
		$row = mysqli_fetch_row ($resultat);
		$classe = $row[0];
		$id_ordre = $row[1];
		mysqli_free_result ($resultat);

		$sql = "SELECT insect_famille.nom as famille 
		FROM insect_photo p 
		LEFT JOIN insect_ordre ON (p.id_ordre = insect_ordre.id_ordre) 
		LEFT JOIN insect_famille ON (p.id_famille = insect_famille.id_famille) 
		WHERE insect_ordre.id_ordre = $id_ordre 
		GROUP BY famille";
		$resultat = mysqli_query ($jeton, $sql);

		$params = "/index?mode=famille";
		$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		$url .= "&amp;fam=";
		$tabfam = array ();
		while ($row = mysqli_fetch_row ($resultat))
		{
			$famille = $row[0];
			// Exclusion de la famille 'Inconnu'
			if ($famille == 'Inconnu')
			{
				continue;
			}
			// Exclusion des pseudo-familles avec ~Schemas
			if (strpos ($famille, '~'))
			{
				continue;
			}
			// Exclusion des superfamilles
			if (strpos ($famille, 'dea'))
			{
				continue;
			}
			$tabfam[] = $row[0];
		}
		mysqli_free_result ($resultat);
		mysqli_close ($jeton);


		$nbf = count ($tabfam);		// Nb total des ordres
		$tr4 = floor ($nbf / 4);		// Nb de tranches de 4
		$reste = $nbf % 4;			// Reste sur la dernière ligne
		if ($reste)
		{
			$finl = 4 - $reste;
			for ($j = 0; $j < $finl; $j++)
			{
				$tabfam[] = '';
			}
			$tr4++;
		}
		reset ($tabfam);
		for ($j = 0; $j < $tr4; $j++)
		{
			$this->template->assign_block_vars('gfam', array(
				'FAM1'		=> $tabfam[($tr4 * 0)+ $j],
				'URL1'		=> $url . $tabfam[($tr4 * 0)+ $j],
				'FAM2'		=> $tabfam[($tr4 * 1)+ $j],
				'URL2'		=> $url . $tabfam[($tr4 * 1)+ $j],
				'FAM3'		=> $tabfam[($tr4 * 2)+ $j],
				'URL3'		=> $url . $tabfam[($tr4 * 2)+ $j],
				'FAM4'		=> $tabfam[($tr4 * 3)+ $j],
				'URL4'		=> $url . $tabfam[($tr4 * 3)+ $j],
				));

		}

		// Breadcrumbs
		$params = "mode=ordre";
		$str_ordre = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $str_ordre,
			'FORUM_NAME'	=> $this->language->lang('INDEX_PAGES_ORDRE'),
		));

		$titre = $this->language->lang('TBALISAGE');
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'ordre.html',
		));
		$this->template->assign_vars(array(
			'CLASSE'		=> $classe,
			'ORDRE'		=> $ordre,
		));
		page_footer();
	}
}
