<?php

$plugin->deleteDbTable(array('NutsGroup', 'NutsUser'), array('groupNotSuperAdmin','mustBeEmpty')); # restricted mod
$plugin->deleteRender();

?>