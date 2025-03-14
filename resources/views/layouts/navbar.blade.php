<nav class="navbar">
    <div class="navbar__logo">
        <a href="{{ route('dashboard') }}">🏐 VolleyQuiz</a>
    </div>
    <div class="navbar__links">
        <a href="#" class="navbar__link">Documentación</a>
        <a href="#" class="navbar__link">Arcade</a>
        <a href="#" class="navbar__link">Zen</a>
        <a href="#" class="navbar__link">Niveles</a>
        <a href="#" class="navbar__link">Ranking</a>

        <!-- Perfil desplegable -->
        <div class="navbar__profile">
            <button class="navbar__profile-btn">{{ auth()->user()->name }} ⬇</button>
            <div class="navbar__dropdown">
                <a href="#" class="navbar__dropdown-item">Editar perfil</a>
                <form action="{{ route('auth.logout') }}" method="POST" class="navbar__logout-form">
                    @csrf
                    <button type="submit" class="navbar__logout-btn">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</nav>
