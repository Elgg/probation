<?php

if (!elgg_is_admin_logged_in()) {
	return;
}

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser || !\ElggProbation\is_on_probation($user)) {
	return;
}

if (elgg_get_logged_in_user_guid() == $user->guid) {
	$key = 'probation:on_probation:self';
} else {
	$key = 'probation:on_probation';
}

if (elgg_is_admin_logged_in()) {
	$link = elgg_view('output/url', [
		'text' => elgg_echo('probation:remove_probation'),
		'href' => "action/probation/approve_user?guid={$user->guid}",
		'is_action' => true,
		'confirm' => true,
	]);
} else {
	$link = "";
}

?>
<div class="elgg-body pal" style="color:#999">
	<p><?= elgg_echo($key); ?> <?= $link ?></p>
</div>
