<script lang="ts">
 import Navbar from '../../lib/navbar/+navbar.svelte';
 import { api } from '$lib/services/api.ts';
 import { goto } from '$app/navigation';

  let selectedImage: File | null = null;
  let imagePreview: string | null = null;
  let error = '';

  let formData = {
    name: '',
    rarity: '',
    type: '',
    date_added: '',
    selectedImage: null
  };

  const rarityOptions = [
    'Common',
    'Holo',
    'Rare',
    'Ultra Rare',
    'Secret',
    'Full Art'
  ];

  const pokemonTypes = [
    'Trainer',
    'Energy',
    'Support',
    'Colorless',
    'Darkness',
    'Dragon',
    'Fairy',
    'Fighting',
    'Fire',
    'Grass',
    'Lightning',
    'Metal',
    'Psychic',
    'Water'
  ];

  function handleImageChange(e: Event) {
        const target = e.target as HTMLInputElement;
        const file = target.files?.[0];
        if (file) {
            selectedImage = file;
            imagePreview = URL.createObjectURL(file);
        }
    }

  async function handleSubmit(event: Event) {
    event.preventDefault();
    
    if (!selectedImage) {
            error = 'Please add main image and at least one possible reward';
            return;
        }
    
    try {
      const submitFormData = new FormData();
      submitFormData.append('name', formData.name);
      submitFormData.append('rarity', formData.rarity);
      submitFormData.append('type', formData.type);
      submitFormData.append('date_added', formData.date_added);
      submitFormData.append('image', selectedImage);

      // Debug logs
      console.log('Form Data contents:');
      for (let pair of submitFormData.entries()) {
          console.log(pair[0] + ': ' + pair[1]);
      }

      const response = await api.postFormData("AddPokemonCard", submitFormData);
      console.log('Full Response:', response);
      
      if (response.status.remarks === 'success') {
        alert('Pokemon card added successfully!');
        goto('/dashboard');
        // Reset form
        formData = {
          name: '',
          rarity: '',
          type: '',
          date_added: '',
          selectedImage: null
        };
        selectedImage = null;
        imagePreview = null;
      } else {
        alert('Error: ' + response.status.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Failed to add Pokemon card: ' + (error as Error).message);
    }
  }
</script>

<Navbar />
<div class="container mx-auto p-4 flex items-center justify-center min-h-[calc(100vh-64px)]">
  <form class="grid grid-cols-2 gap-8 max-w-5xl w-full" on:submit={handleSubmit}>
    <!-- Left Column - Form Fields -->
    <div class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium mb-1">Card Name</label>
        <input
          type="text"
          id="name"
          bind:value={formData.name}
          class="w-full p-2 border rounded-md"
          required
        />
      </div>

      <div>
        <label for="rarity" class="block text-sm font-medium mb-1">Rarity</label>
        <select
          id="rarity"
          bind:value={formData.rarity}
          class="w-full p-2 border rounded-md"
          required
        >
          <option value="">Select Rarity</option>
          {#each rarityOptions as rarity}
            <option value={rarity.toLowerCase()}>{rarity}</option>
          {/each}
        </select>
      </div>

      <div>
        <label for="type" class="block text-sm font-medium mb-1">Type</label>
        <select
          id="type"
          bind:value={formData.type}
          class="w-full p-2 border rounded-md"
          required
        >
          <option value="">Select Type</option>
          {#each pokemonTypes as type}
            <option value={type.toLowerCase()}>{type}</option>
          {/each}
        </select>
      </div>

      <div>
        <label for="date_added" class="block text-sm font-medium mb-1">Date Added</label>
        <input
          type="date"
          id="date_added"
          bind:value={formData.date_added}
          class="w-full p-2 border rounded-md"
          required
        />
      </div>

      <button
        type="submit"
        class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"
      >
        Add Card
      </button>
    </div>

    <!-- Right Column - Image Upload & Preview -->
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex flex-col items-center justify-center">
      {#if imagePreview}
        <img
          src={imagePreview}
          alt="Card preview"
          class="max-w-full max-h-[400px] object-contain mb-4"
        />
      {:else}
        <div class="text-center text-gray-500 mb-4">
          No image selected
        </div>
      {/if}
      
      <label class="cursor-pointer bg-gray-100 py-2 px-4 rounded-md hover:bg-gray-200">
        <span>Choose Image</span>
        <input
          type="file"
          accept="image/*"
          class="hidden"
          on:change={handleImageChange}
          required
        />
      </label>
    </div>
  </form>
</div>
