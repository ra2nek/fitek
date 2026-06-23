<template>
    <button>
        <slot />
    </button>
</template>

<style scoped>
button{
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

</style>
