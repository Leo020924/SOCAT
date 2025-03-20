<template>
    <ModalGenerico :visible="visible" @close="cerrarModal" :onCloseModal="cerrarModal">
        <template #header>
            <h2>{{ accion === 'crear' ? 'Alta de Nombre Común' : 'Editar Nombre Común' }}</h2>
        </template>
        <el-card class="box-card">
            <el-form :model="form" ref="formRef" :rules="rules">
                <el-form-item label="Nombre Común" prop="NomComun">
                    <el-input type="text" v-model="form.NomComun" />
                </el-form-item>
                <el-form-item label="Observaciones" prop="Observaciones">
                    <el-input type="textarea" v-model="form.Observaciones" />
                </el-form-item>
                <el-form-item label="Lengua" prop="Lengua">
                    <el-input type="text" v-model="form.Lengua" />
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
import { ref, watch } from 'vue';
import ModalGenerico from '@/Components/Biotica/ModalGenerico.vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';

const props = defineProps({
    visible: Boolean,
    accion: String,
    nomComEdit: Object,
    paginaActual: Number,
});

const emit = defineEmits(['cerrar', 'resultadoAlta', 'resultadoEditar', 'reload']);

const form = ref({
    NomComun: '',
    Observaciones: '',
    Lengua: '',
    FechaCaptura: null,
    FechaModificacion: null,
    IdOriginal: '',
    Catalogo: ''
});

const originalForm = ref(null);

const rules = {
    NomComun: [
        { required: true, message: 'Ingrese un nombre común', trigger: 'blur' },
        { min: 1, max: 255, message: 'La longitud debe estar entre 1 y 255', trigger: 'blur' }
    ],
    Observaciones: [
        { max: 500, message: 'La longitud debe ser menor o igual a 500', trigger: 'blur' }
    ],
    Lengua: [
        { max: 50, message: 'La longitud debe ser menor o igual a 50', trigger: 'blur' }
    ],
};

watch(() => props.nomComEdit, (newVal) => {
    if (newVal) {
        form.value = {
            NomComun: newVal?.NomComun,
            Observaciones: newVal?.Observaciones,
            Lengua: newVal?.Lengua,
            FechaCaptura: newVal?.FechaCaptura,
            FechaModificacion: newVal?.FechaModificacion,
            IdOriginal: newVal?.IdOriginal,
            Catalogo: newVal?.Catalogo,
        };
        originalForm.value = { ...form.value };
    } else {
        form.value = {
            NomComun: '',
            Observaciones: '',
            Lengua: '',
            FechaCaptura: null,
            FechaModificacion: null,
            IdOriginal: '',
            Catalogo: '',
        };
        originalForm.value = null;
    }
}, { immediate: true });

const cerrarModal = () => {
    if (originalForm.value) {
        form.value = { ...originalForm.value };
    } else {
        form.value = {
            NomComun: '',
            Observaciones: '',
            Lengua: '',
            FechaCaptura: null,
            FechaModificacion: null,
            IdOriginal: '',
            Catalogo: '',
        };
    }
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
            console.log("Acción:", props.accion);
            console.log("nomComEdit:", props.nomComEdit);
            if (props.accion === 'crear') {
                response = await axios.post('/nombres-comunes', form.value);
                 if (response.data.status === 200) {
                    ElMessage({
                        message: response.data.message,
                        type: 'success',
                        duration: 5000,
                    });
                    emit('resultadoAlta', response.data);
                    emit('reload');
                  
                } else {
                    ElMessage({
                        message: response.data.message,
                        type: 'warning',
                        duration: 5000,
                    });
                }
            } else {
                console.log("Datos enviados en PUT:", form.value);
                const data = { ...form.value, IdNomComun: props.nomComEdit?.IdNomComun };
                console.log("Respuesta de la edición:", data);
                response = await axios.put(`/nombres-comunes/${props.nomComEdit?.IdNomComun}`, data);
                 if (response.status === 200) {
                    ElMessage({
                        message: response.data.message,
                        type: 'success',
                        duration: 5000,
                    });
                    emit('resultadoEditar', response.data);
                    emit('reload');
                } else {
                    ElMessage({
                        message: response.data.message,
                        type: 'warning',
                        duration: 5000,
                    });
                }

            }

        } catch (error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                let errorMessage = '';
                for (const field in errors) {
                    errorMessage += errors[field].join('\n') + '\n';
                }
                ElMessage({
                    message: "El nombre que intenta ingresar no cumple con las reglas de ingreso o modificación",
                    type: 'error',
                    duration: 5000,
                });
            } else if (error.response && error.response.status === 400) {
                ElMessage({
                    message: error.response.data.message,
                    type: 'warning',
                    duration: 5000,
                });
            }
            else if (error.response && error.response.status === 500) {
                ElMessage({
                    message: "Ocurrió un error en el servidor. Por favor, inténtelo de nuevo más tarde.",
                    type: 'error',
                    duration: 5000,
                });
                console.error("Error del servidor:", error);
            }
            else if (error !== 'cancel') {
                console.error("Error al guardar el nombre común:", error);
            }
        }
    }
    cerrarModal();
};

</script>
<style scoped>
.box-card {
    width: 100%;
}

.d-flex {
    display: flex;
}

.flex-row-reverse {
    flex-direction: row-reverse;
}
</style>