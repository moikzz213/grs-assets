<template>
  <div
    class="h-screen d-flex flex-column justify-start align-center gag-guest-bg"
    :style="`background-image: url(./assets/images/login-slider/slider-3.jpg); background-color: #000000`"
  >
    <div class="gag-guest-bg-fill"></div>
    <slot />
  </div>
</template>

<script setup></script>

<style scoped>
.gag-guest-bg {
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  position: absolute;
  top: 0;
  left: 0;
  right: auto;
  bottom: auto;
  width: 100%;
  height: 100%;
}
.gag-guest-bg-fill::before {
  content: "";
  background-color: rgba(0, 0, 0, 0.4);
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
}
</style>
