<template>
  <div class="verify">
    <h2>Verify Your Email</h2>
    <form @submit.prevent="verifyCode">
      <input
        type="text"
        v-model="verificationCode"
        placeholder="Enter your verification code"
        required
      />
      <button type="submit">Verify</button>
    </form>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      verificationCode: "",
      message: "",
    };
  },
  methods: {
    async verifyCode() {
      try {
        const email = new URLSearchParams(window.location.search).get("email");
        const response = await fetch("http://localhost/clash/verify.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            email: email,
            verification_code: this.verificationCode,
          }),
        });

        const data = await response.json();
        this.message = data.message;

        if (data.success) {
          // Optionally redirect to login page after successful verification
          this.$router.push("/auth"); // Make sure your Vue router is set up
        }
      } catch (error) {
        console.error("Error:", error);
        this.message = "An error occurred. Please try again.";
      }
    },
  },
};
</script>

<style>
/* Add your styles here */
</style>
