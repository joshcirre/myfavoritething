<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <!-- ... rest of the navigation ... -->
            </div>

            <!-- ... rest of the navigation bar ... -->
        </div>
    </div>

    <!-- ... responsive navigation ... -->
</nav>
