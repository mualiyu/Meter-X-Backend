@extends('layouts.index')

@section('content')

<livewire:dashboard.generalreport>

<livewire:dashboard.sales-overview>

<!-- best seller & traffic -->
<div class="grid grid-cols-3 gap-6 mt-6 xl:grid-cols-1">
    {{-- ##grid grid-cols-3 gap-6 mt-6 xl:grid-cols-1 --}}
    <livewire:dashboard.new-customers>
    <livewire:dashboard.recent-orders>
</div>

@endsection
