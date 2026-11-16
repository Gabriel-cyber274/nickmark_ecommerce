@component('mail::message')
# Admin Action Notification

A non-super admin performed an action on **{{ config('app.name') }}**.

- **Actor:** {{ $actorName }} ({{ $actorEmail }})
- **Action:** {{ ucfirst($action) }}
- **Model:** {{ $model }}
- **Record ID:** {{ $recordId }}
@if ($recordTitle)
- **Record:** {{ $recordTitle }}
@endif

@if (count($changes) > 0)
### Changes

@foreach ($changes as $field => $data)
- **{{ $field }}**: {{ $data['old'] ?? '-' }} &rarr; {{ $data['new'] ?? '-' }}
@endforeach
@endif

---
{{ config('app.name') }}
@endcomponent
