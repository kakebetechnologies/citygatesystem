<?php
/**
 * Streams the (optionally filtered) expenses list as a CSV file — opens
 * directly in Excel with real numeric amounts (not "UGX"-prefixed strings)
 * so totals/sums work natively, plus a computed TOTAL row.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
cg_require_module('expenses', 'view');

const CG_EXPENSE_CATEGORIES_EXPORT = ['Feed', 'Labor', 'Veterinary', 'Utilities', 'Repairs', 'Transport', 'Other'];

$categoryFilter = $_GET['category'] ?? '';
$supplierFilter = (int) ($_GET['supplier_id'] ?? 0);
$fromFilter = $_GET['from'] ?? '';
$toFilter = $_GET['to'] ?? '';
$searchFilter = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if (in_array($categoryFilter, CG_EXPENSE_CATEGORIES_EXPORT, true)) { $where[] = 'e.category = ?'; $params[] = $categoryFilter; }
if ($supplierFilter > 0) { $where[] = 'e.supplier_id = ?'; $params[] = $supplierFilter; }
if ($fromFilter !== '' && DateTime::createFromFormat('Y-m-d', $fromFilter)) { $where[] = 'e.expense_date >= ?'; $params[] = $fromFilter; }
if ($toFilter !== '' && DateTime::createFromFormat('Y-m-d', $toFilter)) { $where[] = 'e.expense_date <= ?'; $params[] = $toFilter; }
if ($searchFilter !== '') { $where[] = 'e.description LIKE ?'; $params[] = '%' . $searchFilter . '%'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT e.*, s.name AS supplier_name FROM expenses e LEFT JOIN suppliers_buyers s ON e.supplier_id = s.id $whereSql ORDER BY e.expense_date DESC, e.id DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$filename = 'City-Gate-Expenses-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel renders é/– etc. correctly
fputcsv($out, ['Date', 'Category', 'Description', 'Amount (UGX)', 'Supplier']);

$total = 0.0;
foreach ($rows as $r) {
   fputcsv($out, [$r['expense_date'], $r['category'], $r['description'], (float) $r['amount'], $r['supplier_name'] ?: '']);
   $total += (float) $r['amount'];
}
fputcsv($out, []);
fputcsv($out, ['', '', '', $total, 'TOTAL (' . count($rows) . ' records)']);

fclose($out);
exit;
