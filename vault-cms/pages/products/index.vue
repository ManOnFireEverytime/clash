<template>
  <div class="products">
    <div class="header d-flex justify-content-between align-items-center">
      <h5>Products</h5>
      <div class="d-flex align-items-center">
        <input type="text" placeholder="Search here" v-model="searchQuery" />
        <NuxtLink :to="{ name: 'products-AddProduct' }">
          <button class="d-flex ms-3">
            <p class="me-2">Add Product</p>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              class="bi bi-plus"
              viewBox="0 0 16 16"
            >
              <path
                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"
              />
            </svg>
          </button>
        </NuxtLink>
      </div>
    </div>

    <div class="tabs d-flex">
      <!-- All tab -->
      <div
        class="tab"
        :class="{ active: selectedCategory === 'all' }"
        @click="selectCategory('all')"
      >
        All
      </div>
      <!-- Category tabs -->
      <div
        v-for="category in categories"
        :key="category.id"
        :class="['tab', { active: selectedCategory === category.id }]"
        @click="selectCategory(category.id)"
      >
        {{ category.name }}
      </div>
    </div>

    <div class="cards row">
      <div v-if="products.length === 0" class="col-12">
        <p>No products found</p>
      </div>

      <div v-for="product in filteredProducts" :key="product.id" class="col-3">
        <div class="card">
          <img
            :src="`http://localhost/clash/products/${product.image1}`"
            alt="card image"
            class="card-img"
          />
          <div class="card__content">
            <p class="mt-2">{{ product.product_name }}</p>
            <h6 class="mt-2">N{{ parseInt(product.price) }}</h6>
            <p class="mt-2 value">
              {{ product.description.slice(0, 20)
              }}{{ product.description.length > 20 ? "..." : "" }}
            </p>
            <NuxtLink
              :to="{
                name: 'products-EditProduct',
                params: { id: product.id },
                query: { id: product.id },
              }"
            >
              <button class="w-100 mt-3">Edit Item</button>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";

const categories = ref([]);
const products = ref([]);
const selectedCategory = ref("all"); // Default to 'all'
const searchQuery = ref("");

const fetchAllProducts = async () => {
  try {
    const response = await fetch("http://localhost/clash/getAllProducts.php");
    const result = await response.json();
    if (result.status === "success") {
      products.value = result.data;
      console.log("Fetched all products:", products.value);
    } else {
      console.error("Failed to load all products:", result);
    }
  } catch (error) {
    console.error("Error:", error);
  }
};

const fetchProductsByCategory = async (categoryId) => {
  try {
    const response = await fetch(
      `http://localhost/clash/getProducts.php?category=${categoryId}`
    );
    const result = await response.json();
    if (result.status === "success") {
      products.value = result.data;
      console.log("Fetched products for category:", products.value);
    } else {
      console.error("Failed to load products:", result);
    }
  } catch (error) {
    console.error("Error:", error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await fetch("http://localhost/clash/getCategories.php");
    const result = await response.json();
    if (result.status === "success") {
      categories.value = result.data;
      // Fetch all products initially
      await fetchAllProducts();
    } else {
      console.error("Failed to load categories:", result);
    }
  } catch (error) {
    console.error("Error:", error);
  }
};

const selectCategory = async (categoryId) => {
  selectedCategory.value = categoryId;
  if (categoryId === "all") {
    await fetchAllProducts();
  } else {
    await fetchProductsByCategory(categoryId);
  }
};

const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value;

  return products.value.filter((product) =>
    product.product_name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

onMounted(fetchCategories);
</script>

<style scoped>
.card-img {
  height: 250px;
}

.tabs {
  margin: 20px 0;
  gap: 10px;
}

.tab {
  cursor: pointer;
  padding: 8px 16px;
  font-size: large;
  font-weight: bolder;
  border-radius: 4px;
}

.tab.active {
  color: #260021 !important;
}
</style>
