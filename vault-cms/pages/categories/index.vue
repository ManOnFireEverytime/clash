<template>
  <div class="products">
    <!-- Header -->
    <div class="header d-flex justify-content-between align-items-center">
      <h5>Categories</h5>
      <div class="d-flex align-items-center">
        <NuxtLink :to="{ name: 'categories-AddCategory' }">
          <button class="d-flex ms-3">
            <p class="me-2">Add Category</p>
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

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger" role="alert">
      {{ error }}
      <button
        class="btn btn-outline-danger btn-sm ms-2"
        @click="fetchCategories"
      >
        Retry
      </button>
    </div>

    <!-- Categories List -->
    <div v-else class="categories-container">
      <!-- Categories Tabs -->
      <!-- Categories Table -->
      <table class="table categories-table">
        <thead>
          <tr>
            <th>Category Name</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td
              @click="selectCategory(category.id)"
              :class="{ 'table-active': selectedCategory === category.id }"
              style="cursor: pointer"
            >
              {{ category.name }}
            </td>
            <td class="text-end">
              <NuxtLink
                :to="{
                  name: 'categories-EditCategory',
                  params: { id: category.id },
                  query: { id: category.id },
                }"
                class="btn btn-sm btn-outline-primary me-2"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  fill="currentColor"
                  class="bi bi-pencil-square"
                  viewBox="0 0 16 16"
                >
                  <path
                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293z"
                  />
                  <path
                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"
                  />
                </svg>
              </NuxtLink>
              <button
                class="btn btn-sm btn-outline-danger"
                @click="confirmDelete(category)"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  fill="currentColor"
                  class="bi bi-trash"
                  viewBox="0 0 16 16"
                >
                  <path
                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"
                  />
                  <path
                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"
                  />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- No Categories State -->
      <div v-if="categories.length === 0" class="text-center py-4">
        <p>No categories found. Add your first category!</p>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="modal-backdrop"
      @click="showDeleteModal = false"
    >
      <div class="modal-content" @click.stop>
        <h4>Delete Category</h4>
        <p>Are you sure you want to delete "{{ categoryToDelete?.name }}"?</p>
        <div class="modal-actions">
          <button
            class="btn btn-secondary me-2"
            @click="showDeleteModal = false"
          >
            Cancel
          </button>
          <button
            class="btn btn-danger"
            :disabled="deleteLoading"
            @click="deleteCategory"
          >
            <span
              v-if="deleteLoading"
              class="spinner-border spinner-border-sm me-1"
            ></span>
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

// State
const categories = ref([]);
const loading = ref(true);
const error = ref(null);
const selectedCategory = ref(null);
const showDeleteModal = ref(false);
const categoryToDelete = ref(null);
const deleteLoading = ref(false);

// Fetch categories from API
const fetchCategories = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await fetch("http://localhost/clash/getCategories.php");
    const result = await response.json();

    if (result.status === "success") {
      categories.value = result.data;
      if (categories.value.length > 0 && !selectedCategory.value) {
        selectedCategory.value = categories.value[0].id;
      }
    } else {
      throw new Error(result.message || "Failed to fetch categories");
    }
  } catch (err) {
    error.value = err.message;
    console.error("Error fetching categories:", err);
  } finally {
    loading.value = false;
  }
};

// Select category handler
const selectCategory = (categoryId) => {
  selectedCategory.value = categoryId;
  emit("category-selected", categoryId);
};

// Delete handlers
const confirmDelete = (category) => {
  categoryToDelete.value = category;
  showDeleteModal.value = true;
};

const deleteCategory = async () => {
  if (!categoryToDelete.value) return;

  try {
    deleteLoading.value = true;
    const response = await fetch(
      `http://localhost/clash/deleteCategory.php?id=${categoryToDelete.value.id}`,
      { method: "DELETE" }
    );
    const result = await response.json();

    if (result.status === "success") {
      // Remove category from local state
      categories.value = categories.value.filter(
        (cat) => cat.id !== categoryToDelete.value.id
      );
      showDeleteModal.value = false;
      categoryToDelete.value = null;

      // If deleted category was selected, select first available category
      if (selectedCategory.value === categoryToDelete.value.id) {
        selectedCategory.value = categories.value.id || null;
      }
    } else {
      throw new Error(result.message || "Failed to delete category");
    }
  } catch (err) {
  } finally {
    deleteLoading.value = false;
  }
};

// Fetch categories on component mount
onMounted(() => {
  fetchCategories();
});

// Define emits
const emit = defineEmits(["category-selected"]);
</script>

<style scoped>
.categories-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px;
}
.category-actions {
  opacity: 0;
  transition: opacity 0.2s ease;
}

.category-item:hover .category-actions {
  opacity: 1;
}

/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 90%;
  width: 400px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}
.categories-table th,
.categories-table td {
  vertical-align: middle;
  padding: 10px 15px;
}

.categories-table .table-active {
  background-color: #e9ecef;
}

.categories-table th {
  border-bottom: 2px solid #dee2e6;
}

.categories-table td {
  background-color: #f8f9fa;
  border-radius: 8px;
}

.categories-table td:last-child {
  text-align: right;
}

.categories-table .btn-outline-primary,
.categories-table .btn-outline-danger {
  transition: all 0.2s ease;
}

.categories-table .btn-outline-primary:hover,
.categories-table .btn-outline-danger:hover {
  transform: scale(1.05);
}
</style>
