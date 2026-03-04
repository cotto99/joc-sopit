<script setup>
import ClienteLayout from '@/Layouts/ClienteLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({ ticket: Object })

const formComentario = useForm({ contenido: '' })

function enviarComentario() {
    formComentario.post(route('cliente.tickets.comentario', props.ticket.id), {
        onSuccess: () => formComentario.reset()
    })
}

const estadoClass = {
    abierto:    'bg-red-100 text-red-700 border-red-200',
    asignado:   'bg-blue-100 text-blue-700 border-blue-200',
    en_proceso: 'bg-yellow-100 text-yellow-700 border-yellow-200',
    en_espera:  'bg-orange-100 text-orange-700 border-orange-200',
    resuelto:   'bg-green-100 text-green-700 border-green-200',
    cerrado:    'bg-gray-100 text-gray-600 border-gray-200',
}

const estadoDescripcion = {
    abierto:    'Tu ticket fue recibido y está en espera de ser asignado.',
    asignado:   'Tu ticket fue asignado a un técnico.',
    en_proceso: 'Un técnico está trabajando en tu ticket.',
    en_espera:  'El ticket está en espera de información o recursos.',
    resuelto:   '¡Tu ticket fue resuelto! Revisá los detalles.',
    cerrado:    'Este ticket fue cerrado.',
}

const tipoIcono = {
    comentario: '💬',
    estado:     '🔄',
    accion:     '⚡',
}

function fmt(fecha) {
    if (!fecha) return '—'
    return new Date(fecha).toLocaleString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    })
}

function fmtMoneda(val) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency', currency: 'GTQ'
    }).format(val || 0)
}
</script>

<template>
    <Head :title="`Ticket ${ticket.codigo}`" />
    <ClienteLayout>

        <!-- Header del ticket -->
        <div class="mb-6">
            <a :href="route('cliente.tickets.index')"
               class="text-indigo-600 hover:underline text-sm">← Mis Tickets</a>
            <div class="flex items-center gap-3 mt-2 flex-wrap">
                <h2 class="text-xl font-black text-gray-800">{{ ticket.titulo }}</h2>
                <span class="font-mono text-sm text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded">
                    {{ ticket.codigo }}
                </span>
            </div>
        </div>

        <!-- Banner de estado -->
        <div :class="estadoClass[ticket.estado]"
             class="rounded-xl border p-4 mb-6 flex items-center gap-4">
            <div class="text-3xl">
                {{ { abierto:'🔴', asignado:'🔵', en_proceso:'🟡', en_espera:'🟠', resuelto:'🟢', cerrado:'⚫' }[ticket.estado] }}
            </div>
            <div>
                <p class="font-bold capitalize text-lg">
                    {{ ticket.estado.replace('_', ' ') }}
                </p>
                <p class="text-sm opacity-80">{{ estadoDescripcion[ticket.estado] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Info del ticket -->
            <div class="lg:col-span-1 space-y-4">

                <!-- Detalles -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-700 mb-3">📋 Detalles</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Descripción</p>
                            <p class="text-gray-700 leading-relaxed">{{ ticket.descripcion }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <p class="text-xs text-gray-400">Sucursal</p>
                                <p class="font-medium">{{ ticket.sucursal?.nombre || 'General' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Tipo</p>
                                <p class="font-medium">{{ ticket.categoria?.nombre || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Prioridad</p>
                                <p class="font-medium capitalize">{{ ticket.prioridad }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Creado</p>
                                <p class="font-medium">{{ fmt(ticket.created_at) }}</p>
                            </div>
                            <div v-if="ticket.fecha_resolucion" class="col-span-2">
                                <p class="text-xs text-gray-400">Resuelto</p>
                                <p class="font-medium text-green-600">{{ fmt(ticket.fecha_resolucion) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Técnico -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-700 mb-3">👨‍💻 Tu Técnico</h3>
                    <div v-if="ticket.tecnico" class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-xl flex-shrink-0">
                            👨‍💻
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">
                                {{ ticket.tecnico.nombre }} {{ ticket.tecnico.apellido }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ ticket.tecnico.especialidad || 'Soporte IT' }}
                            </p>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <p class="text-gray-400 text-sm">⏳ Pendiente de asignación</p>
                        <p class="text-xs text-gray-400 mt-1">Te notificaremos cuando se asigne un técnico</p>
                    </div>
                </div>

                <!-- Factura / Cobro -->
                <div v-if="ticket.factura || ticket.cargos?.length"
                     class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-700 mb-3">💰 Detalle de Cobro</h3>

                    <!-- Cargos -->
                    <div v-if="ticket.cargos?.length" class="space-y-2 mb-3">
                        <div v-for="c in ticket.cargos" :key="c.id"
                             class="flex justify-between text-sm border-b pb-2">
                            <div>
                                <p class="font-medium text-gray-700">{{ c.descripcion }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ c.cantidad }} × {{ fmtMoneda(c.precio_unitario) }}
                                </p>
                            </div>
                            <p class="font-bold text-gray-800">{{ fmtMoneda(c.subtotal) }}</p>
                        </div>
                    </div>

                    <!-- Factura generada -->
                    <div v-if="ticket.factura"
                         class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-xs text-green-600 font-bold mb-1">📄 FACTURA</p>
                        <p class="font-mono text-sm text-green-700">{{ ticket.factura.numero }}</p>
                        <div class="mt-2 space-y-1 text-xs text-green-700">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>{{ fmtMoneda(ticket.factura.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>IVA (12%):</span>
                                <span>{{ fmtMoneda(ticket.factura.impuesto) }}</span>
                            </div>
                            <div class="flex justify-between font-black text-base border-t border-green-300 pt-1 mt-1">
                                <span>TOTAL:</span>
                                <span>{{ fmtMoneda(ticket.factura.total) }}</span>
                            </div>
                        </div>
                        <span :class="ticket.factura.estado === 'pagada'
                                ? 'bg-green-200 text-green-800'
                                : 'bg-yellow-100 text-yellow-700'"
                              class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs capitalize font-semibold">
                            {{ ticket.factura.estado }}
                        </span>
                    </div>

                    <div v-else-if="ticket.cargos?.length"
                         class="text-center text-xs text-gray-400 mt-2 py-2 border-t">
                        Factura pendiente de generación
                    </div>
                </div>
            </div>

            <!-- Historial / seguimiento -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-5 py-4 border-b bg-indigo-50">
                        <h3 class="font-bold text-indigo-800">📜 Historial del Ticket</h3>
                        <p class="text-xs text-indigo-500 mt-0.5">
                            Seguimiento completo de todas las acciones
                        </p>
                    </div>

                    <!-- Timeline -->
 
                    <div class="p-5 space-y-4 max-h-[450px] overflow-y-auto">
                        <div v-for="s in ticket.seguimientos" :key="s.id"
                             class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm"
                                 :class="s.tipo === 'estado'
                                     ? 'bg-indigo-100'
                                     : 'bg-gray-100'">
                                {{ tipoIcono[s.tipo] }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-semibold text-xs text-gray-700">
                                        {{ s.user?.name || 'Sistema' }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ fmt(s.created_at) }}</span>
                                </div>
                                <!-- Cambio de estado -->
                                <div v-if="s.tipo === 'estado' && s.estado_nuevo"
                                     class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span v-if="s.estado_anterior"
                                          class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 capitalize">
                                        {{ s.estado_anterior.replace('_', ' ') }}
                                    </span>
                                    <span v-if="s.estado_anterior" class="text-gray-400 text-xs">→</span>
                                    <span :class="estadoClass[s.estado_nuevo]"
                                          class="text-xs px-2 py-0.5 rounded-full capitalize border">
                                        {{ s.estado_nuevo.replace('_', ' ') }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-3 py-2">
    <p class="text-sm text-gray-700">{{ s.contenido }}</p>
</div>

<div v-if="s.foto_evidencia" class="mt-2">
    <a :href="s.foto_evidencia" target="_blank">
        <img :src="s.foto_evidencia"
             class="h-40 rounded-lg border object-cover hover:opacity-90 transition cursor-pointer" />
    </a>
    <p class="text-xs text-gray-400 mt-1">📷 Toca para ver completa</p>
</div>
                            </div>
                        </div>

                        <div v-if="!ticket.seguimientos?.length"
                             class="text-center text-gray-400 py-8">
                            <p class="text-3xl mb-2">📋</p>
                            <p class="text-sm">Sin actividad registrada aún</p>
                        </div>
                    </div>

                    <!-- Comentario del cliente -->
                    <div v-if="!['resuelto','cerrado'].includes(ticket.estado)"
                         class="px-5 py-4 border-t bg-gray-50">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">
                            💬 Agregar información adicional
                        </h4>
                        <textarea v-model="formComentario.contenido" rows="3"
                                  class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                  placeholder="¿Tenés información adicional sobre el problema?"></textarea>
                        <div class="flex justify-end mt-2">
                            <button @click="enviarComentario"
                                    :disabled="!formComentario.contenido || formComentario.processing"
                                    class="bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-800 disabled:opacity-50">
                                {{ formComentario.processing ? '⏳ Enviando...' : 'Enviar' }}
                            </button>
                        </div>
                    </div>

                    <!-- Ticket cerrado -->
                    <div v-else class="px-5 py-4 border-t bg-green-50 text-center">
                        <p class="text-green-700 font-bold text-sm">
                            {{ ticket.estado === 'resuelto' ? '✅ Ticket Resuelto' : '⚫ Ticket Cerrado' }}
                        </p>
                        <p class="text-green-600 text-xs mt-1">
                            {{ ticket.estado === 'resuelto'
                                ? '¡Gracias por confiar en nosotros!'
                                : 'Este ticket ya no acepta comentarios.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ClienteLayout>
</template>