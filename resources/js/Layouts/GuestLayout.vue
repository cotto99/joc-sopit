<script setup>
</script>

<template>
    <div class="relative min-h-screen bg-[#050c1a] overflow-hidden">

        <!-- 🔥 Fondo con efecto gradiente elegante -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#050c1a] via-[#0a1f3d] to-[#061226]"></div>

        <!-- Glow decorativo -->
        <div class="absolute w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-3xl -top-40 -left-40"></div>
        <div class="absolute w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-3xl -bottom-40 -right-40"></div>

        <!-- Contenido -->
        <div class="relative z-10">
            <slot />
        </div>

    </div>
</template>