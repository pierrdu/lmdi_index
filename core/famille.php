<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2022 LMDI - Pierre Duhem
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
			$token = mysqli_connect ("localhost", "root", "pierred", "qi_330b1");
		}
		else
		{
			$jeton = mysqli_connect ("galerieins.mysql.db", "galerieins", "Pi47Duhem", "galerieins");
			$token = mysqli_connect ("galerieiforum.mysql.db", "galerieiforum", "Pi47Duhem", "galerieiforum");
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
		WHERE insect_famille.nom = '$famille' AND bi.genre != 'Inconnu' 
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
			// Suppression de la sous-espèce éventuelle
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
			$taxon = $genre . ' ' . $espece;
			/*	Si c'est qqch inconnu, il ne faut pas tester sur tout, mais 
				seulement sur le contenu de la case genre.
				*/
			if ($this->existe_balise ($token, $taxon) || $this->existe_balise ($token, $genre))
			{
				$urltax = sprintf ($url, "$genre+$espece");
			}
			else
			{
				continue;
			}
			$this->template->assign_block_vars('gbal', array(
				'NUM'		=> $cpteur,
				'SFAM'		=> $sfamille,
				'TRI'		=> $tribu,
				'GEN'		=> "<i>$genre</i>",
				'TAX'		=> $espece == 'sp.' ? "<i>$genre</i> $espece" : "<i>$genre $espece</i>",
				'URLSFAM'		=> sprintf ($url, $sfamille),
				'URLTRI'		=> sprintf ($url, $tribu),
				'URLGEN'		=> sprintf ($url, $genre),
				'URLTAX'		=> $urltax,
				));
		}
		$url_famille = "<a href=\"" . sprintf ($url, $famille) . "\">$famille</a>";
		mysqli_free_result ($resultat);
		mysqli_close ($jeton);
		mysqli_close ($token);


		// Breadcrumbs
		$params = "mode=famille";
		$str_famille = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $str_famille,
			'FORUM_NAME'	=> $this->language->lang('INDEX_PAGES_FAMILLE'),
		));


		$titre = $this->language->lang('TBALISAGE');
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'famille.html',
		));
		$this->template->assign_vars(array(
			'CLASSE'		=> $classe,
			'ORDRE'		=> $ordre,
			'FAMILLE'		=> $url_famille,
		));
		page_footer();
	}	// Fin de main


	function existe_balise ($token, $taxon)
	{
		$sql = "SELECT * FROM " . $this->table_rh_tt . " WHERE tag = '$taxon'";
		$result = mysqli_query ($token, $sql);
		$row = mysqli_fetch_row ($result);
		if ($row)
			return (1);
		else
			return (0);
	}	// fin de existe_balise
}
