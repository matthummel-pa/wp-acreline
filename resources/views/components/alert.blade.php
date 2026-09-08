@props([
  'type' => null,
  'message' => null,
])

@php($class = match ($type) {
  'success' => 'text-emerald-50 bg-emerald-700',
  'caution' => 'text-amber-50 bg-amber-700',
  'warning' => 'text-red-50 bg-red-700',
  default => 'text-white bg-[color:var(--accent)]',
})

<div {{ $attributes->merge(['class' => "px-2 py-1 {$class}"]) }}>
  {!! $message ?? $slot !!}
</div>
