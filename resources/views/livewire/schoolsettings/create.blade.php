<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Models\SchoolSettings;

new class extends Component {
    use WithFileUploads;

    #[Validate('image|max:2048')]
    public $logo;
    #[Validate('required|string')]
    public $school_name;
    #[Validate('required|integer')]
    public $current_year;
    #[Validate('required|string')]
    public $term;
    #[Validate('required|date')]
    public $term_start_date;
    #[Validate('required|date')]
    public $term_end_date;
    #[Validate('required|date')]
    public $next_term_start_date;
    #[Validate('required|date')]
    public $next_term_end_date;
    #[Validate('required|string')]
    public $school_motto;
    #[Validate('required|string')]
    public $school_vision;
    public $settings;

    //Initialize the component
    public function mount(): void
    {
        //Get the school settings or create new one if it doesn't exist
        $this->settings = SchoolSettings::first() ?? new SchoolSettings();

        //can also be written as
        // if(SchoolSettings::first()) {
        //     $this->settings = SchoolSettings::first();
        // }else{
        //     $this->settings = new SchoolSettings();
        // }
    }

    public function saveSettings(): void
    {
        //Validate the form
        $this->validate();

        if ($this->logo) {
            //save the logo
            $logoPath = $this->logo->store('logos', 'public');
            $this->settings->logo_url = $logoPath;
        } else {
            // If there is no new logo (null), use the existing one
            $existingSettings = SchoolSettings::first();
            if ($existingSettings) {
                $this->settings->logo_url = $existingSettings->logo_url;
            }
        }

        //Set other required fields
        $this->settings->school_name = $this->school_name;
        $this->settings->current_year = $this->current_year;
        $this->settings->term = $this->term;
        $this->settings->term_start_date = $this->term_start_date;
        $this->settings->term_end_date = $this->term_end_date;
        $this->settings->next_term_start_date = $this->next_term_start_date;
        $this->settings->next_term_end_date = $this->next_term_end_date;
        $this->settings->school_motto = $this->school_motto;
        $this->settings->school_vision = $this->school_vision;

        //Save or update the settings
        if ($this->settings) {
            $this->settings->save();
        }

        //Show success message
        $this->dispatch('success', message: 'School settings saved successfully');
    }
    //Redirect the view
    public function with(): array
    {
        return [
            'settings' => $this->settings,
        ];
    }
}; ?>

<div>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">

        <form wire:submit="saveSettings" class="mt-4">
            {{-- Flash success message --}}
            <div x-data="{ open: false, message: '' }" x-cloak
                @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 5000)"
                x-show="open" class="px-4 py-2 mt-4 font-bold text-white bg-green-500 rounded">
                <span x-text="message"></span>
            </div>

            <input type="file" id="logo"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('logo') border-red-500 @enderror"
                wire:model="logo" placeholder="Logo">

            @error('logo')
                <div class="mt-1 text-red-500">
                    {{ $message }}
                </div>
            @enderror

            <div class="mb-3">
                <input type="text" id="school_name"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('school_name') border-red-500 @enderror"
                    wire:model="school_name" placeholder="Enter school name">

                @error('school_name')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="current_year"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('current_year') border-red-500 @enderror"
                    wire:model="current_year" placeholder="Enter current year">

                @error('current_year')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="date" id="term_start_date"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('term_start_date') border-red-500 @enderror"
                    wire:model="term_start_date" placeholder="Enter term start date">

                @error('term_start_date')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="date" id="term_end_date"
                    class="date w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('term_end_date') border-red-500 @enderror"
                    wire:model="term_end_date" placeholder="Enter term start date">

                @error('term_end_date')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="date" id="next_term_start_date"
                    class="date w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('next_term_start_date') border-red-500 @enderror"
                    wire:model="next_term_start_date" placeholder="Enter next term start date">

                @error('next_term_start_date')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="date" id="next_term_end_date"
                    class="date w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('next_term_end_date') border-red-500 @enderror"
                    wire:model="next_term_end_date" placeholder="Enter next term end date">

                @error('next_term_end_date')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="term"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('term') border-red-500 @enderror"
                    wire:model="term" placeholder="Enter term">

                @error('term')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="school_motto"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('school_motto') border-red-500 @enderror"
                    wire:model="school_motto" placeholder="Enter school motto">

                @error('school_motto')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="school_vision"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('school_vision') border-red-500 @enderror"
                    wire:model="school_vision" placeholder="Enter school vision">

                @error('school_vision')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="px-4 py-2 mt-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                Submit
            </button>
        </form>


        {{-- Flash error message --}}
        @if (session('error'))
            <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 5000)" x-show="open"
                class="px-4 py-2 mt-4 font-bold text-white bg-red-500 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>
