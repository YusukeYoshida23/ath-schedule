import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
});

// トークンとCSRFトークンの両方を設定するインターセプター
api.interceptors.request.use(config => {
  // Bearerトークンの設定
  const token = localStorage.getItem('token');
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`;
  }

  // CSRFトークンの設定
  const csrfToken = document.cookie
    .split(';')
    .find(cookie => cookie.trim().startsWith('XSRF-TOKEN='));
  if (csrfToken) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken.split('=')[1]);
  }

  return config;
});

// エラーハンドリングを追加
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      // 必要に応じてログインページへリダイレクト
    }
    return Promise.reject(error);
  }
);

export const getCsrfToken = async () => {
  try {
    const response = await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
      withCredentials: true
    });
    return response;
  } catch (error) {
    console.error('Failed to get CSRF token:', error);
    throw error;
  }
};

export default api;
