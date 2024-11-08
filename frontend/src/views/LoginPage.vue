<template>
  <div class="container mt-4">
    <h2>{{ $t('auth.login.title') }}</h2>
    <div v-if="error" class="alert alert-danger">
      {{ $t('auth.login.error') }}
    </div>
    <form @submit.prevent="login">
      <div class="mb-3">
        <label for="email" class="form-label">{{ $t('auth.login.email') }}:</label>
        <input type="email" class="form-control" id="email" v-model="email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">{{ $t('auth.login.password') }}:</label>
        <input type="password" class="form-control" id="password" v-model="password" required>
      </div>
      <button type="submit" class="btn btn-primary">{{ $t('auth.login.submit') }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api, { getCsrfToken } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref(null)

const login = async () => {
  try {
    error.value = null
    await getCsrfToken()

    const response = await api.post('/auth/login', {
      email: email.value,
      password: password.value
    })

    if (response.data && response.data.token) {
      authStore.setToken(response.data.token)
      if (response.data.user) {
        authStore.setUser(response.data.user)
      }
      router.push('/dashboard')
    }
  } catch (err) {
    console.error('Login failed:', err)
    error.value = err.response?.data?.message || 'Login failed. Please try again.'
  }
}
</script>
