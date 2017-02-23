<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace lmdi\index\core;

class index
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

	function main($cap)
	{
		// ABC-links production
		$sql  = 'SELECT DISTINCT UPPER(LEFT(TRIM(tag),1)) AS a FROM ' . $this->table_rh_tt . '
				ORDER BY a';
		$result = $this->db->sql_query($sql);

		$abc_links = '<span id="haut"></span><br /><p class="glossa">';

		while ($row = $this->db->sql_fetchrow($result))
		{
			$l = $row['a'];
			$params = "/index?mode=index&amp;cap=$l";
			$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
			if ($l == $cap)
			{
				$abc_links .= "\n<span class=\"capc\">$l&nbsp;</span>" ;
			}
			else
			{
				$abc_links .= "\n<a class=\"cap\" href =\"$url\">$l</a>&nbsp;" ;
			}
		}
		$this->db->sql_freeresult ($result);

		$autres = "Autres";
		$params = "/index?mode=index&amp;cap=$autres";
		$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		if ($autres == $cap)
		{
			$abc_links .= "\n<span class=\"capc\">$autres&nbsp;</span>" ;
		}
		else
		{
			$abc_links .= "\n<a class=\"cap\" href =\"$url\">$autres</a>&nbsp;" ;
		}
		$abc_links .= '</p>';

		// Contents table production
		$str_balise  = $this->user->lang['INDEX_BALISE'];
		$str_nombre  = $this->user->lang['INDEX_NB_BAL'];
		$str_numero  = $this->user->lang['INDEX_NUMERO'];
		$str_code    = $this->user->lang['INDEX_CODE'];
		$str_action  = $this->user->lang['INDEX_ACTION'];
		$str_delete  = $this->user->lang['INDEX_DELETE'];
		$str_edit    = $this->user->lang['INDEX_EDIT'];

		$corps  = '<table class="deg"><tr class="deg">';
		$corps .= '<th class="deg0">' . $str_numero . '</th>';
		$corps .= '<th class="deg0">' . $str_code . '</th>';
		$corps .= '<th class="deg0">' . $str_balise . '</th>';
		$corps .= '<th class="deg0">' . $str_nombre . '</th>';
		$corps .= '<th colspan="2" class="deg1">' . $str_action .'</th></tr>';

		$cpt  = 0;
		$top = $this->ext_path_web . "/styles/top.gif";
		// SELECT * FROM `phpbb3_rh_topictags_tag` WHERE LEFT(`phpbb3_rh_topictags_tag`.`tag`, 1) = 'C' ORDER BY tag
		// SELECT * FROM `phpbb3_rh_topictags_tag` WHERE LEFT(`phpbb3_rh_topictags_tag`.`tag`, 1) NOT BETWEEN 'A' AND 'Z' ORDER BY tag
		// Attention, cela fait disparaître les minuscules, parce que la colonne tag est codée
		// en utf8_bin
		if ($cap == $autres)
		{
			$sql = "SELECT * 
					FROM " . $this->table_rh_tt . "
					WHERE LEFT($this->table_rh_tt.tag, 1) NOT BETWEEN 'A' AND 'Z' 
					ORDER BY tag";
			$result = $this->db->sql_query ($sql);
		}
		else
		{
			$sql  = "SELECT * 
					FROM " . $this->table_rh_tt . "
					WHERE LEFT($this->table_rh_tt.tag, 1) = '$cap' 
					ORDER BY tag";
			$result = $this->db->sql_query ($sql);
		}

		$cpt = 0;
		// $corps .= "<tr class=\"deg\"><td class=\"glossi\" colspan=\"4\" id=$cap>&nbsp;$cap</td></tr>";
		while ($row = $this->db->sql_fetchrow($result))
		{
			$cpt++;
			$code   = $row['id'];
			$tag    = $row['tag'];
			$nbre   = $row['count'];
			$url1 = append_sid($this->phpbb_root_path.'app.'.$this->phpEx."/tag/$tag");
			$url2 = append_sid($this->phpbb_root_path.'app.'.$this->phpEx."/index?mode=del&amp;id=$code");
			$url3 = append_sid($this->phpbb_root_path.'app.'.$this->phpEx."/index?mode=ed&amp;id=$code");
			$corps .= "\n<tr class='deg'>";
			$corps .= "<td class='deg0'>$cpt</td>";
			$corps .= "<td class='deg0' id=\"$code\">$code</td>";
			$corps .= "<td class='deg0'><a href=\"$url1\">$tag</a></td>";
			$corps .= "<td class='deg0'>$nbre</td>";
			$corps .= "<td class='deg4'><a href=\"$url2\">$str_delete</a></td>";
			$corps .= "<td class='deg4'><a href=\"$url3\">$str_edit</a></td>";
			$corps .= "</tr>";
		}
		$this->db->sql_freeresult ($result);
		$corps .= "<tr><td colspan=\"5\">&nbsp;</td>";
		$corps .= "<td class=\"haut\" ><a href=\"#haut\"><img src=\"$top\"></a></td></tr>";
		$corps .= "</table>";

		if ($this->auth->acl_get('a_') || $this->auth->acl_getf_global ('m_'))
		{
			$autorisation = 1;
		}
		else
		{
			$autorisation = 0;
		}

		$titre = $this->user->lang['TBALISAGE'];
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'index.html',
		));
		$this->template->assign_vars(array(
			'TITLE'		=> $titre,
			'ABC'		=> $abc_links,
			'CORPS'		=> $corps,
			'S_AUTOR'		=> $autorisation,
		));

		page_footer();
	}
}
