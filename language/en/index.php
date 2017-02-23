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
	'BALISAGE'		=> 'Indexing',
	'LBALISAGE'		=> 'Indexing',
	'TBALISAGE'		=> 'Indexing tags',
	'INDEX_NUMERO'		=> '#',
	'INDEX_CODE'		=> 'Id',
	'INDEX_BALISE'		=> 'Tags',
	'INDEX_NB_BAL'		=> 'Topics tagged',
	'INDEX_ACTION'		=> 'Actions',
	'INDEX_DELETE'		=> 'Delete',
	'INDEX_EDIT_PAGES'	=> 'Tag Edition Pages',
	'INDEX_DISPLAY_PAGES'	=> 'Tag Display Pages',
	'INDEX_EDIT'		=> 'Edit',
	'TAG_EDITION'		=> 'Tag Edition',
	'TAG_DELETION'		=> 'Tag Deletion',
	'TOPICTAGS_TAG_MERGED'	=> "The tags '%s' have been merged.",
	'TOPICTAGS_MISSING_TAG'	=> 'You didn’t gave a tag name.',
	'TOPICTAGS_SAME_TAG'	=> 'The tag name didn’t change.',
	'TOPICTAGS_TAG_DELETED'	=> 'the tag was deleted.',

	// Edition page
	'LMDI_CONFIG_TAGS'	=> 'You may edit below the tag wording.<br />If you give an existing tag name, both tags get merged together.<br />This is a practical way to correct misspellings.<br /><br />',
	'LMDI_NEW_TAG_NAME'	=> 'Tag Edit Zone',
	// Deletion page
	'TOPICTAGS_DEL_TAGS'	=> "You asked for the deletion of tag '%s'.<br />Are you sure?<br /> This action can't be cancelled afterwards.<br /><br />",


));
