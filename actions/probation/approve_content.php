<?php

$object = get_entity(get_input('guid'));
if (!$object) {
	register_error('No GUID');
	forward();
}
if (!($object instanceof ElggObject) || !$object->probationary) {
	register_error('The entity is not held in probation.');
	forward();
}

\ElggProbation\approve_content($object);
forward(REFERER);
