<?php

use Livewire\Volt\Component;
use App\Models\Stream;
use App\Models\ClassForm;
use Livewire\Attributes\Validate;

new class extends Component {
    public $name = '';
    public $class_id = '';

    public function submit(): void
    {
        //custom validation ikut format
        $validatedData = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:streams,name',
                //custom guna regular expression (perfect utk custom apa yang nak validate)
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^Form\s\d+[a-zA-Z\s]*$/', $value)) {
                        $fail('The ' . $attribute . ' must start with "Form" followed by a space, a number, and optionally letters or spaces.');
                    }
                },
            ],
            //utk validate relationnya utk stream masuk dalam DB bila save/create
            'class_id' => ['required', 'exists:class_forms,id'],
        ]);

        //Create the stream to DB
        Stream::create($validatedData);

        //show success message
        $this->dispatch('success', message: 'Stream created successfully');

        //reset form
        $this->name = '';
        $this->class_id = '';
    }

    //load class forms dari relation
    public function with(): array
    {
        $class_forms = ClassForm::all();
        return [
            'class_forms' => $class_forms,
        ];
    }
}; ?>

<div>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Add Stream</h2>

        <form wire:submit="submit" class="mt-4">
            <input type="text" id="name"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                wire:model="name" placeholder="Enter stream name">

            @error('name')
                <div class="mt-1 text-red-500">
                    {{ $message }}
                </div>
            @enderror

            <div class="mt-4 form-group">
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('class_id') border-red-500 @enderror "
                    wire:model="class_id">
                    <option value="">Select Class</option>
                    @foreach ($class_forms as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            @error('class_id')
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
