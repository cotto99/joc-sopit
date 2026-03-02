<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="bg-indigo-700 text-white shadow-lg sticky top-0 z-40">
            <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-black">🛠️ Portal de Soporte</h1>
                    <p class="text-indigo-200 text-xs">{{ page.props.auth?.user?.name }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <Link :href="route('cliente.tickets.index')"
                          class="text-sm text-indigo-200 hover:text-white">
                        Mis Tickets
                    </Link>
                    <Link :href="route('cliente.tickets.create')"
                          class="bg-yellow-400 text-indigo-900 px-4 py-2 rounded-lg text-sm font-bold hover:bg-yellow-300">
                        + Nuevo Ticket
                    </Link>
                    <Link :href="route('logout')" method="post" as="button"
                          class="text-xs text-indigo-300 hover:text-white">
                        Salir
                    </Link>
                </div>
            </div>
        </header>
        <main class="max-w-5xl mx-auto px-4 py-6">
            <slot />
        </main>
    </div>
</template>