<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Stream;
use App\Models\ClassForm;
use App\Models\Student;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $student_name;
    #[Validate('required|unique:students,adm_no')]
    public $adm_no;
    #[Validate('required|exists:class_forms,id')]
    public $class;
    public $class_forms;
    public $stream_id;
    public $streams;
    public $student;
    public $term;

    //Initialize the component
    public function mount(): void
    {
        $this->streams = Stream::all();
        $this->class_forms = ClassForm::all();
        $this->class = null;
    }

    //Additional Validation form form mismatch
    public function rules(): array
    {
        return [
            'stream_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $selectedStream = Stream::find($value);
                    if ($selectedStream && substr($selectedStream->name, 5, 1) != $this->class) {
                        $fail('The selected stream does not match the form.');
                    }
                },
            ],
        ];
    }

    public function submit(): void
    {
        //validate nya dah declare kat atas guna cara php terbaru
        $this->validate();

        //Find the class with the selected ID
        $selectedClass = ClassForm::find($this->class); //Form 1, Form 2, etc

        //Set the form value based on the selected class or use a default value
        $formValue = $selectedClass ? intval(substr($selectedClass->name, -1)) : 1;

        //Find the highest current sequence number for the given form
        $maxSequenceNumber = Student::where('form', $formValue)->max('form_sequence_number');

        //If there's no student with the same form, set the sequence number to 1 or otherwise increment the max sequence number
        $formSequenceNumber = $maxSequenceNumber ? $maxSequenceNumber + 1 : 1;

        //Create a new student record
        $student = Student::create([
            'student_name' => $this->student_name,
            'adm_no' => $this->adm_no,
            // 'term' => $this->term,
            'stream_id' => $this->stream_id,
            'form' => $formValue,
            'form_sequence_number' => $formSequenceNumber,
        ]);

        //show success message
        $this->dispatch('success', message: 'Student created successfully');

        //Reset the form fields
        $this->student_name = '';
        $this->adm_no = '';
        $this->class = '';
        $this->stream_id = '';
        // $this->term = '';
    }
}; ?>

<div>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Add Student Detail</h2>

        <form wire:submit="submit" class="mt-4">
            <input type="text" id="name"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('student_name') border-red-500 @enderror"
                wire:model="student_name" placeholder="Enter student name">

            @error('student_name')
                <div class="mt-1 text-red-500">
                    {{ $message }}
                </div>
            @enderror

            <div class="mb-3">
                <input type="text" id="adm_no"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('adm_no') border-red-500 @enderror"
                    wire:model="adm_no" placeholder="Enter admission number">

                @error('adm_no')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('class') border-red-500 @enderror "
                    wire:model="class">
                    <option value="">Select Class</option>
                    @foreach ($class_forms as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>

                @error('class')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('stream_id') border-red-500 @enderror "
                    wire:model="stream_id">
                    <option value="">Select Stream</option>
                    @foreach ($streams as $stream)
                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                    @endforeach
                </select>

                @error('stream_id')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

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
