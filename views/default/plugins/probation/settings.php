<?php

namespace ElggProbation;

$plugin = $vars['entity'];
/* @var \ElggPlugin $plugin */

?>

<div>
	<?= elgg_view("input/checkbox", [
		'label' => 'Quarantine content by users on probation',
		'name' => 'params[' . QUARANTINE_CONTENT . ']',
		'checked' => (bool)$plugin->{QUARANTINE_CONTENT},
		'default' => '',
	]) ?>
	<p class="elgg-text-help">New objects are not allowed to be public, and will be monitored until it (or its owner) is approved.</p>

	<div class="mll">
		<?= elgg_view("input/checkbox", [
			'label' => 'Completely hide quarantined content from other users',
			'name' => 'params[' . QUARANTINE_PRIVATE . ']',
			'checked' => (bool)$plugin->{QUARANTINE_PRIVATE},
			'default' => '',
		]) ?>
	</div>
</div>
