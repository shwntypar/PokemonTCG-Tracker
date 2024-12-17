/* import EncryptionService from './encryption'; */
const baseUrl = "http://localhost/PokemonTCG-Tracker/api";
 import EncryptionService from "./encryption.ts";

async function fetchWithAuth(endpoint: string, options: RequestInit = {}) {
  const token = localStorage.getItem("token");

  const headers = {
    "Content-Type": "application/json",
    ...(token && { Authorization: `Bearer ${token}` }),
    ...options.headers,
  };

  // Encrypt request body if present
  if (options.body && typeof options.body === 'string') {
    const data = JSON.parse(options.body);
    options.body = JSON.stringify({
      encrypted: EncryptionService.encrypt(data)
    });
  }

  const response = await fetch(`${baseUrl}/${endpoint}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.status?.message || 'Request failed');
  }

  const jsonResponse = await response.json();

  // Decrypt payload if it's encrypted
  if (jsonResponse.payload && typeof jsonResponse.payload === 'string') {
    jsonResponse.payload = EncryptionService.decrypt(jsonResponse.payload);
  }

  return jsonResponse;
}

export const api = {
  get: (endpoint: string) => fetchWithAuth(endpoint),
  post: (endpoint: string, data: any) =>
    fetchWithAuth(endpoint, {
      method: "POST",
      body: JSON.stringify(data),
    }),
  postFormData: async (endpoint: string, formData: FormData) => {
    try {
      const response = await fetch(`${baseUrl}/${endpoint}`, {
        method: 'POST',
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      
      // Decrypt payload if it's encrypted
      if (data.payload && typeof data.payload === 'string') {
        data.payload = EncryptionService.decrypt(data.payload);
      }
      
      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  },
};