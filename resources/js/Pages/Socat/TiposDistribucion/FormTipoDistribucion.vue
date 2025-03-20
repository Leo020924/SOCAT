<template>
    <ModalGenerico :visible="visible" @close="cerrarModal" :title="accion === 'crear' ? 'Alta de Tipo de Distribución' : 'Editar Tipo de Distribución'"
        :onCloseModal="cerrarModal">
        <template #header>
            <h2>{{ accion === 'crear' ? 'Alta de Tipo de Distribución' : 'Editar Tipo de Distribución' }}</h2>
        </template>
        <el-card class="box-card">
            <el-form :model="form" ref="formRef" :rules="rules">
                <el-form-item label="Descripción" prop="Descripcion">
                    <el-input type="text" v-model="form.Descripcion" />
                </el-form-item>
            </el-form>

            <template #footer>
                <el-button @click="cerrarModal">Cancelar</el-button>
                <el-button type="primary" @click="submitForm">Confirmar</el-button>
            </template>
        </el-card>
    </ModalGenerico>
</template>

<script setup>
import { ElMessageBox, ElMessage } from 'element-plus'; 
import { ref, defineEmits, watch } from 'vue';
import axios from 'axios';
import ModalGenerico from '@/Components/Biotica/ModalGenerico.vue';

const props = defineProps({
    visible: Boolean,
    accion: String,
    tipoDistEdit: Object,
    paginaActual: Number,
});

const emit = defineEmits(['cerrar', 'resultadoAlta', 'resultadoEditar']);

const form = ref({
    Descripcion: '',
    FechaCaptura: null,
    FechaModificacion: null
});

const originalForm = ref(null);

const rules = {
    Descripcion: [
        { required: true, message: 'Ingrese una descripción', trigger: 'blur' },
        { min: 1, max: 255, message: 'La longitud debe estar entre 1 y 255', trigger: 'blur' }
    ],
};

watch(() => props.tipoDistEdit, (newValue) => {
    if (newValue) {
        form.value = {
            Descripcion: newValue?.Descripcion,
            FechaCaptura: newValue?.FechaCaptura,
            FechaModificacion: newValue?.FechaModificacion,
        };
        originalForm.value = { ...form.value };
    } else {
        form.value = {
            Descripcion: '',
            FechaCaptura: null,
            FechaModificacion: null
        };
        originalForm.value = null;
    }
}, { immediate: true });

const cerrarModal = () => {
    emit('cerrar');
};

const formRef = ref(null);
const submitForm = async () => {
    const isValid = await formRef.value.validate();
    if (isValid) {
        try {
            await ElMessageBox.confirm(
                '¿Estás seguro de que deseas guardar este registro?',
                'Confirmar guardado',
                {
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning',
                    draggable: true
                }
            )
            let response;
            if (props.accion === 'crear') {
                response = await axios.post('/tipos-distribucion', form.value);
                emit('resultadoAlta', response.data);
            } else {
                response = await axios.put(`/tipos-distribucion/${props.tipoDistEdit.IdTipoDistribucion}`, form.value);
                emit('resultadoEditar', response.data);
            }
            cerrarModal();
        } catch (error) {
            console.error("Error al guardar:", error);
        }
    }
};
</script>