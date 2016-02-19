<?php
/**
*
* @package Show first post only to guest
* @copyright (c) 2016 Rich McGirr (RMcGirr83)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace rmcgirr83\sfpo\migrations;

class sfpo_v3 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\rmcgirr83\sfpo\migrations\sfpo_v2');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'forums'	=> array(
					'sfpo_guest_enable_timeout'	=> array('UINT', 0),
				),
			),
		);
	}
}
