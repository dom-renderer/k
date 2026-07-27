@extends('layouts.app')

@section('title', 'Documentation - InApp Inventory Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <h1 class="fs-3 mb-1">Documentation</h1>
        <p>This documentation will guide you through the setup and usage of the Laravel 13 InApp Inventory Dashboard application.</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <!-- Prerequisites -->
    <div class="mb-4">
      <div class="mb-2">
        <h2 class="h5 mb-1">Prerequisites</h2>
        <p>Before you begin, ensure you have the following installed:</p>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item ps-0">PHP (v8.4 or higher)</li>
        <li class="list-group-item ps-0">Composer (v2.8 or higher)</li>
        <li class="list-group-item ps-0">Node.js & npm / yarn</li>
      </ul>
    </div>

    <!-- Installation -->
    <div class="mb-4">
      <h2 class="h5 mb-2">Installation</h2>
      <ol class="list-group list-group-numbered list-group-flush">
        <li class="list-group-item ps-0">Clone or open the Laravel repository</li>
        <li class="list-group-item ps-0">
          Install PHP dependencies:
          <pre class="bg-light border rounded p-3 mt-2"><code>composer install</code></pre>
        </li>
        <li class="list-group-item ps-0">
          Install frontend dependencies:
          <pre class="bg-light border rounded p-3 mt-2"><code>npm install</code></pre>
        </li>
      </ol>
    </div>

    <!-- Usage -->
    <div class="mb-6">
      <h2 class="h5 mb-2">Run the app</h2>
      <p>To start the Vite development server:</p>
      <pre class="bg-light border rounded p-3"><code>npm run dev</code></pre>
      <p>To start the Laravel HTTP server:</p>
      <pre class="bg-light border rounded p-3"><code>php artisan serve</code></pre>
    </div>

    <!-- Next Steps -->
    <div class="mb-4">
      <h2 class="h5 mb-2">Next Steps</h2>
      <ol class="list-group list-group-numbered list-group-flush">
        <li class="list-group-item ps-0">Review Blade views in <code>resources/views</code></li>
        <li class="list-group-item ps-0">Customize controllers in <code>app/Http/Controllers</code></li>
        <li class="list-group-item ps-0">
          Build frontend assets for production:
          <pre class="bg-light border rounded p-3 mt-4"><code>npm run build</code></pre>
        </li>
      </ol>
    </div>

    <!-- Project Structure -->
    <div class="mb-4">
      <h2 class="h5 mb-0">Project Structure</h2>
      <pre class="bg-light border rounded p-3"><code>
inapp-laravel/
├── app/
│   └── Http/Controllers/   # Application Controllers
├── resources/
│   ├── js/                 # JS & Charts
│   ├── scss/               # Bootstrap SCSS styles
│   └── views/              # Blade layout & page views
├── routes/
│   └── web.php             # Laravel Web Routes
├── vite.config.js          # Laravel Vite Plugin configuration
└── package.json            # NPM dependencies
      </code></pre>
    </div>

    <!-- Support -->
    <div class="mb-2">
      <h2 class="h5">Support</h2>
      <p>
        For issues or questions, please refer to the documentation or create an issue in the repository. Also you can contact
        us at <a href="#!" class="text-primary">CodesCandy</a>.
      </p>
    </div>
  </div>
</div>
@endsection
