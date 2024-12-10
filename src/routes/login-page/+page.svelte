<script lang="ts">
    import { goto } from "$app/navigation";
    
    let username: string = '';
    let password: string = '';
    let showPassword: boolean = false;

    const handleSubmit = () => {
        // Add your login logic here
        console.log('Login attempt:', { username, password });
    };

    async function loginUser() {
        goto("/dashboard");
    }
</script>

<div class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Image Background -->
    <div 
        class="absolute top-0 left-0 w-full h-full bg-cover bg-center -z-10"
        style="background-image: url('/wallpaperflare.com_wallpaper (3).jpg');"
    ></div>

    <!-- Dark overlay to improve form visibility -->
    <div class="absolute top-0 left-0 w-full h-full bg-black/40 -z-5"></div>

    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-lg shadow-md w-full max-w-sm z-10">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        
        <form on:submit|preventDefault={handleSubmit}>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input 
                    bind:value={username}
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    id="username" 
                    type="text" 
                    placeholder="Username"
                >
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <div class="relative">
                    <input 
                        bind:value={password}
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline pr-10" 
                        id="password" 
                        type={showPassword ? 'text' : 'password'} 
                        placeholder="********"
                    >
                    <button 
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-gray-600 h-full"
                        on:click={() => showPassword = !showPassword}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button 
                type="submit"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                on:click={loginUser}
            >
                Sign In
            </button>
        </form>
    </div>
</div>
