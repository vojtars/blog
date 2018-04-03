<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require __DIR__ . '/.maintenance.php';

// absolute filesystem path to this web root
define('WWW_DIR', __DIR__);

//abslolute path to webtemp
define('EMAIL_TEMPLATES_DIR', WWW_DIR . '/../app/templates/emails/');
define('IMG_UPLOAD_DIR', WWW_DIR . '/img/upload/');
define('IMG_UPLOAD_TEMP_DIR', WWW_DIR . '/img/upload/temp/');
define('IMG_GALLERY_DIR', WWW_DIR . '/img/upload/gallery/');
define('IMG_DIR', WWW_DIR . '/img/');
define('FILES_DIR', WWW_DIR . '/files/');




$container = require __DIR__ . '/../app/bootstrap.php';

$container->getByType(Nette\Application\Application::class)->run();
