<script setup>
    const props = defineProps({
                titulo: {
                  type: String,
                  required: true,
                },
              });
</script>
<template>
    <div class="bg-gray-400 shadow-md sm:rounded-lg w-full sm:w-3/4 md:w-2/3 lg:w-[1000px] xl:w-[1300px] 2xl:w-[1585px] py-4 mx-auto">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-8">
            {{titulo}}
        </h2>
    </div>
</template>