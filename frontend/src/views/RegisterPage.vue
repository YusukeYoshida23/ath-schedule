<template>
  <div class="container mt-4">
    <h2>新規登録</h2>
    <form @submit.prevent="register">
      <div class="mb-3">
        <label for="name" class="form-label">{{ $t('auth.register.name') }}:</label>
        <input type="text" class="form-control" id="name" v-model="name" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">{{ $t('auth.register.email') }}:</label>
        <input type="email" class="form-control" id="email" v-model="email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">{{ $t('auth.register.password') }}:</label>
        <input type="password" class="form-control" id="password" v-model="password" required>
      </div>
      <div class="mb-3">
        <label for="password_confirmation" class="form-label">{{ $t('auth.register.password_confirmation') }}:</label>
        <input type="password" class="form-control" id="password_confirmation" v-model="password_confirmation" required>
      </div>
      <button type="submit" class="btn btn-primary">{{ $t('auth.register.submit') }}</button>
    </form>
  </div>
</template>

<script>
import api, { getCsrfToken } from '@/services/api'

export default {
  name: 'RegisterPage',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    }
  },
  methods: {
    async register() {
      try {
        // CSRFトークンを取得し、成功を確認
        await getCsrfToken();
        
        const response = await api.post('/auth/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        });

        if (response.data.token) {
          localStorage.setItem('token', response.data.token);
          this.$router.push('/dashboard');
        }
      } catch (error) {
        console.error('Registration failed', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
          console.error('Status:', error.response.status);
          console.error('Headers:', error.response.headers);
        }
      }
    }
  }
}
</script>
