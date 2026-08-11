<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"

// Print report view is its own standalone document (no sidebar/nav), so it
// must be handled BEFORE admin/includes/header.php renders any chrome.
if (isset($_GET['print'])) {
   require_once __DIR__ . '/sales-print.php';
   exit;
}

$pageModule = 'sales';
$pageTitle = 'Sales & Invoicing';
$pageSub = 'Record sales and generate invoices & receipts';
require_once __DIR__ . '/includes/header.php';

$canFull = cg_can('sales', 'full');
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_sale', 'edit_sale'], true)) {
   cg_verify_csrf();
   if (!$canFull) {
      http_response_code(403);
      die('You do not have permission to record sales.');
   }

   $editId = (int) ($_POST['id'] ?? 0);
   $product = trim($_POST['product'] ?? '');
   $quantity = $_POST['quantity'] ?? '';
   $unit = trim($_POST['unit'] ?? '');
   $buyer = trim($_POST['buyer'] ?? '');
   $buyerPhone = trim($_POST['buyer_phone'] ?? '');
   $amount = $_POST['amount'] ?? '';
   $saleDate = $_POST['sale_date'] ?? '';
   $status = $_POST['status'] ?? 'Unpaid';

   if ($product === '') $errors[] = 'Product is required.';
   if ($quantity === '' || !is_numeric($quantity) || (float)$quantity <= 0) $errors[] = 'Quantity must be a positive number.';
   if ($unit === '') $errors[] = 'Unit is required.';
   if ($buyer === '') $errors[] = 'Buyer name is required.';
   if ($amount === '' || !is_numeric($amount) || (float)$amount < 0) $errors[] = 'Amount must be a valid number.';
   if ($saleDate === '' || !DateTime::createFromFormat('Y-m-d', $saleDate)) $errors[] = 'A valid date is required.';
   if (!in_array($status, ['Paid', 'Unpaid'], true)) $errors[] = 'Invalid status.';
   if ($_POST['action'] === 'edit_sale' && $editId <= 0) $errors[] = 'Invalid record.';

   if (!$errors) {
      if ($_POST['action'] === 'edit_sale') {
         $stmt = $pdo->prepare('UPDATE sales SET product=?, quantity=?, unit=?, buyer=?, buyer_phone=?, amount=?, status=?, sale_date=? WHERE id=?');
         $stmt->execute([$product, $quantity, $unit, $buyer, $buyerPhone !== '' ? $buyerPhone : null, $amount, $status, $saleDate, $editId]);
         header('Location: sales.php?updated=1');
      } else {
         $stmt = $pdo->prepare('INSERT INTO sales (product, quantity, unit, buyer, buyer_phone, amount, status, sale_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
         $stmt->execute([$product, $quantity, $unit, $buyer, $buyerPhone !== '' ? $buyerPhone : null, $amount, $status, $saleDate, $user['id']]);
         header('Location: sales.php?added=1');
      }
      exit;
   }
}

if (isset($_GET['added'])) {
   $success = 'Sale recorded successfully.';
} elseif (isset($_GET['updated'])) {
   $success = 'Sale updated successfully.';
}

$editRecord = null;
if ($canFull && isset($_GET['edit'])) {
   $s = $pdo->prepare('SELECT * FROM sales WHERE id=?');
   $s->execute([(int) $_GET['edit']]);
   $editRecord = $s->fetch() ?: null;
}

$logoPath = __DIR__ . '/../images/logo/citygatelogo.png';
$logoDataUri = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

function fmt_ugx($n) { return 'UGX ' . number_format((float)$n); }

function statusBadge($status) {
   $cls = $status === 'Paid' ? 'cg-badge-paid' : 'cg-badge-unpaid';
   return '<span class="cg-badge ' . $cls . '">' . htmlspecialchars($status) . '</span>';
}

$revenueThisMonth = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM sales WHERE status='Paid' AND MONTH(sale_date)=MONTH(CURDATE()) AND YEAR(sale_date)=YEAR(CURDATE())")->fetchColumn();
$totalRecords = (int) $pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn();
$unpaidCount = (int) $pdo->query("SELECT COUNT(*) FROM sales WHERE status='Unpaid'")->fetchColumn();

$sales = $pdo->query("SELECT * FROM sales ORDER BY sale_date DESC, id DESC")->fetchAll();

$today = date('Y-m-d');
?>
<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon"><i class="fa fa-money"></i></div><div><div class="cg-stat-card-num"><?php echo fmt_ugx($revenueThisMonth); ?></div><div class="cg-stat-card-label">Revenue This Month</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon gold"><i class="fa fa-list-alt"></i></div><div><div class="cg-stat-card-num"><?php echo $totalRecords; ?></div><div class="cg-stat-card-label">Total Sales Records</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon rose"><i class="fa fa-exclamation-circle"></i></div><div><div class="cg-stat-card-num"><?php echo $unpaidCount; ?></div><div class="cg-stat-card-label">Unpaid Records</div></div></div>
</div>

<?php if ($success): ?>
<div class="alert alert-success" style="border-radius:10px;"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger" style="border-radius:10px;">
   <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
</div>
<?php endif; ?>

<?php if ($canFull):
   $f = $editRecord ?: [];
   $fVal = function ($key, $default = '') use ($f) {
      return htmlspecialchars((string) ($f[$key] ?? $default));
   };
?>
<div class="cg-panel" id="cgSaleFormPanel" style="<?php echo ($errors || $editRecord) ? '' : 'display:none;'; ?>">
   <div class="cg-panel-head"><h2><?php echo $editRecord ? 'Edit Sale' : 'Record New Sale'; ?></h2></div>
   <form method="post" class="row g-3" style="padding:4px 2px 8px;">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="<?php echo $editRecord ? 'edit_sale' : 'add_sale'; ?>">
      <?php if ($editRecord): ?><input type="hidden" name="id" value="<?php echo (int) $editRecord['id']; ?>"><?php endif; ?>
      <div class="col-md-4"><label class="form-label">Product</label><input type="text" class="form-control" name="product" value="<?php echo $editRecord ? $fVal('product') : htmlspecialchars($_POST['product'] ?? ''); ?>" required></div>
      <div class="col-md-2"><label class="form-label">Quantity</label><input type="number" min="0" step="any" class="form-control" name="quantity" value="<?php echo $editRecord ? $fVal('quantity') : htmlspecialchars($_POST['quantity'] ?? ''); ?>" required></div>
      <div class="col-md-2"><label class="form-label">Unit</label><input type="text" class="form-control" name="unit" placeholder="e.g. Trays, Litres, Kg" value="<?php echo $editRecord ? $fVal('unit') : htmlspecialchars($_POST['unit'] ?? ''); ?>" required></div>
      <div class="col-md-4"><label class="form-label">Buyer Name</label><input type="text" class="form-control" name="buyer" value="<?php echo $editRecord ? $fVal('buyer') : htmlspecialchars($_POST['buyer'] ?? ''); ?>" required></div>
      <div class="col-md-4"><label class="form-label">Buyer Phone</label><input type="text" class="form-control" name="buyer_phone" value="<?php echo $editRecord ? $fVal('buyer_phone') : htmlspecialchars($_POST['buyer_phone'] ?? ''); ?>"></div>
      <div class="col-md-3"><label class="form-label">Amount (UGX)</label><input type="number" min="0" step="any" class="form-control" name="amount" value="<?php echo $editRecord ? $fVal('amount') : htmlspecialchars($_POST['amount'] ?? ''); ?>" required></div>
      <div class="col-md-3"><label class="form-label">Date</label><input type="date" class="form-control" name="sale_date" value="<?php echo $editRecord ? $fVal('sale_date') : htmlspecialchars($_POST['sale_date'] ?? $today); ?>" required></div>
      <div class="col-md-2">
         <label class="form-label">Status</label>
         <?php $curStatus = $editRecord ? ($editRecord['status'] ?? 'Unpaid') : ($_POST['status'] ?? 'Unpaid'); ?>
         <select class="form-select" name="status">
            <option value="Paid" <?php echo ($curStatus === 'Paid') ? 'selected' : ''; ?>>Paid</option>
            <option value="Unpaid" <?php echo ($curStatus === 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
         </select>
      </div>
      <div class="col-12" style="display:flex;gap:10px;">
         <button type="submit" class="cg-btn cg-btn-primary"><?php echo $editRecord ? 'Save Changes' : 'Save Sale'; ?></button>
         <?php if ($editRecord): ?>
            <a href="sales.php" class="cg-btn cg-btn-outline">Cancel</a>
         <?php else: ?>
            <button type="button" class="cg-btn cg-btn-outline" id="cgCancelSaleForm">Cancel</button>
         <?php endif; ?>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Sales</h2>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
         <a href="sales.php?print=1" target="_blank" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-print"></i> Print Sales Report</a>
         <?php if ($canFull): ?>
         <button class="cg-btn cg-btn-primary cg-btn-sm" id="cgToggleSaleForm"><i class="fa fa-plus"></i> Record Sale</button>
         <?php endif; ?>
      </div>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead>
            <tr><th>Product</th><th>Qty</th><th>Buyer</th><th>Amount</th><th>Date</th><th>Status</th><th>Actions</th></tr>
         </thead>
         <tbody>
         <?php if (!$sales): ?>
            <tr><td colspan="7" class="text-muted">No sales recorded yet.</td></tr>
         <?php else: foreach ($sales as $s): ?>
            <tr>
               <td><?php echo htmlspecialchars($s['product']); ?></td>
               <td><?php echo htmlspecialchars(rtrim(rtrim(number_format((float)$s['quantity'], 2), '0'), '.')); ?> <?php echo htmlspecialchars($s['unit']); ?></td>
               <td><?php echo htmlspecialchars($s['buyer']); ?></td>
               <td><?php echo fmt_ugx($s['amount']); ?></td>
               <td><?php echo htmlspecialchars($s['sale_date']); ?></td>
               <td><?php echo statusBadge($s['status']); ?></td>
               <td style="white-space:nowrap;">
                  <button class="cg-btn cg-btn-outline cg-btn-sm cg-invoice-btn" data-sale='<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES); ?>'><i class="fa fa-eye"></i> View Invoice</button>
                  <button class="cg-btn cg-btn-outline cg-btn-sm cg-receipt-btn" data-sale='<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES); ?>'><i class="fa fa-file-o"></i> Receipt</button>
                  <?php if ($canFull): ?>
                  <a href="sales.php?edit=<?php echo (int) $s['id']; ?>#cgSaleFormPanel" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                  <?php endif; ?>
               </td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php
$extraScripts = [
   'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
   'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js',
];
require_once __DIR__ . '/includes/footer.php';
?>
<script>
(function () {
   var toggleBtn = document.getElementById('cgToggleSaleForm');
   var formPanel = document.getElementById('cgSaleFormPanel');
   var cancelBtn = document.getElementById('cgCancelSaleForm');

   if (toggleBtn && formPanel) {
      toggleBtn.addEventListener('click', function () {
         formPanel.style.display = (formPanel.style.display === 'none') ? '' : 'none';
      });
   }
   if (cancelBtn && formPanel) {
      cancelBtn.addEventListener('click', function () {
         formPanel.style.display = 'none';
      });
   }

   var FARM_LOGO = <?php echo json_encode($logoDataUri); ?>;
   var GREEN = [47, 93, 58], GREEN_DARK = [31, 63, 39], GOLD = [201, 169, 120], INK = [30, 30, 30], MUTED = [110, 110, 110];

   function fmtUGX(n) {
      return 'UGX ' + Number(n || 0).toLocaleString();
   }

   // Branded header used by both documents: circular logo badge top-left
   // (with a gold ring, mirroring a classic farm/agro invoice template),
   // farm name + tagline beside it, a soft decorative corner accent, and
   // the document title + number/date right-aligned.
   function addFarmHeader(doc, docTitle, docNo, docDate) {
      // Soft decorative corner accent (top-right)
      doc.setFillColor(245, 238, 224);
      doc.circle(214, -6, 26, 'F');

      // Circular logo badge, top-left
      var cx = 27, cy = 23, r = 14;
      doc.setDrawColor(GOLD[0], GOLD[1], GOLD[2]);
      doc.setLineWidth(1);
      doc.circle(cx, cy, r, 'S');
      if (FARM_LOGO) {
         try {
            var logoW = r * 1.5, logoH = logoW * (426 / 585);
            doc.addImage(FARM_LOGO, 'PNG', cx - logoW / 2, cy - logoH / 2, logoW, logoH);
         } catch (e) { /* ring + text still communicate the brand if the image fails */ }
      }

      doc.setFont('helvetica', 'bold');
      doc.setFontSize(16);
      doc.setTextColor(GREEN[0], GREEN[1], GREEN[2]);
      doc.text('CITY GATE MIXED FARM', 46, 21);

      doc.setFont('helvetica', 'italic');
      doc.setFontSize(9.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('Integrated Farm — Poultry · Dairy · Goats · Crops', 46, 27);

      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8.5);
      doc.text('Amuca, Lira City, Uganda  ·  +256 123 456 789  ·  info@citygatefarms.com', 46, 32.5);

      // Title + doc number, right-aligned
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(20);
      doc.setTextColor(INK[0], INK[1], INK[2]);
      doc.text(docTitle, 196, 20, { align: 'right' });
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(9.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('No. ' + docNo, 196, 27, { align: 'right' });
      doc.text('Date: ' + docDate, 196, 32.5, { align: 'right' });

      doc.setDrawColor(GOLD[0], GOLD[1], GOLD[2]);
      doc.setLineWidth(0.8);
      doc.line(14, 42, 196, 42);
   }

   // Bottom green band footer, consistent on both documents.
   function addFarmFooter(doc, message) {
      doc.setFillColor(GREEN[0], GREEN[1], GREEN[2]);
      doc.rect(0, 281, 210, 16, 'F');
      doc.setFont('helvetica', 'italic');
      doc.setFontSize(10);
      doc.setTextColor(255, 255, 255);
      doc.text(message, 105, 288, { align: 'center' });
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8);
      doc.setTextColor(230, 230, 220);
      doc.text('Amuca, Lira City, Uganda  ·  +256 123 456 789  ·  info@citygatefarms.com', 105, 293.5, { align: 'center' });
   }

   function statusColor(status) {
      return status === 'Paid' ? [31, 107, 58] : [163, 46, 46];
   }

   function openPdf(doc, filename) {
      // Open in a new tab for viewing (browser's native PDF viewer, which
      // has its own Print/Save buttons) rather than forcing an immediate
      // download.
      try {
         var url = doc.output('bloburl');
         var win = window.open(url, '_blank');
         if (!win) doc.save(filename); // popup blocked — fall back to download
      } catch (e) {
         doc.save(filename);
      }
   }

   function generateInvoice(record) {
      var jsPDFCtor = window.jspdf && window.jspdf.jsPDF;
      if (!jsPDFCtor) { alert('PDF library failed to load. Please check your connection and try again.'); return; }
      var doc = new jsPDFCtor();

      addFarmHeader(doc, 'INVOICE', 'INV-' + record.id, record.sale_date || '—');

      // Bill To
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9.5);
      doc.setTextColor(GOLD[0], GOLD[1] - 20, GOLD[2] - 60);
      doc.text('BILL TO', 14, 52);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(12);
      doc.setTextColor(INK[0], INK[1], INK[2]);
      doc.text(record.buyer || '—', 14, 59);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(9.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text(record.buyer_phone || '—', 14, 65);

      var qty = Number(record.quantity || 0);
      var amount = Number(record.amount || 0);
      var unitPrice = qty ? amount / qty : amount;

      doc.autoTable({
         startY: 74,
         head: [['#', 'Description', 'Quantity', 'Unit Price', 'Amount']],
         body: [[
            '1',
            record.product,
            qty + ' ' + (record.unit || ''),
            'UGX ' + unitPrice.toLocaleString(undefined, { maximumFractionDigits: 0 }),
            'UGX ' + amount.toLocaleString()
         ]],
         theme: 'grid',
         headStyles: { fillColor: GREEN, textColor: 255, fontStyle: 'bold', halign: 'left' },
         alternateRowStyles: { fillColor: [250, 247, 240] },
         columnStyles: { 0: { cellWidth: 10 }, 4: { halign: 'right' }, 3: { halign: 'right' } },
         styles: { fontSize: 10, cellPadding: 5 },
         margin: { left: 14, right: 14 }
      });

      var y = (doc.lastAutoTable && doc.lastAutoTable.finalY) ? doc.lastAutoTable.finalY : 100;

      // Totals box, right-aligned
      var boxW = 70, boxX = 196 - boxW;
      doc.setDrawColor(230, 230, 224);
      doc.setFillColor(250, 247, 240);
      doc.roundedRect(boxX, y + 8, boxW, 22, 2, 2, 'FD');
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(10);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('Total Due', boxX + 6, y + 17);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(14);
      doc.setTextColor(GREEN[0], GREEN[1], GREEN[2]);
      doc.text('UGX ' + amount.toLocaleString(), boxX + boxW - 6, y + 17, { align: 'right' });
      var sc = statusColor(record.status);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9);
      doc.setTextColor(sc[0], sc[1], sc[2]);
      doc.text((record.status || '—').toUpperCase(), boxX + boxW - 6, y + 25, { align: 'right' });

      // Payment details, left
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9.5);
      doc.setTextColor(GOLD[0], GOLD[1] - 20, GOLD[2] - 60);
      doc.text('PAYMENT DETAILS', 14, y + 16);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(9);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('Payable via Cash, Mobile Money, or Bank Transfer.', 14, y + 22);
      doc.text('Contact +256 123 456 789 to arrange payment or delivery.', 14, y + 27);

      // Signature line
      var sigY = y + 48;
      doc.setDrawColor(190, 190, 180);
      doc.line(14, sigY, 70, sigY);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('Authorized Signature — City Gate Mixed Farm', 14, sigY + 5);

      addFarmFooter(doc, 'Thank you for your business!');
      openPdf(doc, 'Invoice-' + record.id + '.pdf');
   }

   function generateReceipt(record) {
      var jsPDFCtor = window.jspdf && window.jspdf.jsPDF;
      if (!jsPDFCtor) { alert('PDF library failed to load. Please check your connection and try again.'); return; }
      var doc = new jsPDFCtor();

      addFarmHeader(doc, 'RECEIPT', 'RCT-' + record.id, record.sale_date || '—');

      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9.5);
      doc.setTextColor(GOLD[0], GOLD[1] - 20, GOLD[2] - 60);
      doc.text('RECEIVED FROM', 14, 52);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(12);
      doc.setTextColor(INK[0], INK[1], INK[2]);
      doc.text(record.buyer || '—', 14, 59);

      var qty = Number(record.quantity || 0);
      var amount = Number(record.amount || 0);

      doc.autoTable({
         startY: 68,
         head: [['Description', 'Quantity', 'Amount Paid']],
         body: [[record.product, qty + ' ' + (record.unit || ''), 'UGX ' + amount.toLocaleString()]],
         theme: 'grid',
         headStyles: { fillColor: GREEN, textColor: 255, fontStyle: 'bold' },
         columnStyles: { 2: { halign: 'right' } },
         styles: { fontSize: 10, cellPadding: 5 },
         margin: { left: 14, right: 14 }
      });

      var y = (doc.lastAutoTable && doc.lastAutoTable.finalY) ? doc.lastAutoTable.finalY : 90;

      var boxW = 70, boxX = 196 - boxW;
      doc.setDrawColor(230, 230, 224);
      doc.setFillColor(250, 247, 240);
      doc.roundedRect(boxX, y + 8, boxW, 22, 2, 2, 'FD');
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(10);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('Amount Paid', boxX + 6, y + 17);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(14);
      doc.setTextColor(GREEN[0], GREEN[1], GREEN[2]);
      doc.text('UGX ' + amount.toLocaleString(), boxX + boxW - 6, y + 17, { align: 'right' });
      var sc = statusColor(record.status);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9);
      doc.setTextColor(sc[0], sc[1], sc[2]);
      doc.text((record.status || '—').toUpperCase(), boxX + boxW - 6, y + 25, { align: 'right' });

      var sigY = y + 48;
      doc.setDrawColor(190, 190, 180);
      doc.line(14, sigY, 70, sigY);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('Authorized Signature — City Gate Mixed Farm', 14, sigY + 5);

      addFarmFooter(doc, 'Thank you, payment received.');
      openPdf(doc, 'Receipt-' + record.id + '.pdf');
   }

   document.querySelectorAll('.cg-invoice-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var record = JSON.parse(btn.getAttribute('data-sale'));
         generateInvoice(record);
      });
   });
   document.querySelectorAll('.cg-receipt-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var record = JSON.parse(btn.getAttribute('data-sale'));
         generateReceipt(record);
      });
   });
})();
</script>
