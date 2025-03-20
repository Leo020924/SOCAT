<script setup>
    import { ref, defineEmits } from 'vue';

//Definición de variables 
    const props = defineProps({
                datosArbol: {
                    type: Array,
                    required: true,
                },
                defaultProps: {
                    type: Object,
                    requiered:true,    
                }
            });
    
    const tree = ref(null);

    const grpMarcados = ref({
        ids: '',
        grupos: '',
        catalogos: ''
    });

    const emit = defineEmits(['regresaMarcados']);

    //Definicion de la funcion 
    const marcarTodos = () => {
        let indc = [];
        props.datosArbol.forEach(element => {
            indc.push(element.id); 
        });
        
        if(tree.value) {
            tree.value.setCheckedKeys(indc);
        }
    };

    const desmarcarTodos = () => {
        if(tree.value) {
            tree.value.setCheckedKeys([]);
        }
    };

    const recuperaMarcados = () => {
        let idsGruposTax = '';
        let grupos = '';
        let catalogos = '';

        tree.value.getCheckedNodes().forEach(element => {
            
            if(idsGruposTax === '')
            {
                idsGruposTax = element.id;
            }else{
                idsGruposTax += ", " + element.id;  
            }
             
            if(String(element.id).indexOf('A')===-1)
            {
                if(grupos === '')
                {
                    grupos = element.label;
                }else{
                    if(grupos.indexOf(element.label) === -1){
                        grupos += ", " + element.label;
                    }
                }
            }

            if(catalogos === '')
            {
                catalogos = element.catalogo;
            }else{
                if(catalogos.indexOf(element.catalogo) === -1){
                    catalogos += ", " + element.catalogo;
                }
            }
        });

            grpMarcados.value.ids = idsGruposTax;
            grpMarcados.value.grupos = grupos;
            grpMarcados.value.catalogos = catalogos;

            emit('regresaMarcados', grpMarcados.value)
    };

    //Definición de funciones expuetas para que sean ejecutadas por el componente padre
    defineExpose({ 
        marcarTodos,
        desmarcarTodos,
        recuperaMarcados
    }); 

</script>

<template>

    <el-tree
        :data="datosArbol"
        :render-after-expand="true"
        :auto-expand-parent="true"
        show-checkbox
        node-key="id"
        ref="tree"
        highlight-current
        :props="defaultProps">
    </el-tree>
</template>