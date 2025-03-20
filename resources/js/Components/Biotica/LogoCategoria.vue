<script setup>
    const props = defineProps({
                    rutaCategoria: {
                      type: String,
                      required: true,
                    },
                  });
</script>

<template>
    <i class = "icon">
        <img :src = "rutaCategoria" style="width: 28px; height: 28px">
    </i>
</template>