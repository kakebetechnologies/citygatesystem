<?php $extraScripts = $extraScripts ?? []; ?>
      </div>
   </div>

   <script>
      document.getElementById('cgAdminToggle').addEventListener('click', function () {
         document.getElementById('cgAdminSidebar').classList.toggle('is-open');
         document.getElementById('cgAdminOverlay').classList.toggle('is-open');
      });
      document.getElementById('cgAdminOverlay').addEventListener('click', function () {
         document.getElementById('cgAdminSidebar').classList.remove('is-open');
         document.getElementById('cgAdminOverlay').classList.remove('is-open');
      });
   </script>
<?php foreach ($extraScripts as $src): ?>
   <script src="<?php echo htmlspecialchars($src); ?>"></script>
<?php endforeach; ?>
</body>
</html>
