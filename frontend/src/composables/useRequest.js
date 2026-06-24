import { ref } from 'vue'
import { apiError } from '../api/http'

export function useRequest() {
  const loading = ref(false)
  const error = ref('')
  const success = ref('')

  async function run(callback, successMessage = '') {
    loading.value = true
    error.value = ''
    success.value = ''
    try {
      const result = await callback()
      success.value = successMessage
      return result
    } catch (err) {
      error.value = apiError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return { loading, error, success, run }
}
