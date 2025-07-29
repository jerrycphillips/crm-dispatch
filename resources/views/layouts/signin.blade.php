<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sign In | TailAdmin - Tailwind CSS Admin Dashboard Template</title>
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body
    x-data="{ loaded: false, darkMode: false }"
    class="bg-gray-50"
    style="background-color: #f9fafb !important;"
  >
    <!-- ===== Preloader Start ===== -->
    {{-- @include('partials.preloader') --}}
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-6" style="background-color: #f9fafb !important;">
      <!-- Login Card -->
      <div class="w-full max-w-md bg-slate-800 rounded-2xl p-6 sm:p-8" 
           style="background-color: #1e293b !important; 
                  box-shadow: 0 32px 64px rgba(0, 0, 0, 0.4), 
                              0 16px 32px rgba(0, 0, 0, 0.3),
                              0 8px 16px rgba(0, 0, 0, 0.2),
                              0 4px 8px rgba(0, 0, 0, 0.1);
                  transform: translateY(-4px);">
        
        <!-- Logo -->
        <div class="flex justify-center">
          <img src="{{ asset('assets/images/ays_translucent.png') }}?v={{ time() }}" 
               alt="AYS Logo" 
               class="h-16 sm:h-20 md:h-24 lg:h-28 w-auto">
        </div>
        
        <!-- Form -->
        <div class="w-full">
          <div class="w-full">
            <div>
              <div class="mb-2 sm:mb-3">
                <h1
                  class="mb-2 text-2xl sm:text-3xl font-semibold text-white"
                >
                  Sign In
                </h1>
                <p class="text-base sm:text-base text-gray-300">
                  Enter your email and password to sign in!
                </p>
              </div>
              <div class="mb-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-4">
                  <button
                    class="inline-flex items-center justify-center gap-3 py-3 px-4 text-sm font-normal text-white transition-colors bg-slate-700 rounded-lg hover:bg-slate-600"
                  >
                    <svg
                      width="20"
                      height="20"
                      viewBox="0 0 20 20"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M18.7511 10.1944C18.7511 9.47495 18.6915 8.94995 18.5626 8.40552H10.1797V11.6527H15.1003C15.0011 12.4597 14.4654 13.675 13.2749 14.4916L13.2582 14.6003L15.9087 16.6126L16.0924 16.6305C17.7788 15.1041 18.7511 12.8583 18.7511 10.1944Z"
                        fill="#4285F4"
                      />
                      <path
                        d="M10.1788 18.75C12.5895 18.75 14.6133 17.9722 16.0915 16.6305L13.274 14.4916C12.5201 15.0068 11.5081 15.3666 10.1788 15.3666C7.81773 15.3666 5.81379 13.8402 5.09944 11.7305L4.99473 11.7392L2.23868 13.8295L2.20264 13.9277C3.67087 16.786 6.68674 18.75 10.1788 18.75Z"
                        fill="#34A853"
                      />
                      <path
                        d="M5.10014 11.7305C4.91165 11.186 4.80257 10.6027 4.80257 9.99992C4.80257 9.3971 4.91165 8.81379 5.09022 8.26935L5.08523 8.1534L2.29464 6.02954L2.20333 6.0721C1.5982 7.25823 1.25098 8.5902 1.25098 9.99992C1.25098 11.4096 1.5982 12.7415 2.20333 13.9277L5.10014 11.7305Z"
                        fill="#FBBC05"
                      />
                      <path
                        d="M10.1789 4.63331C11.8554 4.63331 12.9864 5.34303 13.6312 5.93612L16.1511 3.525C14.6035 2.11528 12.5895 1.25 10.1789 1.25C6.68676 1.25 3.67088 3.21387 2.20264 6.07218L5.08953 8.26943C5.81381 6.15972 7.81776 4.63331 10.1789 4.63331Z"
                        fill="#EB4335"
                      />
                    </svg>
                    Sign in with Google
                  </button>
                  <button
                    class="inline-flex items-center justify-center gap-3 py-3 text-sm font-normal text-white transition-colors bg-slate-700 rounded-lg px-7 hover:bg-slate-600"
                  >
                    <svg
                      width="21"
                      class="fill-current"
                      height="20"
                      viewBox="0 0 21 20"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M15.6705 1.875H18.4272L12.4047 8.75833L19.4897 18.125H13.9422L9.59717 12.4442L4.62554 18.125H1.86721L8.30887 10.7625L1.51221 1.875H7.20054L11.128 7.0675L15.6705 1.875ZM14.703 16.475H16.2305L6.37054 3.43833H4.73137L14.703 16.475Z"
                      />
                    </svg>

                    Sign in with X
                  </button>
                </div>
                <div class="relative py-3 sm:py-5">
                  <div class="absolute inset-0 flex items-center">
                    <div
                      class="w-full border-t border-slate-600"
                    ></div>
                  </div>
                  <div class="relative flex justify-center text-sm">
                    <span
                      class="p-2 text-gray-300 bg-slate-800 sm:px-5 sm:py-2"
                      >Or</span
                    >
                  </div>
                </div>
                <form method="POST" action="{{ route('login') }}">
                  @csrf
                  <div class="space-y-4 sm:space-y-5">
                    <!-- Email -->
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-gray-300"
                      >
                        Email<span class="text-red-400">*</span>
                      </label>
                      <input
                        type="email"
                        id="loginEmail"
                        name="loginEmail"
                        value="{{ old('loginEmail') }}"
                        placeholder="info@gmail.com"
                        required
                        autofocus
                        class="h-12 w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-3 text-base text-white shadow-theme-xs placeholder:text-gray-400 focus:border-blue-400 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 @error('loginEmail') border-red-500 @enderror"
                      />
                      @error('loginEmail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                      @enderror
                    </div>
                    <!-- Password -->
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-gray-300"
                      >
                        Password<span class="text-red-400">*</span>
                      </label>
                      <div x-data="{ showPassword: false }" class="relative">
                        <input
                          type="password"
                          x-bind:type="showPassword ? 'text' : 'password'"
                          id="password"
                          name="password"
                          placeholder="Enter your password"
                          required
                          class="h-12 w-full rounded-lg border border-slate-600 bg-slate-700 py-3 pl-4 pr-12 text-base text-white shadow-theme-xs placeholder:text-gray-400 focus:border-blue-400 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 @error('password') border-red-500 @enderror"
                        />
                        @error('password')
                          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <button
                          type="button"
                          @click="showPassword = !showPassword"
                          class="absolute z-30 text-gray-400 cursor-pointer right-3 top-1/2 -translate-y-1/2 hover:text-gray-300 p-1 rounded"
                        >
                          <!-- Eye Open Icon (default - password hidden) -->
                          <svg
                            x-show="!showPassword"
                            class="w-5 h-5 fill-current"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                              fill="currentColor"
                            />
                          </svg>
                          <!-- Eye Slash Icon (password visible) -->
                          <svg
                            x-show="showPassword"
                            class="w-5 h-5 fill-current"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            style="display: none;"
                          >
                            <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                              fill="currentColor"
                            />
                          </svg>
                        </button>
                      </div>
                    </div>
                    <!-- Checkbox -->
                    <div class="flex items-center justify-between">
                      <div x-data="{ checkboxToggle: false }">
                        <label
                          for="remember"
                          class="flex items-center text-sm font-normal text-gray-300 cursor-pointer select-none"
                        >
                          <div class="relative">
                            <input
                              type="checkbox"
                              id="remember"
                              name="remember"
                              class="sr-only"
                              @change="checkboxToggle = !checkboxToggle"
                            />
                            <div
                              :class="checkboxToggle ? 'border-blue-500 bg-blue-500' : 'bg-transparent border-slate-600'"
                              class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]"
                            >
                              <span :class="checkboxToggle ? '' : 'opacity-0'">
                                <svg
                                  width="14"
                                  height="14"
                                  viewBox="0 0 14 14"
                                  fill="none"
                                  xmlns="http://www.w3.org/2000/svg"
                                >
                                  <path
                                    d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                                    stroke="white"
                                    stroke-width="1.94437"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                  />
                                </svg>
                              </span>
                            </div>
                          </div>
                          Keep me logged in
                        </label>
                      </div>
                      <a
                        href="#"
                        class="text-sm text-blue-400 hover:text-blue-300"
                        >Forgot password?</a
                      >
                    </div>
                    <!-- Button -->
                    <div>
                      <button
                        type="submit"
                        class="flex items-center justify-center w-full px-4 py-3 text-base font-medium text-white transition rounded-lg bg-blue-600 shadow-theme-xs hover:bg-blue-700 h-12"
                      >
                        Sign In
                      </button>
                    </div>
                  </div>
                </form>
                <div class="mt-5">
                  <p
                    class="text-sm font-normal text-center text-gray-300 sm:text-start"
                  >
                    Don't have an account?
                    <a
                      href="#"
                      class="text-blue-400 hover:text-blue-300"
                      >Sign Up</a
                    >
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Login Card -->
      </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->
    
    <!-- JavaScript includes -->
    <script src="{{ asset('assets/js/index.js') }}"></script>
  </body>
</html>
