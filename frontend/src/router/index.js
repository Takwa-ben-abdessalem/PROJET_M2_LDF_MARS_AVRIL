import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import EventListView from '../views/EventListView.vue'
import EventDetailView from '../views/EventDetailView.vue'
import DashboardView from '../views/DashboardView.vue'
import EventFormView from '../views/EventFormView.vue'
import MyEventsView from '../views/MyEventsView.vue'
import MyRegistrationsView from '../views/MyRegistrationsView.vue'
import ProfileView from '../views/ProfileView.vue'
import PrivacyView from '../views/PrivacyView.vue'

const routes = [
  { path: '/', component: HomeView },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/events', component: EventListView },
  { path: '/events/:id', component: EventDetailView, props: true },
  { path: '/dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/dashboard/registrations', component: MyRegistrationsView, meta: { requiresAuth: true } },
  { path: '/dashboard/my-events', component: MyEventsView, meta: { requiresAuth: true, requiresOrganizer: true } },
  { path: '/events/create', component: EventFormView, meta: { requiresAuth: true, requiresOrganizer: true } },
  { path: '/events/:id/edit', component: EventFormView, props: true, meta: { requiresAuth: true, requiresOrganizer: true } },
  { path: '/profile', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/privacy', component: PrivacyView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  if (auth.isAuthenticated && !auth.user) {
    try {
      await auth.fetchProfile()
    } catch {
      auth.logout()
    }
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return '/login'
  }

  if (to.meta.requiresOrganizer && !auth.isOrganizer) {
    return '/events'
  }
})

export default router
