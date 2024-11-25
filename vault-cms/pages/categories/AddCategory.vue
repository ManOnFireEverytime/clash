<template>
  <div class="add__product">
    <h6 class="header">
      Categories <span class="ms-2 me-2 stroke">/</span>
      <span>Add Category</span>
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
        <button type="submit">Add Category</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";

// Form data
const categoryName = ref("");

const submitCategory = async () => {
  // Form validation
  if (!categoryName.value) {
    alert("Please fill in all required fields");
    return;
  }

  const categoryData = {
    category_name: categoryName.value,
  };

  try {
    const response = await fetch("http://localhost/clash/addCategory.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(categoryData),
    });

    const result = await response.json();

    if (result.status === "success") {
      alert("Category added successfully!");
      resetForm();
    } else {
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Failed to submit category. Please try again.");
  }
};

const resetForm = () => {
  // Reset all form fields
  categoryName.value = "";
};
</script>

<style scoped>
.custom-file-upload {
  border: 2px dashed #ccc;
  display: inline-block;
  padding: 6px 12px;
  cursor: pointer;
  width: 100%;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.custom-file-uploads {
  border: 2px dashed #ccc;
  display: inline-block;
  padding: 6px 12px;
  cursor: pointer;
  width: 100%;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

input[type="file"] {
  display: none;
}

.range input[type="range"] {
  width: 100%;
  margin: 10px 0 20px 0;
}

textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

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
