<script setup>
    import { DArrowRight } from '@element-plus/icons-vue';

    // Declaramos los eventos que este componente puede emitir
    const emit = defineEmits(['traspasa']);

    // Definimos la función `editar`
    const traspasaDatos = () =>{
        emit('traspasa');
    } 
</script>

<template>
    <el-tooltip class="item" effect="dark" content="Asociar catálogo" placement="right-start">
        <el-button  @click.prevent="traspasaDatos"
                    circle
                    icon="el-icon-d-arrow-right"
                    type="primary">
            <el-icon>
                <DArrowRight />
            </el-icon>
        </el-button>
    </el-tooltip>
</template>