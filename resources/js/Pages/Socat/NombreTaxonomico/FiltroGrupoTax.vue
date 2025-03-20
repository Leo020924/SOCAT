<script setup>
    import { ref, onMounted, defineEmits } from 'vue';
    import arbolCheck from "@/Components/Biotica/ArbCheck.vue";
    import { ElMessageBox } from 'element-plus';
    import btnTraspaso from "@/Components/Biotica/BtnTraspaso.vue";

    //Definición de variables 
    const props = defineProps({
                grupos: {
                    type: Object,
                    required: true,
                },
            });

    const propiedades = {
        children: 'children',
        label: 'label'
    }

    const datos =  props.grupos["original"];
    const checkAll = ref(false);
    const arbolRef = ref(null);

    const emit = defineEmits(['regresaGrupos', 'cerrar']);

    //const emit = defineEmits(['cerrar', 'valorAlta', 'valorActual']);

    //Definicion de las funciones 
    const open = (mensaje) => {
                            ElMessageBox.alert(mensaje, 'Grupos Taxonómicos', {
                                confirmButtonText: 'OK',
                            })
                        }
    
    onMounted(() => {
        open("Se debe seleccionar al menos un grupo taxonómico");
    });

    //Función referenciada para ejecutar las funciones desde el componente padre hasta el componente hijo
    const marcar = () => {
        if(arbolRef.value){
            arbolRef.value.marcarTodos();
        }
    }   

    const desmarcar = () => {
        if(arbolRef.value){
            arbolRef.value.desmarcarTodos();
        }
    }

    const recuperaMarcados = () => {
        if(arbolRef.value){
            arbolRef.value.recuperaMarcados();
        }
    }

    const recibeGrupos = (data) =>{
       if(data['ids'].length > 0)
       {
        emit('cerrar', false);
        emit('regresaGrupos', data);
       }
       else{
        open("Se debe seleccionar al menos un grupo taxonómico");
       }
    }

</script>

<template>
    <div class="common-layout">
        <el-container>
            <el-header class="header">
                <h1 class="titulo">Catálogo de grupos taxonómicos</h1>
            </el-header>
            <el-main class="contenido">
                <div>
                    <btnTraspaso @traspasa="recuperaMarcados()" />
                    <br />
                    <div v-show="!checkAll">
                        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="marcar">
                            Marcar todos
                        </el-checkbox>
                    </div>
                    <div v-show="checkAll">
                        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="desmarcar">
                            Desmarcar todos
                        </el-checkbox>
                    </div>
                </div>
                <arbolCheck :datosArbol="datos" 
                            :defaultProps="propiedades" 
                            ref="arbolRef"
                            @regresaMarcados="recibeGrupos"/>
            </el-main>
        </el-container>
    </div>
</template>

<style scoped>
    .common-layout {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .header {
        text-align: center;
        padding: 0.5rem; /* Reducir el padding en móviles */
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
    }

    .titulo {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem; /* Añadir margen inferior para separar del contenido */
    }

    .contenido {
        max-width: 100%;
        padding: 0.5rem; /* Reducir el padding en móviles */
        margin-top: 1rem; /* Añadir margen superior para separar del título */
    }

    /* Estilos para el botón y el checkbox */
    .contenido > div {
        display: flex;
        flex-direction: column; /* Colocar elementos en vertical */
        align-items: flex-start; /* Alinear a la izquierda */
        gap: 0.5rem; /* Espacio entre elementos */
    }

    /* Responsividad */
    @media (min-width: 768px) {
        .titulo {
            font-size: 2rem;
        }
    }

    @media (max-width: 480px) {
        .titulo {
            font-size: 1rem; /* Reducir el tamaño de la fuente en móviles */
        }

        .header {
            padding: 0.25rem; /* Reducir el padding en móviles */
        }

        .contenido {
            padding: 0.25rem; /* Reducir el padding en móviles */
            margin-top: 0.5rem; /* Ajustar margen superior en móviles */
        }
    }
</style>