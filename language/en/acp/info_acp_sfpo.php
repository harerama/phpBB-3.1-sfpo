<?php
/**
*
* @package Show first post only to guest
* @copyright (c) 2016 Rich McGirr (RMcGirr83)
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

$lang = array_merge($lang, array(
	// ACP
	'ENABLE_SFPO' 			=> 'Enable show first post only to guest',
	'ENABLE_SFPO_EXPLAIN' 	=> 'If set to yes unregistered users / guests are able to view only the first post of any topic. The rest of the posts in the topic will ask them to login or register.',
	'ENABLE_SFPO_TIMEOUT' 			=> 'SFPO trigger timeout',
	'ENABLE_SFPO_TIMEOUT_EXPLAIN' 	=> 'Allowed time in seconds of regular access - SFPO will only be enabled after this session time (in seconds).',
	'SFPO_CHARACTERS'		=> 'Number of characters to display',
	'SFPO_CHARACTERS_EXPLAIN'	=> 'Enter the number of characters to display for the first topic (default is 150). Setting the value to 0 disables this feature.',
	'SFPO_CHARS'			=> 'Characters',
));
