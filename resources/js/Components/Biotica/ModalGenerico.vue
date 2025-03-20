<template>
  <div v-if="visible" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <slot name="header"></slot>
        <button @click="closeModal" class="modal-close-button">
          <span style="font-size: 24px; color: rgba(0, 0, 0, 0.5)">×</span>
        </button>
      </div>
      <div class="modal-body">
        <slot></slot>
      </div>
      <div class="modal-footer">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
  visible: {
    type: Boolean,
    required: true
  },
  onCloseModal: {  // <- Prop requerida
    type: Function,
    required: true
  }
})

const emit = defineEmits(["close"]);

const closeModal = () => {
  emit("close");
  props.onCloseModal(); // Llama a la función que se pasa como prop
};

</script>


<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 600px;
  max-width: 100%;
  padding: 20px;
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}

.modal-close-button {
  background-color: transparent;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  font-weight: bold;
}

.modal-body {
  padding: 20px 0;
}

.modal-footer {
  padding-top: 10px;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: flex-end;
}
</style>