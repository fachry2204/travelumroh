<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/pwa-icon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#38BDF8" />
    <link rel="apple-touch-icon" href="/pwa-icon.svg">
    <meta name="description" content="Aplikasi Manajemen Travel Umroh Terpadu">
    <title>Sistem Informasi Travel Umroh</title>
    @vite(['resources/js/main.js'])
  </head>
  <body class="bg-sky-50 text-slate-900 font-sans antialiased">
    <div id="app"></div>
  </body>
</html>
