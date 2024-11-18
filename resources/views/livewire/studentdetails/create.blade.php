<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\StudentDetail;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $primary_school;
    #[Validate('required|integer')]
    public $kcpe_year;
    #[Validate('required|integer')]
    public $kcpe_marks;
    #[Validate('required|integer')]
    public $kcpe_position;
    public $student_id;
    //yg ini utk deaclare relation dari model tu
    public $students;

    //Initialize the component
    public function mount(): void
    {
        //Get all students
        $this->students = Student::with('stream', 'classForm')->get();
        // dd($this->students);
    }

    public function submit()
    {
        //Validate the rules
        $this->validate();

        //Find the student with the selected student ID
        $student = Student::find($this->student_id);

        //Update or create the student details
        StudentDetail::updateOrCreate(
            [
                'student_id' => $this->student_id,
            ],
            [
                'primary_school' => $this->primary_school,
                'kcpe_year' => $this->kcpe_year,
                'kcpe_marks' => $this->kcpe_marks,
                'kcpe_position' => $this->kcpe_position,
            ],
        );

        //Show success message
        $this->dispatch('success', message: 'Student details added successfully');

        //Reset the form fields
        $this->reset(['primary_school', 'kcpe_year', 'kcpe_marks', 'kcpe_position']);
    }

    //return all student
    public function with(): array
    {
        return [
            'students' => $this->students,
        ];
    }
}; ?>

<div>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Add Student Primary School Details</h2>

        <form wire:submit="submit" class="mt-4">
            <div class="mb-3">
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('student_id') border-red-500 @enderror "
                    wire:model="student_id">
                    <option value="">Select Student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }} - {{ $student->adm_no }} - ({{ $student->classForm->name ?? 'N/A' }} -
                            {{ $student->stream->name ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>

                @error('student_id')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="primary_school"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('primary_school') border-red-500 @enderror"
                    wire:model="primary_school" placeholder="Enter primary school">

                @error('primary_school')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="kcpe_year"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('kcpe_year') border-red-500 @enderror"
                    wire:model="kcpe_year" placeholder="Enter kcpe year">

                @error('kcpe_year')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="kcpe_marks"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('kcpe_marks') border-red-500 @enderror"
                    wire:model="kcpe_marks" placeholder="Enter kcpe marks">

                @error('kcpe_marks')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="kcpe_position"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('kcpe_position') border-red-500 @enderror"
                    wire:model="kcpe_position" placeholder="Enter kcpe position">

                @error('kcpe_position')
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
