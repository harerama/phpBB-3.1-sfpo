<?php
/**
*
* @package Show first post only to guest
* @copyright (c) 2016 Rich McGirr (RMcGirr83)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace rmcgirr83\sfpo\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	public function __construct(
		\phpbb\content_visibility $content_visibility,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user)
	{
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			// ACP activities
			'core.acp_manage_forums_request_data'		=> 'acp_manage_forums_request_data',
			'core.acp_manage_forums_initialise_data'	=> 'acp_manage_forums_initialise_data',
			'core.acp_manage_forums_display_form'		=> 'acp_manage_forums_display_form',
			// Viewing a topic
			'core.viewtopic_assign_template_vars_before'	=>	'viewtopic_assign_template_vars_before',
			'core.viewtopic_get_post_data'			=> 'viewtopic_get_post_data',
		);
	}

	// Submit form (add/update)
	public function acp_manage_forums_request_data($event)
	{
		$sfpo_array = $event['forum_data'];
		$sfpo_array['sfpo_guest_enable'] = $this->request->variable('sfpo_guest_enable', 0);
		$event['forum_data'] = $sfpo_array;
	}

	// Default settings for new forums
	public function acp_manage_forums_initialise_data($event)
	{
		if ($event['action'] == 'add')
		{
			$sfpo_array = $event['forum_data'];
			$sfpo_array['sfpo_guest_enable'] = (int) 0;
			$event['forum_data'] = $sfpo_array;
		}
	}

	// ACP forums template output
	public function acp_manage_forums_display_form($event)
	{
		$sfpo_array = $event['template_data'];
		$sfpo_array['S_SFPO_GUEST_ENABLE'] = $event['forum_data']['sfpo_guest_enable'];
		$event['template_data'] = $sfpo_array;
	}

	/**
	* Forum check
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function viewtopic_assign_template_vars_before($event)
	{
		$topic_data = $event['topic_data'];
		$post_id = $event['post_id'];
		$start = $event['start'];
		$total_posts = $event['total_posts'];
		$s_sfpo = (!empty($topic_data['sfpo_guest_enable']) && ($this->user->data['user_id'] == ANONYMOUS));

		if ($s_sfpo)
		{
			$topic_data['prev_posts'] = $start = 0;
			$total_posts = 1;
			$post_id == $topic_data['topic_first_post_id'];
		}

		$event['total_posts'] = $total_posts;
		$event['topic_data'] = $topic_data;
		$event['post_id'] = $post_id;
		$event['start'] = $start;
	}

	/**
	* Forum check
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function viewtopic_get_post_data($event)
	{
		$topic_data = $event['topic_data'];
		$sql_ary = $event['sql_ary'];
		$s_sfpo = (!empty($topic_data['sfpo_guest_enable']) && ($this->user->data['user_id'] == ANONYMOUS));

		if ($s_sfpo)
		{
			$this->user->add_lang_ext('rmcgirr83/sfpo', 'common');
			$post_list = (int) $topic_data['topic_first_post_id'];
			$sql_ary['WHERE'] = $this->db->sql_in_set('p.post_id', $post_list) . ' AND u.user_id = p.poster_id';

			$topic_replies = $this->content_visibility->get_count('topic_posts', $topic_data, $event['forum_id']) - 1;
			$this->template->assign_vars(array(
				'S_SFPO'	=> true,
				'SFPO_MESSAGE'	=> $this->user->lang('SFPO_MSG_REPLY', $topic_replies), 
			));
		}

		$event['sql_ary'] = $sql_ary;
	}
}