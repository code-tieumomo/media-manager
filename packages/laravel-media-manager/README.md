# Laravel Media Manager

A full-featured media manager for Laravel applications, allowing file and folder management via a web interface and a reusable image picker component.

## Installation

1. Require the package via Composer (from your Laravel app root):

```bash
composer require code-tieumomo/laravel-media-manager
```

2. Publish the configuration and assets:

```bash
php artisan vendor:publish --provider="MediaManager\MediaManagerServiceProvider" --tag=config
php artisan vendor:publish --provider="MediaManager\MediaManagerServiceProvider" --tag=views
```

3. (Optional) Adjust the configuration in `config/media-manager.php` as needed.

## Usage

- Access the media manager at `/media` (must be authenticated).
- Use the `<x-media-picker />` Livewire component in your Blade views or Livewire forms to select/upload images via a modal.

## Configuration

- **disk**: Storage disk to use (default: `public`)
- **allowed_types**: Array of allowed MIME types/extensions
- **max_file_size**: Maximum file size in KB

## Features

- File/folder upload, move, delete, and preview
- Drag & drop uploads
- Image picker modal for forms
- Configurable and extendable

## Media Picker Usage

### Blade Usage

```blade
<x-media-picker />
```

### Livewire Usage (with event listener)

```php
// In your Livewire parent component
public $selectedImageUrl;

protected $listeners = ['mediaPickerSelected' => 'setImage'];

public function setImage($url)
{
    $this->selectedImageUrl = $url;
}
```

```blade
<!-- In your Blade or Livewire view -->
<x-media-picker />
@if($selectedImageUrl)
    <img src="{{ $selectedImageUrl }}" class="w-32 h-32 object-cover mt-2" />
@endif
```

---

For more details, see the documentation (coming soon). 