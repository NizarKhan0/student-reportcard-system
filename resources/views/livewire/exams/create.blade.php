<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Exam;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|exists:students,adm_no')]
    public $adm_no;
    #[Validate('required|exists:subjects,id')]
    public $subject_id;
    #[Validate('required|numeric')]
    public $exam1;
    #[Validate('required|numeric')]
    public $exam2;
    #[Validate('required|numeric')]
    public $exam3;
    #[Validate('required|string|max:255')]
    public $teacher;
    public $students;
    public $subjects;
    public $studentDetails;

    //Initialize the component
    public function mount(): void
    {
        // Get all subjects and ensure it's an array or empty collection
        $this->subjects = Subject::all();
        // dd($this->subjects);

        // Get the first subject
        $this->subject_id = $this->subjects->first() ? $this->subjects->first()->id : null;
        // dd($this->subject_id);

        // Fetch all students with form and stream, defaulting to an empty collection if null
        $this->students = Student::with('stream', 'classForm')->get();
        // dd($this->students);
    }

    //Update the student details
    public function updateAdmNo($adm_no): void
    {
        //Fetch the student details including form and stream
        // $this->studentDetails = Student::where('adm_no', $adm_no)->with('stream', 'classForm')->first();
        $this->studentDetails = Student::where('adm_no', $adm_no)->with('stream', 'class')->first();
        // dd($this->studentDetails);
    }

    public function submit(): void
    {
        //Validate the inputs data
        $this->validate();

        //Check if the student with the given admission number exists in the database
        $student = Student::where('adm_no', $this->adm_no)->first();
        //Check if the subject already  exists for the student
        $existingExam = Exam::where('student_id', $student->id)
            ->where('subject_id', $this->subject_id)
            ->exists();

        if ($existingExam) {
            //Show error message (default laravel punya flash message)
            Session::flash('error' , 'Subject already exists for the student.');
            return;
        }
        //Create a new exam record
        $exam = Exam::create([
            'student_id' => $student->id,
            'subject_id' => $this->subject_id,
            'exam1' => $this->exam1,
            'exam2' => $this->exam2,
            'exam3' => $this->exam3,
            'teacher' => $this->teacher,
        ]);

        //Show success message
        $this->dispatch('success', message: 'Exam created successfully');

        //Reset the form fields
        $this->reset(['subject_id', 'exam1', 'exam2', 'exam3', 'teacher', 'studentDetails']);
    }
}; ?>

<div>
    {{-- {{ dd($this->studentDetails) }} --}}
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Add Exam Detail</h2>

        <form wire:submit="submit" class="mt-4">
            <div class="mb-3">
                <label for="adm_no">Admission Number</label>
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('adm_no') border-red-500 @enderror "
                    wire:model="adm_no">
                    <option value="">Select a Student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->adm_no }}">
                            {{ $student->name }} - {{ $student->adm_no }} - ({{ $student->classForm->name }} -
                            {{ $student->stream->name }})
                        </option>
                    @endforeach
                </select>

                @error('adm_no')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            @if ($studentDetails)
                <div class="mb-3">
                    <label class="form-label">Selected Student Details:</label>
                    <div>
                        <p>Name: {{ $studentDetails->name }}</p>
                        <p>Admission Number: {{ $studentDetails->adm_no }}</p>
                        <p>Form: {{ $studentDetails->form }}</p>
                        <p>Stream: {{ $studentDetails->stream->name }}</p>
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label for="subject_id">Subject</label>
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('subject_id') border-red-500 @enderror "
                    wire:model="subject_id">
                    <option value="">Select a Subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>

                @error('subject_id')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="exam1"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('exam1') border-red-500 @enderror"
                    wire:model="exam1" placeholder="Enter exam 1">

                @error('exam1')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="exam2"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('exam2') border-red-500 @enderror"
                    wire:model="exam2" placeholder="Enter exam 2">

                @error('exam2')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="exam3"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('exam3') border-red-500 @enderror"
                    wire:model="exam3" placeholder="Enter exam 3">

                @error('exam3')
                    <div class="mt-1 text-red-500">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="text" id="teacher"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('teacher') border-red-500 @enderror"
                    wire:model="teacher" placeholder="Enter teacher">

                @error('teacher')
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
