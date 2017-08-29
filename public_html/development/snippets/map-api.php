<?php
$api_key = 'AIzaSyDciT6LoYZtEEnSduTCkwdIjEwrInivTdI';
?>
<script src="//maps.googleapis.com/maps/api/js?key=<?=$api_key?>&libraries=places&callback=<?=isset($mapCallback) ? $mapCallback : 'void'?>" async defer></script>
