# termii-laravel

A modern, fully-typed Laravel SDK for the [Termii](https://termii.com) messaging platform.  
Supports Laravel 10, 11, and **12** (PHP 8.1+).

[![Latest Version on Packagist](https://img.shields.io/packagist/v/olatmi/termii-laravel.svg?style=flat-square)](https://packagist.org/packages/olatmi/termii-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/olatmi/termii-laravel.svg?style=flat-square)](https://packagist.org/packages/olatmi/termii-laravel)
[![Tests](https://img.shields.io/github/actions/workflow/status/olatmi/termii-laravel/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/olatmi/termii-laravel/actions)
[![License](https://img.shields.io/packagist/l/olatmi/termii-laravel.svg?style=flat-square)](https://packagist.org/packages/olatmi/termii-laravel)

---

## Features

- ✅ **Laravel 10 / 11 / 12** compatible (PHP 8.1+)
- ✅ **Switch API** — SMS, WhatsApp, Bulk, Number messages
- ✅ **Token API** — OTP via SMS, Voice, In-App, and Email
- ✅ **Insight API** — Balance, History, Phone Status & DND Search
- ✅ **Laravel Notification Channel** — Drop-in `via()` support
- ✅ **Typed responses** via `TermiiResponse` DTO
- ✅ **Auto-discovery** via package manifest (no manual registration)
- ✅ Proper exception hierarchy (`TermiiApiException`, `TermiiException`)

---

## Installation

```bash
composer require olatmi/termii-laravel
```

Publish the config file:

```bash
php artisan vendor:publish --tag="termii-config"
```

---

## Configuration

Add to your `.env`:

```env
TERMII_API_KEY=your-api-key-here
TERMII_BASE_URL=https://api.ng.termii.com
TERMII_SENDER_ID=YourApp
TERMII_CHANNEL=generic
```

> Your account-specific Base URL is found on your Termii dashboard under **Settings > API**.

---

## Usage

### Via Facade

```php
use Hassan\Termii\Facades\Termii;

// Send a simple SMS
$response = Termii::sendMessage('2347012345678', 'Hello from YourApp!');

// Send OTP
$response = Termii::sendOtp(
    to: '2347012345678',
    messageText: 'Your verification code is < 1234 >. Valid for 10 minutes.',
);

$pinId = $response->get('pinId');

// Verify OTP
$result = Termii::verifyOtp($pinId, $userEnteredPin);

if ($result->verified()) {
    // ✅ OTP is valid
}
```

### Via Dependency Injection

```php
use Hassan\Termii\Termii;

class AuthController extends Controller
{
    public function __construct(protected Termii $termii) {}

    public function sendCode(Request $request)
    {
        $response = $this->termii->sendOtp(
            to: $request->phone,
            messageText: 'Your OTP: < 1234 >',
            pinLength: 6,
            pinTimeToLive: 10,
        );

        session(['otp_pin_id' => $response->get('pinId')]);

        return response()->json(['message' => 'OTP sent']);
    }

    public function verifyCode(Request $request)
    {
        $result = $this->termii->verifyOtp(
            pinId: session('otp_pin_id'),
            pin: $request->otp,
        );

        return $result->verified()
            ? response()->json(['verified' => true])
            : response()->json(['error' => 'Invalid OTP'], 422);
    }
}
```

---

## API Reference

### Switch Service (Messaging)

```php
// Send SMS / WhatsApp
Termii::sendMessage(to: '2347012345678', sms: 'Your message');
Termii::sendMessage(to: '2347012345678', sms: 'Hi!', from: 'MyBrand', channel: 'dnd');

// Send to multiple numbers
Termii::sendMessage(to: ['2347012345678', '2348012345678'], sms: 'Broadcast message');

// Bulk send (Termii bulk endpoint)
Termii::sendBulk(to: ['2347012345678', '2348012345678'], sms: 'Hello!');

// WhatsApp media message
Termii::sendMediaMessage(
    to: '2347012345678',
    sms: 'Check this out!',
    mediaUrl: 'https://example.com/image.png',
    mediaCaption: 'Our latest promo',
);

// Send using Termii auto-generated number
Termii::sendWithNumber(to: '2347012345678', sms: 'Hello!');

// Sender ID management
Termii::getSenderIds();
Termii::requestSenderId(senderId: 'MyBrand', useCase: 'Transactional', company: 'My Company Ltd');
```

### Token Service (OTP)

```php
// Send OTP via SMS
$response = Termii::sendOtp(
    to: '2347012345678',
    messageText: 'Your code is < 1234 >',
    pinPlaceholder: '< 1234 >',
    pinLength: 6,
    pinAttempts: 3,
    pinTimeToLive: 10,
    messageType: 'NUMERIC', // or 'ALPHANUMERIC'
);

// Verify OTP
Termii::verifyOtp(pinId: $pinId, pin: '123456');

// Voice OTP
Termii::sendVoiceOtp(to: '2347012345678', pinLength: 4);

// Voice call with custom code
Termii::sendVoiceCall(to: '2347012345678', code: 4521);

// In-app OTP (token returned in response — no SMS sent)
$response = Termii::sendInAppOtp(to: '2347012345678', pinLength: 6);
$token = $response->get('data.otp'); // use directly in your app

// Email OTP
Termii::sendEmailOtp(
    emailAddress: 'user@example.com',
    code: '482910',
    emailConfigurationId: 'your-email-config-id',
);
```

### Insight Service (Analytics)

```php
// Account balance
$balance = Termii::balance();
echo $balance->get('balance'); // "₦1,500.00"

// Message history
$history = Termii::history();

// Check if number is fake / ported
$status = Termii::status(phoneNumber: '7012345678', countryCode: 'NG');

// Verify number & detect DND status
$search = Termii::search(phoneNumber: '2347012345678');
```

### Accessing Individual Services

```php
// Use the sub-services directly for grouped access
Termii::switch()->sendMessage(...);
Termii::token()->sendOtp(...);
Termii::insight()->balance();
```

---

## TermiiResponse

All methods return a `TermiiResponse` object:

```php
$response = Termii::sendMessage('2347012345678', 'Hello');

$response->successful();      // bool
$response->failed();          // bool
$response->status();          // int HTTP status code
$response->toArray();         // array
$response->collect();         // Illuminate\Support\Collection
$response->get('pinId');      // mixed — supports dot notation
$response->messageId();       // shortcut for message_id / pinId
$response->verified();        // bool — shortcut for OTP verification
$response->balance;           // magic property access
```

---

## Laravel Notification Channel

```php
use Illuminate\Notifications\Notification;
use Hassan\Termii\TermiiChannel;
use Hassan\Termii\TermiiMessage;

class WelcomeNotification extends Notification
{
    public function via($notifiable): array
    {
        return [TermiiChannel::class];
    }

    public function toTermii($notifiable): TermiiMessage
    {
        return TermiiMessage::create("Welcome to our platform! Your account is now active.")
            ->from('MyApp')
            ->channel('generic');
    }
}
```

Add to your `User` model (or any `Notifiable`):

```php
public function routeNotificationForTermii(): string
{
    return $this->phone_number;
}
```

---

## Exception Handling

```php
use Hassan\Termii\Exceptions\TermiiApiException;
use Hassan\Termii\Exceptions\TermiiException;

try {
    $response = Termii::sendOtp('2347012345678', 'Your OTP: < 1234 >');
} catch (TermiiApiException $e) {
    // HTTP-level error (4xx, 5xx)
    logger()->error('Termii API error', [
        'message'     => $e->getMessage(),
        'status_code' => $e->getStatusCode(),
        'context'     => $e->getContext(),
    ]);
} catch (TermiiException $e) {
    // Connection / configuration errors
    logger()->error('Termii connection error', ['message' => $e->getMessage()]);
}
```

---

## Channels

| Value        | Description                              |
|--------------|------------------------------------------|
| `generic`    | Default. Routes to DND & non-DND numbers |
| `dnd`        | Forces delivery to DND-enabled numbers   |
| `whatsapp`   | Sends via WhatsApp                       |

---

## Testing

```bash
composer test
```

---

## Credits

Built on the [Termii API](https://developers.termii.com). Inspired by the original [lara-termii](https://github.com/zeevx/lara-termii) package, rebuilt from scratch for Laravel 12 compatibility, modern PHP 8.1+ features, and a cleaner architecture.

## License

MIT — see [LICENSE.md](LICENSE.md)
