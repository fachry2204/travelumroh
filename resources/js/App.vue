<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'

const auth = useAuthStore()
const appStore = useAppStore()

onMounted(async () => {
  appStore.fetchSettings()
  if (localStorage.getItem('token')) {
    await auth.fetchUser()
  }
})
</script>
