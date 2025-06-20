<nav class="navbar">
    <div class="navbar__container">
        <!-- Sección Izquierda: Logo -->
        <div class="navbar__logo">
            <a id="navbar__title" href="{{ route('home') }}">🏐 VolleyQuiz</a>
        </div>

        <!-- Sección Central: Links -->
        <div class="navbar__links">
            <a href="{{ route('documentacion.dashboard') }}" class="navbar__link">Documentación</a>
            <a href="{{ route('zen.index') }}" class="navbar__link">Zen</a>

            @auth
                @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="navbar__link">Administración</a>
                @endif
            @endauth
        </div>

        <!-- Sección Derecha: Perfil y Cerrar Sesión -->
        <div class="navbar__profile-actions">
            @auth
                <span class="navbar__profile-btn">{{ auth()->user()->name }}</span>

                <form action="{{ route('auth.logout') }}" method="POST" class="navbar__logout-form">
                    @csrf
                    <button type="submit" class="navbar__logout-btn">Cerrar sesión</button>
                </form>
            @else
                <a href="{{ route('auth.login') }}" class="navbar__profile-btn">Iniciar sesión</a>
            @endauth
        </div>
    </div>
</nav>
