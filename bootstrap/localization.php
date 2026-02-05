<?php
// localization.php

use App\Core\Localization;

// Load default locale
Localization::load(TRANSLATIONS_PATH, $_ENV['APP_LOCALE'] ?? 'en-us');