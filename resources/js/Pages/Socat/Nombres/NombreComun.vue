<script setup>
import { ref, watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import NuevoButton from '@/Components/Biotica/NuevoButton.vue';
import EditarButton from '@/Components/Biotica/EditarButton.vue';
import EliminarButton from '@/Components/Biotica/EliminarButton.vue';
import FiltrarPor from '@/Components/Biotica/FiltrarPorInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { ElMessageBox } from 'element-plus';
import FormNombreComun from '@/Pages/Socat/Nombres/FormNombreComun.vue';

const { datosNombreComun } = usePage().props;

const currentData = ref([]);
const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = 100;
const prevPageUrl = ref(null);
const nextPageUrl = ref(null);
const nombreComun = ref('');
const modalVisible = ref(false);
const nombreComunEditado = ref(null);
const sorting = ref({ column: null, order: null });

// Función para obtener los datos con el término de búsqueda y la página actual
const fetchFilteredData = async () => {
    try {
        const response = await axios.get('/busca-nombre-comun', {
            params: {
                nombreComun: nombreComun.value,
                page: currentPage.value,
                sortBy: sorting.value.column,
                sortOrder: sorting.value.order,
            }
        });
        console.log("Este es el response", response);
        currentData.value = response.data.data;
        totalItems.value = response.data.totalItems;
        prevPageUrl.value = response.data.prevPageUrl;
        nextPageUrl.value = response.data.nextPageUrl;
    } catch (error) {
        console.error('Error al obtener los datos:', error);
    }
};

// Esto es para el ordenamieto DESC y ASC
const handleSortChange = (column) => {
    sorting.value.column = column.prop;
    sorting.value.order = column.order;
    fetchFilteredData();
};


// Función para agregar un autor
const nuevoNombreComun = () => {
    nombreComunEditado.value = null;
    modalVisible.value = true;
    console.log("Modal visible:", modalVisible.value);
};

// Función para editar un autor
const editarNombreComun = (nombreComun) => {
    nombreComunEditado.value = nombreComun;
    modalVisible.value = true;
    console.log("Modal visible:", modalVisible.value);
    console.log('NombreComun a editar', nombreComun.IdNomComun);
};

// Función para eliminar un autor
const eliminarNombreComun = async (idNomComun) => {
    console.log("ID del NombreComun a eliminar", idNomComun);
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro de que deseas eliminar este registro?',
            'Confirmar eliminación',
            {
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                type: 'warning',
                draggable: true
            }
        )

        await axios.delete(`/nombres-comunes/${idNomComun}`);
        fetchFilteredData();
    } catch (error) {
        if (error !== 'cancel') {
            console.error("Error al eliminar el NombreComun:", error);
        }
    }
};


// Función para buscar por autor
const buscarPor = () => {
    currentPage.value = 1;
    fetchFilteredData();
};

// Función para manejar el cambio de página en la paginación
const handlePageChange = (page) => {
    currentPage.value = page;
    fetchFilteredData();
};

const handleResultado = async (nuevoNombreComun) => {
    console.log('handleResultado', nuevoNombreComun);
    modalVisible.value = false;
    await fetchFilteredData();
};


const cerrarModal = () => {
    modalVisible.value = false
}

const reload = () => {
    fetchFilteredData();
}

const props = defineProps({
    datosNombreComun: {
        type: Object,
        required: false,
    },
});

watchEffect(() => {
    if (props.datosNombreComun) {
        fetchFilteredData();
    }
});

onMounted(() => {
    currentData.value = datosNombreComun?.data || [];
    totalItems.value = datosNombreComun?.totalItems || 0;
    currentPage.value = datosNombreComun?.currentPage || 1;
    prevPageUrl.value = datosNombreComun?.prevPageUrl || null;
    nextPageUrl.value = datosNombreComun?.nextPageUrl || null;
    modalVisible.value = false;
});
</script>

<template>
    <AppLayout title="NombreComun">
        <template #header>
        </template>

        <div class="app-container">
            <div class="content-wrapper">
                <h1 style="font-size: 24px;" class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Nombres Comunes
                </h1>

                <div class="table-wrapper">
                    <el-card class="box-card">
                        <template #header>
                            <div class="header-container">
                                <div class="left">
                                    <FiltrarPor v-model:busca="nombreComun" @update:busca="buscarPor()" />
                                </div>
                                <div class="right">
                                    <NuevoButton @crear="nuevoNombreComun" />
                                </div>
                            </div>
                        </template>
                        <div class="table-responsive">
                            <el-table :data="currentData" :border="true" height="400" style="width: 100%"
                                :highlight-current-row="true" @sort-change="handleSortChange">
                                <el-table-column type="expand">
                                    <template #default="{ row }">
                                        <div class="bg-gray-50 p-4 rounded-md">
                                            <p><strong>Id Nom Comun:</strong> {{ row.IdNomComun }}</p>
                                            <p><strong>Fecha de Captura:</strong> {{ row.FechaCaptura }}</p>
                                            <p><strong>Fecha de Modificación:</strong> {{ row.FechaModificacion }}</p>
                                            <p><strong>Id Original:</strong> {{ row.IdOriginal }}</p>
                                            <p><strong>Catalogo:</strong> {{ row.Catalogo }}</p>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="NomComun" label="Nombre Común" min-width="120" sortable="custom"
                                    align="center"></el-table-column>
                                <el-table-column prop="Observaciones" label="Observaciones" min-width="150"
                                    sortable="custom" align="center"></el-table-column>
                                <el-table-column prop="Lengua" label="Lengua" min-width="150" sortable="custom"
                                    align="center"></el-table-column>

                                    <el-table-column label="Acciones" width="120">
                                <template #default="{ row }">
                                    <div class="flex justify-around">
                                        <EditarButton @editar="editarNombreComun(row)" />
                                        <EliminarButton @eliminar="eliminarNombreComun(row.IdNomComun)" />
                                    </div>
                                </template>
                            </el-table-column>
                            </el-table>
                        </div>
                    </el-card>
                    <div v-if="totalItems > itemsPerPage" class="pagination-wrapper">
                        <el-pagination :current-page="currentPage" :page-size="itemsPerPage" :total="totalItems"
                            @current-change="handlePageChange" layout="prev, pager, next" background
                            class="my-2">
                            <template #prev>
                                <el-button :disabled="!prevPageUrl" @click="handlePrevNext('prev')"
                                    size="small">Anterior</el-button>
                            </template>
                            <template #next>
                                <el-button :disabled="!nextPageUrl" @click="handlePrevNext('next')"
                                    size="small">Siguiente</el-button>
                            </template>
                            <template #total>
                                 <span class="total-text"></span>
                            </template>
                        </el-pagination>
                    </div>
                </div>
            </div>

            <FormNombreComun :visible="modalVisible" :nomComEdit="nombreComunEditado"
                :accion="nombreComunEditado ? 'editar' : 'crear'" @resultadoAlta="handleResultado"
                @resultadoEditar="handleResultado" @cerrar="cerrarModal" @reload="fetchFilteredData" />
        </div>
    </AppLayout>
</template>

<style scoped>
/* Contenedor principal que engloba toda la app */
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f0f2f5;
    align-items: center;
    /* Centrar horizontalmente */
    justify-content: flex-start;
    /* Alineación vertical al inicio */
    padding: 10px;
    /* Reducir el padding para pantallas pequeñas */
    box-sizing: border-box;
}

/* Contenedor para el contenido principal (excluyendo el menú) */
.content-wrapper {
    width: 100%;
    max-width: 1200px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    padding: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 10px auto;
   
}

.table-wrapper {
    width: 100%;
    margin-top: 15px;
}

.table-responsive {
  overflow-x: auto;
  transform: translate3d(0, 0, 0);
}

.header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 15px;
}

.header-wrapper .left,
.header-wrapper .right {
    flex: 1;
}

.pagination-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 20px;
    margin-bottom: 10px;
}

.el-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    font-family: 'Roboto', sans-serif;
    color: #495057;
    background-color: #fff;
    width: 100%;
}

.el-table__header th {
    background-color: #343a40;
    font-weight: 500;
    color: #fff;
    text-align: left;
    padding: 12px 15px;
    border-bottom: 2px solid #495057;
    font-size: 14px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.el-table__body td {
    text-align: left;
    padding: 12px 15px;
    font-size: 14px;
    border-bottom: 1px solid #495057;
}

.el-table__body tr:hover {
    background-color: #343a4010;
    transition: background-color 0.15s ease-in-out;
}

.el-table-column.type-expand .el-table__cell {
    padding: 15px;
    background-color: #343a4005;
    border-bottom: 1px solid #495057;
}

.el-table-column.type-expand .el-table__cell:hover {
    background-color: #343a4020;
}

.bg-gray-50 {
    background-color: #343a4005;
    color: #495057;
}

.el-pagination {
    display: flex;
    justify-content: center;
    margin-top: 10px;
    margin-bottom: 10px;
}

.el-pagination button,
.el-pagination span {
    font-size: 12px;
    padding: 6px 10px;
}

@media (max-width: 768px) {
    .app-container {
        padding: 5px;
    }

    .content-wrapper {
        padding: 5px;
    }

    .el-table__header th {
        font-size: 12px;
        padding: 10px 6px;
    }

    .el-table__body td {
        font-size: 12px;
        padding: 8px 6px;
    }

    .el-pagination button,
    .el-pagination span {
        font-size: 12px;
        padding: 5px 10px;
    }
}
</style>