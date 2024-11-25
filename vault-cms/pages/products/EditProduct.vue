<template>
  <div class="add__product">
    <h6 class="header">
      Products <span class="ms-2 me-2 stroke">/</span> <span>Edit Product</span>
    </h6>

    <form
      @submit.prevent="updateProduct"
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

      <!-- Right column with updated image handling -->
      <div class="right col-6">
        <p>Upload Product Image</p>
        <label
          for="file-upload-main"
          class="custom-file-upload"
          :style="{
            background: image1 ? `url(${getImageUrl(image1)})` : '',
            backgroundSize: 'contain',
            backgroundRepeat: 'no-repeat',
            backgroundPosition: 'center',
          }"
        >
          <svg
            v-if="!image1"
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
                background: images[index - 1]
                  ? `url(${getImageUrl(images[index - 1])})`
                  : '',
                backgroundSize: 'contain',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'center',
              }"
            >
              <svg
                v-if="!images[index - 1]"
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
        <button
          type="button"
          @click="showDeleteModal = true"
          class="delete-button"
        >
          Delete Product
        </button>
        <button type="submit">Update Product</button>
      </div>
    </form>
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Confirm Deletion</h3>
        <p>
          Are you sure you want to delete this product? This action cannot be
          undone.
        </p>
        <div class="modal-buttons">
          <button @click="deleteProduct" class="confirm-button">Confirm</button>
          <button @click="showDeleteModal = false" class="cancel-button">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import imgUrl from "~/includes/ImgUrl";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const productId = route.params.id || route.query.id;

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
const showDeleteModal = ref(false);
const IMAGE_BASE_URL = "http://localhost/clash/products/";

// Helper function to get full image URL
const getImageUrl = (imagePath) => {
  if (!imagePath) return "";
  if (imagePath.startsWith("http") || imagePath.startsWith("blob")) {
    return imagePath;
  }
  return IMAGE_BASE_URL + imagePath;
};

const fetchProduct = async () => {
  try {
    const response = await fetch(
      `http://localhost/clash/getProduct.php?id=${productId}`
    );
    const result = await response.json();
    if (result.status === "success") {
      const product = result.data;
      productName.value = product.product_name;
      category.value = product.category;
      price.value = parseInt(product.price);
      description.value = product.description;
      // min.value = parseInt(product.min_range);
      // max.value = parseInt(product.max_range);
      image1.value = product.image1 || "";
      images.value = [
        product.image2 || "",
        product.image3 || "",
        product.image4 || "",
        product.image5 || "",
      ];
    } else {
      alert("Failed to load product details.");
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
    } else {
      alert("Failed to load categories.");
    }
  } catch (error) {
    console.error("Error:", error);
  }
};

// Call fetch functions on component mount
onMounted(() => {
  fetchProduct();
  fetchCategories();
});

const imageUpload = async (event, index) => {
  const file = event.target.files[0];
  if (!file) return;

  try {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (index === 0) {
        image1.value = e.target.result; // This will be base64
      } else {
        images.value[index - 1] = e.target.result; // This will be base64
      }
    };
    reader.readAsDataURL(file);
  } catch (error) {
    console.error("Error uploading image:", error);
    alert("Failed to upload image. Please try again.");
  }
};

const updateProduct = async () => {
  const productData = {
    id: productId,
    product_name: productName.value,
    category: category.value,
    price: parseFloat(price.value),
    // min: parseFloat(min.value),
    // max: parseFloat(max.value),
    description: description.value,
    image1: image1.value,
    image2: images.value[0],
    image3: images.value[1],
    image4: images.value[2],
    image5: images.value[3],
  };

  try {
    const response = await fetch("http://localhost/clash/updateProduct.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(productData),
    });

    const result = await response.json();
    if (result.status === "success") {
      alert("Product updated successfully!");
      // Update the local image paths with the returned data if needed
      if (result.images) {
        image1.value = result.images[0];
        images.value = result.images.slice(1);
      }
    } else {
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Failed to update product. Please try again.");
  }
};

const deleteProduct = async () => {
  try {
    const response = await fetch(`http://localhost/clash/deleteProduct.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: productId }), // Pass the ID in the body
    });
    const result = await response.json();
    if (result.status === "success") {
      router.push("/products");
    } else {
      router.push("/products");
    }
  } catch (error) {
    router.push("/products");
  } finally {
    showDeleteModal.value = false; // Close modal after deletion attempt
  }
};
</script>

<style scoped>
.delete-button {
  background-color: #d9534f;
  color: #fff;
  margin-right: 10px;
  cursor: pointer;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 300px;
  text-align: center;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.confirm-button {
  background-color: #d9534f;
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  border-radius: 4px;
}

.cancel-button {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  border-radius: 4px;
}
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
