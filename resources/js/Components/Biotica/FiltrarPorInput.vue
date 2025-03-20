<script>
export default {
  name: "FiltrarPorInput",
  props: {
    busca: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      localBuscar: this.busca
    };
  },
  methods: {
    emitirFiltro() {
      this.$emit("update:busca", this.localBuscar); 
    }
  }
};
</script>

<template>
    <span class="demo-input-label">Filtrar por </span>
    <el-input
      style="width:180px"
      v-model="localBuscar"
      @keyup.enter="emitirFiltro"
      placeholder="Escribe para filtrar"
    ></el-input>
  </template>

<style>
.el-input__inner:focus {
  outline: none; 
  border-color: transparent; 
}
</style>