<li>
    <div class="folder-item" style="margin-left: {{ $nivel * 24 }}px;">
        <span class="folder-name">📁 {{ $carpeta->name }}</span>
        <form action="{{ route('admin.folders.destroy', $carpeta->id) }}" method="POST"
            onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta carpeta?');">
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
