<template>
  <div class="flex h-screen overflow-hidden bg-sky-50">
    <aside class="w-60 bg-white border-r border-sky-100 flex flex-col fixed lg:relative h-full z-20 shadow-sm">
      <div class="p-4 border-b border-sky-100">
        <div class="font-bold text-slate-900 text-sm">Area Perwakilan</div>
        <div class="text-xs text-sky-500">{{ auth.user?.name }}</div>
      </div>
      <nav class="flex-1 p-3 space-y-1">
        <router-link to="/perwakilan/dashboard" class="sidebar-link" active-class="active">
          <span class="text-base w-5">📊</span>
          <span class="text-sm">Dashboard</span>
        </router-link>
      </nav>
      <div class="p-4 border-t border-sky-100">
        <button @click="handleLogout" class="w-full text-sm py-2 px-4 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-medium">Logout</button>
      </div>
    </aside>
    <div class="flex-1 flex flex-col overflow-hidden ml-60 lg:ml-0">
      <header class="bg-white border-b border-sky-100 px-6 py-4">
        <h1 class="text-lg font-semibold text-slate-900">Perwakilan Dashboard</h1>
      </header>
      <main class="flex-1 overflow-y-auto p-6"><router-view /></main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const router = useRouter()
async function handleLogout() { await auth.logout(); router.push('/login') }
</script>
