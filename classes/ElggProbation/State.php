<?php

namespace ElggProbation;

class State {
	/**
	 * @var \ElggEntity[] keys are GUID (for uniqueness)
	 */
	static $entities_quarantined = [];

	/**
	 * @var \ElggUser[] keys are GUID (for uniqueness)
	 */
	static $users_leaving_probation = [];

	static $handle_updates = true;
}
