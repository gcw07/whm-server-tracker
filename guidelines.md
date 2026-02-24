# WHM Server Tracker - Development Guidelines

## Project Overview

WHM Server Tracker is a Laravel-based application designed to monitor and manage multiple WHM (Web Host Manager) servers. It tracks server information, accounts, backups, disk usage, and provides monitoring capabilities including uptime checks, lighthouse audits, and WordPress version detection.

## Technology Stack

### Backend
- **Framework:** Laravel 12.x
- **PHP Version:** 8.4+
- **Database:** MySQL
- **Queue System:** Laravel Horizon for queue management
- **Testing:** Pest PHP for unit, feature, and integration tests
- **API Client:** Guzzle for HTTP requests to WHM API

### Frontend
- **Framework:** Livewire 4.x with Flux UI components
- **CSS:** Tailwind CSS 4.x with Tailwind Vite plugin
- **Build Tool:** Vite 7.x
- **Alpine.js:** For lightweight JavaScript interactions

### Additional Packages
- **spatie/laravel-uptime-monitor:** Uptime monitoring
- **spatie/laravel-backup:** Backup management
- **spatie/lighthouse-php:** Lighthouse performance audits
- **spatie/simple-excel:** Excel export functionality
- **laravel/fortify:** Authentication scaffolding

## Project Structure

### Directory Organization

```
app/
├── Actions/              # Single-purpose action classes (e.g., Fortify actions)
├── Casts/                # Custom Eloquent casts
├── Collections/          # Custom collection classes
├── Console/Commands/     # Artisan commands
├── Enums/                # PHP 8.1+ enums
├── Events/               # Event classes
├── Exceptions/           # Custom exceptions (organized by domain)
├── Jobs/                 # Queue jobs
├── Listeners/            # Event listeners
├── Livewire/             # Livewire components and forms
├── Models/               # Eloquent models
│   ├── Concerns/         # Model traits
│   └── Presenters/       # Presenter traits for models
├── Notifications/        # Notification classes
├── Providers/            # Service providers
└── Services/             # Business logic services (organized by domain)
    └── WHM/
        └── DataProcessors/  # Data processing classes

resources/
├── css/                  # CSS files
├── js/                   # JavaScript files
└── views/
    ├── components/       # Blade components (prefixed with ⚡ for Livewire)
    ├── layouts/          # Layout files
    └── pages/            # Page views (organized by domain, prefixed with ⚡)

tests/
├── Factories/            # Test data factories
├── Feature/              # Feature tests
├── Integration/          # Integration tests
└── Unit/                 # Unit tests
```

## Code Conventions

### PHP Code Style

#### Indentation
- **PHP files:** 4 spaces
- **Blade templates:** 2 spaces
- **YAML/YML files:** 2 spaces
- Use spaces, not tabs
- UTF-8 encoding
- LF line endings

#### Models

**Pattern Used:**
- Guarded properties: `protected $guarded = []` (mass assignment protection disabled)
- Extensive PHPDoc blocks with `@property`, `@property-read`, `@method`, and `@mixin` tags
- Presenter traits for computed attributes
- Eloquent scopes using PHP 8 attributes (`#[Scope]`)
- Type-safe enums for constants
- Encrypted sensitive fields (e.g., tokens)
- Custom casts for complex data types

**Example Model Structure:**
```php
<?php

namespace App\Models;

use App\Enums\ServerTypeEnum;
use App\Models\Presenters\ServerPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property-read mixed $formatted_name
 * @method static Builder<static>|ModelName search(string $term)
 */
class ModelName extends Model
{
    use PresenterTrait;

    protected $guarded = [];

    protected $appends = ['formatted_attribute'];

    protected $hidden = ['sensitive_field'];

    protected function casts(): array
    {
        return [
            'enum_field' => EnumClass::class,
            'sensitive_field' => 'encrypted',
            'custom_field' => CustomCast::class,
            'date_field' => 'datetime',
        ];
    }

    #[Scope]
    public function search(Builder $query, string $term): void
    {
        $query->whereAny(['field1', 'field2'], 'LIKE', "%$term%");
    }
}
```

#### Enums

**Pattern Used:**
- Backed string enums (PHP 8.1+)
- Static `labels()` method for human-readable names

**Example:**
```php
<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public static function labels(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }
}
```

#### Presenters

**Pattern Used:**
- Traits in `App\Models\Presenters` namespace
- Use Laravel's `Attribute` class for accessors
- Name pattern: `{Model}Presenter`
- Keep display logic separate from model business logic

**Example:**
```php
<?php

namespace App\Models\Presenters;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait ModelPresenter
{
    protected function formattedAttribute(): Attribute
    {
        return Attribute::make(
            get: fn () => "Formatted: {$this->attribute}",
        );
    }
}
```

#### Livewire Components

**Pattern Used:**
- Full-page components in `resources/views/pages/` with ⚡ prefix
- Forms in `app/Livewire/Forms/` namespace using Livewire Form objects
- Form validation rules in protected `rules()` method
- File naming: snake_case for views, PascalCase for classes

**Example Form:**
```php
<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class CreateEntityForm extends Form
{
    public string $name = '';
    public string $address = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
        ];
    }

    public function store()
    {
        $this->validate();

        return Entity::create([
            'name' => $this->name,
            'address' => $this->address,
        ]);
    }
}
```

#### Services

**Pattern Used:**
- Organized by domain (e.g., `WHM`)
- Service classes for external API integration
- Data processors for handling API responses
- Use dependency injection via constructor

**Example:**
```php
<?php

namespace App\Services\Domain;

class DomainService
{
    public function __construct(
        protected DependencyClass $dependency
    ) {}

    public function performAction(): void
    {
        // Business logic here
    }
}
```

#### Jobs

**Pattern Used:**
- Implement `ShouldQueue` interface
- Use standard queue traits: `Dispatchable`, `InteractsWithQueue`, `Queueable`, `SerializesModels`
- Public `$tries` property for retry attempts
- Constructor injection for dependencies

**Example:**
```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(
        public Model $model
    ) {}

    public function handle(Service $service): void
    {
        // Job logic
    }
}
```

#### Exceptions

**Pattern Used:**
- Organize by domain in subdirectories
- Descriptive exception names

**Example:**
```php
<?php

namespace App\Exceptions\Domain;

use Exception;

class SpecificException extends Exception
{
    // Custom exception logic
}
```

#### Events and Listeners

**Pattern Used:**
- Event classes with public properties
- Listener classes with descriptive names (e.g., `Send{NotificationType}`)
- Register in EventServiceProvider

### Blade Templates

**Conventions:**
- 2-space indentation
- Flux UI components for UI elements (`flux:button`, `flux:card`, `flux:table`, etc.)
- Use `@class` directive for conditional classes
- Livewire wire directives for interactivity (`wire:model.live`, `wire:click`)
- Component prefix ⚡ for Livewire full-page components

**Example:**
```blade
<div>
  <flux:card class="p-0">
    <flux:table :paginate="$this->items">
      <flux:table.columns>
        <flux:table.column sortable>Name</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @foreach ($this->items as $item)
          <flux:table.row :key="$item->id" @class([
            'bg-red-100' => $item->is_critical,
            'bg-white' => !$item->is_critical,
          ])>
            <flux:table.cell>{{ $item->name }}</flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
```

### Routes

**Pattern Used:**
- Middleware-based grouping
- Route prefixes for resource organization
- Livewire route registration using `Route::livewire()`
- Named routes for all routes
- Redirect root to primary page

**Example:**
```php
Route::prefix('entities')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::entity.listings')->name('entities.index');
    Route::livewire('/create', 'pages::entity.create')->name('entities.create');
    Route::livewire('/{entity}', 'pages::entity.details')->name('entities.show');
    Route::livewire('/{entity}/edit', 'entity.edit')->name('entities.edit');
});
```

### Testing

**Framework:** Pest PHP

**Test Organization:**
- **Unit Tests:** `tests/Unit/` - Test individual classes and methods
- **Feature Tests:** `tests/Feature/` - Test HTTP requests and Livewire components
- **Integration Tests:** `tests/Integration/` - Test external integrations

**Conventions:**
- Use `LazilyRefreshDatabase` trait for database tests
- Test factories in `tests/Factories/` namespace
- Use `beforeEach()` for test setup
- Descriptive test names using `test()` function or `it()` helper
- Data providers using `with()` for parameterized tests
- Act as authenticated user: `$this->actingAs($user)`

**Example:**
```php
<?php

use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authorized user can view the listing page', function () {
    $this->actingAs($this->user)
        ->get(route('entities.index'))
        ->assertSuccessful();
});

it('validates required fields', function ($field, $value, $rule) {
    $this->actingAs($this->user);

    Livewire::test('pages::entity.create')
        ->set("form.$field", $value)
        ->call('save')
        ->assertHasErrors(["form.$field" => $rule]);
})->with([
    ['name', '', 'required'],
    ['email', 'invalid', 'email'],
]);
```

## Database Conventions

### Migrations

- **Timestamps:** Use `CarbonImmutable` for timestamp columns
- **Foreign keys:** Use standard Laravel conventions
- **Naming:** Descriptive migration names with date prefix
- **Boolean defaults:** Explicitly set default values

### Table Naming

- Plural, snake_case (e.g., `servers`, `accounts`, `lighthouse_audits`)
- Pivot tables: alphabetically ordered (e.g., `account_server`)

## Configuration

### Environment Files

- `.env.example` provides template
- Sensitive values encrypted in database (tokens)
- Custom config files in `config/` directory (e.g., `config/server-tracker.php`)

### Composer Scripts

The project uses custom composer scripts for common tasks:
- `composer setup` - Full project setup
- `composer dev` - Start development environment (runs server, queue, logs, and vite concurrently)
- `composer test` - Run test suite
- `composer ide-helper` - Generate IDE helper files

## Naming Conventions

### Files and Directories
- **Classes:** PascalCase (e.g., `ServerController.php`)
- **Views:** snake_case (e.g., `server_listing.blade.php`)
- **Directories:** snake_case or lowercase
- **Livewire pages:** Prefix with ⚡ symbol

### Database
- **Tables:** Plural snake_case (e.g., `servers`, `accounts`)
- **Columns:** snake_case (e.g., `server_type`, `created_at`)
- **Foreign keys:** Singular model name + `_id` (e.g., `server_id`)

### PHP
- **Classes:** PascalCase
- **Methods:** camelCase
- **Variables:** camelCase
- **Constants:** UPPER_SNAKE_CASE
- **Enums:** PascalCase with values in snake_case

## Architecture Patterns

### Domain-Driven Organization

The application organizes code by domain/feature:
- Servers
- Accounts
- Monitors
- Users
- Authentication

### Service Layer Pattern

Business logic is extracted into service classes:
- `WhmApi` - WHM API integration
- Data processors for handling specific API responses

### Presenter Pattern

Display logic is separated from models using presenter traits:
- Formatted attributes
- Computed properties
- Display helpers

### Event-Driven Architecture

Events and listeners for:
- Data fetching success/failure
- Certificate expiration warnings
- Uptime check notifications
- Domain name expiration

### Queue-Based Processing

Background jobs for:
- Fetching server data
- Running lighthouse audits
- Checking WordPress versions
- Domain name checks
- Blacklist checks

## Development Workflow

### Local Development

```bash
# Start all services concurrently
composer dev

# This runs:
# - php artisan serve (Laravel server)
# - php artisan queue:listen (Queue worker)
# - php artisan pail (Log viewer)
# - npm run dev (Vite dev server)
```

### Testing

```bash
# Run all tests
composer test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Code Quality

- **Linter:** Laravel Pint for code style formatting
- **IDE Helpers:** Use `composer ide-helper` to generate IDE autocomplete files
- **EditorConfig:** Project includes `.editorconfig` for consistent formatting

## API Integration

### WHM API

- Uses Guzzle for HTTP requests
- Asynchronous promises for concurrent requests
- Custom header: `Authorization: whm {username}:{token}`
- SSL verification disabled (configurable)
- Base URL: `{protocol}://{address}:{port}/json-api/`
- API endpoints use `api.version=1` parameter

## UI Patterns

### Flux UI Components

The application extensively uses Flux UI (Livewire Flux):
- `flux:button` - Buttons with variants and icons
- `flux:card` - Card containers
- `flux:table` - Data tables with sorting and pagination
- `flux:badge` - Status badges with colors
- `flux:dropdown` - Dropdown menus
- `flux:modal` - Modal dialogs
- `flux:tooltip` - Tooltips
- `flux:tab.group` - Tab navigation

### Color Coding

- **Red:** Critical/errors/disk full
- **Orange:** Warnings/disk critical
- **Yellow:** Caution/disk warning
- **Green:** Success/active
- **Amber:** Security only PHP versions

### Icons

Uses Heroicons via Flux components:
- `icon="plus"` for create actions
- `icon="pencil"` for edit actions
- `icon="trash"` for delete actions
- `icon="arrow-top-right-on-square"` for external links

## Security

### Authentication

- Laravel Fortify for authentication
- Two-factor authentication support
- Login tracking (stores login history)

### Authorization

- Middleware-based route protection (`auth` middleware)
- All authenticated routes require login

### Data Protection

- Sensitive fields encrypted (API tokens)
- CSRF protection on forms
- Password reset functionality

## Performance Considerations

### Caching

- Livewire component caching
- View caching in production

### Queue Management

- Laravel Horizon for queue monitoring
- High priority queue for critical jobs
- Retry logic on jobs (typically 5 tries)

### Database

- Eager loading relationships to prevent N+1 queries
- Indexed columns for search functionality
- Pagination on large datasets

## Monitoring Features

### Uptime Monitoring

- Spatie uptime monitor integration
- Downtime statistics tracking
- Notifications for uptime events

### Lighthouse Audits

- Performance scoring
- SEO analysis
- Accessibility checks
- Best practices
- Stored audit results

### WordPress Detection

- Version checking via RSS feed
- Automatic updates tracking

### Blacklist Checking

- IP blacklist monitoring
- Status tracking

### Domain Name Expiration

- Domain expiration date tracking
- Early warning notifications

## Deployment

### Build Process

```bash
# Production build
npm run build

# Optimize composer autoloader
composer install --optimize-autoloader --no-dev
```

### Environment

- PHP 8.4+ required
- ext-simplexml extension required
- MySQL database
- Redis for queue and cache (recommended)

## Best Practices

### Code Organization

1. Keep models lean, use presenter traits for display logic
2. Extract business logic into service classes
3. Use form objects for Livewire forms
4. Organize exceptions by domain
5. Use enums for constant values

### Testing

1. Write tests for all features
2. Use factories for test data
3. Test happy paths and validation rules
4. Mock external API calls

### Performance

1. Use queue jobs for slow operations
2. Eager load relationships
3. Paginate large datasets
4. Cache expensive computations

### Security

1. Never commit `.env` file
2. Encrypt sensitive database fields
3. Validate all user input
4. Use CSRF protection on forms

### Documentation

1. Use PHPDoc blocks for models with all properties and methods
2. Add comments for complex business logic
3. Keep README up to date
4. Document API integrations

## Version Control

### Commit Messages

- Use descriptive commit messages
- Follow conventional commits when possible
- Reference issue numbers when applicable

### Branching

- `main` branch for production-ready code
- Feature branches for new features
- Pull requests for code review

## Additional Resources

- Laravel Documentation: https://laravel.com/docs
- Livewire Documentation: https://livewire.laravel.com
- Flux UI Documentation: https://fluxui.dev
- Pest PHP Documentation: https://pestphp.com
- Tailwind CSS Documentation: https://tailwindcss.com
