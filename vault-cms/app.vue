<template>
  <div>
    <NuxtLayout>
      <AppHeader v-if="shouldBeDisplayed()" />
      <SideNav v-if="shouldBeDisplayed()" />
      <NuxtPage class="page" />
    </NuxtLayout>
  </div>
</template>

<script lang="ts" setup>
import "bootstrap/dist/css/bootstrap.min.css";
const route = useRoute();

const shouldBeDisplayed = () => {
  return (
    route.name !== "auth" &&
    route.name !== "register" &&
    route.name !== "verify"
  );
};

definePageMeta({
  middleware: "auth",
});
</script>

<style lang="scss">
@use "~/styles/index.scss";
.page {
  margin-left: calc(20% + 10px);
  position: absolute;
  top: 90px;
  width: calc(80% - 20px);
  background-color: #fff;
  border-radius: 5px;
  padding: 16px;
}
</style>
