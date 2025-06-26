<?php
/**
 * MGW Hide Content - Bezpośredni dostęp do modułu ACP
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Ten plik to alternatywny sposób dostępu do panelu MGW Hide Content
 * Użyj: http://twoja-domena.pl/admin/mgw_hide_direct.php
 */

// Redirect to main ACP module
header("Location: index.php?module=config-mgw_hide");
exit;
?> 