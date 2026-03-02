<script setup>
import ClienteLayout from '@/Layouts/ClienteLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    sucursales: Array,
    categorias: Array,
})

const form = useForm({
    sucursal_id:  '',
    categoria_id: '',
    titulo:       '',
    descripcion:  '',
    prioridad:    'media',
})

function enviar() {
    form.post(route('cliente.tickets.store'))
}
</script>

<template>
    <Head title="Nuevo Ticket" />
    <ClienteLayout>

        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a :href="route('cliente.tickets.index')"
                   class="text-indigo-600 hover:underline text-sm">← Mis Tickets</a>
                <h2 class="text-2xl font-black text-gray-800 mt-2">🎫 Nuevo Ticket de Soporte</h2>
                <p class="text-gray-500 text-sm">Describí tu problema y te contactamos a la brevedad</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6 space-y-5">

                <!-- Sucursal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sucursal
                    </label>
                    <select v-model="form.sucursal_id"
                            class="w-full border-2 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        <option value="">Empresa general / Sin sucursal</option>
                        <option v-for="s in sucursales" :key="s.id" :value="s.id">
                            {{ s.nombre }}
                        </option>
                    </select>
                </div>

                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de soporte
                    </label>
                    <select v-model="form.categoria_id"
                            class="w-full border-2 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        <option value="">Sin categoría específica</option>
                        <option v-for="c in categorias" :key="c.id" :value="c.id">
                            {{ c.nombre }}
                            <span v-if="!c.precio_variable"> — Q{{ c.precio_base }}</span>
                        </option>
                    </select>
                </div>

                <!-- Prioridad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Prioridad
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <button v-for="p in [
                            { val: 'baja',    label: '🟢 Baja',    desc: 'No urgente'    },
                            { val: 'media',   label: '🔵 Media',   desc: 'Normal'        },
                            { val: 'alta',    label: '🟠 Alta',    desc: 'Afecta trabajo'},
                            { val: 'critica', label: '🔴 Crítica', desc: 'Sin servicio'  },
                        ]" :key="p.val"
                                type="button"
                                @click="form.prioridad = p.val"
                                class="p-3 rounded-xl border-2 text-center transition text-xs"
                                :class="form.prioridad === p.val
                                    ? 'border-indigo-500 bg-indigo-50'
                                    : 'border-gray-200 hover:border-gray-300'">
                            <p class="font-bold">{{ p.label }}</p>
                            <p class="text-gray-400 mt-0.5">{{ p.desc }}</p>
                        </button>
                    </div>
                    <p v-if="form.errors.prioridad" class="text-red-500 text-xs mt-1">
                        {{ form.errors.prioridad }}
                    </p>
                </div>

                <!-- Título -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Título del problema *
                    </label>
                    <input v-model="form.titulo" type="text"
                           class="w-full border-2 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                           placeholder="Ej: Internet sin conexión en oficina principal" />
                    <p v-if="form.errors.titulo" class="text-red-500 text-xs mt-1">
                        {{ form.errors.titulo }}
                    </p>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción detallada *
                    </label>
                    <textarea v-model="form.descripcion" rows="5"
                              class="w-full border-2 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                              placeholder="Describí el problema con detalle: cuándo comenzó, qué equipos están afectados, qué mensajes de error aparecen, etc."></textarea>
                    <p v-if="form.errors.descripcion" class="text-red-500 text-xs mt-1">
                        {{ form.errors.descripcion }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Entre más detalle des, más rápido podemos ayudarte.
                    </p>
                </div>

                <!-- Botón -->
                <button @click="enviar" :disabled="form.processing"
                        class="w-full bg-indigo-700 text-white py-3.5 rounded-xl font-black text-base hover:bg-indigo-800 disabled:opacity-50 shadow-lg transition">
                    {{ form.processing ? '⏳ Enviando...' : '🎫 Crear Ticket de Soporte' }}
                </button>
            </div>

            <!-- Info adicional -->
            <div class="mt-4 bg-indigo-50 rounded-xl p-4 text-sm text-indigo-700">
                <p class="font-bold mb-1">¿Qué pasa después?</p>
                <ol class="space-y-1 text-xs">
                    <li>1. Tu ticket se registra con un código único</li>
                    <li>2. Nuestro equipo lo revisa y asigna a un técnico</li>
                    <li>3. El técnico se pone en contacto contigo</li>
                    <li>4. Podés ver el seguimiento en tiempo real aquí</li>
                </ol>
            </div>
        </div>
    </ClienteLayout>
</template>