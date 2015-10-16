<?php

namespace ElggProbation;

function event_enable_user($event, $type, \ElggUser $user) {
	$age = time() - $user->time_created;
	if ($age < 86400 * 14) {
		// a user being enabled within the first couple weeks is probably an email confirmation.
		add_probation($user);
	}
}

// mark new objects by those on probation
function event_create_object($event, $type, \ElggObject $object) {
	if (!is_on_probation($object->getOwnerEntity())) {
		return true;
	}

	$object->{QUARANTINED} = '1';

	if (elgg_get_plugin_setting(QUARANTINE_PRIVATE, PLUGIN_ID)) {
		system_message(elgg_echo('probation:moderated:private'));
	} else {
		system_message(elgg_echo('probation:moderated'));
	}

	State::$entities_quarantined[$object->guid] = $object;
}

function event_update_object($event, $type, \ElggObject $object) {
	if (!State::$handle_updates || !$object->{QUARANTINED}) {
		return;
	}

	State::$entities_quarantined[$object->guid] = $object;
}

function event_shutdown() {
	State::$handle_updates = false;

	if (State::$users_leaving_probation) {
		set_time_limit(0);
	}

	$ia = elgg_set_ignore_access(true);

	$force_private = elgg_get_plugin_setting(QUARANTINE_PRIVATE, PLUGIN_ID);

	foreach (State::$entities_quarantined as $entity) {
		if ($entity->{ORIGINAL_ACCESS_ID} === null || ($entity->access_id != ACCESS_PRIVATE)) {
			$entity->{ORIGINAL_ACCESS_ID} = $entity->access_id;
		}

		if ($force_private) {
			$entity->access_id = ACCESS_PRIVATE;
		} elseif ($entity->access_id == ACCESS_PUBLIC) {
			$entity->access_id = ACCESS_LOGGED_IN;
		}

		$entity->save();
	}

	foreach (State::$users_leaving_probation as $user) {
		$options = [
			'owner_guid' => $user->guid,
			'metadata_name' => QUARANTINED,
			'metadata_value' => '1',
			'limit' => 0,
		];

		$batch = new \ElggBatch('elgg_get_entities_from_metadata', $options, null, 25, false);
		foreach ($batch as $object) {
			/* @var \ElggObject $object */
			approve_content($object);
		}
	}

	elgg_set_ignore_access($ia);
}
