<template>
  <div>
    <h2>API Test</h2>
    <button @click="fetchData">Test API</button>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      message: ''
    }
  },
  methods: {
    async fetchData() {
      try {
        const response = await axios.get('http://localhost:8000/api/test')
        this.message = response.data.message
      } catch (error) {
        console.error('Error:', error)
        this.message = 'Error occurred while fetching data'
      }
    }
  }
}
</script>
