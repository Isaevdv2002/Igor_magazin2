<?php

if (basename($_SERVER['PHP_SELF']) === 'data.json') {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}

?>