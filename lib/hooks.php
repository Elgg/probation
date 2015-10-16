<?php

namespace ElggProbation;

use \Elgg\Notifications\Notification;
use UFCOE\Elgg\MenuList;

function hook_register_hover_menu($hook, $type, $items, $params) {
	$list = new MenuList($items);

	if (is_on_probation()) {
		$list->remove('send');
	}
	if (elgg_is_admin_logged_in()) {
		$user = elgg_extract('entity', $params);
		if (is_on_probation($user)) {
			$item = \ElggMenuItem::factory([
				'name' => 'probation_remove',
				'text' => elgg_echo('probation:remove_probation'),
				'href' => elgg_add_action_tokens_to_url("action/probation/approve_user?guid={$user->guid}"),
				'section' => 'admin',
			]);
			$list->push($item);
		}
	}

	return $list->getItems();
}

/**
 * @see elgg_send_email
 *
 * @todo hold notifications until approved...
 */
function hook_email_system($hook, $type, $value, $params) {
	// cancel email if sender in on probation
	$email_params = $params['params'];
	$notification = elgg_extract('notification', $email_params);
	if (!$notification instanceof Notification) {
		return;
	}

	$sender = $notification->getSender();
	if (!$sender instanceof \ElggUser) {
		return;
	}

	if (is_on_probation($sender)) {
		// lie that the notification was sent so other handlers won't try
		return true;
	}
}

/**
 * @see \Elgg\Notifications\NotificationsService::enqueueEvent
 *
 * @todo hold notifications until approved...
 */
function hook_enqueue_notification($hook, $type, $value, $params) {
	// don't enqueue notification event if object is probationary
	$object = elgg_extract('object', $params);
	if (!$object instanceof \ElggObject) {
		return;
	}

	if ($object->{QUARANTINED}) {
		return false;
	}
}

function hook_register_menu_topbar($hook, $type, $items, $params) {
	$list = new MenuList($items);
	$list->remove('messages');
	return $list->getItems();
}

function hook_prepare_entity_menu($hook, $type, $menus, $params) {
	$entity = elgg_extract('entity', $params);
	if (!$entity->{QUARANTINED}) {
		return;
	}

	$list = new MenuList($menus['default']);

	$list->move(\ElggMenuItem::factory([
		'name' => 'probation_approve_content',
		'href' => elgg_add_action_tokens_to_url("action/probation/approve_content?guid={$entity->guid}"),
		'text' => 'Approve (probation)',
		'title' => 'Restore this content to its desired access level',
	]), 0);

	$list->remove('access');

	$menus['default'] = $list->getItems();
	return $menus;
}

function hook_register_title_menu($hook, $type, $items, $params) {
	foreach ($items as $key => $item) {
		/* @var \ElggMenuItem $item */
		$href = (string)$item->getHref();
		if (false !== strpos($href, '/groups/add/')) {
			unset($items[$key]);
		}
	}
	return $items;
}
