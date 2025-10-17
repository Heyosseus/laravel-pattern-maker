# Laravel Pattern Maker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/heyosseus/laravel-pattern-maker.svg?style=flat-square)](https://packagist.org/packages/heyosseus/laravel-pattern-maker)
[![Total Downloads](https://img.shields.io/packagist/dt/heyosseus/laravel-pattern-maker.svg?style=flat-square)](https://packagist.org/packages/heyosseus/laravel-pattern-maker)
[![License](https://img.shields.io/packagist/l/heyosseus/laravel-pattern-maker.svg?style=flat-square)](https://packagist.org/packages/heyosseus/laravel-pattern-maker)

A Laravel package for quickly generating design pattern boilerplate code. Stop writing repetitive pattern structures and focus on implementing your business logic!

## ✨ Features

- **5 Design Patterns Supported**: Adapter, Strategy, Decorator, Factory, Observer
- **Artisan Commands**: Simple, intuitive commands for each pattern
- **Smart Auto-Detection**: Automatically detects Models, Services, Repositories
- **Customizable Namespaces**: Use custom namespaces for generated files
- **Separate Files**: Interface and implementation files generated separately
- **Laravel Integration**: Full Laravel service provider integration

## 🚀 Installation

Install the package via Composer:

```bash
composer require heyosseus/laravel-pattern-maker
```

The package will automatically register its service provider thanks to Laravel's auto-discovery.

## 📋 Available Commands

| Pattern   | Command             | Description                                   |
| --------- | ------------------- | --------------------------------------------- |
| Adapter   | `pattern:adapter`   | Wrap external classes with unified interface  |
| Strategy  | `pattern:strategy`  | Switch between algorithms at runtime          |
| Decorator | `pattern:decorator` | Add responsibilities to objects dynamically   |
| Factory   | `pattern:factory`   | Create objects without specifying exact class |
| Observer  | `pattern:observer`  | Notify multiple objects about state changes   |

## 🛠️ Usage Guide

### 1. Adapter Pattern

**Purpose**: Allows incompatible interfaces to work together. Perfect for integrating third-party libraries or services.

#### Basic Usage

```bash
php artisan pattern:adapter {AdapterName} {AdapteeClass}
```

#### Examples

**Adapt a Service:**

```bash
php artisan pattern:adapter PaymentAdapter StripeService
```

**Result**: Imports `App\Services\StripeService`

**Adapt a Model:**

```bash
php artisan pattern:adapter UserAdapter User
```

**Result**: Imports `App\Models\User`

**Adapt External Library:**

```bash
php artisan pattern:adapter GuzzleAdapter GuzzleHttp\\Client
```

**Result**: Imports `GuzzleHttp\Client`

#### Generated Files

- `app/Patterns/Adapter/PaymentAdapterInterface.php`
- `app/Patterns/Adapter/PaymentAdapter.php`

#### Custom Namespace

```bash
php artisan pattern:adapter PaymentAdapter StripeService --namespace=App\\Infrastructure\\Adapters
```

#### Usage Example

```php
use App\Patterns\Adapter\PaymentAdapterInterface;

class OrderService
{
    public function __construct(private PaymentAdapterInterface $paymentAdapter) {}

    public function processPayment($amount)
    {
        return $this->paymentAdapter->handle($amount);
    }
}
```

---

### 2. Strategy Pattern

**Purpose**: Define a family of algorithms, encapsulate each one, and make them interchangeable at runtime.

#### Basic Usage

```bash
php artisan pattern:strategy {ContextName} {Strategy1?} {Strategy2?} ...
```

#### Examples

**Payment Strategies:**

```bash
php artisan pattern:strategy Payment CreditCardStrategy PayPalStrategy CryptoStrategy
```

**Notification Strategies:**

```bash
php artisan pattern:strategy Notification EmailStrategy SmsStrategy PushStrategy
```

**Create Context Only:**

```bash
php artisan pattern:strategy Shipping
```

Then add strategies later:

```bash
php artisan pattern:strategy Shipping StandardShipping ExpressShipping
```

#### Generated Files

- `app/Patterns/Strategy/PaymentStrategyInterface.php`
- `app/Patterns/Strategy/PaymentContext.php`
- `app/Patterns/Strategy/CreditCardStrategy.php`
- `app/Patterns/Strategy/PayPalStrategy.php`
- `app/Patterns/Strategy/CryptoStrategy.php`

#### Usage Example

```php
use App\Patterns\Strategy\PaymentContext;
use App\Patterns\Strategy\CreditCardStrategy;
use App\Patterns\Strategy\PayPalStrategy;

class PaymentService
{
    public function processPayment($type, $amount)
    {
        $strategy = match($type) {
            'credit_card' => new CreditCardStrategy(),
            'paypal' => new PayPalStrategy(),
            default => throw new \Exception('Invalid payment type')
        };

        $context = new PaymentContext($strategy);
        return $context->executeStrategy($amount);
    }
}
```

---

### 3. Decorator Pattern

**Purpose**: Attach additional responsibilities to objects dynamically without altering their structure.

#### Basic Usage

```bash
php artisan pattern:decorator {BaseName} {Decorator1?} {Decorator2?} ...
```

#### Examples

**Notification Decorators:**

```bash
php artisan pattern:decorator Notification LoggingDecorator CachingDecorator EncryptionDecorator
```

**Data Processing Decorators:**

```bash
php artisan pattern:decorator DataProcessor ValidationDecorator CompressionDecorator
```

#### Generated Files

- `app/Patterns/Decorator/NotificationComponentInterface.php`
- `app/Patterns/Decorator/NotificationComponent.php`
- `app/Patterns/Decorator/LoggingDecorator.php`
- `app/Patterns/Decorator/CachingDecorator.php`
- `app/Patterns/Decorator/EncryptionDecorator.php`

#### Usage Example

```php
use App\Patterns\Decorator\NotificationComponent;
use App\Patterns\Decorator\LoggingDecorator;
use App\Patterns\Decorator\CachingDecorator;

// Base notification
$notification = new NotificationComponent();

// Add logging
$notification = new LoggingDecorator($notification);

// Add caching
$notification = new CachingDecorator($notification);

// Execute with all decorators
$result = $notification->operation($data);
```

---

### 4. Factory Pattern

**Purpose**: Create objects without specifying the exact class to create. Useful for creating families of related objects.

#### Basic Usage

```bash
php artisan pattern:factory {FactoryName} {Product1?} {Product2?} ...
```

#### Examples

**Vehicle Factory:**

```bash
php artisan pattern:factory Vehicle Car Bike Truck
```

**Database Connection Factory:**

```bash
php artisan pattern:factory Database MySQL PostgreSQL SQLite
```

**Report Factory:**

```bash
php artisan pattern:factory Report PDFReport ExcelReport CSVReport
```

#### Generated Files

- `app/Patterns/Factory/VehicleFactoryInterface.php`
- `app/Patterns/Factory/VehicleFactory.php`
- `app/Patterns/Factory/Car.php`
- `app/Patterns/Factory/Bike.php`
- `app/Patterns/Factory/Truck.php`

#### Usage Example

```php
use App\Patterns\Factory\VehicleFactory;

class TransportService
{
    public function __construct(private VehicleFactory $vehicleFactory) {}

    public function createVehicle($type)
    {
        return $this->vehicleFactory->create($type);
    }
}

// Usage
$factory = new VehicleFactory();
$car = $factory->create('Car');
$bike = $factory->create('Bike');
```

---

### 5. Observer Pattern

**Purpose**: Define a one-to-many dependency between objects so that when one object changes state, all dependents are notified.

#### Basic Usage

```bash
php artisan pattern:observer {SubjectName} {Observer1?} {Observer2?} ...
```

#### Examples

**Order Events:**

```bash
php artisan pattern:observer Order EmailObserver LogObserver InventoryObserver
```

**User Registration:**

```bash
php artisan pattern:observer User WelcomeEmailObserver ProfileSetupObserver AnalyticsObserver
```

**File Upload:**

```bash
php artisan pattern:observer FileUpload VirusScanObserver ThumbnailObserver
```

#### Generated Files

- `app/Patterns/Observer/OrderObserverInterface.php`
- `app/Patterns/Observer/OrderSubject.php`
- `app/Patterns/Observer/EmailObserver.php`
- `app/Patterns/Observer/LogObserver.php`
- `app/Patterns/Observer/InventoryObserver.php`

#### Usage Example

```php
use App\Patterns\Observer\OrderSubject;
use App\Patterns\Observer\EmailObserver;
use App\Patterns\Observer\LogObserver;
use App\Patterns\Observer\InventoryObserver;

class OrderService
{
    private OrderSubject $subject;

    public function __construct()
    {
        $this->subject = new OrderSubject();

        // Attach observers
        $this->subject->attach(new EmailObserver());
        $this->subject->attach(new LogObserver());
        $this->subject->attach(new InventoryObserver());
    }

    public function createOrder($orderData)
    {
        // Create order logic here
        $order = Order::create($orderData);

        // Notify all observers
        $this->subject->notify(['order' => $order]);

        return $order;
    }
}
```

## 🎯 Real-World Use Cases

### Payment Gateway Integration

```bash
# Create adapters for different payment providers
php artisan pattern:adapter StripeAdapter Stripe\\StripeClient
php artisan pattern:adapter PayPalAdapter PayPalCheckoutSdk\\Core\\PayPalHttpClient
php artisan pattern:adapter SquareAdapter SquareConnect\\Client

# Create strategy for payment processing
php artisan pattern:strategy Payment StripeStrategy PayPalStrategy SquareStrategy
```

### Notification System

```bash
# Create observer for user events
php artisan pattern:observer User EmailObserver SmsObserver PushObserver SlackObserver

# Create decorator for notification enhancement
php artisan pattern:decorator Notification LoggingDecorator RetryDecorator RateLimitDecorator
```

### File Storage System

```bash
# Create adapters for different storage providers
php artisan pattern:adapter S3Adapter Aws\\S3\\S3Client
php artisan pattern:adapter GoogleAdapter Google\\Cloud\\Storage\\StorageClient

# Create factory for file processors
php artisan pattern:factory FileProcessor ImageProcessor VideoProcessor DocumentProcessor
```

### Logging System

```bash
# Create strategy for different log formats
php artisan pattern:strategy Logger JsonLogger XMLLogger PlainTextLogger

# Create decorator for log enhancement
php artisan pattern:decorator Logger TimestampDecorator ContextDecorator EncryptionDecorator
```

## 🔧 Advanced Features

### Custom Namespaces

All commands support custom namespaces:

```bash
php artisan pattern:adapter PaymentAdapter StripeService --namespace=App\\Infrastructure\\Adapters
php artisan pattern:strategy Payment CreditCard --namespace=App\\Domain\\Strategies
php artisan pattern:decorator Notification Logging --namespace=App\\Services\\Decorators
php artisan pattern:factory Vehicle Car --namespace=App\\Factories
php artisan pattern:observer User Email --namespace=App\\Events\\Observers
```

### Smart Class Detection

The package automatically detects and imports classes based on naming conventions:

- **Services**: `PaymentService` → `App\Services\PaymentService`
- **Repositories**: `UserRepository` → `App\Repositories\UserRepository`
- **Models**: `User` → `App\Models\User`
- **Full Paths**: `Vendor\Package\Class` → Uses as-is

### Generated File Structure

```
app/
└── Patterns/
    ├── Adapter/
    │   ├── PaymentAdapterInterface.php
    │   └── PaymentAdapter.php
    ├── Strategy/
    │   ├── PaymentStrategyInterface.php
    │   ├── PaymentContext.php
    │   └── CreditCardStrategy.php
    ├── Decorator/
    │   ├── NotificationComponentInterface.php
    │   ├── NotificationComponent.php
    │   └── LoggingDecorator.php
    ├── Factory/
    │   ├── VehicleFactoryInterface.php
    │   ├── VehicleFactory.php
    │   └── Car.php
    └── Observer/
        ├── OrderObserverInterface.php
        ├── OrderSubject.php
        └── EmailObserver.php
```

## 🧪 Testing

After generating patterns, you should write tests for your implementations:

```php
// tests/Unit/Patterns/Adapter/PaymentAdapterTest.php
class PaymentAdapterTest extends TestCase
{
    public function test_payment_adapter_processes_payment()
    {
        $mockService = Mockery::mock(StripeService::class);
        $adapter = new PaymentAdapter($mockService);

        $mockService->shouldReceive('handle')
            ->with(100)
            ->once()
            ->andReturn(['status' => 'success']);

        $result = $adapter->handle(100);

        $this->assertEquals(['status' => 'success'], $result);
    }
}
```

## 📚 Learning Resources

### Design Patterns Explained

- **Adapter**: [Adapter Pattern](https://refactoring.guru/design-patterns/adapter)
- **Strategy**: [Strategy Pattern](https://refactoring.guru/design-patterns/strategy)
- **Decorator**: [Decorator Pattern](https://refactoring.guru/design-patterns/decorator)
- **Factory**: [Factory Pattern](https://refactoring.guru/design-patterns/factory-method)
- **Observer**: [Observer Pattern](https://refactoring.guru/design-patterns/observer)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Ideas for New Patterns

- Builder Pattern
- Command Pattern
- Repository Pattern
- Singleton Pattern
- Proxy Pattern
- Chain of Responsibility
- Template Method

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`

## 📄 Requirements

- PHP ^8.0|^8.1|^8.2|^8.3
- Laravel ^9.0|^10.0|^11.0|^12.0

## 🏷️ Versioning

We use [SemVer](https://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/Heyosseus/laravel-pattern-maker/tags).

## 📝 Changelog

### v1.0.0 (2025-10-17)

- Initial release
- Added Adapter Pattern support
- Added Strategy Pattern support
- Added Decorator Pattern support
- Added Factory Pattern support
- Added Observer Pattern support
- Smart class detection for Models, Services, Repositories
- Separate interface and implementation files
- Custom namespace support

## 👨‍💻 Credits

- **Author**: [Rati Rukhadze](https://github.com/Heyosseus)
- **Contributors**: See [contributors list](https://github.com/Heyosseus/laravel-pattern-maker/contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 🌟 Support

If you find this package helpful, please consider:

- ⭐ Starring the repository
- 🐛 [Reporting bugs](https://github.com/Heyosseus/laravel-pattern-maker/issues)
- 💡 [Suggesting new features](https://github.com/Heyosseus/laravel-pattern-maker/issues)
- 📖 Improving documentation

## 🚀 What's Next?

- More design patterns
- IDE integration and snippets
- Pattern validation and suggestions
- Performance optimizations
- Enhanced documentation and examples

---

**Happy coding with design patterns!** 🎉
