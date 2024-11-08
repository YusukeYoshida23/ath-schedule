<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">アススケ</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link class="nav-link" to="/">{{ $t('header.home') }}</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/dashboard" v-if="authStore.isLoggedIn">
              {{ $t('header.dashboard') }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/login" v-if="!authStore.isLoggedIn">
              {{ $t('header.login') }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/register" v-if="!authStore.isLoggedIn">
              {{ $t('header.register') }}
            </router-link>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" @click.prevent="logout" v-if="authStore.isLoggedIn">
              {{ $t('header.logout') }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
  try {
    await api.post('/auth/logout')
    authStore.clearAuth()
    router.push('/')
  } catch (error) {
    console.error('Logout failed', error)
    // エラーが発生しても、認証情報はクリアする
    authStore.clearAuth()
    router.push('/')
  }
}
</script>
