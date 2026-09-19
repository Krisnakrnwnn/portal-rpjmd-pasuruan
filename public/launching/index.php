<?php

// Keep /launching reachable when PHP's built-in server treats this asset
// directory as a static route before applying the Laravel front controller.
require_once __DIR__.'/../index.php';
