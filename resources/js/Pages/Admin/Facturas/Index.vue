<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({ facturas: Object })

const estadoClass = {
    pendiente: 'bg-yellow-100 text-yellow-700',
    pagada:    'bg-green-100 text-green-700',
    anulada:   'bg-red-100 text-red-500',
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency', currency: 'GTQ'
    }).format(val || 0)
}

function fmtFecha(f) {
    if (!f) return '—'
    return new Date(f).toLocaleDateString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    })
}
</script>

<template>
    <Head title="Facturas" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">📄 Facturas</h1>
        </template>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-yellow-400">
                <p class="text-2xl font-black text-yellow-600">
                    {{ facturas.data?.filter(f => f.estado === 'pendiente').length }}
                </p>
                <p class="text-xs text-gray-500 mt-1">⏳ Pendientes</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-green-400">
                <p class="text-2xl font-black text-green-600">
                    {{ facturas.data?.filter(f => f.estado === 'pagada').length }}
                </p>
                <p class="text-xs text-gray-500 mt-1">✅ Pagadas</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-indigo-400">
                <p class="text-2xl font-black text-indigo-600">
                    {{ fmt(facturas.data?.reduce((s, f) => s + parseFloat(f.total), 0)) }}
                </p>
                <p class="text-xs text-gray-500 mt-1">💰 Total facturado</p>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[750px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">N° Factura</th>
                        <th class="text-left px-4 py-3">Ticket</th>
                        <th class="text-left px-4 py-3">Empresa</th>
                        <th class="text-right px-4 py-3">Subtotal</th>
                        <th class="text-right px-4 py-3">IVA</th>
                        <th class="text-right px-4 py-3">Total</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="f in facturas.data" :key="f.id"
                        class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-700">
                            {{ f.numero }}
                        </td>
                        <td class="px-4 py-3">
                            <a :href="route('admin.tickets.show', f.ticket_id)"
                               class="font-mono text-xs text-indigo-500 hover:underline">
                                {{ f.ticket?.codigo }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ f.empresa?.nombre }}</td>
                        <td class="px-4 py-3 text-right font-mono text-xs">{{ fmt(f.subtotal) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-xs text-gray-400">{{ fmt(f.impuesto) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-800">{{ fmt(f.total) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="estadoClass[f.estado]"
                                  class="text-xs px-2 py-1 rounded-full capitalize font-semibold">
                                {{ f.estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400">
                            {{ fmtFecha(f.created_at) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a :href="route('admin.facturas.show', f.id)"
                               class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-indigo-700">
                                Ver →
                            </a>
                        </td>
                    </tr>
                    <tr v-if="!facturas.data?.length">
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            <p class="text-4xl mb-2">📄</p>
                            <p>No hay facturas generadas aún</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="facturas.last_page > 1"
                 class="px-4 py-3 border-t flex gap-2 justify-end flex-wrap">
                <a v-for="p in facturas.links" :key="p.label"
                   :href="p.url" v-html="p.label"
                   class="px-3 py-1 rounded border text-sm"
                   :class="p.active ? 'bg-indigo-700 text-white' : 'text-gray-600 hover:bg-gray-50'" />
            </div>
        </div>
    </AdminLayout>
</template>