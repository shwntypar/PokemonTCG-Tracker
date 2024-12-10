<script lang="ts">
    import Navbar from "../../lib/navbar/+navbar.svelte";
    import { onMount } from "svelte";
    import { api } from "$lib/navbar/services/api.ts";

    async function getPokemonCards(){
        try{
            const response = await api.get("getPokemonCards");
            pokemonCards = response.payload;
            console.log(pokemonCards);
        }catch(error){
            console.error(error);
        }
    }

    let pokemonCards:any = $state();
    
    onMount(
        getPokemonCards
    );


</script>

 

<Navbar />

<style>
    img {
        width: full;
        height: 320px;
    }
</style>

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Your Card Collection</h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        {#each pokemonCards as card}
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                <img 
                    src="/uploads/{card.image}" 
                    alt={card.name} 
                    class="w-full h-auto object-cover mx-auto"
                >
                <div class="text-start p-4">
                    <h2 class="text-lg font-bold mb-2">{card.name}</h2>
                    <p class="text-gray-600">Rarity: {card.rarity}</p>
                    <p class="text-gray-600">Type {card.type}</p>
                </div>
            </div>
        {/each}
    </div>
</div>