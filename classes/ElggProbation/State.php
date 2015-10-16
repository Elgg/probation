<?php

namespace ElggProbation;

class State {
	/**
	 * @var \ElggEntity[] keys are GUID (for uniqueness)
	 */
	static $entities_to_keep_private = [];

	/**
	 * @var \ElggUser[] keys are GUID (for uniqueness)
	 */
	static $users_leaving_probation = [];

	static $handle_updates = true;
}
