{{-- Cabecera HTML común a los dos layouts (app y guest), para no repetirla. --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ isset($title) ? $title.' · ' : '' }}ImportaTrack EC</title>
{{-- Fuentes: IBM Plex Sans (interfaz) e IBM Plex Mono (cifras). preconnect abre antes la
     conexión con Google Fonts y display=swap muestra el texto sin esperar a la fuente. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
