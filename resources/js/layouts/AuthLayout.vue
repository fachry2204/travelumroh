<template>
  <div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-slate-900 flex items-center justify-center p-4">
    <!-- Background decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-sky-400/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <router-link to="/" class="inline-flex items-center gap-3">
          <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-xl">
            <span class="text-3xl">🕌</span>
          </div>
          <div class="text-left">
            <div class="font-bold text-white text-xl">Travel Umroh</div>
            <div class="text-sky-300 text-sm">Indonesia</div>
          </div>
        </router-link>
      </div>

      <!-- Card -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
        <router-view />
      </div>

      <p class="text-center text-sky-300/60 text-xs mt-6">
        &copy; {{ new Date().getFullYear() }} Travel Umroh Indonesia
      </p>
    </div>
  </div>
</template>
