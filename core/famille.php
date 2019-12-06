<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2019 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*	Module traitant les familles (appelé depuis la page des ordres).
	*/

namespace lmdi\index\core;

class famille
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

	function main($famille)
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
		$sql = "SELECT insect_classe.nom as classe, insect_ordre.nom as id_ordre 
		FROM insect_photo p 
		LEFT JOIN insect_classe ON (p.id_classe = insect_classe.id_classe) 
		LEFT JOIN insect_ordre ON (p.id_ordre = insect_ordre.id_ordre) 
		LEFT JOIN insect_famille ON (p.id_famille = insect_famille.id_famille) 
		WHERE insect_famille.nom = '$famille' LIMIT 1";
		$resultat = mysqli_query ($jeton, $sql);
		$row = mysqli_fetch_row ($resultat);
		$classe = $row[0];
		$ordre = $row[1];
		mysqli_free_result ($resultat);

		$sql = "SELECT 
		insect_famille.nom as famille, insect_sfamille.nom as sfamille,
		insect_tribu.nom as tribu, bi.genre as genre, bi.espece as espece
		FROM insect_photo p 
		LEFT JOIN insect_famille ON (p.id_famille = insect_famille.id_famille) 
		LEFT JOIN insect_binom bi ON (p.id_binom = bi.id_binom) 
		LEFT JOIN insect_sfamille ON (bi.id_sfamille = insect_sfamille.id_sfamille) 
		LEFT JOIN insect_tribu ON (bi.id_tribu = insect_tribu.id_tribu) 
		WHERE insect_famille.nom = '$famille' AND bi.genre != 'Inconnu' AND bi.espece != 'inconnu' 
		GROUP BY famille, sfamille, tribu, genre, espece";
		$resultat = mysqli_query ($jeton, $sql);

		$params = "/tag/%s";
		$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		$cpteur = 0;
		$oesp = '';
		while ($row = mysqli_fetch_row ($resultat))
		{
			$famille = $row[0];
			$sfamille = $row[1];
			$tribu = $row[2];
			$genre = $row[3];
			$espece = $row[4];
			$pos = strpos ($espece, ' ');
			if ($pos)
			{
				$espece = substr ($espece, 0, $pos);
				if ($espece == $oesp)
				{
					continue;
				}
			}
			$oesp = $espece;
			$cpteur ++;
			$this->template->assign_block_vars('gbal', array(
				'NUM'		=> $cpteur,
				'FAM'		=> $famille,
				'SFAM'		=> $sfamille,
				'TRI'		=> $tribu,
				'GEN'		=> "<i>$genre</i>",
				'TAX'		=> $espece == 'sp.' ? "<i>$genre</i> $espece" : "<i>$genre $espece</i>",
				'URLFAM'		=> sprintf ($url, $famille),
				'URLSFAM'		=> sprintf ($url, $sfamille),
				'URLTRI'		=> sprintf ($url, $tribu),
				'URLGEN'		=> sprintf ($url, $genre),
				'URLTAX'		=> sprintf ($url, "$genre+$espece"),
				));
		}
		mysqli_free_result ($resultat);
		mysqli_close ($jeton);


		// Breadcrumbs
		$params = "mode=famille";
		$str_famille = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $str_famille,
			'FORUM_NAME'	=> $this->language->lang('INDEX_PAGES_FAMILLE'),
		));

		// Link to the active (edition) pages
		$params = "/index?mode=index&amp;cap=A";
		$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		$abc_links .= "$str_links &mdash; <a href =\"$url\">Cliquez ici</a></h2><br><br>";



		$titre = $this->language->lang('TBALISAGE');
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'famille.html',
		));
		$this->template->assign_vars(array(
			'CLASSE'		=> $classe,
			'ORDRE'		=> $ordre,
		));
		page_footer();
	}
}
