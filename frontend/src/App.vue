<template>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

  <div id="app">
      <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
#app {
  font-family: 'Raleway', sans-serif;
  font: 400;
  text-align: center;
  color: #2c3e50;
}

:root {
  --g-dialog-content-bg: #f3f3f3;
  --g-dialog-content-border-radius: 20px;
  --g-dialog-overlay-bg: rgba(0, 0, 0, 0.5);
}

</style>
