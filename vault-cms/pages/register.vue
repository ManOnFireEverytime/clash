<template>
  <div class="auth">
    <div class="login">
      <h4 class="mb-5">Register to admin CMS</h4>

      <form @submit.prevent="register">
        <input v-model="username" type="text" placeholder="Username" required />
        <input v-model="email" type="email" placeholder="Email" required />
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          required
        />

        <button class="mt-5" type="submit">Register</button>
      </form>

      <p v-if="message">{{ message }}</p>
      <p class="mt-3">
        Already have an account?
        <NuxtLink to="/auth" class="register-link" style="color: #260021"
          >Login</NuxtLink
        >
      </p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      email: "",
      password: "",
      message: "",
    };
  },
  methods: {
    async register() {
      const data = {
        username: this.username, // Adjust if necessary based on your backend expectations
        email: this.email,
        password: this.password,
      };

      try {
        const response = await fetch(
          "http://localhost/clash/signup-logic.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams(data),
          }
        );

        const result = await response.json();

        if (result.success) {
          // Redirect to verification page if needed
          window.location.href = result.redirect || "/default-success-page"; // Replace with the desired redirect
        } else {
          this.message = result.message; // Display the error message
        }
      } catch (error) {
        console.error("Error:", error);
        this.message =
          "An error occurred during registration. Please try again.";
      }
    },
  },
};
</script>

<style scoped>
/* Add your styles here */
</style>
