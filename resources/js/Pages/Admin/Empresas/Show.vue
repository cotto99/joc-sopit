<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ empresa: Object })

// Form nueva sucursal
const formSucursal = useForm({
    nombre: '', direccion: '', telefono: ''
})

// Form nuevo contacto
const formContacto = useForm({
    nombre: '', apellido: '', telefono: '',
    cargo: '', sucursal_id: '', email: '', password: ''
})

const tabActiva = ref('sucursales')

function guardarSucursal() {
    formSucursal.post(route('admin.sucursales.store', props.empresa.id), {
        onSuccess: () => formSucursal.reset()
    })
}

function guardarContacto() {
    formContacto.post(route('admin.contactos.store', props.empresa.id), {
        onSuccess: () => formContacto.reset()
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
</script>

<template>
    <Head :title="empresa.nombre" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <a :href="route('admin.empresas.index')"
                   class="text-gray-400 hover:text-gray-600 text-sm">← Empresas</a>
                <h1 class="text-xl font-bold text-gray-800">🏢 {{ empresa.nombre }}</h1>
                <span :class="empresa.activo
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-500'"
                      class="text-xs px-2 py-1 rounded-full">
                    {{ empresa.activo ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
        </template>

        <!-- Info empresa -->
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400">NIT</p>
                    <p class="font-semibold">{{ empresa.nit || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Teléfono</p>
                    <p class="font-semibold">{{ empresa.telefono || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Email</p>
                    <p class="font-semibold">{{ empresa.email || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Dirección</p>
                    <p class="font-semibold">{{ empresa.direccion || '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-4">
            <button v-for="tab in [
                { key: 'sucursales', label: '🏪 Sucursales' },
                { key: 'contactos',  label: '👤 Contactos'  },
                { key: 'tickets',    label: '🎫 Tickets'    },
            ]" :key="tab.key"
                    @click="tabActiva = tab.key"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    :class="tabActiva === tab.key
                        ? 'bg-indigo-700 text-white'
                        : 'bg-white text-gray-600 hover:bg-gray-50 shadow'">
                {{ tab.label }}
                <span v-if="tab.key === 'sucursales'" class="ml-1 text-xs opacity-70">
                    ({{ empresa.sucursales?.length }})
                </span>
                <span v-if="tab.key === 'contactos'" class="ml-1 text-xs opacity-70">
                    ({{ empresa.contactos?.length }})
                </span>
            </button>
        </div>

        <!-- Tab Sucursales -->
        <div v-if="tabActiva === 'sucursales'">
            <div class="bg-white rounded-xl shadow p-5 mb-4">
                <h3 class="font-bold text-gray-700 mb-3">Nueva Sucursal</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Nombre *</label>
                        <input v-model="formSucursal.nombre" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Teléfono</label>
                        <input v-model="formSucursal.telefono" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Dirección</label>
                        <input v-model="formSucursal.direccion" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                </div>
                <button @click="guardarSucursal" :disabled="formSucursal.processing"
                        class="mt-3 bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-800 disabled:opacity-50">
                    + Agregar Sucursal
                </button>
            </div>
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3">Nombre</th>
                            <th class="text-left px-4 py-3">Dirección</th>
                            <th class="text-left px-4 py-3">Teléfono</th>
                            <th class="text-center px-4 py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="s in empresa.sucursales" :key="s.id"
                            class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ s.nombre }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ s.direccion || '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ s.telefono || '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span :class="s.activo
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-500'"
                                      class="text-xs px-2 py-1 rounded-full">
                                    {{ s.activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!empresa.sucursales?.length">
                            <td colspan="4" class="text-center py-8 text-gray-400">
                                Sin sucursales registradas
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Contactos -->
        <div v-if="tabActiva === 'contactos'">
            <div class="bg-white rounded-xl shadow p-5 mb-4">
                <h3 class="font-bold text-gray-700 mb-3">Nuevo Contacto / Usuario</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Nombre *</label>
                        <input v-model="formContacto.nombre" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Apellido *</label>
                        <input v-model="formContacto.apellido" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Cargo</label>
                        <input v-model="formContacto.cargo" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Teléfono</label>
                        <input v-model="formContacto.telefono" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Sucursal</label>
                        <select v-model="formContacto.sucursal_id"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                            <option value="">Sin sucursal</option>
                            <option v-for="s in empresa.sucursales" :key="s.id" :value="s.id">
                                {{ s.nombre }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Email (login) *</label>
                        <input v-model="formContacto.email" type="email"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Contraseña *</label>
                        <input v-model="formContacto.password" type="password"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                </div>
                <button @click="guardarContacto" :disabled="formContacto.processing"
                        class="mt-3 bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-800 disabled:opacity-50">
                    + Agregar Contacto
                </button>
            </div>
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3">Nombre</th>
                            <th class="text-left px-4 py-3">Cargo</th>
                            <th class="text-left px-4 py-3">Sucursal</th>
                            <th class="text-left px-4 py-3">Teléfono</th>
                            <th class="text-center px-4 py-3">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in empresa.contactos" :key="c.id"
                            class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ c.nombre }} {{ c.apellido }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ c.cargo || '—' }}</td>
                            <td class="px-4 py-3">
                                <span v-if="c.sucursal"
                                      class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full">
                                    {{ c.sucursal?.nombre }}
                                </span>
                                <span v-else class="text-gray-400 text-xs">—</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ c.telefono || '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span v-if="c.user"
                                      class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                                    ✅ Con acceso
                                </span>
                                <span v-else class="text-gray-400 text-xs">Sin acceso</span>
                            </td>
                        </tr>
                        <tr v-if="!empresa.contactos?.length">
                            <td colspan="5" class="text-center py-8 text-gray-400">
                                Sin contactos registrados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Tickets -->
        <div v-if="tabActiva === 'tickets'">
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3">Código</th>
                            <th class="text-left px-4 py-3">Título</th>
                            <th class="text-left px-4 py-3">Sucursal</th>
                            <th class="text-center px-4 py-3">Estado</th>
                            <th class="text-center px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in empresa.tickets" :key="t.id"
                            class="border-t hover:bg-gray-50 cursor-pointer"
                            @click="$inertia.visit(route('admin.tickets.show', t.id))">
                            <td class="px-4 py-3 font-mono text-xs text-indigo-600 font-bold">
                                {{ t.codigo }}
                            </td>
                            <td class="px-4 py-3 font-medium">{{ t.titulo }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ t.sucursal?.nombre || '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span :class="estadoClass[t.estado]"
                                      class="text-xs px-2 py-1 rounded-full capitalize">
                                    {{ t.estado.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-400">
                                {{ new Date(t.created_at).toLocaleDateString('es-GT') }}
                            </td>
                        </tr>
                        <tr v-if="!empresa.tickets?.length">
                            <td colspan="5" class="text-center py-8 text-gray-400">
                                Sin tickets registrados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>