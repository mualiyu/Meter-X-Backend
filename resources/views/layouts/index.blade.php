<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{asset('/img/fav.png')}}" type="image/x-icon">
  <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css">
  <link rel="stylesheet" type="text/css" href="{{asset('/css/style.css')}}">

  {{-- new addes atyles --}}
  <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css"  rel="stylesheet" />


  {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
  <title>{{ config('app.name', 'MeterX') }}</title>
</head>
<body class="bg-gray-100">

@include('layouts.navbar')

<div class="h-screen flex flex-row flex-wrap">

    @include('layouts.sidebar')

    <!-- strat content -->
    <div class="bg-gray-100 flex-1 p-6 md:mt-16">



      @yield('content')

    </div>
    <!-- end content -->

  </div>
  <!-- end wrapper -->




<!-- script -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{asset('js/scripts.js')}}"></script>
<!-- end script -->

{{-- New --}}
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

<script>
document.addEventListener('livewire:load', function () {
    Livewire.on('close', ({ modal }) => {
        // Logic to close the modal
        $dispatch('close', { modal });
    });
});
</script>

</body>
</html>
