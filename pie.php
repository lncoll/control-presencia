        <br />
        </div>
        <footer id="footer">
            <p>© 2024 - <?= date('Y') ?> - Todos los derechos reservados</p>
        </footer>
        <script>
            history.pushState(null, null, '<?= $_SERVER['REQUEST_URI'] ?>');
        </script>
    </div>
    </body>
</html>