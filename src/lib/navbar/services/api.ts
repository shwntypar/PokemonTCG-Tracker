/* import EncryptionService from './encryption'; */
const baseUrl = "http://localhost/PokemonTCG-Tracker/api";

async function fetchWithAuth(endpoint: string, options: RequestInit = {}) {
  const token = localStorage.getItem("token");

  const headers: HeadersInit = {
    ...(token && { Authorization: `Bearer ${token}` }),
    ...options.headers,
  };

  if (!(options.body instanceof FormData)) {
    (headers as Record<string, string>)['Content-Type'] = 'application/json';
  }

  const response = await fetch(`${baseUrl}/${endpoint}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.status?.message || 'Request failed');
  }

  // console.log('Raw API response:', jsonResponse);

  // DECRYPT PAYLOAD GIVEN THAT ITS CONVERTED TO STRING
/*   if (jsonResponse.payload && typeof jsonResponse.payload === 'string') {
      jsonResponse.payload = EncryptionService.decrypt(jsonResponse.payload);
      // console.log('Decrypted payload:', jsonResponse.payload);
  } */

  // Only parse JSON once
  return response.json();
}

export const api = {
  get: (endpoint: string) => fetchWithAuth(endpoint),
  post: (endpoint: string, data: any) =>
    fetchWithAuth(endpoint, {
      method: "POST",
      body: data instanceof FormData ? data : JSON.stringify(data),
    }),
};