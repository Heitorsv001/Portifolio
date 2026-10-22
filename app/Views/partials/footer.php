<footer class="site-footer">
    <div class="container footer-content">
        <div class="footer-identity">
            <p><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></p>
            <p>Desenvolvedor Web Full Stack</p>
        </div>
        <div class="footer-actions">
            <?php require __DIR__ . '/social-links.php'; ?>
            <a class="text-link footer-top" href="#topo">Voltar ao topo</a>
        </div>
    </div>
</footer>
