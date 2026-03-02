@php
    $url = $get('photo');
@endphp

@if ($url)
    <div class="mt-3">
        <img src="{{ $url }}" class="h-24 rounded-lg border" />
    </div>
@else
    <div class="text-sm text-gray-500">
        Belum ada foto
    </div>
@endif