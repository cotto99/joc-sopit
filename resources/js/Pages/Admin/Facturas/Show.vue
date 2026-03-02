<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({ factura: Object })

const formEstado = useForm({ estado: props.factura.estado })

function actualizarEstado() {
    formEstado.patch(route('admin.facturas.estado', props.factura.id))
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

const estadoClass = {
    pendiente: 'bg-yellow-100 text-yellow-700',
    pagada:    'bg-green-100 text-green-700',
    anulada:   'bg-red-100 text-red-500',
}
</script>

<template>
    <Head :title="`Factura ${factura.numero}`" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <a :href="route('admin.facturas.index')"
                   class="text-gray-400 hover:text-gray-600 text-sm">← Facturas</a>
                <h1 class="text-xl font-bold text-gray-800">📄 {{ factura.numero }}</h1>
                <span :class="estadoClass[factura.estado]"
                      class="text-xs px-2 py-1 rounded-full capitalize font-semibold">
                    {{ factura.estado }}
                </span>
            </div>
        </template>

        <div class="max-w-2xl mx-auto">

            <!-- Cabecera factura -->
            <div class="bg-white rounded-xl shadow p-6 mb-4">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-indigo-700">🛠️ JOC-SOPIT</h2>
                        <p class="text-gray-500 text-sm">Sistema de Soporte IT</p>
                    </div>
                    <div class="text-right">
                        <p class="font-mono font-bold text-lg text-gray-800">{{ factura.numero }}</p>
                        <p class="text-xs text-gray-400">{{ fmtFecha(factura.created_at) }}</p>
                    </div>
                </div>

                <!-- Datos empresa -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-xs text-gray-400 mb-1">Facturado a:</p>
                    <p class="font-bold text-gray-800 text-lg">{{ factura.empresa?.nombre }}</p>
                    <p class="text-xs text-gray-500">NIT: {{ factura.empresa?.nit || 'CF' }}</p>
                    <p class="text-xs text-gray-500">{{ factura.empresa?.direccion || '—' }}</p>
                </div>

                <!-- Ticket relacionado -->
                <div class="mb-6">
                    <p class="text-xs text-gray-400 mb-2">Ticket de soporte:</p>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-bold text-indigo-600">
                            {{ factura.ticket?.codigo }}
                        </span>
                        <a :href="route('admin.tickets.show', factura.ticket_id)"
                           class="text-xs text-indigo-500 hover:underline">
                            Ver ticket →
                        </a>
                    </div>
                </div>

                <!-- Detalle de cargos -->
                <table class="w-full text-sm mb-6">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-2 text-gray-600">Descripción</th>
                            <th class="text-center py-2 text-gray-600">Cant.</th>
                            <th class="text-right py-2 text-gray-600">P. Unit.</th>
                            <th class="text-right py-2 text-gray-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in factura.ticket?.cargos" :key="c.id"
                            class="border-b border-gray-100">
                            <td class="py-3 text-gray-700">{{ c.descripcion }}</td>
                            <td class="py-3 text-center text-gray-500">{{ c.cantidad }}</td>
                            <td class="py-3 text-right font-mono text-gray-600">{{ fmt(c.precio_unitario) }}</td>
                            <td class="py-3 text-right font-mono font-semibold">{{ fmt(c.subtotal) }}</td>
                        </tr>
                        <tr v-if="!factura.ticket?.cargos?.length">
                            <td colspan="4" class="py-4 text-center text-gray-400 text-xs">
                                Sin cargos detallados
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Totales -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-mono">{{ fmt(factura.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">IVA (12%)</span>
                        <span class="font-mono">{{ fmt(factura.impuesto) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-black border-t pt-2 mt-2">
                        <span>TOTAL</span>
                        <span class="text-indigo-700 font-mono">{{ fmt(factura.total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actualizar estado -->
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-bold text-gray-700 mb-3">Actualizar Estado</h3>
                <div class="flex gap-3">
                    <select v-model="formEstado.estado"
                            class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="pendiente">⏳ Pendiente</option>
                        <option value="pagada">✅ Pagada</option>
                        <option value="anulada">❌ Anulada</option>
                    </select>
                    <button @click="actualizarEstado" :disabled="formEstado.processing"
                            class="bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-indigo-800 disabled:opacity-50">
                        Guardar
                    </button>
                </div>
                <p v-if="formEstado.recentlySuccessful"
                   class="text-green-600 text-sm mt-2">✅ Estado actualizado</p>
            </div>
        </div>
    </AdminLayout>
</template>