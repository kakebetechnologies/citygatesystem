<?php
/**
 * Tasks & Schedule — feeding/vaccination/cleaning/repair schedules,
 * staff-assigned, sorted server-side into Overdue / Upcoming / Completed
 * using the database's CURDATE() so client clocks can't be trusted.
 *
 * ob_start(): the shared header.php streams its nav HTML immediately, and
 * this environment's php.ini has output_buffering=4096 — the nav alone
 * exceeds that, so PHP auto-flushes before our later header('Location')
 * redirects run, breaking the PRG pattern with a "headers already sent"
 * warning. Wrapping this page's own output in an explicit buffer (which,
 * unlike the implicit one, only flushes at script end) sidesteps it
 * without touching the shared header.php.
 */
ob_start();
$pageModule = 'tasks';
$pageTitle = 'Tasks & Schedule';
$pageSub = 'Assign, track, and complete farm tasks';
require_once __DIR__ . '/includes/header.php';

$canEdit = cg_can('tasks', 'full');
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'save') {
      $title = trim($_POST['title'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $category = trim($_POST['category'] ?? '');
      $dueDate = $_POST['due_date'] ?? '';
      $dueTime = trim($_POST['due_time'] ?? '');
      $assignedTo = (int) ($_POST['assigned_to'] ?? 0);
      $recurrence = trim($_POST['recurrence_label'] ?? '');
      $livestockId = (int) ($_POST['livestock_id'] ?? 0);

      if ($title === '') $errors[] = 'Title is required.';
      if ($dueDate === '' || !DateTime::createFromFormat('Y-m-d', $dueDate)) $errors[] = 'A valid due date is required.';

      if (!$errors) {
         $stmt = $pdo->prepare('INSERT INTO tasks (title, description, category, due_date, due_time, assigned_to, recurrence_label, livestock_id, created_by) VALUES (?,?,?,?,?,?,?,?,?)');
         $stmt->execute([
            $title,
            $description ?: null,
            $category ?: null,
            $dueDate,
            $dueTime !== '' ? $dueTime : null,
            $assignedTo > 0 ? $assignedTo : null,
            $recurrence ?: null,
            $livestockId > 0 ? $livestockId : null,
            $user['id'],
         ]);
         header('Location: tasks.php?saved=1');
         exit;
      }
   } elseif ($action === 'complete') {
      $id = (int) ($_POST['id'] ?? 0);
      $pdo->prepare("UPDATE tasks SET status='Done', completed_at=NOW() WHERE id=?")->execute([$id]);
      header('Location: tasks.php?completed=1');
      exit;
   } elseif ($action === 'delete') {
      $id = (int) ($_POST['id'] ?? 0);
      $pdo->prepare('DELETE FROM tasks WHERE id=?')->execute([$id]);
      header('Location: tasks.php?deleted=1');
      exit;
   }
}

if (isset($_GET['saved'])) $message = 'Task added.';
if (isset($_GET['completed'])) $message = 'Task marked done.';
if (isset($_GET['deleted'])) $message = 'Task deleted.';

$users = $pdo->query("SELECT id, name, role FROM users WHERE status='active' ORDER BY name")->fetchAll();
$livestock = $pdo->query("SELECT id, identifier FROM livestock_records WHERE status='Active' ORDER BY identifier")->fetchAll();

$taskSql = "SELECT t.*, u.name AS assigned_name, l.identifier AS livestock_identifier
   FROM tasks t
   LEFT JOIN users u ON t.assigned_to = u.id
   LEFT JOIN livestock_records l ON t.livestock_id = l.id";

$overdue = $pdo->query("$taskSql WHERE t.status='Pending' AND t.due_date < CURDATE() ORDER BY t.due_date ASC, t.due_time ASC")->fetchAll();
$upcoming = $pdo->query("$taskSql WHERE t.status='Pending' AND t.due_date >= CURDATE() ORDER BY t.due_date ASC, t.due_time ASC")->fetchAll();
$completed = $pdo->query("$taskSql WHERE t.status='Done' ORDER BY t.completed_at DESC, t.due_date DESC")->fetchAll();

$today = date('Y-m-d');

function cg_task_row(array $t, bool $canEdit, string $badgeClass, string $badgeLabel, bool $showDone): void {
   echo '<tr>';
   echo '<td><strong>' . htmlspecialchars($t['title']) . '</strong>';
   if (!empty($t['description'])) echo '<div class="text-muted" style="font-size:12px;">' . htmlspecialchars($t['description']) . '</div>';
   echo '</td>';
   echo '<td>' . htmlspecialchars($t['category'] ?: '—') . '</td>';
   echo '<td>' . htmlspecialchars($t['due_date']) . ($t['due_time'] ? ' ' . htmlspecialchars(substr($t['due_time'], 0, 5)) : '') . '</td>';
   echo '<td>' . htmlspecialchars($t['assigned_name'] ?: '—') . '</td>';
   echo '<td>' . htmlspecialchars($t['recurrence_label'] ?: '—') . '</td>';
   if (!empty($t['livestock_id'])) {
      echo '<td><a href="livestock.php?id=' . (int) $t['livestock_id'] . '">' . htmlspecialchars($t['livestock_identifier'] ?: ('#' . $t['livestock_id'])) . '</a></td>';
   } else {
      echo '<td>—</td>';
   }
   echo '<td><span class="cg-badge ' . $badgeClass . '">' . htmlspecialchars($badgeLabel) . '</span></td>';
   if ($canEdit) {
      echo '<td class="text-nowrap">';
      if ($showDone) {
         echo '<form method="post" style="display:inline">'
            . cg_csrf_field()
            . '<input type="hidden" name="action" value="complete">'
            . '<input type="hidden" name="id" value="' . (int) $t['id'] . '">'
            . '<button type="submit" class="cg-btn cg-btn-primary cg-btn-sm">Mark Done</button>'
            . '</form> ';
      }
      echo '<form method="post" style="display:inline" onsubmit="return confirm(\'Delete this task?\');">'
         . cg_csrf_field()
         . '<input type="hidden" name="action" value="delete">'
         . '<input type="hidden" name="id" value="' . (int) $t['id'] . '">'
         . '<button type="submit" class="cg-btn cg-btn-danger cg-btn-sm">Delete</button>'
         . '</form>';
      echo '</td>';
   }
   echo '</tr>';
}
?>

<?php if ($message): ?>
<div class="alert alert-success" style="border-radius:10px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger" style="border-radius:10px;">
   <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
</div>
<?php endif; ?>

<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon rose"><i class="fa fa-exclamation-triangle"></i></div><div><div class="cg-stat-card-num"><?php echo count($overdue); ?></div><div class="cg-stat-card-label">Overdue</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon gold"><i class="fa fa-clock-o"></i></div><div><div class="cg-stat-card-num"><?php echo count($upcoming); ?></div><div class="cg-stat-card-label">Upcoming</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon"><i class="fa fa-check-square-o"></i></div><div><div class="cg-stat-card-num"><?php echo count($completed); ?></div><div class="cg-stat-card-label">Completed</div></div></div>
</div>

<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2>Add Task</h2></div>
   <form method="post" class="row g-3">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <div class="col-md-4">
         <label class="form-label">Title</label>
         <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
      </div>
      <div class="col-md-4">
         <label class="form-label">Description</label>
         <textarea name="description" class="form-control" rows="1"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
      </div>
      <div class="col-md-2">
         <label class="form-label">Category</label>
         <input type="text" name="category" class="form-control" placeholder="e.g. Feeding, Vaccination, Cleaning, Repairs" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
         <label class="form-label">Due Date</label>
         <input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($_POST['due_date'] ?? $today); ?>" required>
      </div>
      <div class="col-md-2">
         <label class="form-label">Due Time</label>
         <input type="time" name="due_time" class="form-control" value="<?php echo htmlspecialchars($_POST['due_time'] ?? ''); ?>">
      </div>
      <div class="col-md-3">
         <label class="form-label">Assigned To</label>
         <select name="assigned_to" class="form-select">
            <option value="0">— Unassigned —</option>
            <?php foreach ($users as $u): ?>
            <option value="<?php echo (int) $u['id']; ?>" <?php echo ((int) ($_POST['assigned_to'] ?? 0) === (int) $u['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['role']); ?>)</option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-md-3">
         <label class="form-label">Recurrence Label</label>
         <input type="text" name="recurrence_label" class="form-control" placeholder="e.g. Daily, Weekly, Quarterly" value="<?php echo htmlspecialchars($_POST['recurrence_label'] ?? ''); ?>">
      </div>
      <div class="col-md-4">
         <label class="form-label">Linked Livestock</label>
         <select name="livestock_id" class="form-select">
            <option value="0">— None —</option>
            <?php foreach ($livestock as $l): ?>
            <option value="<?php echo (int) $l['id']; ?>" <?php echo ((int) ($_POST['livestock_id'] ?? 0) === (int) $l['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($l['identifier']); ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
         <button type="submit" class="cg-btn cg-btn-primary w-100"><i class="fa fa-plus"></i> Add Task</button>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head"><h2 style="color:#A32E2E;">Overdue</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Task</th><th>Category</th><th>Due</th><th>Assigned To</th><th>Recurrence</th><th>Livestock</th><th>Status</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
         <tbody>
         <?php if (!$overdue): ?>
            <tr><td colspan="8" class="text-muted">No overdue tasks.</td></tr>
         <?php else: foreach ($overdue as $t) cg_task_row($t, $canEdit, 'cg-badge-rejected', 'Overdue', true); endif; ?>
         </tbody>
      </table>
   </div>
</div>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>Upcoming</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Task</th><th>Category</th><th>Due</th><th>Assigned To</th><th>Recurrence</th><th>Livestock</th><th>Status</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
         <tbody>
         <?php if (!$upcoming): ?>
            <tr><td colspan="8" class="text-muted">No upcoming tasks.</td></tr>
         <?php else: foreach ($upcoming as $t) cg_task_row($t, $canEdit, 'cg-badge-pending', 'Pending', true); endif; ?>
         </tbody>
      </table>
   </div>
</div>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>Completed</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Task</th><th>Category</th><th>Due</th><th>Assigned To</th><th>Recurrence</th><th>Livestock</th><th>Status</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
         <tbody>
         <?php if (!$completed): ?>
            <tr><td colspan="8" class="text-muted">No completed tasks yet.</td></tr>
         <?php else: foreach ($completed as $t) cg_task_row($t, $canEdit, 'cg-badge-approved', 'Done', false); endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
