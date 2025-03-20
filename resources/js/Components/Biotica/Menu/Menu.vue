<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const permisos = page.props.permisos || [];


//Variables reactivas
const activeIndex2 = ref(null);
const cargando = ref(true);

const handleSelect = (selectedValue) => { };

const props = defineProps({
    active: Boolean,
    users: {
        type: Object,
        required: false,
    },
});

// Estado reactivo para el ancho de la pantalla
const screenWidth = ref(window.innerWidth);

// Función para actualizar el ancho de la pantalla
const updateScreenWidth = () => {
    screenWidth.value = window.innerWidth;
};

// Computada para determinar si es una pantalla pequeña
const isSmallScreen = computed(() => screenWidth.value < 768);

// Escuchadores para el ciclo de vida del componente
onMounted(() => {
    window.addEventListener('resize', updateScreenWidth);
    verificarCarga();
});

onUnmounted(() => {
    window.removeEventListener('resize', updateScreenWidth);
});

//Verifica la carga de permisos 
const verificarCarga = () => {
    if (permisos && permisos.length > 0) {
        cargando.value = false;
    }
};

//Funcion para validae la visbilidad de los objetos 
const hasPermisos = (etiqueta) => {
    const permiso = permisos.find(item => item.NombreModulo === etiqueta);

    return permiso?.Visible || false;
};

watch(
    () => props.permisos,
    () => {
        verificarCarga();
    },
    { deep: true, immediate: true }
);

</script>

<template>
    <el-menu :class="isSmallScreen ? 'vertical-menu' : 'horizontal-menu'"
        :mode="isSmallScreen ? 'vertical' : 'horizontal'" :default-active="activeIndex2" background-color="#545c64"
        text-color="#fff" active-text-color="#ffd04b" @select="handleSelect" style="width: 100%;">
        <el-sub-menu index="2" v-if="hasPermisos('MnuCatalogos')">
            <template #title>Catálogos</template>

            <el-menu-item index="2-1" v-if="hasPermisos('MnuCatAutores')">
                <Link :href="route('autorTaxon.index')">
                Catálogo de Autores
                </Link>
            </el-menu-item>

            <el-menu-item index="2-2" v-if="hasPermisos('MnuCatGrpTax')">
                <Link :href="route('grupoTaxonomico.index')"> <!-- Asumo que tu ruta se llama así -->
                Grupos taxonómicos
                </Link>
            </el-menu-item>

            <el-menu-item index="2-3" v-if="hasPermisos('MnuCatNomCom')">
                <Link :href="route('nombresComunes.index')"> <!-- Asumo que tu ruta se llama así -->
                    Nombre común
                </Link>
            </el-menu-item>

            <el-menu-item index="2-4" v-if="hasPermisos('MnuCatTipDist')">
                <Link :href="route('tiposDistribucion.index')"> <!-- Asumo que tu ruta se llama así -->
                    Tipo de distribución
                </Link>
            </el-menu-item>
            
            <el-menu-item index="2-5" v-if="hasPermisos('MnuCatTipRel')">Tipo de relación</el-menu-item>

            <el-menu-item index="2-6" v-if="hasPermisos('MnuCatCatTax')">
                <Link :href="route('grafica.index')"> <!-- Asumo que tu ruta se llama así -->
                    Categoría taxonómica
                </Link>
            </el-menu-item>
        </el-sub-menu>
        <el-sub-menu index="3" v-if="hasPermisos('MnuNomenclatura')">
            <template #title>Nomenclatura</template>
            
            <el-menu-item index="3-1" v-if="hasPermisos('MnuNomCientifico')">
                <Link :href="route('nombreTax.index')">
                    Nombre científico
                </Link>
            </el-menu-item>

            <el-sub-menu index="3-2" v-if="hasPermisos('MnuAsociar')">
                <template #title>Asociar con</template>
                <el-menu-item index="3-2-1" v-if="hasPermisos('MnuAscNomComun')">Nombres comunes</el-menu-item>
                <el-menu-item index="3-2-2" v-if="hasPermisos('MnuAscCaracteristicas')">Características</el-menu-item>
                <el-menu-item index="3-2-3" v-if="hasPermisos('MnuAscRegion')">Regiones</el-menu-item>
            </el-sub-menu>
        </el-sub-menu>
        <el-sub-menu index="4" v-if="hasPermisos('MnuBibliografia')">
            <template #title>Bibliografía</template>
            <el-menu-item index="4-1" v-if="hasPermisos('MnuBiblioRef')">Referencias</el-menu-item>
        </el-sub-menu>
    </el-menu>
</template>

<style scoped>
.horizontal-menu {
    width: 100%;
    /* Esto asegura que el menú horizontal ocupe todo el espacio disponible */
    display: flex;
    justify-content: space-between;
    /* Distribuye el contenido del menú de manera uniforme */
}

.vertical-menu {
    width: 100%;
    display: block;
}

.el-menu-item {
    white-space: nowrap;
    /* Esto asegura que los textos no se corten y se ajusten a una sola línea */
}

.el-menu {
    width: 100%;
    /* Asegura que el menú ocupe todo el espacio horizontal */
}
</style>