@php
    $type = null;
    $message = null;

    if (session()->has('error')) {
        $type = 'error';
        $message = session()->pull('error');
    } elseif (session()->has('warning')) {
        $type = 'warning';
        $message = session()->pull('warning');
    } elseif (session()->has('info')) {
        $type = 'info';
        $message = session()->pull('info');
    } elseif (session()->has('success')) {
        $type = 'success';
        $message = session()->pull('success');
    } elseif (session()->has('status')) {
        $type = 'success';
        $message = session()->pull('status');
    }
@endphp

@if ($type && $message)
    <div
        wire:key="swal-flash-{{ md5($type.(string) $message) }}"
        x-data
        x-init="
            $nextTick(() => {
                if (! window.notify) return;
                if (@js($type) === 'error') notify.error(@js($message));
                else if (@js($type) === 'warning') notify.warning(@js($message));
                else if (@js($type) === 'info') notify.info(@js($message));
                else notify.success(@js($message));
            })
        "
        class="hidden"
        aria-hidden="true"
    ></div>
@endif

@if (isset($errors) && $errors->any())
    <div
        wire:key="swal-errors-{{ md5(implode('|', $errors->all())) }}"
        x-data
        x-init="
            $nextTick(() => {
                if (window.notify) notify.validation(@js($errors->all()));
            })
        "
        class="hidden"
        aria-hidden="true"
    ></div>
@endif
