<?php

use Livewire\Volt\Component;
use App\Models\ClassForm;
use Livewire\Attributes\Validate;

new class extends Component {
    public $name = '';

    public function submit(): void
    {
        // Validate the class name format first
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Custom validation function
                function ($attribute, $value, $fail) {
                    // Check format
                    if (!preg_match('/^Form\s[1-4]$/i', $value)) {
                        $fail('The ' . $attribute . ' must be in the format "Form 1", "Form 2", "Form 3", or "Form 4".');
                    }
                },
            ],
        ]);

        // Convert input name to lowercase for a case-insensitive comparison
        $nameLowercase = strtolower($this->name);

        // Check if the class name already exists in the database
        $classExists = ClassForm::whereRaw('LOWER(name) = ?', [$nameLowercase])->exists();

        if ($classExists) {
            session()->flash('error', 'Class name already exists in the database.');
            return; // Stop the execution
        }

        // Save the class name to the database
        ClassForm::create([
            'name' => $this->name,
        ]);

        // Show a success message
        //message ni untuk pass ke view alpinejs
        $this->dispatch('success', message: 'Class created successfully.');

        // Reset the form
        $this->name = '';
    }
};
?>

<div>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Add Class</h2>

        <form wire:submit.prevent="submit" class="mt-4">
            <input type="text" id="name"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                wire:model="name" placeholder="Enter class name">

            @error('name')
                <div class="mt-1 text-red-500">
                    {{ $message }}
                </div>
            @enderror

            <button type="submit" class="px-4 py-2 mt-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                Submit
            </button>
        </form>

        {{-- Flash success message --}}
        <div x-data="{ open: false, message: '' }" x-cloak
            @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 5000)"
            x-show="open" class="px-4 py-2 mt-4 font-bold text-white bg-green-500 rounded">
            <span x-text="message"></span>
        </div>

        {{-- Flash error message --}}
        @if (session('error'))
            <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 5000)" x-show="open"
                class="px-4 py-2 mt-4 font-bold text-white bg-red-500 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>
