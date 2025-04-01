<nav class="navbar">
    <!-- Sección Izquierda: Logo -->
    <div class="navbar__logo">
        <a href="{{ route('dashboard') }}">🏐 VolleyQuiz</a>
    </div>

    <!-- Sección Central: Links -->
    <div class="navbar__links">
        <a href="{{ route('documentacion.index') }}" class="navbar__link">Documentación</a>
        <a href="#" class="navbar__link">Arcade</a>
        <a href="#" class="navbar__link">Zen</a>
        <a href="#" class="navbar__link">Niveles</a>
        <a href="#" class="navbar__link">Ranking</a>

        @auth
            @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.dashboard') }}" class="navbar__link">Administración</a>
            @endif
        @endauth
    </div>

    <!-- Sección Derecha: Perfil y Cerrar Sesión -->
    <div class="navbar__profile-actions">
        @auth
            <a href="{{ route('users.perfil') }}" class="navbar__profile-btn">{{ auth()->user()->name }}</a>

            <form action="{{ route('auth.logout') }}" method="POST" class="navbar__logout-form">
                @csrf
                <button type="submit" class="navbar__logout-btn">Cerrar sesión</button>
            </form>
        @else
            <a href="{{ route('auth.login') }}" class="navbar__profile-btn">Iniciar sesión</a>
        @endauth
    </div>
</nav>
