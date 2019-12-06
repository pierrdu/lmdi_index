<?php
// balremp.php
// (c) 2016-2019 - LMDI Pierre Duhem
// Page de recherche/remplacement des noms de genre et des balides pour les animateurs

namespace lmdi\index\core;

class balremp
{
	protected $template;
	protected $db;
	protected $auth;
	protected $ext_manager;
	protected $path_helper;
	protected $gloss_helper;
	// Strings
	protected $phpEx;
	protected $phpbb_root_path;
	protected $rh_tt;
	protected $rh_t;
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
		\phpbb\request\request $request,
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
		$this->request			= $request;
		$this->phpEx 			= $phpEx;
		$this->phpbb_root_path 	= $phpbb_root_path;
		$this->rh_tt			= $table_rh_tt;
		$this->rh_t			= $table_rh_t;

		$this->ext_path = $this->ext_manager->get_extension_path('lmdi/index', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}

	public $u_action;


	private function delete_tags ($tabid)
	{
		if (count ($tabid))
		{
			$ids = '(' . implode (',', $tabid) . ')';
			// var_dump ($ids);
			$sql = "DELETE from $this->rh_tt WHERE id IN $ids";
			$this->db->sql_query ($sql);
			$sql = "DELETE from $this->rh_t WHERE tag_id IN $ids";
			$this->db->sql_query ($sql);
		}
	}


	private function get_ids ($tag)
	{
		$ids = array ();
		$sql  = "SELECT * ";
		$sql .= "FROM $this->rh_tt ";
		$sql .= "WHERE tag LIKE '$tag%' ";
		// var_dump ($sql);
		$result = $this->db->sql_query ($sql);
		while ($row = $this->db->sql_fetchrow ($result))
		{
			$ids[] = $row['id'];
		}
		$this->db->sql_freeresult ($result);
		return ($ids);
	}


	function main ($mode)
	{
		$form_key = 'lmdi_index';
		$message = "";
		$ogenus = $this->request->variable ('ogenus', '', true);
		if ($ogenus)
		{
			if (!check_form_key($form_key))
			{
				trigger_error('FORM_INVALID');
			}
			else
			{
				$dgenus = $this->request->variable ('dgenus', '', true);
				if ($ogenus && $dgenus)
				{
					$genuso = '[' . $ogenus;
					$genusd = '[' . $dgenus;
					// Replacement in titles
					$sql = "update " . TOPICS_TABLE . "
						set topic_title = REPLACE (topic_title, '$genuso','$genusd')
						where topic_title like '$genuso%'";
					// var_dump ($sql);
					$this->db->sql_query ($sql);
					// Replacement in title of the first post
					$sql = "update " . POSTS_TABLE . "
						set post_subject = REPLACE (post_subject, '$genuso','$genusd')
						where post_subject like '$genuso%'";
					// var_dump ($sql);
					$this->db->sql_query ($sql);
					// Deletion of corresponding tags
					list ($gen, $sgen) = explode (' ', $ogenus);
					$ids = $this->get_ids ($gen);
					$this->delete_tags ($ids);
					$message = $this->language->lang('TAGREMP_OK');
					$message = sprintf ($message, $ogenus, $dgenus);
				}
				else
				{
					$message = $this->language->lang('TAGREMP_ERROR');
					$message = sprintf ($message, $ogenus, $dgenus);
				}
			}
		}
		add_form_key ($form_key);
		$str_action = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index?mode=remp');
		$this->template->assign_vars (array (
			'MESSAGE'			=> $this->language->lang('REMP_PAGE_EXPLAIN'),
			'U_ACTION'		=> $str_action,
			'RESULT'			=> $message,
			));
		$this->template->set_filenames (array(
			'body' => 'balremp.html',
		));
		// Breadcrumb
		$params = "mode=index";
		$str_index = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $str_index,
			'FORUM_NAME'	=> $this->language->lang('TAG_REPLACEMENT'),
			));

		page_header($this->language->lang('TBALISAGE'));
		make_jumpbox(append_sid($this->phpbb_root_path . 'viewforum.' . $this->phpEx));
		page_footer();
	}
}
