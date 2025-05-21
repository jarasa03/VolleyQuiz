@extends('layouts.app')

@section('title', 'Inicio')

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
@endsection
