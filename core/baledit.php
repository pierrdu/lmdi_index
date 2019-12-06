<?php
// baledit.php
// (c) 2016-2019 - LMDI Pierre Duhem
// Page d'édition des balides pour les animateurs
// Tag edition page for moderators

namespace lmdi\index\core;

class baledit
{
	protected $template;
	protected $language;
	protected $db;
	protected $cache;
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
		\phpbb\cache\service $cache,
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
		$this->cache			= $cache;
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


	function main ($id = 0, $mode = '')
	{
		$form_key = 'lmdi_index';

		if ($id)
		{
			$tag = $this->get_balise ($id);
		}
		else
		{
			$id = $this->request->variable ('tag_code', 0);
			$tag = $this->request->variable ('tag_name', '', true);
		}

		$message = "";
		switch ($mode)
		{
			case 'save' :	// Sauvegarde de la balise modifiée
				if (!check_form_key($form_key))
				{
					trigger_error('FORM_INVALID');
				}
				else
				{
					$this->ren_balise ($id, $tag);
					$this->invalidate_cache ($tag);
					// Message d'information et redirection
					$params = "mode=edition&amp;cap=" . substr ($tag, 0, 1);
					$url = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
					$url .= "#$id"; // Anchor target = code de la balise sauvegardée
					$url = "<a href=\"$url\">";
					$message = sprintf ($this->language->lang('TOPICTAGS_TAG_SAVED'), $tag, $url, '</a>');
					trigger_error($message);
					break;
				}
			case 'ed' :	// Page d'édition d'une balise individuelle
				add_form_key ($form_key);
				$str_action = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index?mode=save');
				$this->template->assign_vars (array (
					'TAG_NAME'		=> $tag,
					'TAG_CODE'		=> $id,
					'U_ACTION'		=> $str_action,
					));
				$this->template->set_filenames (array(
					'body' => 'baledit.html',
				));
				$params = "mode=ed&amp;id=$id";
				$str_index = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
				$this->template->assign_block_vars('navlinks', array(
					'U_VIEW_FORUM'	=> $str_index,
					'FORUM_NAME'	=> $this->language->lang('TAG_EDITION'),
					));
				break;
			case 'destroy' :	// Exécution de la suppression
				if (!check_form_key($form_key))
				{
					trigger_error('FORM_INVALID');
				}
				else
				{
					$yes = $this->request->variable ('yes', '', true);
					$no = $this->request->variable ('no', '', true);
					if ($yes && $id)
					{
						$this->suppression_balise ($id);
						$this->invalidate_cache ($tag);
						// Message d'information et redirection
						$params = "mode=edition&amp;cap=" . substr ($tag, 0, 1);
						$url = append_sid($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
						$url = "<a href=\"$url\">";
						$message = sprintf ($this->language->lang('TOPICTAGS_TAG_DELETED'), $tag, $url, '</a>');
						trigger_error($message);
					}
					break;
				}
			case 'del' :	// Page de suppression
				$explain = $this->language->lang('TOPICTAGS_DEL_TAGS');
				$explain = sprintf ($explain, $tag);
				add_form_key ($form_key);
				$str_action = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index?mode=destroy');
				$this->template->assign_vars (array (
					'MESSAGE'			=> $message,
					'TOPICTAGS_DELETION' => $explain,
					'TAG_CODE'		=> $id,
					'TAG_NAME'		=> $tag,
					'U_ACTION'		=> $str_action,
					));
				$this->template->set_filenames (array(
					'body' => 'baldel.html',
				));
				$params = "mode=del&amp;id=$id";
				$str_index = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index', $params);
				$this->template->assign_block_vars('navlinks', array(
					'U_VIEW_FORUM'	=> $str_index,
					'FORUM_NAME'	=> $this->language->lang('TAG_DELETION'),
					));
			break;
			}

		page_header($this->language->lang('TBALISAGE'));
		make_jumpbox(append_sid($this->phpbb_root_path . 'viewforum.' . $this->phpEx));
		page_footer();
	}	// main


	private function suppression_balise ($id)
	{
		$sql = "DELETE FROM $this->rh_tt WHERE id = $id";
		$this->db->sql_query ($sql);
		$sql = "DELETE FROM $this->rh_t WHERE tag_id = $id";
		$this->db->sql_query ($sql);
	}


	private function get_balise ($id)
	{
		$sql  = "SELECT * FROM $this->rh_tt WHERE id = $id ";
		$result = $this->db->sql_query ($sql);
		$row = $this->db->sql_fetchrow ($result);
		$tag = $row['tag'];
		$this->db->sql_freeresult ($result);
		return ($tag);
	}


	private function get_ids ($tag)
	{
		$ids = array ();
		$sql  = "SELECT * FROM $this->rh_tt WHERE tag = '$tag' ";
		$result = $this->db->sql_query ($sql);
		while ($row = $this->db->sql_fetchrow ($result))
		{
			$ids[] = $row['id'];
		}
		$this->db->sql_freeresult ($result);
		return ($ids);
	}


	private function get_nbbal ($id)
	{
		$sql  = "SELECT COUNT from $this->rh_tt WHERE id = $id";
		$result = $this->db->sql_query ($sql);
		$row = $this->db->sql_fetchrow ($result);
		$nbbal = (int) $row['count'];
		$this->db->sql_freeresult ($result);
		return ($nbbal);
	}


	private function fusion_balises ($nid, $oid)
	{
		// Nombre de balisages supprimés avec l'ancienne balise
		$nbbal = $this->get_nbbal ($oid);
		// Ajout de ce nombre au total de la nouvelle
		$sql  = "UPDATE $this->rh_tt SET count=count+$nbbal WHERE id = $nid";
		$this->db->sql_query ($sql);
		// Modification des enregistrements de rh_t qui avaient l'ancien code
		$sql  = "UPDATE $this->rh_t SET tag_id = $nid WHERE tag_id = $oid";
		$this->db->sql_query ($sql);
		// Suppression de l'ancienne balise
		$sql = "DELETE FROM $this->rh_tt WHERE id = $oid";
		$this->db->sql_query ($sql);
	}


	private function ren_balise ($id, $tag)
	{
		$sql = "UPDATE $this->rh_tt SET tag = '$tag' WHERE id =$id";
		$this->db->sql_query ($sql);
	}


	private function invalidate_cache ($tag)
	{
		$init = substr ($tag, 0, 1);
		$this->cache->destroy('_tags_table_' . $init);
	}

}
