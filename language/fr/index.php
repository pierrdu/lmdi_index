<?php
/**
* index.php
* @package phpBB Extension - LMDI Indexing
* @copyright (c) 2016 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge ($lang, array(
	// URL
	'BALISAGE'		=> 'Indexation',
	'LBALISAGE'		=> 'Indexation',
	'TBALISAGE'		=> 'Balises d’indexation',
	// Static page
	'INDEX_NUMERO'		=> 'N°',
	'INDEX_CODE'		=> 'Code',
	'INDEX_BALISE'		=> 'Balises',
	'INDEX_NB_BAL'		=> 'Nombre d’attributions',
	'INDEX_ACTION'		=> 'Opérations',
	'INDEX_DELETE'		=> 'Suppression',
	'INDEX_EDIT_PAGES'	=> 'Pages d’édition des balises d’indexation',
	'INDEX_DISPLAY_PAGES'	=> 'Pages d’affichage des balises d’indexation',
	'INDEX_EDIT'		=> 'Édition',
	'TAG_EDITION'		=> 'Édition d’une balise',
	'TAG_DELETION'		=> 'Suppression d’une balise',
	'TOPICTAGS_TAG_MERGED'	=> "Les balises « %s » ont été fusionnées.",
	'TOPICTAGS_MISSING_TAG'	=>'Vous n’avez pas saisi un nom de balise.',
	'TOPICTAGS_SAME_TAG'	=> 'La balise n’a pas été modifiée.',
	'TOPICTAGS_TAG_DELETED'	=> 'La balise a été supprimée.',

	// Edition page
	'LMDI_CONFIG_TAGS'	=> 'Vous pouvez ci-dessous éditer la balise d’indexation.<br />Si vous saisissez une chaîne de balise qui existe déjà, les deux balises sont fusionnées.<br />Cela constitue un moyen de regrouper plusieurs balises mal écrites.<br /><br />',
	'LMDI_NEW_TAG_NAME'	=> 'Zone d’édition de la balise',
	// Deletion page
	'TOPICTAGS_DEL_TAGS'	=> 'Vous avez demandé la suppression de la balise « %s ».<br />Êtes-vous certain ?<br />Cette opération ne peut pas être annulée par la suite.<br /><br />',

));
