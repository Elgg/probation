<?php

namespace ElggProbation;

const PLUGIN_ID = 'probation';
const QUARANTINED = 'quarantined';
const ON_PROBATION = 'on_probation';
const ORIGINAL_ACCESS_ID = 'original_access_id';

const QUARANTINE_CONTENT = 'quarantine_content';
const QUARANTINE_PRIVATE = 'quarantine_private';

// testing
const FORCE_PROBATION = true;

require __DIR__ . '/lib/events.php';
require __DIR__ . '/lib/hooks.php';
require __DIR__ . '/lib/functions.php';

// run late in init event
function init() {
	if (is_on_probation()) {
		// don't allow sending messages
		elgg_unregister_action('messages/send');
		elgg_register_plugin_hook_handler('register', 'menu:topbar', __NAMESPACE__ . '\\hook_register_menu_topbar', 999);

		// don't allow group creation
		elgg_unregister_action('groups/edit');
		elgg_register_plugin_hook_handler('register', 'menu:title', __NAMESPACE__ . '\\hook_register_title_menu', 999);

		// mark content as probationary and make it private.
		if (elgg_get_plugin_setting(QUARANTINE_CONTENT, PLUGIN_ID)) {
			elgg_register_event_handler('create', 'object', __NAMESPACE__ . '\\event_create_object');
			elgg_register_event_handler('update', 'object', __NAMESPACE__ . '\\event_update_object');
		}
	}

	if (elgg_is_admin_logged_in()) {
		elgg_register_admin_menu_item('administer', 'probation', 'users');

		// Add approval links to entity menus
		elgg_register_plugin_hook_handler('prepare', 'menu:entity', __NAMESPACE__ . '\\hook_prepare_entity_menu', 999);
	}

	// show message above user profile
	elgg_extend_view('profile/details', 'probation/profile_details_400', 400);

	elgg_register_action('probation/approve_content', __DIR__ . '/actions/probation/approve_content.php', 'admin');
	elgg_register_action('probation/approve_user', __DIR__ . '/actions/probation/approve_user.php', 'admin');

	// hiding "Send message" or adding "Remove probation"
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\hook_register_hover_menu', 999);

	// put all newly-enabled users on probation
	elgg_register_event_handler('enable', 'user', __NAMESPACE__ . '\\event_enable_user');

	// we don't enqueue notification events for probationary content
	elgg_register_plugin_hook_handler('enqueue', 'notification', __NAMESPACE__ . '\\hook_enqueue_notification');
	// nor do we allow notify_user() for comments/replies
	elgg_register_plugin_hook_handler('email', 'system', __NAMESPACE__ . '\\hook_email_system', 1);

	elgg_register_event_handler('shutdown', 'system', __NAMESPACE__ . '\\event_shutdown');
}

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init', 999);
