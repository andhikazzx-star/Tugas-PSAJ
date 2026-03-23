<?php
/**
 * Root Redirect / Loader for e-Rapor Sisipan
 * Redirects to the /public folder if mod_rewrite is unavailable.
 */
header("Location: public/");
exit;
