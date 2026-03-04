<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    ticket:     Object,
    tecnicos:   Array,
    categorias: Array,
})

// Forms
const formAsignar    = useForm({ tecnico_id: props.ticket.tecnico_id || '' })
const formEstado = useForm({
    estado:          props.ticket.estado,
    comentario:      '',
    visible_cliente: true,
    foto_evidencia:  null,
})
const formComentario = useForm({
    contenido:       '',
    visible_cliente: true,
    foto_evidencia:  null,
})
const formFactura = useForm({
    aplica_iva:     false,
    porcentaje_iva: 12,
    notas:          '',
})

const formCargo      = useForm({ descripcion: '', cantidad: 1, precio_unitario: '' })


const previewEstado      = ref(null)
const previewComentario  = ref(null)
const modalFactura       = ref(false)
const modalAsignar  = ref(false)
const modalEstado   = ref(false)
const modalCargo    = ref(false)


function onFotoEstado(e) {
    const file = e.target.files[0]
    if (!file) return
    formEstado.foto_evidencia = file
    previewEstado.value = URL.createObjectURL(file)
}

function onFotoComentario(e) {
    const file = e.target.files[0]
    if (!file) return
    formComentario.foto_evidencia = file
    previewComentario.value = URL.createObjectURL(file)
}
function cambiarEstado() {
    formEstado.post(route('admin.tickets.estado', props.ticket.id), {
        forceFormData: true,
        onSuccess: () => {
            modalEstado.value  = false
            previewEstado.value = null
        }
    })
}

function asignar() {
    formAsignar.patch(route('admin.tickets.asignar', props.ticket.id), {
        onSuccess: () => modalAsignar.value = false
    })
}



function enviarComentario() {
    formComentario.post(route('admin.tickets.comentario', props.ticket.id), {
        forceFormData: true,
        onSuccess: () => {
            formComentario.reset()
            previewComentario.value = null
        }
    })
}

function agregarCargo() {
    formCargo.post(route('admin.tickets.cargo', props.ticket.id), {
        onSuccess: () => {
            formCargo.reset()
            modalCargo.value = false
        }
    })
}

function eliminarCargo(cargoId) {
    if (confirm('¿Eliminar este cargo?'))
        router.delete(route('admin.tickets.cargo.eliminar', cargoId), { preserveScroll: true })
}

function generarFactura() {
    formFactura.post(route('admin.tickets.factura', props.ticket.id), {
        onSuccess: () => modalFactura.value = false
    })
}

const totalCargos = computed(() =>
    props.ticket.cargos?.reduce((sum, c) => sum + parseFloat(c.subtotal), 0) || 0
)

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}

function fmtFecha(f) {
    if (!f) return '—'
    return new Date(f).toLocaleString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    })
}

const estadoClass = {
    abierto:    'bg-red-100 text-red-700',
    asignado:   'bg-blue-100 text-blue-700',
    en_proceso: 'bg-yellow-100 text-yellow-700',
    en_espera:  'bg-orange-100 text-orange-700',
    resuelto:   'bg-green-100 text-green-700',
    cerrado:    'bg-gray-100 text-gray-500',
}

const prioridadClass = {
    baja:    'bg-gray-100 text-gray-500',
    media:   'bg-blue-100 text-blue-600',
    alta:    'bg-orange-100 text-orange-600',
    critica: 'bg-red-100 text-red-700',
}

const tipoSeguimientoIcono = {
    comentario: '💬',
    estado:     '🔄',
    accion:     '⚡',
}
</script>

<template>
    <Head :title="`Ticket ${ticket.codigo}`" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3 flex-wrap">
                <a :href="route('admin.tickets.index')"
                   class="text-gray-400 hover:text-gray-600 text-sm">← Tickets</a>
                <span class="font-mono font-bold text-indigo-700">{{ ticket.codigo }}</span>
                <span :class="estadoClass[ticket.estado]"
                      class="text-xs px-2 py-1 rounded-full capitalize">
                    {{ ticket.estado.replace('_', ' ') }}
                </span>
                <span :class="prioridadClass[ticket.prioridad]"
                      class="text-xs px-2 py-1 rounded-full capitalize">
                    {{ ticket.prioridad }}
                </span>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Columna izquierda: info + acciones -->
            <div class="lg:col-span-1 space-y-4">

                <!-- Info del ticket -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-700 mb-3">📋 Información</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Título</p>
                            <p class="font-semibold text-gray-800">{{ ticket.titulo }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Descripción</p>
                            <p class="text-gray-600 text-xs leading-relaxed">{{ ticket.descripcion }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <div>
                                <p class="text-xs text-gray-400">Empresa</p>
                                <p class="font-medium text-xs">{{ ticket.empresa?.nombre }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Sucursal</p>
                                <p class="font-medium text-xs">{{ ticket.sucursal?.nombre || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Categoría</p>
                                <p class="font-medium text-xs">{{ ticket.categoria?.nombre || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Creado</p>
                                <p class="font-medium text-xs">{{ fmtFecha(ticket.created_at) }}</p>
                            </div>
                            <div v-if="ticket.fecha_resolucion">
                                <p class="text-xs text-gray-400">Resuelto</p>
                                <p class="font-medium text-xs text-green-600">{{ fmtFecha(ticket.fecha_resolucion) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Técnico asignado -->
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-700">👨‍💻 Técnico</h3>
                        <button @click="modalAsignar = true"
                                class="text-xs text-indigo-600 hover:underline">
                            {{ ticket.tecnico ? 'Cambiar' : 'Asignar' }}
                        </button>
                    </div>
                    <div v-if="ticket.tecnico">
                        <p class="font-semibold text-gray-800">
                            {{ ticket.tecnico.nombre }} {{ ticket.tecnico.apellido }}
                        </p>
                        <p class="text-xs text-gray-400">{{ ticket.tecnico.especialidad || 'Sin especialidad' }}</p>
                    </div>
                    <p v-else class="text-gray-400 text-sm">Sin técnico asignado</p>
                </div>

                <!-- Acciones de estado -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-700 mb-3">🔄 Cambiar Estado</h3>
                    <button @click="modalEstado = true"
                            class="w-full bg-indigo-700 text-white py-2 rounded-lg text-sm hover:bg-indigo-800">
                        Actualizar Estado
                    </button>
                </div>

                <!-- Cargos -->
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-700">💰 Cargos</h3>
                        <button @click="modalCargo = true"
                                class="text-xs text-indigo-600 hover:underline">+ Agregar</button>
                    </div>
                    <div class="space-y-2">
                        <div v-for="c in ticket.cargos" :key="c.id"
                             class="flex items-start justify-between gap-2 text-xs border-b pb-2">
                            <div class="flex-1">
                                <p class="font-medium text-gray-700">{{ c.descripcion }}</p>
                                <p class="text-gray-400">{{ c.cantidad }} × {{ fmt(c.precio_unitario) }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-gray-800">{{ fmt(c.subtotal) }}</p>
                                <button @click="eliminarCargo(c.id)"
                                        class="text-red-400 hover:text-red-600 text-xs">Eliminar</button>
                            </div>
                        </div>
                        <div v-if="!ticket.cargos?.length"
                             class="text-gray-400 text-xs text-center py-2">
                            Sin cargos registrados
                        </div>
                    </div>
                    <div v-if="ticket.cargos?.length"
                         class="border-t pt-2 mt-2 flex justify-between font-bold text-sm">
                        <span>Total</span>
                        <span class="text-indigo-700">{{ fmt(totalCargos) }}</span>
                    </div>

                    <!-- Factura -->
<div class="mt-4">
    <div v-if="ticket.factura"
         class="bg-green-50 border border-green-200 rounded-lg p-3 text-xs space-y-1">
        <p class="font-bold text-green-700">✅ Factura generada</p>
        <p class="text-green-600 font-mono">{{ ticket.factura.numero }}</p>
        <div v-if="ticket.factura.aplica_iva"
             class="text-green-600">
            <p>Subtotal: {{ fmt(ticket.factura.subtotal) }}</p>
            <p>IVA ({{ ticket.factura.porcentaje_iva }}%): {{ fmt(ticket.factura.impuesto) }}</p>
        </div>
        <p class="font-black text-green-800 text-sm">
            Total: {{ fmt(ticket.factura.total) }}
        </p>
        <span :class="ticket.factura.estado === 'pagada'
                ? 'bg-green-100 text-green-700'
                : 'bg-yellow-100 text-yellow-700'"
              class="px-2 py-0.5 rounded-full capitalize text-xs">
            {{ ticket.factura.estado }}
        </span>
    </div>

    <button v-else-if="ticket.cargos?.length"
            @click="modalFactura = true"
            class="w-full mt-2 bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700">
        📄 Generar Factura
    </button>
</div>

<!-- Modal Generar Factura con IVA -->
<div v-if="modalFactura"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="bg-green-600 text-white p-5 rounded-t-xl">
            <h3 class="font-bold">📄 Generar Factura</h3>
        </div>
        <div class="p-5 space-y-4">

            <!-- Subtotal de referencia -->
            <div class="bg-gray-50 rounded-lg p-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal cargos:</span>
                    <span class="font-bold">{{ fmt(totalCargos) }}</span>
                </div>
            </div>

            <!-- IVA toggle -->
            <div class="border rounded-xl p-4 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" v-model="formFactura.aplica_iva" class="sr-only" />
                        <div :class="formFactura.aplica_iva ? 'bg-green-500' : 'bg-gray-300'"
                             class="w-10 h-5 rounded-full transition-colors"></div>
                        <div :class="formFactura.aplica_iva ? 'translate-x-5' : 'translate-x-0'"
                             class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                    </div>
                    <span class="font-medium text-gray-700">Aplicar IVA</span>
                </label>

                <div v-if="formFactura.aplica_iva" class="space-y-2">
                    <label class="block text-sm text-gray-600">Porcentaje de IVA</label>
                    <div class="relative">
                        <input v-model="formFactura.porcentaje_iva"
                               type="number" step="0.1" min="0" max="100"
                               class="w-full border rounded-lg px-3 py-2 text-sm pr-8 focus:ring-2 focus:ring-green-400" />
                        <span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
                    </div>
                </div>
            </div>

            <!-- Preview del total -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-mono">{{ fmt(totalCargos) }}</span>
                </div>
                <div v-if="formFactura.aplica_iva" class="flex justify-between text-green-700">
                    <span>IVA ({{ formFactura.porcentaje_iva }}%):</span>
                    <span class="font-mono">
                        {{ fmt(totalCargos * formFactura.porcentaje_iva / 100) }}
                    </span>
                </div>
                <div class="flex justify-between font-black text-base border-t pt-2">
                    <span>TOTAL:</span>
                    <span class="text-green-700">
                        {{ fmt(formFactura.aplica_iva
                            ? totalCargos + (totalCargos * formFactura.porcentaje_iva / 100)
                            : totalCargos) }}
                    </span>
                </div>
            </div>

            <!-- Notas -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">Notas (opcional)</label>
                <textarea v-model="formFactura.notas" rows="2"
                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400"
                          placeholder="Observaciones de la factura..."></textarea>
            </div>
        </div>
        <div class="p-5 border-t flex gap-3">
            <button @click="modalFactura = false"
                    class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm">
                Cancelar
            </button>
            <button @click="generarFactura" :disabled="formFactura.processing"
                    class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-green-700 disabled:opacity-50">
                {{ formFactura.processing ? '⏳ Generando...' : '✅ Confirmar Factura' }}
            </button>
        </div>
    </div>
</div>
                </div>
            </div>

            <!-- Columna derecha: seguimiento -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-5 py-4 border-b bg-indigo-50">
                        <h3 class="font-bold text-indigo-800">📜 Historial de Seguimiento</h3>
                    </div>

                    <!-- Timeline -->
                    <div class="p-5 space-y-4 max-h-[500px] overflow-y-auto">
                        <div v-for="s in ticket.seguimientos" :key="s.id"
                             class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm"
                                 :class="s.tipo === 'estado'
                                     ? 'bg-indigo-100'
                                     : s.tipo === 'accion'
                                     ? 'bg-yellow-100'
                                     : 'bg-gray-100'">
                                {{ tipoSeguimientoIcono[s.tipo] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-xs text-gray-700">
                                        {{ s.user?.name || 'Sistema' }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ fmtFecha(s.created_at) }}
                                    </span>
                                    <span v-if="!s.visible_cliente"
                                          class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded">
                                        Solo interno
                                    </span>
                                </div>
                                <!-- Cambio de estado -->
                                <div v-if="s.tipo === 'estado' && s.estado_nuevo"
                                     class="flex items-center gap-2 mb-1">
                                    <span v-if="s.estado_anterior"
                                          :class="estadoClass[s.estado_anterior]"
                                          class="text-xs px-2 py-0.5 rounded-full capitalize">
                                        {{ s.estado_anterior.replace('_', ' ') }}
                                    </span>
                                    <span v-if="s.estado_anterior" class="text-gray-400 text-xs">→</span>
                                    <span :class="estadoClass[s.estado_nuevo]"
                                          class="text-xs px-2 py-0.5 rounded-full capitalize">
                                        {{ s.estado_nuevo.replace('_', ' ') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                                    {{ s.contenido }}
                                </p>
                               
<div v-if="s.foto_evidencia" class="mt-2">
    <a :href="s.foto_evidencia" target="_blank">
        <img :src="s.foto_evidencia"
             class="h-40 rounded-lg border object-cover hover:opacity-90 transition cursor-pointer" />
    </a>
    <p class="text-xs text-gray-400 mt-1">📷 Ver foto completa</p>
</div>
                            </div>
                        </div>
                        <div v-if="!ticket.seguimientos?.length"
                             class="text-center text-gray-400 py-8">
                            Sin seguimientos registrados
                        </div>
                    </div>

                    <!-- Agregar comentario -->
                    <!-- Agregar comentario con foto -->
<div class="px-5 py-4 border-t bg-gray-50">
    <h4 class="text-sm font-bold text-gray-700 mb-3">💬 Agregar Comentario</h4>
    <textarea v-model="formComentario.contenido" rows="3"
              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 mb-2"
              placeholder="Escribí un comentario o nota de seguimiento..."></textarea>

    <!-- Upload foto -->
    <div class="mb-3">
        <label class="block text-xs text-gray-500 mb-1">📷 Foto de evidencia (opcional)</label>
        <input type="file" accept="image/*"
               @change="onFotoComentario"
               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
        <div v-if="previewComentario" class="mt-2">
            <img :src="previewComentario"
                 class="h-32 rounded-lg border object-cover" />
            <button @click="formComentario.foto_evidencia = null; previewComentario = null"
                    class="text-xs text-red-400 hover:text-red-600 mt-1">
                ✕ Quitar foto
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-xs text-gray-500 cursor-pointer">
            <input type="checkbox" v-model="formComentario.visible_cliente" class="rounded" />
            Visible para el cliente
        </label>
        <button @click="enviarComentario" :disabled="formComentario.processing"
                class="bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-800 disabled:opacity-50">
            {{ formComentario.processing ? '⏳ Enviando...' : 'Enviar' }}
        </button>
    </div>
</div>
                </div>
            </div>
        </div>

        <!-- Modal asignar técnico -->
        <div v-if="modalAsignar"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="bg-indigo-700 text-white p-5 rounded-t-xl">
                    <h3 class="font-bold">👨‍💻 Asignar Técnico</h3>
                </div>
                <div class="p-5">
                    <label class="block text-sm text-gray-600 mb-1">Seleccioná un técnico *</label>
                    <select v-model="formAsignar.tecnico_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="">Seleccionar...</option>
                        <option v-for="t in tecnicos" :key="t.id" :value="t.id">
                            {{ t.nombre }} {{ t.apellido }}
                            <span v-if="t.especialidad"> — {{ t.especialidad }}</span>
                        </option>
                    </select>
                </div>
                <div class="p-5 border-t flex gap-3">
                    <button @click="modalAsignar = false"
                            class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm">
                        Cancelar
                    </button>
                    <button @click="asignar" :disabled="!formAsignar.tecnico_id || formAsignar.processing"
                            class="flex-1 bg-indigo-700 text-white py-2 rounded-lg text-sm font-bold hover:bg-indigo-800 disabled:opacity-50">
                        Asignar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal cambiar estado -->
        <div v-if="modalEstado"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="bg-indigo-700 text-white p-5 rounded-t-xl sticky top-0">
            <h3 class="font-bold">🔄 Cambiar Estado</h3>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nuevo estado *</label>
                <select v-model="formEstado.estado"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                    <option value="abierto">🔴 Abierto</option>
                    <option value="asignado">🔵 Asignado</option>
                    <option value="en_proceso">🟡 En Proceso</option>
                    <option value="en_espera">🟠 En Espera</option>
                    <option value="resuelto">🟢 Resuelto</option>
                    <option value="cerrado">⚫ Cerrado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Comentario</label>
                <textarea v-model="formEstado.comentario" rows="3"
                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400"
                          placeholder="Explicá el motivo del cambio..."></textarea>
            </div>

            <!-- Foto de evidencia -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">📷 Foto de evidencia (opcional)</label>
                <input type="file" accept="image/*"
                       @change="onFotoEstado"
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <div v-if="previewEstado" class="mt-2">
                    <img :src="previewEstado"
                         class="h-32 rounded-lg border object-cover w-full" />
                    <button @click="formEstado.foto_evidencia = null; previewEstado = null"
                            class="text-xs text-red-400 hover:text-red-600 mt-1">
                        ✕ Quitar foto
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                <input type="checkbox" v-model="formEstado.visible_cliente" class="rounded" />
                Visible para el cliente
            </label>
        </div>
        <div class="p-5 border-t flex gap-3 sticky bottom-0 bg-white">
            <button @click="modalEstado = false"
                    class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm">
                Cancelar
            </button>
            <button @click="cambiarEstado" :disabled="formEstado.processing"
                    class="flex-1 bg-indigo-700 text-white py-2 rounded-lg text-sm font-bold hover:bg-indigo-800 disabled:opacity-50">
                {{ formEstado.processing ? '⏳ Guardando...' : 'Actualizar' }}
            </button>
        </div>
    </div>
</div>

        <!-- Modal agregar cargo -->
        <div v-if="modalCargo"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="bg-green-600 text-white p-5 rounded-t-xl">
                    <h3 class="font-bold">💰 Agregar Cargo</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Descripción *</label>
                        <input v-model="formCargo.descripcion" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400"
                               placeholder="Ej: Hora de soporte remoto" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Cantidad *</label>
                            <input v-model="formCargo.cantidad" type="number" step="0.5" min="0.5"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Precio unitario *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Q</span>
                                <input v-model="formCargo.precio_unitario" type="number" step="0.01"
                                       class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                            </div>
                        </div>
                    </div>
                    <div v-if="formCargo.cantidad && formCargo.precio_unitario"
                         class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-green-600">Subtotal</p>
                        <p class="text-2xl font-black text-green-700">
                            Q{{ (formCargo.cantidad * formCargo.precio_unitario).toFixed(2) }}
                        </p>
                    </div>
                </div>
                <div class="p-5 border-t flex gap-3">
                    <button @click="modalCargo = false"
                            class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm">
                        Cancelar
                    </button>
                    <button @click="agregarCargo" :disabled="formCargo.processing"
                            class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-green-700 disabled:opacity-50">
                        Agregar Cargo
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>