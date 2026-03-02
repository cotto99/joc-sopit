<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
    stats:              Object,
    tickets_recientes:  Array,
    tickets_criticos:   Array,
})

const estadoClass = {
    abierto:    'bg-red-100 text-red-700',
    asignado:   'bg-blue-100 text-blue-700',
    en_proceso: 'bg-yellow-100 text-yellow-700',
    en_espera:  'bg-orange-100 text-orange-700',
    resuelto:   'bg-green-100 text-green-700',
    cerrado:    'bg-gray-100 text-gray-500',
}

const prioridadClass = {
    baja:    'bg-gray-100 text-gray-600',
    media:   'bg-blue-100 text-blue-600',
    alta:    'bg-orange-100 text-orange-700',
    critica: 'bg-red-100 text-red-700',
}
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">📊 Dashboard</h1>
        </template>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-red-500">
                <p class="text-3xl font-black text-red-600">{{ stats.abiertos }}</p>
                <p class="text-xs text-gray-500 mt-1">🔴 Abiertos</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-blue-500">
                <p class="text-3xl font-black text-blue-600">{{ stats.asignados }}</p>
                <p class="text-xs text-gray-500 mt-1">🔵 Asignados</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-yellow-500">
                <p class="text-3xl font-black text-yellow-600">{{ stats.en_proceso }}</p>
                <p class="text-xs text-gray-500 mt-1">🟡 En Proceso</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-green-500">
                <p class="text-3xl font-black text-green-600">{{ stats.resueltos }}</p>
                <p class="text-xs text-gray-500 mt-1">🟢 Resueltos</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-orange-500">
                <p class="text-3xl font-black text-orange-600">{{ stats.en_espera }}</p>
                <p class="text-xs text-gray-500 mt-1">🟠 En Espera</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-gray-400">
                <p class="text-3xl font-black text-gray-600">{{ stats.cerrados }}</p>
                <p class="text-xs text-gray-500 mt-1">⚫ Cerrados</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-indigo-500">
                <p class="text-3xl font-black text-indigo-600">{{ stats.empresas }}</p>
                <p class="text-xs text-gray-500 mt-1">🏢 Empresas</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-teal-500">
                <p class="text-3xl font-black text-teal-600">{{ stats.tecnicos }}</p>
                <p class="text-xs text-gray-500 mt-1">👨‍💻 Técnicos</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Tickets recientes -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="font-bold text-gray-700">🎫 Tickets Recientes</h2>
                    <a :href="route('admin.tickets.index')"
                       class="text-xs text-indigo-500 hover:underline">Ver todos →</a>
                </div>
                <div class="divide-y">
                    <div v-for="t in tickets_recientes" :key="t.id"
                         class="px-5 py-3 hover:bg-gray-50 cursor-pointer"
                         @click="$inertia.visit(route('admin.tickets.show', t.id))">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-mono text-xs text-indigo-600 font-bold">{{ t.codigo }}</span>
                                    <span :class="prioridadClass[t.prioridad]"
                                          class="text-xs px-2 py-0.5 rounded-full capitalize">
                                        {{ t.prioridad }}
                                    </span>
                                </div>
                                <p class="font-medium text-gray-800 text-sm truncate">{{ t.titulo }}</p>
                                <p class="text-xs text-gray-400">
                                    🏢 {{ t.empresa?.nombre }}
                                    <span v-if="t.sucursal"> — {{ t.sucursal?.nombre }}</span>
                                </p>
                            </div>
                            <span :class="estadoClass[t.estado]"
                                  class="text-xs px-2 py-1 rounded-full capitalize flex-shrink-0">
                                {{ t.estado.replace('_', ' ') }}
                            </span>
                        </div>
                    </div>
                    <div v-if="!tickets_recientes.length"
                         class="px-5 py-8 text-center text-gray-400">
                        No hay tickets aún
                    </div>
                </div>
            </div>

            <!-- Tickets críticos -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b bg-red-50">
                    <h2 class="font-bold text-red-700">🚨 Tickets Críticos</h2>
                </div>
                <div class="divide-y">
                    <div v-for="t in tickets_criticos" :key="t.id"
                         class="px-5 py-3 hover:bg-red-50 cursor-pointer"
                         @click="$inertia.visit(route('admin.tickets.show', t.id))">
                        <p class="font-mono text-xs text-red-600 font-bold">{{ t.codigo }}</p>
                        <p class="font-medium text-gray-800 text-sm truncate">{{ t.titulo }}</p>
                        <p class="text-xs text-gray-400">🏢 {{ t.empresa?.nombre }}</p>
                    </div>
                    <div v-if="!tickets_criticos.length"
                         class="px-5 py-8 text-center text-gray-400 text-sm">
                        Sin tickets críticos 🎉
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>