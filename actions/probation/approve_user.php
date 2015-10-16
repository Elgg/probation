<?php

$user = get_user(get_input('guid'));
if (!$user) {
	register_error('No user');
	forward();
}
if (!\ElggProbation\is_on_probation($user)) {
	register_error('The user is not on probation.');
	forward();
}

\ElggProbation\remove_probation($user);

system_message(elgg_echo('probation:removed_probation'));
forward(REFERER);
