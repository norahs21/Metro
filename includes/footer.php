</main>

<footer class="site-footer">
    <div class="line-stripe" aria-hidden="true"></div>
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="index.php" class="logo">
                <span class="logo-mark"><?php echo icon('train'); ?></span>
                <span class="logo-text">حول المحطة</span>
            </a>
            <p>دليلك للأماكن القريبة من محطات مترو الرياض: انزل في المحطة، وامشِ دقائق، واكتشف وش حولك.</p>
        </div>

        <nav class="footer-links" aria-label="روابط سريعة">
            <h2>روابط سريعة</h2>
            <ul>
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="stations.php">المحطات</a></li>
                <li><a href="suggest.php">اقترح مكان</a></li>
                <li><a href="admin/login.php">دخول المشرف</a></li>
            </ul>
        </nav>

        <div class="footer-lines">
            <h2>المسارات</h2>
            <ul>
                <?php foreach (get_lines($conn) as $line): ?>
                    <li>
                        <span class="dot" style="<?php echo line_style($line['color']); ?>"></span>
                        <a href="stations.php?line=<?php echo (int) $line['id']; ?>"><?php echo e($line['name']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <p class="footer-bottom">حول المحطة © <?php echo date('Y'); ?>. مشروع مقرر تقنيات الإنترنت CSC457</p>
</footer>

</body>
</html>
