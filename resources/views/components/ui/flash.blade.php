@props(['showErrors' => true])
@if (session('ok'))
  <x-ui.alert type="success" class="mb-4">{{ session('ok') }}</x-ui.alert>
@endif
@if (session('error'))
  <x-ui.alert type="danger" class="mb-4">{{ session('error') }}</x-ui.alert>
@endif
@if ($showErrors && $errors->any())
  <x-ui.alert type="danger" class="mb-4">
    @foreach ($errors->all() as $e)
      <div>• {{ $e }}</div>
    @endforeach
  </x-ui.alert>
@endif

