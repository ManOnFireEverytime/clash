<template>
  <div class="add__product">
    <h6 class="header">
      Categories <span class="ms-2 me-2 stroke">/</span>
      <span>Edit Category</span>
    </h6>

    <form @submit.prevent="submitCategory" class="row">
      <div class="left col-6">
        <label class="mb-2" for="category_name">Category Name</label>
        <input
          type="text"
          id="category_name"
          v-model="categoryName"
          placeholder="Enter Name"
          required
        />
      </div>

      <div class="w-100 d-flex mt-4 mb-5 justify-content-end">
        <button type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const categoryName = ref("");
const error = ref(null);
const loading = ref(true);

const route = useRoute();
const router = useRouter();
const categoryId = route.params.id || route.query.id;
console.log("Category ID:", categoryId);

const fetchCategory = async () => {
  try {
    const response = await fetch(
      `http://localhost/clash/getCategory.php?id=${categoryId}`
    );
    const result = await response.json();

    if (result.status === "success") {
      categoryName.value = result.data.name;
    } else {
      throw new Error(result.message || "Failed to load category details");
    }
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

const submitCategory = async () => {
  if (!categoryName.value) {
    alert("Please fill in all required fields");
    return;
  }

  try {
    const response = await fetch(
      `http://localhost/clash/updateCategory.php?id=${categoryId}`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name: categoryName.value }),
      }
    );

    const result = await response.json();
    if (result.status === "success") {
      router.push("/categories"); // Redirect to /categories
    } else {
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Failed to update category. Please try again.");
  }
};

onMounted(fetchCategory);
</script>

<style scoped>
select,
input[type="text"],
input[type="number"] {
  width: 100%;
  padding: 8px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
