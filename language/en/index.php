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
	'INDEX_NUMERO'		=> '#',
	'INDEX_CODE'		=> 'Code',
	'INDEX_BALISE'		=> 'Tags',
	'INDEX_NB_BAL'		=> 'Number assigned',
	'BACKTOP'			=> 'Top',

	'TAG_EDITION'		=> 'Tag Edition',
	'TAG_DELETION'		=> 'Tag Deletion',
	'TOPICTAGS_TAG_MERGED'	=> 'The tag  %s was successfully saved.<br />%s<b>Click here</b>%s to come back to the edition page.',
	'TOPICTAGS_TAG_MERGED'	=> "The tags '%s' have been merged.",
	'TOPICTAGS_MISSING_TAG'	=> 'You didn’t gave a tag name.',
	'TOPICTAGS_SAME_TAG'	=> 'The tag name didn’t change.',
	'TOPICTAGS_TAG_DELETED'	=> 'The tag %s was deleted. <br />%s<b>Click here</b>%s to come back to the edition page.',

	// Edition page
	'LMDI_CONFIG_TAGS'	=> 'You may edit below the tag wording.<br />If you give an existing tag name, both tags get merged together.<br />This is a practical way to correct misspellings.<br /><br />',
	'LMDI_NEW_TAG_NAME'	=> 'Tag Edit Zone',

	// Deletion page
	'TOPICTAGS_DEL_TAGS'	=> "You asked for the deletion of tag '%s'.<br />Are you sure?<br /> This action can't be cancelled afterwards.<br /><br />",

	// Search & replacement page
	'TAG_REPLACEMENT'		=> 'Genus replacement and tags deletion',
	'URL_REMP_PAGE'		=> 'For the %s — <a href="%s">Please click here</a>',
	'REMP_PAGE'			=> 'Tags and Genus Search & Replace Page',
	'REMP_PAGE_EXPLAIN'		=> 'This page is used to search and replace genus names placed between sqquare brackets in the topic titles and to delete the corresponding genus tags (and the species tags depending on the genus).<br>
	For instance: Type «<i>Protaetia</i>» in the search zone and «<i>Netocia</i>» in the replacement zone. The form adds automatically the opening square bracket. The deletion feature deletes all tags bound to <i>Protaetia</i>, i.e. <i>P.</i> sp, etc. The tags will be recreated in the next automatic tagging session, based on the new taxonomic data.',
	'GENUS_SEARCH'			=> 'Genus name in the title string',
	'GENUS_SEARCH_EXPLAIN'	=> 'Genus name after the opening square bracket in the topic title. suivant immédiatement le crochet ouvrant dans le titre du sujet. Vous pouvez inclure le nom du sous-genre entre parenthèses s’il est présent.',
	'GENUS_REMP'			=> 'Genre de remplacement',
	'GENUS_REMP_EXPLAIN'	=> 'Genre qui remplacera dans le titre du sujet.',
	'TAGREMP_ERROR'		=> 'Error specifying parameters %s and %s.',
	'TAGREMP_OK'			=> 'Genus %s was replaced by %s.',




));
