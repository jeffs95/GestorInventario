@php
    $mensajes = [
        'success' => ['msg' => session('success'), 'color' => '#2dce89', 'icon' => '✓'],
        'error'   => ['msg' => session('error'),   'color' => '#f5365c', 'icon' => '✕'],
        'warning' => ['msg' => session('warning'), 'color' => '#fb6340', 'icon' => '⚠'],
        'info'    => ['msg' => session('info'),    'color' => '#11cdef', 'icon' => 'ℹ'],
    ];
    $hayAlertas = collect($mensajes)->some(fn($v) => $v['msg']) || $errors->any();
@endphp

@if($hayAlertas)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const _toast = (text, background, duration = 5000) => {
        Toastify({
            text,
            duration,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            close: true,
            style: {
                background,
                borderRadius: '0.75rem',
                padding: '0.8rem 1.25rem',
                fontSize: '0.875rem',
                fontFamily: 'Open Sans, sans-serif',
                fontWeight: '600',
                boxShadow: '0 8px 25px -5px rgba(0,0,0,.25)',
                minWidth: '280px',
                maxWidth: '400px',
            },
        }).showToast();
    };

    @foreach($mensajes as $tipo => $config)
        @if($config['msg'])
            _toast(
                '{{ addslashes($config['msg']) }}',
                '{{ $config['color'] }}'
            );
        @endif
    @endforeach

    @if($errors->any())
        @foreach($errors->all() as $error)
            _toast('{{ addslashes($error) }}', '#f5365c', 7000);
        @endforeach
    @endif
});
</script>
@endif
