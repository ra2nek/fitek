<script setup>
import { reactive } from 'vue';
import LoadingIcon from './LoadingIcon.vue';

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
  text: {
    type: String,
  },
  onSend: {
    type: Function,
    required: true,
  },
});

const storage = reactive({
  isLoading: false,
});

async function handleClick(event) {
  const target = event.target;

  disable(target);

  try {
    const status = await props.onSend();
    enable();
  } catch (error) {
    console.error('Error during async operation:', error);
    enable();
  }
}

function disable() {
  storage.isLoading = true;
}

function enable() {
  storage.isLoading = false;
}
</script>

<template>
  
  <button :disabled="disabled || storage.isLoading" @click="handleClick">
    <span v-show="!storage.isLoading">{{ text }} <slot /> </span>
    <span v-show="storage.isLoading"><LoadingIcon /></span>
  </button>

</template>

<style scoped>
button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 4px 8px;
  background-color: var(--secondary);
  border: 1px solid transparent;
  border-radius: 4px;
  font-weight: 600;
  font-size: 0.75rem;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

button:hover {
  background-color: var(--hover);
}

button:focus {
  background-color: var(--hover);
  outline: none;
  box-shadow: 0 0 0 2px #01630e;
}

button:active {
  background-color: var(--secondary);
}

button:disabled {
  background-color: var(--disabled);
}
</style>
