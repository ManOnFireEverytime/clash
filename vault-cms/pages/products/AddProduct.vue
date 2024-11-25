<template>
  <div class="add__product">
    <h6 class="header">
      Products <span class="ms-2 me-2 stroke">/</span> <span>Add Product</span>
    </h6>

    <form
      @submit.prevent="submitProduct"
      class="row"
      enctype="multipart/form-data"
    >
      <div class="left col-6">
        <label class="mb-2" for="product_name">Product Name</label>
        <input
          type="text"
          id="product_name"
          v-model="productName"
          placeholder="Enter Name"
          required
        />

        <label class="mb-2" for="category">Category</label>
        <select id="category" v-model="category" required>
          <option value="" disabled>Select Category</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>

        <label class="mb-2" for="price">Price</label>
        <input
          type="number"
          id="price"
          v-model="price"
          placeholder="Enter Price"
          step="0.01"
          min="0"
          required
        />

        <!-- <div class="range">
          <p class="mb-4 d-block">Value Range</p>

          <label for="minRange">Minimum: £{{ min }}</label>
          <input
            type="range"
            v-model="min"
            id="minRange"
            name="minRange"
            min="100"
            max="2000"
          />

          <label for="maxRange">Maximum: £{{ max }}</label>
          <input
            type="range"
            v-model="max"
            id="maxRange"
            name="maxRange"
            min="100"
            max="2000"
          />
        </div> -->
      </div>

      <div class="right col-6">
        <p>Upload Product Image</p>
        <label
          for="file-upload-main"
          class="custom-file-upload"
          :style="{
            background: image1 ? `url(${image1})` : '',
            backgroundSize: 'cover',
          }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            class="bi bi-plus-circle-fill"
            viewBox="0 0 16 16"
          >
            <path
              d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"
            />
          </svg>
        </label>
        <input
          id="file-upload-main"
          type="file"
          @change="imageUpload($event, 0)"
          accept="image/*"
        />

        <div class="row">
          <div class="col-3" v-for="index in 4" :key="index">
            <label
              :for="'file-upload-' + index"
              class="custom-file-uploads"
              :style="{
                background: images[index] ? `url(${images[index]})` : '',
                backgroundSize: 'cover',
              }"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-plus-circle-fill"
                viewBox="0 0 16 16"
              >
                <path
                  d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"
                />
              </svg>
            </label>
            <input
              :id="'file-upload-' + index"
              type="file"
              @change="imageUpload($event, index)"
              accept="image/*"
            />
          </div>
        </div>
      </div>

      <div class="col-12 mt-5">
        <label for="description">Description</label>
        <textarea
          v-model="description"
          id="description"
          rows="10"
          placeholder="Describe Product"
          required
        ></textarea>
      </div>

      <div class="w-100 d-flex mt-4 mb-5 justify-content-end">
        <button type="submit">Add Product</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import imgUrl from "~/includes/ImgUrl";

// Form data
const productName = ref("");
const category = ref("");
const categories = ref([]);
const price = ref("");
const description = ref("");
// const min = ref(100);
// const max = ref(100);

// Image handling
const image1 = ref("");
const images = ref(["", "", "", ""]);

const fetchCategories = async () => {
  try {
    const response = await fetch("http://localhost/clash/getCategories.php");
    const result = await response.json();
    if (result.status === "success") {
      categories.value = result.data;
    } else {
      alert("Failed to load categories.");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Failed to fetch categories.");
  }
};

// Call fetchCategories on component mount
onMounted(fetchCategories);
const imageUpload = async (event, index) => {
  const file = event.target.files[0];
  if (!file) return;

  try {
    const url = await imgUrl.getUrl(file);
    if (index === 0) {
      image1.value = url;
    } else {
      images.value[index - 1] = url;
    }
  } catch (error) {
    console.error("Error uploading image:", error);
    alert("Failed to upload image. Please try again.");
  }
};

const submitProduct = async () => {
  // Form validation
  if (
    !productName.value ||
    !category.value ||
    !price.value ||
    !description.value
  ) {
    alert("Please fill in all required fields");
    return;
  }

  const productData = {
    product_name: productName.value,
    category: category.value,
    price: parseFloat(price.value),
    // min: parseFloat(min.value),
    // max: parseFloat(max.value),
    description: description.value,
    images: [image1.value, ...images.value].filter((img) => img),
  };

  try {
    const response = await fetch("http://localhost/clash/addProduct.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(productData),
    });

    const result = await response.json();

    if (result.status === "success") {
      alert("Product added successfully!");
      resetForm();
    } else {
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Failed to submit product. Please try again.");
  }
};

const resetForm = () => {
  // Reset all form fields
  productName.value = "";
  category.value = "";
  price.value = "";
  description.value = "";
  // min.value = 100;
  // max.value = 100;
  image1.value = "";
  images.value = ["", "", "", ""];
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
