<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<body>
  <h1>Hello, {{ $coop->owner->name }}</h1>
  <p>The coop {{ $coop->name }} has been canceled due to {{ $cancellation_reason }}</p>
</body>
</html>
