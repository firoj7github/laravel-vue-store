# 🚀 Laravel + Vue js Project Setup

এই প্রজেক্টে **Laravel 12** এবং **Vue 3** ব্যবহার করা হয়েছে। নিচের ধাপগুলো অনুসরণ করে সহজে লোকাল মেশিনে রান করতে পারবেন।  

---

## ✅ Prerequisites
- PHP 8.2 বা তার উপরে
- Composer
- Node.js (LTS) & npm

---

## ⚡ Installation & Setup

প্রথমে Laravel প্রজেক্ট তৈরি করুন এবং Vue সহ Breeze ইনস্টল করুন।  

```bash
# Laravel Installer ইনস্টল (যদি আগে না করা থাকে)
composer global require laravel/installer

# নতুন Laravel প্রজেক্ট তৈরি করুন
laravel new laravel-vue-axios-pinia

# প্রজেক্ট ফোল্ডারে যান
cd laravel-vue-axios-pinia

# Laravel Breeze ইনস্টল করুন
composer require laravel/breeze --dev
php artisan breeze:install blade

# Vue এবং প্রয়োজনীয় প্যাকেজ ইনস্টল করুন
npm install vue vue-router pinia axios
npm install @vitejs/plugin-vue

# বাকি ডিপেন্ডেন্সি ইনস্টল করুন
npm install

# প্রজেক্ট রান করুন
composer run dev

```


## কোথায় কোথায় কি setup করতে হবে

### Vite Configuration (vite.config.js)
- @vitejs/plugin-vue দিয়ে (.vue) support যোগ করা হয়।
- use this code copy paste with same file name

### Vue Entry Point (resources/js/app.js)
- createApp() দিয়ে root application instance তৈরি হয়।
- .use(router) routing setup করা হয়।
- .use(createPinia()) global state ব্যবহার করতে চাই
- .mount('#app') Vue app browser এ দেখানোর জন্য mount করতে হয়
- use this code copy paste with same file name

### Main Vue Component (resources/js/App.vue)
- use this code copy paste with same file name

### Welcome Blade পরিবর্তন (resources/views/welcome.blade.php)
- use this code copy paste with same file name

### Add catch-all route for React ( web.php )
- /{any} → সব URL route catch করবে।
```bash
Route::get('/{any}', function () {
    return view('welcome'); // React root view
})->where('any', '.*');
```