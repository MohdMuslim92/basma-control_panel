<template>
  <div class="toggle-switch-container">
    <input
      type="checkbox"
      class="toggle-checkbox"
      :id="`toggle-switch-${userId}`"
      :checked="isAdmin"
      @change="toggle"
    />
    <label class="toggle-label" :for="`toggle-switch-${userId}`">
      <span class="toggle-inner"></span>
      <span class="toggle-switch"></span>
    </label>
  </div>
</template>

<script>
export default {
  props: {
    isAdmin: {
      type: Boolean,
      required: true
    },
    userId: {
      type: [String, Number],
      required: true
    }
  },
  methods: {
    toggle() {
      this.$emit('toggle');
    }
  }
}
</script>