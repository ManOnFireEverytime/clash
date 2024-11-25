export default defineNuxtRouteMiddleware((to, from) => {
  // Check if user is logged in by looking for token in sessionStorage
  if (import.meta.client) {
    const token = sessionStorage.getItem('token')
    
    // If user is not logged in and trying to access any page except auth
    if (!token && to.path !== '/auth') {
      return navigateTo('/auth')
    }
    
    // If user is logged in and trying to access auth page
    if (token && to.path === '/auth') {
      return navigateTo('/')
    }
  }
})