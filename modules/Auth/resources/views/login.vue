<script setup>
import { ref, computed } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const togglePassword = ref(false)
const currentYear = computed(() => new Date().getFullYear())

const fillAdmin = () => {
  form.email = 'admin@example.com'
  form.password = 'password'
}

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Login" />

  <div class="auth-page-wrapper pt-5">
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
      <div class="bg-overlay"></div>

      <div class="shape">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
          viewBox="0 0 1440 120">
          <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
        </svg>
      </div>
    </div>

    <div class="auth-page-content">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="text-center mt-sm-5 mb-4 text-white-50">
              <div>
                <Link href="/" class="d-inline-block auth-logo">
                  <img src="/images/logo-light.png" alt="" height="20">
                </Link>
              </div>
              <p class="mt-3 fs-15 fw-medium">Premium Admin & Dashboard Template</p>
            </div>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card mt-4">
              <div class="card-body p-4">
                <div class="text-center mt-2">
                  <h5 class="text-primary">Welcome Back !</h5>
                  <p class="text-muted">Sign in to continue to Velzon.</p>
                </div>
                <div class="p-2 mt-4">
                  <div v-if="form.errors.email || form.errors.password" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ form.errors.email || form.errors.password }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>

                  <form @submit.prevent="submit">
                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input
                        type="email"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.email }"
                        id="email"
                        v-model="form.email"
                        placeholder="Enter email"
                        required
                        autofocus
                      >
                      <div v-if="form.errors.email" class="invalid-feedback">
                        {{ form.errors.email }}
                      </div>
                    </div>

                    <div class="mb-3">
                      <div class="float-end">
                        <Link href="#" class="text-muted">Forgot password?</Link>
                      </div>
                      <label class="form-label" for="password-input">Password</label>
                      <div class="position-relative auth-pass-inputgroup mb-3">
                        <input
                          :type="togglePassword ? 'text' : 'password'"
                          class="form-control pe-5 password-input"
                          :class="{ 'is-invalid': form.errors.password }"
                          placeholder="Enter password"
                          id="password-input"
                          v-model="form.password"
                          required
                        >
                        <button
                          class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon"
                          type="button"
                          id="password-addon"
                          @click="togglePassword = !togglePassword"
                        >
                          <i class="ri-eye-fill align-middle"></i>
                        </button>
                        <div v-if="form.errors.password" class="invalid-feedback">
                          {{ form.errors.password }}
                        </div>
                      </div>
                    </div>

                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        v-model="form.remember"
                        id="auth-remember-check"
                      >
                      <label class="form-check-label" for="auth-remember-check">Remember me</label>
                    </div>

                    <div class="mt-4">
                      <button class="btn btn-success w-100" type="submit" :disabled="form.processing">
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        {{ form.processing ? 'Please wait...' : 'Sign In' }}
                      </button>
                    </div>

                    <div class="mt-3">
                      <button type="button" class="btn btn-soft-secondary w-100" @click="fillAdmin">
                        <i class="ri-user-star-line me-1"></i> Fill Admin Credentials
                      </button>
                    </div>

                    <div class="mt-4 text-center">
                      <div class="signin-other-title">
                        <h5 class="fs-13 mb-4 title">Sign In with</h5>
                      </div>
                      <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                        <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                        <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                        <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="mt-4 text-center">
              <p class="mb-0">Don't have an account ? <Link href="#" class="fw-semibold text-primary text-decoration-underline"> Signup </Link></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="text-center">
              <p class="mb-0 text-muted">&copy; {{ currentYear }} Velzon. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand</p>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
