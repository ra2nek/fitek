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
    background-color: transparent;
    border: .1rem solid var(--secondary);
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.75rem;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
    

button:hover {
  background-color: var(--light);
}

button:focus {
  background-color: var(--btn-outlined);
  outline: none;
  color: white;
  border: .1rem solid var(--primary);
  box-shadow: 0 0 0 2px #000000;
}

button:active {
  background-color: var(--primary);
}

button.checked{
  background-color: var(--light);
}

button.checked:hover{
  background-color: var(--primary);
}

button.checked:focus {
  background-color: var(--btn-outlined);
}

</style>
