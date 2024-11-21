<?php

use function Livewire\Volt\{state};

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<div>
    <span wire:click="logout" class="w-full text-start">
            {{ __('Log Out') }}
    </span>
</div>
