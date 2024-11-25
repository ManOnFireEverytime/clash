<template>
  <div class="auth">
    <div class="login">
      <h4 class="mb-5">Login to admin CMS</h4>

      <form @submit.prevent="login">
        <input
          v-model="usernameOrEmail"
          type="text"
          placeholder="Username or Email"
          required
        />
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          required
        />

        <button class="mt-5" type="submit">LOG IN</button>
      </form>

      <p v-if="message">{{ message }}</p>
      <p class="mt-3">
        Don't have an account?
        <NuxtLink to="/register" class="register-link" style="color: #260021"
          >Register here</NuxtLink
        >
      </p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      usernameOrEmail: "",
      password: "",
      message: "",
    };
  },
  methods: {
    async login() {
      const data = {
        username_or_email: this.usernameOrEmail,
        password: this.password,
      };

      try {
        const response = await fetch("http://localhost/clash/login-logic.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data),
        });

        const result = await response.json();

        if (result.success) {
          sessionStorage.setItem("token", result.token); // Save the token
          sessionStorage.setItem("username", data.username_or_email); // Save username or email
          window.location.href = "/"; // Redirect to home
        } else {
          this.message = result.message; // Display the error message
        }
      } catch (error) {
        console.error("Error:", error);
        this.message = "An error occurred during login. Please try again.";
      }
    },
  },
};
</script>

<style scoped>
/* Add your styles here */
</style>
