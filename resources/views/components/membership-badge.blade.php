@php
// Usage: @include('components.membership-badge', ['type' => $member->membership_type])
// $type can be numeric (1,7,30,365) or string, and optional $label may be provided.
$typeVal = isset($type) ? $type : null;
$label = isset($label) ? $label : (\App\Models\User::MEMBERSHIP_TYPES[$typeVal] ?? 'Session');

// Map to classes — preserve existing color scheme
$classes = 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ';
switch ($label) {
    case 'Annual':
        $classes .= 'bg-purple-900 text-purple-200';
        break;
    case 'Weekly':
        $classes .= 'bg-green-900 text-green-200';
        break;
    case 'Monthly':
        $classes .= 'bg-blue-900 text-blue-200';
        break;
    case 'Session':
        $classes .= 'bg-yellow-900 text-yellow-200';
        break;
    default:
        $classes .= 'bg-gray-800 text-gray-200';
}
@endphp

<span class="{{ $classes }}" aria-label="Membership type: {{ $label }}">{{ $label }}</span>
