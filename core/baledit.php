<?php
// baledit.php
// (c) 2016 - LMDI Pierre Duhem
// Page d'édition des balides pour les animateurs
// Tag edition page for moderators

namespace lmdi\index\core;

class baledit
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
		\phpbb\user $user,
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
		$this->user 			= $user;
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


	private function suppression_balise ($id)
	{
		$sql = "DELETE from $this->rh_tt WHERE id = $id";
		$this->db->sql_query ($sql);
		$sql = "DELETE from $this->rh_t WHERE tag_id = $id";
		$this->db->sql_query ($sql);
	}

	private function get_balise ($id)
	{
		$sql  = "SELECT * ";
		$sql .= "FROM $this->rh_tt ";
		$sql .= "WHERE id = $id ";
		$result = $this->db->sql_query ($sql);
		$row = $this->db->sql_fetchrow ($result);
		$tag = $row['tag'];
		$this->db->sql_freeresult ($result);
		return ($tag);
	}


	private function get_ids ($tag)
	{
		$ids = array ();
		$sql  = "SELECT * ";
		$sql .= "FROM $this->rh_tt ";
		$sql .= "WHERE tag = '$tag' ";
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
		$sql  = "SELECT count from $this->rh_tt WHERE id = $id";
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
		$sql  = "UPDATE $this->rh_tt set count=count+$nbbal WHERE id = $nid";
		// var_dump ($sql);
		$this->db->sql_query ($sql);
		// Modification des enregistrements de rh_t qui avaient l'ancien code
		$sql  = "UPDATE $this->rh_t set tag_id = $nid WHERE tag_id = $oid";
		// var_dump ($sql);
		$this->db->sql_query ($sql);
		// Suppression de l'ancienne balise
		$sql = "DELETE from $this->rh_tt WHERE id = $oid";
		// var_dump ($sql);
		$this->db->sql_query ($sql);
	}


	private function ren_balise ($id, $tag)
	{
		$sql = "UPDATE $this->rh_tt set tag = '$tag' WHERE id =$id";
		$this->db->sql_query ($sql);
	}


	function main ($id = 0, $mode = '')
	{
		$abc_links = "";
		$illustration = "";
		$corps = "";
		$biblio = "";
		$form_key = 'lmdi_index';
		// var_dump ($id);
		// var_dump ($mode);

		if ($id)
		{
			$tag = $this->get_balise ($id);
		}
		else
		{
			$id = $this->request->variable ('tag_code', 0);
			$tag = $this->request->variable ('tag_name', '');
		}

		$message = "";
		switch ($mode)
		{
			case 'save' :
				if (!check_form_key($form_key))
				{
					trigger_error('FORM_INVALID');
				}
				else
				{
					// var_dump ("Entrée dans save...");
					if (empty ($tag))
					{
						$message = $this->user->lang['TOPICTAGS_MISSING_TAG'];
					}
					$otag = $this->get_balise ($id);
					if ($otag == $tag)
					{
						$message = $this->user->lang['TOPICTAGS_SAME_TAG'];
					}
					else
					{
						// Si la balise existait déjà, il faut fusionner
						$ids = $this->get_ids ($tag);
						if (!empty ($ids))
						{
							foreach ($ids as $nid)
							{
								$this->fusion_balises ($nid, $id);
							}
							$message = $this->user->lang['TOPICTAGS_TAG_MERGED'];
							$message = sprintf ($message, $otag);
						}
						else // Sinon, on renomme seulement la balise dans la table tt
						{
							$this->ren_balise ($id, $tag);
						}
					$params = '/index?mode=index&amp;cap=' . substr ($tag, 0, 1);
					$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
					redirect ($url);
					break;
					}
				}
			case 'ed' :
				add_form_key ($form_key);
				$str_action = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . '/index?mode=save');
				$this->template->assign_vars (array (
					'MESSAGE'			=> $message,
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
					'FORUM_NAME'	=> $this->user->lang['TAG_EDITION'],
					));
			break;
			case 'destroy' :
				if (!check_form_key($form_key))
				{
					trigger_error('FORM_INVALID');
				}
				else
				{
					var_dump ("Entrée dans le code de suppression...");
					$yes = $this->request->variable ('yes', '', true);
					$no = $this->request->variable ('no', '', true);
					if ($yes && $id)
					{
						$this->suppression_balise ($id);
					}
					$params = '/index?mode=index&amp;cap=' . substr ($tag, 0, 1);
					$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
					redirect ($url);
					break;
				}
			case 'del' :
				$explain = $this->user->lang['TOPICTAGS_DEL_TAGS'];
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
					'FORUM_NAME'	=> $this->user->lang['TAG_DELETION'],
					));
			break;
			}

		page_header($this->user->lang['TBALISAGE']);
		make_jumpbox(append_sid($this->phpbb_root_path . 'viewforum.' . $this->phpEx));
		page_footer();
	}
}
