<option value="{{ $carpeta->id }}">
    {{ str_repeat('— ', $nivel) . $carpeta->name }}
</option>

@if ($carpeta->children && $carpeta->children->count())
    @foreach ($carpeta->children as $child)
        @include('admin.folders.partials.option', ['carpeta' => $child, 'nivel' => $nivel + 1])
    @endforeach
@endif
