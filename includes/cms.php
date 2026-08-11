<?php
/**
 * Server-side CMS content helper. Reads editable text set via
 * admin/cms.php from the cms_content table, with a fallback default when
 * nothing's been saved yet — no client-side JS/flash-of-original-content
 * needed since this renders directly into the page on the server.
 * Requires config/db.php ($pdo) to already be included.
 */
function cg_cms_all(PDO $pdo, string $page): array {
   static $cache = [];
   if (isset($cache[$page])) return $cache[$page];

   $stmt = $pdo->prepare('SELECT field_key, value FROM cms_content WHERE page_key = ?');
   $stmt->execute([$page]);
   $rows = [];
   foreach ($stmt->fetchAll() as $row) {
      $rows[$row['field_key']] = $row['value'];
   }
   $cache[$page] = $rows;
   return $rows;
}

function cg_cms(PDO $pdo, string $page, string $field, string $default = ''): string {
   $rows = cg_cms_all($pdo, $page);
   $value = $rows[$field] ?? '';
   return $value !== '' ? $value : $default;
}
