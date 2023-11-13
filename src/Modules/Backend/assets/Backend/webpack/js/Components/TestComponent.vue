<script>
export default {
  name: 'TestComponent',
  props: {
    message: String
  },
  methods: {
    emitSelectedFolder() {
      const eventUpdate = new CustomEvent("update", {
        bubbles: true,
        detail: { image: () => 'image' },
      });
      this.$refs['image-selector'].dispatchEvent(eventUpdate)
      console.log('emitting')
    }
  }
}
</script>

<template>
 <div @click="emitSelectedFolder" class="image-selector" ref="image-selector">Placeholder</div>
</template>

<style scoped>

</style>
