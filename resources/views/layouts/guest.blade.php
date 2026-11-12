{{-- Layout para visitantes sin sesión (login): una sola columna centrada. --}}
<!DOCTYPE html>
<html lang="es" class="h-full bg-fondo">
<head>
    @include('partials.head')
</head>
<body class="flex min-h-full items-center justify-center px-4 py-10 font-sans text-texto antialiased">
<main class="w-full max-w-sm">
    @yield('content')
</main>
</body>
</html>
