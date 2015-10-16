<?php

namespace ElggProbation;

$users = elgg_list_entities_from_metadata([
	'type' => 'user',
	'metadata_name' => ON_PROBATION,
	'metadata_value' => '1',
	'limit' => 50,
]);

?>
<div class="elgg-module elgg-module-inline">
	<div class="elgg-body">
		<?= $users ?>
	</div>
</div>
