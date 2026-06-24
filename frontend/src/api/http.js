import axios from 'axios'

export const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: { 'Content-Type': 'application/json' },
})

http.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export function apiError(error) {
  const fields = error.response?.data?.fields
  if (fields && typeof fields === 'object') {
    return Object.values(fields).flat().join(' ')
  }

  return error.response?.data?.error || error.response?.data?.message || 'Une erreur est survenue.'
}
