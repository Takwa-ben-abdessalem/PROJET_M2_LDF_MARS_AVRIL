import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { http } from '../api/http'

function decodeJwt(token) {
  try {
    const payload = JSON.parse(atob(token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')))
    return payload
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token'))
  const user = ref(null)

  const isAuthenticated = computed(() => Boolean(token.value))
  const roles = computed(() => user.value?.roles || decodeJwt(token.value)?.roles || [])
  const isOrganizer = computed(() => user.value?.roles?.includes('ROLE_ORGANIZER') || false)

  async function login(credentials) {
    const { data } = await http.post('/auth/login', credentials)
    token.value = data.token
    localStorage.setItem('token', data.token)
    await fetchProfile()
  }

  async function register(payload) {
    await http.post('/auth/register', payload)
    await login({ email: payload.email, password: payload.password })
  }

  async function fetchProfile() {
    if (!token.value) return
    const { data } = await http.get('/me')
    user.value = data
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isAuthenticated, roles, isOrganizer, login, register, fetchProfile, logout }
})
