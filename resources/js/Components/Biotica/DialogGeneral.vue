<script setup>
import { defineModel } from 'vue';

//Definición de variables 
const props = defineProps({
    botCerrar: {
        type: Boolean,
        required: true,
    },
    pressEsc: {
        type: Boolean,
        required: true,
    },
});

const currentZIndex = 1000;

const dialogFormVisible = defineModel();
</script>

<template>
    <div>
        <el-dialog v-model="dialogFormVisible" :z-index="currentZIndex" :close-on-click-modal="false" :show-close="botCerrar"
            :destroy-on-close="false" :close-on-press-escape="pressEsc" class="my-responsive-dialog">
            <div class="my-dialog-content">
                <slot></slot>
            </div>
        </el-dialog>
    </div>
</template>

<style scoped>
/* Estilos generales para el contenido del diálogo */
.my-dialog-content {
    max-height: 70vh;
    /* Ajusta la altura máxima */
    overflow-y: auto;
    /* Activa el scroll vertical */
    padding: 10px;
}

/* Estilos para hacer el diálogo responsivo */
.my-responsive-dialog {
    width: 90%;
    /* Ocupa el 90% del ancho en pantallas grandes */
    max-width: 500px;
    /* Ancho máximo */
}

/* Estilos para pantallas más pequeñas (móviles) */
@media (max-width: 768px) {
    .my-responsive-dialog {
        width: 95%;
        /* Ocupa el 95% del ancho en pantallas pequeñas */
        margin: 0 auto;
        /* Centrar horizontalmente */
        top: 2%;
        /* Ajustar la posición vertical */
    }

    .my-dialog-content {
        max-height: 60vh;
        /* Reduce aún más la altura máxima en pantallas pequeñas */
    }
}
</style>