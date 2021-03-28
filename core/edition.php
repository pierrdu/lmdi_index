<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016-2019 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace lmdi\index\core;

class edition
{
	protected $template;
	protected $user;
	protected $language;
	protected $db;
	protected $cache;
	protected $config;
	protected $helper;
	protected $auth;
	protected $ext_manager;
	protected $path_helper;
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
		\phpbb\language\language $language,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\cache\service $cache,
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
		$this->language		= $language;
		$this->db 			= $db;
		$this->cache			= $cache;
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

	function main ($cap)
	{
		static $abc_table = null;

		$this->language->add_lang ('index', 'lmdi/index');
		if (!$abc_table)
		{
			$abc_table = $this->compute_abc_table();
		}
		foreach ($abc_table as $let)
		{
			if ($let == $cap)
			{
				$this->template->assign_block_vars('gabc', array(
				'S_URL' => false,
				'ABC' => $let,
				'URL' => "",
				));
			}
			else
			{
				$url = $this->helper->route('lmdi_index_controller', array('mode' => 'edition', 'cap' => $let));
				$this->template->assign_block_vars('gabc', array(
				'S_URL' => true,
				'ABC' => $let,
				'URL' => $url,
				));
			}
		}
		/*	La variable $cap vaut A quand elle n'est pas spécifiée sur la ligne
			de commande (quand on vient de la page d'affichage). Sinon, chacune
			des lettres initiales envoie avec une valeur renseignée correctement
			pour obtenir une page partielle. On pourrait imaginer une mise en
			cache.
			*/
		$table = $this->compute_tags_table ($cap);
		$cpteur = 0;
		foreach ($table as $row)
		{
			if (!$cpteur)
			{
				$anchor = "<span id='$let'></span>";
			}
			else
			{
				$anchor = "";
			}
			$cpteur ++;
			$id = $row['id'];
			$tag = $row['tag'];
			$count = $row['count'];
			$params = "/tag/$tag";
			$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
			$url1 = $this->helper->route('lmdi_index_controller', array('mode' => 'ed', 'id' => $id));
			$url2 = $this->helper->route('lmdi_index_controller', array('mode' => 'del', 'id' => $id));
			$this->template->assign_block_vars('gtags', array(
				'ANCHOR'	=> $anchor,
				'NUM'	=> $cpteur,
				'ID'		=> $id,
				'TAG'	=> $tag,
				'URL'	=> $url,
				'NB'		=> $count,
				'DEL'	=> $url2,
				'ED'		=> $url1,
				));
		}

		// Breadcrumbs
		$params = "mode=edition";
		$str_edition = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $str_edition,
			'FORUM_NAME'	=> $this->language->lang('INDEX_EDIT_PAGES'),
		));

		$titre = $this->language->lang('TBALISAGE');
		$this->template->assign_vars(array(
			'TITLE'		=> $titre,
		));
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'edition.html',
		));
		page_footer();
	}


	public function compute_abc_table()
	{
		$abc_table = $this->cache->get('_tags_abc_table');
		if (!$abc_table || empty($abc_table))
		{
			$abc_table = $this->rebuild_cache_abc_table();
		}
		return ($abc_table);
	}	// compute_abc_table


	private function rebuild_cache_abc_table()
	{
		$abc_table = array();
		$sql = 'SELECT DISTINCT UPPER(LEFT(TRIM(tag),1)) AS a FROM ' . $this->table_rh_tt . '
			ORDER BY a';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$abc_table[] = $row['a'];
		}
		$this->db->sql_freeresult($result);
		$this->cache->put('_tags_abc_table', $abc_table, 86400);
		return ($abc_table);
	}	// rebuild_cache_abc_table


	public function compute_tags_table($cap)
	{
		$tags_table = $this->cache->get('_tags_table_'.$cap);
		if (empty($tags_table))
		{
			$tags_table = $this->rebuild_cache_tags_table($cap);
		}
		return ($tags_table);
	}	// compute_tags_table


	private function rebuild_cache_tags_table($cap)
	{
		$tags_table = array();
		$cap = $this->db->sql_escape ($cap);
		$sql = "SELECT * FROM " . $this->table_rh_tt . "
			WHERE LEFT($this->table_rh_tt.tag, 1) = '$cap' 
			ORDER BY tag";
		$result = $this->db->sql_query ($sql);
		$block = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$block[] = $row;
		}
		$this->db->sql_freeresult($result);
		$this->cache->put('_tags_table_' . $cap, $block, 86400);
		return ($block);
	}	// rebuild_cache_tags_table

}
