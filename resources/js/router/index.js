import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    // ===== PUBLIC =====
    {
      path: '/',
      component: () => import('@/layouts/PublicLayout.vue'),
      children: [
        { path: '', name: 'home', component: () => import('@/views/public/HomeView.vue') },
        { path: 'paket-umroh', name: 'packages', component: () => import('@/views/public/PackagesView.vue') },
        { path: 'paket-umroh/:slug', name: 'package-detail', component: () => import('@/views/public/PackageDetailView.vue') },
        { path: 'daftar/:packageSlug?', name: 'register-booking', component: () => import('@/views/public/BookingFormView.vue') },
        { path: 'artikel', name: 'articles', component: () => import('@/views/public/ArticlesView.vue') },
        { path: 'artikel/:slug', name: 'article-detail', component: () => import('@/views/public/ArticleDetailView.vue') },
        { path: 'galeri', name: 'gallery', component: () => import('@/views/public/GalleryView.vue') },
        { path: 'faq', name: 'faq', component: () => import('@/views/public/FaqView.vue') },
        { path: 'kontak', name: 'contact', component: () => import('@/views/public/ContactView.vue') },
      ]
    },

    // ===== AUTH =====
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { guest: true }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('@/views/auth/ForgotPasswordView.vue'),
      meta: { guest: true }
    },

    // ===== MEMBER =====
    {
      path: '/member',
      component: () => import('@/layouts/MemberLayout.vue'),
      meta: { requiresAuth: true, role: 'member' },
      children: [
        { path: 'dashboard', name: 'member-dashboard', component: () => import('@/views/member/DashboardView.vue') },
        { path: 'booking', name: 'member-bookings', component: () => import('@/views/member/BookingsView.vue') },
        { path: 'booking/:id', name: 'member-booking-detail', component: () => import('@/views/member/BookingDetailView.vue') },
        { path: 'data-jamaah', name: 'member-pilgrims', component: () => import('@/views/member/PilgrimsView.vue') },
        { path: 'dokumen', name: 'member-documents', component: () => import('@/views/member/DocumentsView.vue') },
        { path: 'pembayaran', name: 'member-payments', component: () => import('@/views/member/PaymentsView.vue') },
      ]
    },

    // ===== AGENT =====
    {
      path: '/agen',
      component: () => import('@/layouts/AgentLayout.vue'),
      meta: { requiresAuth: true, role: 'agent' },
      children: [
        { path: 'dashboard', name: 'agent-dashboard', component: () => import('@/views/agent/DashboardView.vue') },
        { path: 'referral', name: 'agent-referral', component: () => import('@/views/agent/ReferralView.vue') },
        { path: 'booking', name: 'agent-bookings', component: () => import('@/views/agent/BookingsView.vue') },
        { path: 'komisi', name: 'agent-commissions', component: () => import('@/views/agent/CommissionsView.vue') },
      ]
    },

    // ===== REPRESENTATIVE =====
    {
      path: '/perwakilan',
      component: () => import('@/layouts/RepresentativeLayout.vue'),
      meta: { requiresAuth: true, role: 'representative' },
      children: [
        { path: 'dashboard', name: 'rep-dashboard', component: () => import('@/views/representative/DashboardView.vue') },
      ]
    },

    // ===== ADMIN =====
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        { path: 'dashboard', name: 'admin-dashboard', component: () => import('@/views/admin/DashboardView.vue') },
        { path: 'paket', name: 'admin-packages', component: () => import('@/views/admin/PackagesView.vue') },
        { path: 'paket/tambah', name: 'admin-package-create', component: () => import('@/views/admin/PackageFormView.vue') },
        { path: 'paket/:id/edit', name: 'admin-package-edit', component: () => import('@/views/admin/PackageFormView.vue') },
        { path: 'booking', name: 'admin-bookings', component: () => import('@/views/admin/BookingsView.vue') },
        { path: 'booking/:id', name: 'admin-booking-detail', component: () => import('@/views/admin/BookingDetailView.vue') },
        { path: 'jamaah', name: 'admin-pilgrims', component: () => import('@/views/admin/PilgrimsView.vue') },
        { path: 'dokumen', name: 'admin-documents', component: () => import('@/views/admin/DocumentsView.vue') },
        { path: 'pembayaran', name: 'admin-payments', component: () => import('@/views/admin/PaymentsView.vue') },
        { path: 'agen', name: 'admin-agents', component: () => import('@/views/admin/AgentsView.vue') },
        { path: 'perwakilan', name: 'admin-representatives', component: () => import('@/views/admin/RepresentativesView.vue') },
        { path: 'komisi', name: 'admin-commissions', component: () => import('@/views/admin/CommissionsView.vue') },
        { path: 'keberangkatan', name: 'admin-departures', component: () => import('@/views/admin/DeparturesView.vue') },
        { path: 'cms', name: 'admin-cms', component: () => import('@/views/admin/CmsView.vue') },
        { path: 'laporan', name: 'admin-reports', component: () => import('@/views/admin/ReportsView.vue') },
        { path: 'laporan-khusus', name: 'admin-custom-reports', component: () => import('@/views/admin/CustomReportsView.vue') },
        { path: 'settings', name: 'admin-settings', component: () => import('@/views/admin/SettingsView.vue') },
        { path: 'audit-logs', name: 'admin-audit-logs', component: () => import('@/views/admin/AuditLogsView.vue') },
      ]
    },

    // 404
    { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('@/views/NotFoundView.vue') },
  ]
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  
  // Set basic title based on route name if no dynamic title is set yet
  const defaultTitle = to.name ? to.name.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Travel Umroh'
  document.title = defaultTitle

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  if (to.meta.guest && auth.isLoggedIn) {
    return next(auth.dashboardRoute)
  }

  if (to.meta.role && auth.user?.role_type !== 'admin' && auth.user?.role_type !== to.meta.role) {
    return next(auth.dashboardRoute)
  }

  next()
})

export default router
