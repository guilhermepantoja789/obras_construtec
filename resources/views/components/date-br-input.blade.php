@props([
    'name',
    'id' => null,
    'value' => '',
    'label',
    'optional' => false,
    'inputClass' => '',
])

@php
    $id = $id ?? $name;
    $raw = old($name, $value);
    $display = '';

    if ($raw) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $raw)) {
            try {
                $display = \Carbon\Carbon::createFromFormat('Y-m-d', $raw)->format('d/m/Y');
            } catch (\Exception $e) {
                $display = $raw;
            }
        } else {
            $display = $raw;
        }
    }
@endphp

<div>
    <x-input-label :for="$id">
        {{ $label }}
        @if($optional)
            <span class="text-slate-600 font-normal normal-case tracking-normal text-[10px]">(opcional)</span>
        @endif
    </x-input-label>
    <x-text-input
        :id="$id"
        :name="$name"
        type="text"
        inputmode="numeric"
        placeholder="DD/MM/AAAA"
        maxlength="10"
        autocomplete="off"
        class="date-br-field mt-1 block w-full {{ $inputClass }}"
        :value="$display"
    />
    <x-input-error class="mt-2" :messages="$errors->get($name)" />
</div>
