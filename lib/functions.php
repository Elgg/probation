<?php

namespace ElggProbation;

function is_on_probation($user = null) {
	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}
	if (!$user) {
		return false;
	}
	if (FORCE_PROBATION) {
		return true;
	}
	return $user->{ON_PROBATION};
}

function add_probation(\ElggUser $user) {
	$user->{ON_PROBATION} = '1';
}

function remove_probation(\ElggUser $user) {
	$user->deleteMetadata(ON_PROBATION);

	State::$users_leaving_probation[$user->guid] = $user;
}

function approve_content(\ElggObject $object) {
	$object->deleteMetadata(QUARANTINED);

	$access_id = $object->{ORIGINAL_ACCESS_ID};
	// delete it so our update handler doesn't re-set it
	$object->deleteMetadata(ORIGINAL_ACCESS_ID);

	if ($access_id !== null) {
		$object->access_id = (int)$access_id;

		$handle = State::$handle_updates;
		State::$handle_updates = false;
		$object->save();
		State::$handle_updates = $handle;
	} else {
		$access_id = $object->access_id;
	}

	// just in case plugins look at river.access_id
	update_river_access_by_object($object->guid, $access_id);
}
