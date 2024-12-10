<script>
    let isOpen = false;
    const toggleMenu = () => isOpen = !isOpen;
</script>

<nav class="sticky top-0 bg-gray-800 p-4 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <div class="text-white text-lg font-bold">
            <a href="/">Your Logo</a>
        </div>

        <!-- Hamburger menu for mobile -->
        <button class="text-white md:hidden" on:click={toggleMenu}>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <!-- Nav links -->
        <div class={`md:flex md:items-center ${isOpen ? 'block' : 'hidden'}`}>
            <a href="/dashboard" class="block mt-4 md:inline-block md:mt-0 text-white hover:text-gray-400 mr-4">Dashboard</a>
            <a href="/AddCard" class="block mt-4 md:inline-block md:mt-0 text-white hover:text-gray-400 mr-4">Add Card</a>
            <a href="/services" class="block mt-4 md:inline-block md:mt-0 text-white hover:text-gray-400 mr-4">Services</a>
            <a href="/contact" class="block mt-4 md:inline-block md:mt-0 text-white hover:text-gray-400">Contact</a>
        </div>
    </div>
</nav>
