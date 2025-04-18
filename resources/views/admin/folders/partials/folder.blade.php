<li class="folder-item-li" style="margin-left: {{ $nivel * 24 }}px;">
    <div class="folder-item">
        <span class="folder-name">📁 {{ $carpeta->name }}</span>

        <!-- Botón de editar -->
        <form action="{{ route('admin.folders.edit', $carpeta->id) }}" method="GET" style="display: inline;">
            <button class="btn edit-folder-btn" title="Editar carpeta">✏️</button>
        </form>

        <!-- Botón de eliminar -->
        <form action="{{ route('admin.folders.destroy', $carpeta->id) }}" method="POST"
            onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta carpeta?');" style="display: inline;">
            @csrf
            @method('DELETE')
            <button class="btn delete delete-folder-btn" title="Eliminar carpeta">🗑</button>
        </form>
    </div>

    @if ($carpeta->children && $carpeta->children->count())
        <ul>
            @foreach ($carpeta->children as $child)
                @include('admin.folders.partials.folder', ['carpeta' => $child, 'nivel' => $nivel + 1])
            @endforeach
        </ul>
    @endif
</li>
