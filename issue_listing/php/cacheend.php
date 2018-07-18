<?php

$c = ob_get_contents();
file_put_contents($cachefile,$c);

?>