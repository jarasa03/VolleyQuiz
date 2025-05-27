<footer class="site-footer">
    <div class="container footer-content">
        <!-- Logo a la izquierda -->
        <div class="footer-logo">
            <a href="{{ route('home') }}">🏐 VolleyQuiz</a>
        </div>

        <!-- Enlaces del footer -->
        <div class="footer-links">
            <a href="{{ route('documentacion.dashboard') }}">Documentación</a>
            <a href="{{ route('zen.index') }}">Zen</a>
            <a href="#">Arcade</a>
            <a href="#">Política de Privacidad</a>
            <a href="#">Ranking</a>
            <a href="#">Niveles</a>
            <a href="#">Aviso Legal</a>
            <a href="#">Política de Cookies</a>
        </div>
    </div>

    <hr id="separador-footer">

    <!-- Copyright abajo del todo -->
    <div class="footer-copy">
        <p>&copy; {{ date('Y') }} VolleyQuiz. Todos los derechos reservados.</p>
    </div>
</footer>
