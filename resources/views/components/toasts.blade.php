@php
    $mensajes = [
        'success' => session('success'),
        'error'   => session('error'),
        'warning' => session('warning'),
        'info'    => session('info'),
    ];
    $hayAlertas = collect($mensajes)->some(fn($v) => $v) || $errors->any();
@endphp

@if($hayAlertas)
<script>
document.addEventListener('DOMContentLoaded', function () {
    @foreach($mensajes as $tipo => $msg)
        @if($msg)
            toast.{{ $tipo }}(@json($msg));
        @endif
    @endforeach

    @if($errors->any())
        @foreach($errors->all() as $error)
            toast.error(@json($error), 6000);
        @endforeach
    @endif
});
</script>
@endif
