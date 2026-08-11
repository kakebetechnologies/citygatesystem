<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'blog';
$pageTitle = 'Blog Manager';
$pageSub = 'Create, edit and publish farm blog posts';
require_once __DIR__ . '/includes/header.php'; // gives $pdo, enforces RBAC, blocks Finance

$canFull = cg_can('blog', 'full');

// Handle delete (POST, CSRF-protected)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
   cg_verify_csrf();
   if (!$canFull) {
      http_response_code(403);
      die('You do not have permission to delete blog posts.');
   }
   $deleteId = (int) ($_POST['id'] ?? 0);
   if ($deleteId > 0) {
      $stmt = $pdo->prepare('DELETE FROM blog_posts WHERE id = ?');
      $stmt->execute([$deleteId]);
   }
   header('Location: blog-manager.php');
   exit;
}

$posts = $pdo->query("
   SELECT bp.*, u.name AS author_name
   FROM blog_posts bp
   LEFT JOIN users u ON u.id = bp.author_id
   ORDER BY bp.created_at DESC
")->fetchAll();

function cg_status_badge(string $status): string {
   $map = ['published' => 'published', 'draft' => 'draft', 'scheduled' => 'scheduled'];
   $cls = $map[$status] ?? 'draft';
   return '<span class="cg-badge cg-badge-' . $cls . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
}
?>
<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Posts (<?php echo count($posts); ?>)</h2>
      <?php if ($canFull): ?>
      <a href="blog-editor.php" class="cg-btn cg-btn-primary cg-btn-sm"><i class="fa fa-plus"></i> New Post</a>
      <?php endif; ?>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead>
            <tr>
               <th>Title</th>
               <th>Category</th>
               <th>Author</th>
               <th>Status</th>
               <th>Publish Date</th>
               <?php if ($canFull): ?><th></th><?php endif; ?>
            </tr>
         </thead>
         <tbody>
         <?php if (!$posts): ?>
            <tr><td colspan="6" class="text-muted">No blog posts yet.</td></tr>
         <?php else: foreach ($posts as $p): ?>
            <tr>
               <td><?php echo htmlspecialchars($p['title']); ?></td>
               <td><?php echo htmlspecialchars($p['category'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($p['author_name'] ?: 'City Gate Mixed Farm'); ?></td>
               <td><?php echo cg_status_badge($p['status']); ?></td>
               <td><?php echo htmlspecialchars($p['publish_date'] ?: '—'); ?></td>
               <?php if ($canFull): ?>
               <td style="white-space:nowrap;">
                  <a href="blog-editor.php?id=<?php echo (int) $p['id']; ?>" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                  <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete this post permanently? This will also remove its gallery images and comments.');">
                     <?php echo cg_csrf_field(); ?>
                     <input type="hidden" name="action" value="delete">
                     <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                     <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm"><i class="fa fa-trash"></i> Delete</button>
                  </form>
               </td>
               <?php endif; ?>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
