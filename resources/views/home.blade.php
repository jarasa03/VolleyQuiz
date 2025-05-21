@extends('layouts.app')

@section('title', 'Inicio')

@push('body-class')
    home-page
@endpush

@section('content')
    <section class="hero" style="background-image: url('/images/voley-hero.webp');">
        <div class="overlay"></div>

        <div class="hero-content">
            <h1>Domina las Normas del Voleibol</h1>
            <p>Practica con tests interactivos, repasa la normativa y alcanza el siguiente nivel.</p>

            <div class="hero-buttons">
                <a href="#" class="zen">Empezar Modo Zen</a>
                <a href="{{ route('documentacion.dashboard') }}" class="docs">Ver Documentación</a>
            </div>
        </div>
    </section>

    <div class="custom-shape-divider-bottom-1747826865">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                opacity=".25" class="shape-fill"></path>
            <path
                d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                opacity=".5" class="shape-fill"></path>
            <path
                d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                class="shape-fill"></path>
        </svg>
    </div>

    <section class="section-intro">
        <div class="container">
            <h2>¿Qué es VolleyQuiz?</h2>
            <p>
                VolleyQuiz es una plataforma para entrenar tus conocimientos de reglamento y arbitraje de voleibol
                mediante tests interactivos, organizados por niveles y categorías. Ideal para árbitros, entrenadores y
                jugadores.
            </p>
        </div>
    </section>

    <div class="custom-shape-divider-bottom-1747825846">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                class="shape-fill"></path>
        </svg>
    </div>

    <section class="section-modes">
        <div class="container">
            <h2>Modos de Juego</h2>
            <div class="modes-grid">
                <a href="{{ route('zen.index') }}" class="mode-card clickable">
                    <h3>Modo Zen</h3>
                    <p>Practica sin presión, sin tiempo ni puntuación. Ideal para aprender.</p>
                </a>

                <div class="mode-card coming-soon">
                    <h3>Modo Arcade</h3>
                    <p>🛠 En desarrollo: compite contra otros usuarios por subir en la clasificación global.</p>
                </div>

                <div class="mode-card coming-soon">
                    <h3>Modo Niveles</h3>
                    <p>🛠 En desarrollo: progresa desbloqueando niveles con preguntas cada vez más complejas.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="custom-shape-divider-bottom-1747828298">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <rect x="1200" height="3.6"></rect>
            <rect height="3.6"></rect>
            <path d="M0,0V3.6H580.08c11,0,19.92,5.09,19.92,13.2,0-8.14,8.88-13.2,19.92-13.2H1200V0Z" class="shape-fill">
            </path>
        </svg>
    </div>

    <section class="section-why">
        <div class="container">
            <h2>¿Por qué usar VolleyQuiz?</h2>
            <div class="why-grid">
                <div class="why-item">
                    <span class="emoji">📚</span>
                    <h3>Aprende Jugando</h3>
                    <p>Refuerza tu conocimiento con tests dinámicos y sin presión.</p>
                </div>
                <div class="why-item">
                    <span class="emoji">🎯</span>
                    <h3>Entrena con Objetivo</h3>
                    <p>Organizado por niveles y categorías para avanzar de forma efectiva.</p>
                </div>
                <div class="why-item">
                    <span class="emoji">⚡</span>
                    <h3>Acceso Rápido</h3>
                    <p>Ingresa, responde y mejora en minutos. Totalmente accesible.</p>
                </div>
                <div class="why-item">
                    <span class="emoji">💡</span>
                    <h3>Ideal para Todos</h3>
                    <p>Perfecto para árbitros, entrenadores y jugadores de cualquier nivel.</p>
                </div>
            </div>
        </div>
    </section>

@endsection
