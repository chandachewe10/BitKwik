import axios from 'axios';

// Update this with your Laravel API base URL
const API_BASE_URL = 'https://5458ca2b9366.ngrok-free.app';

const api = axios.create({
  baseURL: API_BASE_URL.endsWith('/') ? API_BASE_URL.slice(0, -1) : API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'ngrok-skip-browser-warning': 'true', // Skip ngrok browser warning
  },
  timeout: 30000, // 30 second timeout
});

// Add CSRF token interceptor for Laravel
api.interceptors.request.use(
  async (config) => {
    // For GET requests, we might need to fetch CSRF token
    if (config.method !== 'get') {
      try {
        // You may need to fetch CSRF token from Laravel first
        // const csrfResponse = await axios.get(`${API_BASE_URL}/sanctum/csrf-cookie`);
        // config.headers['X-CSRF-TOKEN'] = csrfResponse.data.token;
      } catch (error) {
        console.error('Error fetching CSRF token:', error);
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// API endpoints
export const apiEndpoints = {
  exchangeRates: '/api/exchange-rates',
  checkBalance: '/api/check-balance',
  generateInvoice: '/api/generate-invoice',
  subscriptionPayment: '/subscription/payment', // Web route, not API
  completeSubscription: '/api/complete-subscription',
};

export default api;

