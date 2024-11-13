<?php

use Livewire\Volt\Component;
use App\Models\Subject;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $name = '';

    public function submit(): void
    {
        //Convert input name to lowercase for a case-insensitive comparison
        $nameLowercase = strtolower($this->name);

        // Check if the subject name already exists in the database
        $subjectExists = Subject::whereRaw('LOWER(name) = ?', [$nameLowercase])->exists();

        if ($subjectExists) {
            session()->flash('error', 'Subject name already exists in the database.');
        } else {
            //Perform validation
            $validatedData = $this->validate();

            //Since "name" validation ovverrides the default "required" validation, we need to manually check if "name" is empty
            $validatedData['name'] = $this->name;

            //Create the subject to DB
            Subject::create($validatedData);

            //Flash success message(cara livewire)
            $this->dispatch('success', message: 'Subject created successfully.');

            //Reset input field
            $this->name = '';
        }
    }
}; ?>

<div>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Add Subject</h2>

        <form wire:submit.prevent="submit" class="mt-4">
            <input type="text" id="name"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                wire:model="name" placeholder="Enter subject name">

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
