<?php

use function Livewire\Volt\{state};

use App\Models\ApiService;

state(['apiServices' => []]);

state([
    'name' => fn () => '',
    'description' => fn () => '',
    'website' => fn () => '',
    'api_endpoint' => fn () => '',
    'api_key' => fn () => '',
    'api_secret_key' => fn () => '',
    'api_public_key' => fn () => '',
]);

$loadApiServices = function ()
{
    $apiServices = ApiService::all();
    state(['apiServices' => $apiServices]);
};

$addApiService = function ()
{
    $validatedData = $this->validate([
        'name' => 'required',
        'description' => 'nullable',
        'website' => 'required',
        'api_endpoint' => 'required',
        'api_key' => 'nullable',
        'api_secret_key' => 'nullable',
        'api_public_key' => 'nullable',
    ]);

    ApiService::create($validatedData);

    $loadApiServices();

    state(['name' => '', 'description' => '', 'website' => '', 'api_endpoint' => '', 'api_key' => '', 'api_secret_key' => '', 'api_public_key' => '']);
};

$deleteApiService = function ($id)
{
    ApiService::find($id)->delete();
    loadApiServices();
};

?>

<div>
    <div class="mb-4">
        <button wire:click="loadApiServices"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Load API Services
        </button>
    </div>
    <div class="mb-4">
        <button wire:click="addApiService"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Add New API Service
        </button>
    </div>
    <div class="mb-4">
        <div class="modal" id="addApiServiceModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New API Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit="addApiService">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" wire:model="name">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" wire:model="description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="website" wire:model="website">
                            </div>
                            <div class="mb-3">
                                <label for="api_endpoint" class="form-label">API Endpoint</label>
                                <input type="text" class="form-control" id="api_endpoint" wire:model="api_endpoint">
                            </div>
                            <div class="mb-3">
                                <label for="api_key" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="api_key" wire:model="api_key">
                            </div>
                            <div class="mb-3">
                                <label for="api_secret_key" class="form-label">API Secret Key</label>
                                <input type="text" class="form-control" id="api_secret_key" wire:model="api_secret_key">
                            </div>
                            <div class="mb-3">
                                <label for="api_public_key" class="form-label">API Public Key</label>
                                <input type="text" class="form-control" id="api_public_key" wire:model="api_public_key">
                            </div>
                            <button type="submit" class="btn btn-primary">Add API Service</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="list-group">
        @foreach($apiServices as $apiService)
        <div class="list-group-item">
            <div class="d-flex justify-content-between">
                <h5 class="mb-1">{{ $apiService->name }}</h5>
                <button wire:click="deleteApiService({{ $apiService->id }})" class="btn btn-danger">Delete</button>
            </div>
            <p class="mb-1">{{ $apiService->description }}</p>
            <small>{{ $apiService->website }}</small>
        </div>
        @endforeach
    </div>
</div>
