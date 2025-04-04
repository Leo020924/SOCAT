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
import FormGrupoTaxonomico from '@/Pages/Socat/Grupos/FormGrupoTaxonomico.vue';

const { datosGrupo } = usePage().props;

const currentData = ref([]);
const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = 100;
const prevPageUrl = ref(null);
const nextPageUrl = ref(null);
const grupo = ref('');
const modalVisible = ref(false);
const grupoEditado = ref(null);
const sorting = ref({ column: null, order: null });

// Función para obtener los datos con el término de búsqueda y la página actual
const fetchFilteredData = async () => {
    try {
        const response = await axios.get('/busca-grupo', {
            params: {
                grupo: grupo.value,
                page: currentPage.value,
                sortBy: sorting.value.column,
                sortOrder: sorting.value.order,
            }
        });
        console.log("Este es el response", response);
        currentData.value = response.data.data;
        totalItems.value = response.data.totalItems;
        prevPageUrl.value = response.data.prev_page_url;
        nextPageUrl.value = response.data.next_page_url;
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
const nuevoGrupo = () => {
    grupoEditado.value = null;
    modalVisible.value = true;
    console.log("Modal visible:", modalVisible.value);
};

// Función para editar un autor
const editarGrupo = (grupo) => {
    grupoEditado.value = grupo;
    modalVisible.value = true;
    console.log("Modal visible:", modalVisible.value);
    console.log('Grupo a editar', grupo.IdGrupoSCAT);
};

// Función para eliminar un autor
const eliminarGrupo = async (IdGrupoSCAT) => {
    console.log("ID del grupo a eliminar", IdGrupoSCAT);
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

        await axios.delete(`/grupos-taxonomicos/${IdGrupoSCAT}`);
        fetchFilteredData();
    } catch (error) {
        if (error !== 'cancel') {
            console.error("Error al eliminar el grupo:", error);
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

const handleResultado = async (nuevoGrupo) => {
    console.log('handleResultado', nuevoGrupo);
    modalVisible.value = false;
    fetchFilteredData();
};

const cerrarModal = () => {
    modalVisible.value = false
}

const props = defineProps({
    datosGrupo: {
        type: Object,
        required: false,
    },
});

watchEffect(() => {
    if (props.datosGrupo) {
        fetchFilteredData();
    }
});

onMounted(() => {
    currentData.value = datosGrupo?.data || [];
    totalItems.value = datosGrupo?.total || 0;
    currentPage.value = datosGrupo?.current_page || 1;
    prevPageUrl.value = datosGrupo?.prev_page_url || null;
    nextPageUrl.value = datosGrupo?.next_page_url || null;
    modalVisible.value = false;
});
</script>

<template>
    <AppLayout title="GrupoTaxonomico">
        <template #header>
        </template>

        <div class="app-container">
            <div class="content-wrapper">
                <h1 style="font-size: 24px;" class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Grupos Taxonómicos
                </h1>

                <div class="table-wrapper">
                    <el-card class="box-card">
                        <template #header>
                            <div class="header-container">
                                <div class="left">
                                    <NuevoButton @crear="nuevoGrupo" />
                                </div>
                                <div class="right">
                                    <FiltrarPor v-model:busca="grupo" @update:busca="buscarPor()" />
                                </div>
                            </div>
                        </template>
                        <div class="table-responsive">
                            <el-table :data="currentData" :border="true" height="400" style="width: 100%"
                                :highlight-current-row="true" @sort-change="handleSortChange">
                                <el-table-column type="expand">
                                    <template #default="{ row }">
                                        <div class="bg-gray-50 p-4 rounded-md">
                                            <p><strong>Id Grupo Taxonómico:</strong> {{ row.IdGrupoTaxonomico }}</p>
                                            <p><strong>Fecha de Captura:</strong> {{ row.FechaCaptura }}</p>
                                            <p><strong>Fecha de Modificación:</strong> {{ row.FechaModificacion }}</p>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="GrupoSCAT" label="Nombre del Grupo" min-width="120"
                                    sortable="custom" align="center"></el-table-column>
                                <el-table-column prop="GrupoAbreviado" label="Abreviado" min-width="150"
                                    sortable="custom" align="center"></el-table-column>
                                <el-table-column prop="GrupoSNIB" label="Grupo SNIB" min-width="150" sortable="custom"
                                    align="center"></el-table-column>

                                <el-table-column label="Acciones" width="150">
                                    <template #default="{ row }">
                                        <div class="flex justify-around">
                                            <EditarButton @editar="editarGrupo(row)" />
                                            sortable="custom"
                                        </div>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                    </el-card>
                    <div v-if="totalItems > itemsPerPage" class="pagination-wrapper">
                        <el-pagination :current-page="currentPage" :page-size="itemsPerPage" :total="totalItems"
                            @current-change="handlePageChange" layout="prev, pager, next" background class="my-2">
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

            <FormGrupoTaxonomico :visible="modalVisible" :gpoTaxEdit="grupoEditado"
                :accion="grupoEditado ? 'editar' : 'crear'" @resultadoAlta="handleResultado"
                @resultadoEditar="handleResultado" @cerrar="cerrarModal" />
        </div>

    </AppLayout>
</template>

<style scoped>
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f0f2f5;
    align-items: center;
    justify-content: flex-start;
    padding: 10px;
    box-sizing: border-box;
}

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
    margin: 20px auto;
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

.header-wrapper .left {
    flex: 1;
    text-align: left;
}

.header-wrapper .right {
    flex: 1;
    text-align: right;
}

.pagination-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 30px;
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

.p-4 {
    padding: 1.5rem;
}

.rounded-md {
    border-radius: 0.5rem;
}

/*Ajustes para la paginación */
.el-pagination {
    display: flex;
    justify-content: center;
    margin-top: 10px;
    /* Reducir el margen superior */
    margin-bottom: 10px;
    /* Reducir el margen inferior */
}

.el-pagination button,
.el-pagination span {
    font-size: 12px;
    /* Reducir el tamaño de la fuente */
    padding: 6px 10px;
    /* Reducir el padding */
}

/*Para pantallas más pequeñas */
@media (max-width: 768px) {
    .app-container {
        padding: 5px;
        /* Ajusta aún más el padding para pantallas muy pequeñas */
    }

    .content-wrapper {
        padding: 5px;
        /* Ajusta aún más el padding para pantallas muy pequeñas */
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