<?php
/**
* index.php
* @package phpBB Extension - LMDI Indexing
* @copyright (c) 2016-2017 LMDI - Pierre Duhem
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
	'BACKTOP'			=> 'Haut',

	'INDEX_ACTION'		=> 'Opérations',
	'INDEX_DELETE'		=> 'Suppression',
	'INDEX_EDIT_PAGES'	=> 'Pages d’édition des balises d’indexation',
	'INDEX_PAGES_ORDRE'	=> 'Balises d’indexation par ordre',
	'INDEX_PAGES_FAMILLE'	=> 'Balises d’indexation par famille',
	'INDEX_DISPLAY_PAGES'	=> 'Pages d’affichage des balises d’indexation',
	'INDEX_EDIT'		=> 'Édition',
	'TAG_EDITION'		=> 'Édition d’une balise',
	'TAG_DELETION'		=> 'Suppression d’une balise',
	'TOPICTAGS_TAG_SAVED'	=> 'La balise « %s » a bien été enregistrée.<br />%s<b>Cliquez ici</b>%s pour revenir à la page d’édition.',
	'TOPICTAGS_TAG_DELETED'	=> 'La balise « %s » a été supprimée.<br />%s<b>Cliquez ici</b>%s pour revenir à la page d’édition.',
	'TOPICTAGS_TAG_MERGED'	=> 'Les balises « %s » ont été fusionnées.',
	'TOPICTAGS_MISSING_TAG'	=> 'Vous n’avez pas saisi un nom de balise.',
	'TOPICTAGS_SAME_TAG'	=> 'La balise n’a pas été modifiée.',
	'STATIC_PAGE_EXPLAIN'	=> 'Vous trouverez ci-dessous un tableau des classes et des ordres représentés dans les pages de balises.<br>Cliquez sur les liens pour atteindre la page de l’ordre qui vous intéresse.',
	'STATIC_ORDRE_EXPLAIN'	=> 'Vous trouverez ci-dessous un tableau des familles représentées dans les pages de balises.<br>Cliquez sur les liens pour atteindre la page de la famille qui vous intéresse.',
	'STATIC_FAMILLE_EXPLAIN'	=> 'Vous trouverez ci-dessous un tableau des rangs taxonomiques situés au-dessous des familles.<br>Cliquez sur les liens pour atteindre l’élément qui vous intéresse.',
	'BAL_NUM'		=> 'N°',
	'BAL_FAM'		=> 'Famille',
	'BAL_SFAM'	=> 'Sous-famille',
	'BAL_TRI'		=> 'Tribu',
	'BAL_GEN'		=> 'Genre',
	'BAL_TAX'		=> 'Taxon complet',

	// Edition page
	'LMDI_CONFIG_TAGS'	=> 'Vous pouvez ci-dessous éditer la balise d’indexation.<br />Si vous saisissez une chaîne de balise qui existe déjà, les deux balises sont fusionnées.<br />Cela constitue un moyen de regrouper plusieurs balises mal écrites.<br /><br />',
	'LMDI_NEW_TAG_NAME'	=> 'Zone d’édition de la balise',

	// Deletion page
	'TOPICTAGS_DEL_TAGS'	=> 'Vous avez demandé la suppression de la balise « %s ».<br />Êtes-vous certain ?<br />Cette opération ne peut pas être annulée par la suite.<br /><br />',

	// Search & replacement page
	'TAG_REPLACEMENT'		=> 'Remplacement de genres et suppression de balises',
	'URL_REMP_PAGE'		=> 'Pour la %s — <a href="%s">Cliquez ici</a>',
	'REMP_PAGE'			=> 'Page de remplacement de genres et de suppression de balises',
	'REMP_PAGE_EXPLAIN'		=> 'Cette page est destinée à rechercher et remplacer des noms de genre placés entre crochets carrés dans le titre du sujet et à supprimer les balises de genre correspondantes (ainsi que les balises d’espèce qui en dépendent).<br>
	Exemple&nbsp;: Saisissez « <i>Protaetia</i> » dans la zone de recherche et « <i>Netocia</i> » dans la zone de remplacement. Le formulaire ajoute automatiquement le crochet carré initial. La fonction de suppression porte sur toutes les balises <i>Protaetia</i>, donc <i>P.</i> sp, etc. Les balises seront reposées lors de la passe suivante de balisage automatique, en se fondant sur les nouvelles données taxonomiques.',
	'GENUS_SEARCH'			=> 'Genre placé dans le titre',
	'GENUS_SEARCH_EXPLAIN'	=> 'Genre suivant immédiatement le crochet ouvrant dans le titre du sujet. Vous pouvez inclure le nom du sous-genre entre parenthèses s’il est présent.',
	'GENUS_REMP'			=> 'Genre de remplacement',
	'GENUS_REMP_EXPLAIN'	=> 'Genre qui remplacera dans le titre du sujet.',
	'TAGREMP_ERROR'		=> 'Erreur dans la saisie des paramètres <i>%s</i> et <i>%s</i>.',
	'TAGREMP_OK'			=> 'Le genre <i>%s</i> a bien été remplacé par <i>%s</i>.',


));
