<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2022 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*	La première version travaillait sur des tables codées en dur parce que
	je ne pouvais pas interroger la base de données depuis l'extension.
	*/

namespace lmdi\index\core;

class aiguillage
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

	function main()
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
		$sql = "SELECT insect_classe.nom as classe, insect_ordre.nom as ordre 
		FROM insect_photo p 
		LEFT JOIN insect_classe ON (p.id_classe = insect_classe.id_classe) 
		LEFT JOIN insect_ordre ON (p.id_ordre = insect_ordre.id_ordre) 
		WHERE insect_classe.id_classe < 11
		GROUP BY classe, ordre
		ORDER BY insect_classe.id_classe, ordre";
		$resultat = mysqli_query ($jeton, $sql);

		$oclasse = '';
		$cpteur = 0;
		$tableau = array ();
		$tabordre = array ();
		while ($row = mysqli_fetch_row ($resultat))
		{
			$classe = $row[0];
			$ordre = $row[1];
			if ($ordre == 'Inconnu')
			{
				continue;
			}
			if ($classe != $oclasse)
			{
				if (strlen ($oclasse))
				{
					$ligne = array($oclasse, $tabordre);
					$tableau[] = $ligne;
					unset ($tabordre);
					$tabordre = array ();
				}
				$oclasse = $classe;
			}
			$tabordre[] = $ordre;
			$cpteur ++;
		}	// Fin du while
		// Fermeture de la dernière classe
		$ligne = array ($classe, $tabordre);
		mysqli_free_result ($resultat);
		$tableau[] = $ligne;
		mysqli_close ($jeton);


		$params = "/index?mode=ordre";
		$url = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		$url .= "&amp;ord=";

		$nbc = count($tableau);
		for ($i = 0; $i < $nbc; $i++)
		{
			$tab = $tableau[$i];
			$classe = $tab[0];
			// Enregistrement de la ligne de classe
			$this->template->assign_block_vars('gordres', array(
				'S_CLASSE'	=> true,
				'ORDRE1'		=> $classe,
				'ORDRE2'		=> '',
				'ORDRE3'		=> '',
				'ORDRE4'		=> '',
				));
			$ordres = $tab[1];
			$nbo = count ($ordres);		// Nb total des ordres
			$tr4 = floor ($nbo / 4);		// Nb de tranches de 4
			$reste = $nbo % 4;			// Reste sur la dernière ligne
			if ($reste)
			{
				$finl = 4 - $reste;
				for ($j = 0; $j < $finl; $j++)
				{
					$ordres[] = '';
				}
				$tr4++;
			}
			reset ($ordres);
			// Enregistrement des lignes d'ordre
			for ($j = 0; $j < $tr4; $j++)
			{
				$this->template->assign_block_vars('gordres', array(
					'S_CLASSE'	=> false,
					'ORDRE1'		=> $ordres[($tr4 * 0)+ $j],
					'URL1'		=> $url . $ordres[($tr4 * 0)+ $j],
					'ORDRE2'		=> $ordres[($tr4 * 1)+ $j],
					'URL2'		=> $url . $ordres[($tr4 * 1)+ $j],
					'ORDRE3'		=> $ordres[($tr4 * 2)+ $j],
					'URL3'		=> $url . $ordres[($tr4 * 2)+ $j],
					'ORDRE4'		=> $ordres[($tr4 * 3)+ $j],
					'URL4'		=> $url . $ordres[($tr4 * 3)+ $j],
					));
			}
		}	// Fin du for sur les classes

		// Breadcrumbs
		$params = "mode=aiguillage";
		$str_aiguillage = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $str_aiguillage,
			'FORUM_NAME'	=> $this->language->lang('TBALISAGE'),
		));

		// Administrateur ou modérateur
		if ($this->auth->acl_get('a_') || $this->auth->acl_getf_global ('m_'))
		{
			$autorisation = 1;
			$editor = $this->helper->route('lmdi_index_controller', array('mode' => 'ed'));
		}
		else
		{
			$autorisation = 0;
			$editor = "";
		}


		$titre = $this->language->lang('TBALISAGE');
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'aiguillage.html',
		));
		$this->template->assign_vars(array(
			'EDITOR'		=> generate_board_url() . '/' . append_sid("app.{$this->phpEx}/index", "mode=edition"),
			'TITLE'		=> $titre,
			'S_AUTOR'		=> $autorisation,
		));
		page_footer();
	}
}
