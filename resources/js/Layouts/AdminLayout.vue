<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
const page = usePage()

const nav = [
    { name: 'Dashboard',   href: route('admin.dashboard'),          icon: '📊' },
    { name: 'Tickets',     href: route('admin.tickets.index'),      icon: '🎫' },
    { name: 'Empresas',    href: route('admin.empresas.index'),     icon: '🏢' },
    { name: 'Sucursales',  href: route('admin.sucursales.index'),   icon: '🏪' },
    { name: 'Contactos',   href: route('admin.contactos.index'),    icon: '👤' },
    { name: 'Técnicos',    href: route('admin.tecnicos.index'),     icon: '👨‍💻' },
    { name: 'Categorías',  href: route('admin.categorias.index'),   icon: '🔧' },
    { name: 'Facturas',    href: route('admin.facturas.index'),     icon: '📄' },
]

function isActive(href) {
    try { return page.url.startsWith(new URL(href).pathname) }
    catch { return false }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">

        <!-- TOPBAR MÓVIL -->
        <header class="lg:hidden bg-indigo-900 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-lg">
            <span class="text-lg font-bold">🛠️ JOC-SOPIT</span>
            <div class="flex items-center gap-3">
                <Link :href="route('logout')" method="post" as="button" class="text-xs text-indigo-200">Salir</Link>
                <button @click="sidebarOpen = !sidebarOpen" class="text-2xl">{{ sidebarOpen ? '✕' : '☰' }}</button>
            </div>
        </header>

        <!-- DRAWER MÓVIL -->
        <transition name="fade">
            <div v-if="sidebarOpen" class="lg:hidden fixed inset-0 z-30 flex">
                <div class="absolute inset-0 bg-black/50" @click="sidebarOpen = false"></div>
                <nav class="relative bg-indigo-900 text-white w-72 h-full z-40 pt-4">
                    <div class="px-4 pb-4 border-b border-indigo-800 mb-2">
                        <p class="text-xs text-indigo-300">{{ page.props.auth?.user?.rol }}</p>
                        <p class="font-semibold">{{ page.props.auth?.user?.name }}</p>
                    </div>
                    <Link v-for="item in nav" :key="item.name" :href="item.href"
                          @click="sidebarOpen = false"
                          class="flex items-center gap-4 px-5 py-4 hover:bg-indigo-800 transition"
                          :class="{ 'bg-indigo-800 border-l-4 border-yellow-400': isActive(item.href) }">
                        <span class="text-2xl">{{ item.icon }}</span>
                        <span class="font-medium">{{ item.name }}</span>
                    </Link>
                </nav>
            </div>
        </transition>

        <!-- LAYOUT DESKTOP -->
        <div class="hidden lg:flex min-h-screen">
            <aside class="w-64 bg-indigo-900 text-white flex flex-col sticky top-0 min-h-screen">
                <div class="p-5 border-b border-indigo-800">
                    <h1 class="text-xl font-black">🛠️ JOC-SOPIT</h1>
                    <p class="text-xs text-indigo-300 mt-1">Sistema de Soporte IT</p>
                </div>
                <nav class="flex-1 py-4">
                    <Link v-for="item in nav" :key="item.name" :href="item.href"
                          class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-800 transition"
                          :class="{ 'bg-indigo-800 border-l-4 border-yellow-400': isActive(item.href) }">
                        <span class="text-xl">{{ item.icon }}</span>
                        <span class="text-sm font-medium">{{ item.name }}</span>
                    </Link>
                </nav>
                <div class="p-4 border-t border-indigo-800">
                    <p class="text-xs text-indigo-300 capitalize">{{ page.props.auth?.user?.rol }}</p>
                    <p class="text-sm font-medium">{{ page.props.auth?.user?.name }}</p>
                    <Link :href="route('logout')" method="post" as="button"
                          class="text-xs text-indigo-300 hover:text-white mt-1">
                        Cerrar sesión →
                    </Link>
                </div>
            </aside>
            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow-sm px-6 py-4 sticky top-0 z-10">
                    <slot name="header" />
                </header>
                <main class="flex-1 p-6"><slot /></main>
            </div>
        </div>

        <main class="lg:hidden p-4 pb-6"><slot /></main>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>