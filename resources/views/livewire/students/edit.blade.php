<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\ClassForm;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\StudentDetail;
use Livewire\Attributes\Validate;

new class extends Component {
    //public properties
    public $studentId;
    #[Validate('required')]
    public $name;
    #[Validate('required')]
    public $adm_no;
    #[Validate('required')]
    public $form;
    #[Validate('required')]
    public $stream_id;
    #[Validate('required')]
    public $exam1;
    #[Validate('required')]
    public $exam2;
    #[Validate('required')]
    public $exam3;
    #[Validate('required')]
    public $teacher;
    #[Validate('required')]
    public $subject_id;
    public $subjects;
    public $primary_school;
    public $kcpe_year;
    public $kcpe_marks;
    public $kcpe_position;
    public $classes;
    public $streams;
    public $studentDetailsId;
    public $examId;
    public $form_sequence_number;
    public Student $student;

    //Initialize the component
    public function mount($id): void
    {
        //Find the student with the given ID
        $this->student = Student::find($id);
        //if the student is found, set the properties
        if ($this->student) {
            $this->setStudentProperties();
            $this->setStudentDetails();
            $this->setExamDetails();
        }

        //Get all the classes, streams and subjects
        $this->classes = ClassForm::all();
        $this->streams = Stream::all();
        $this->subjects = Subject::all();
        $this->subject_id = $this->subjects->first()->id;
    }

    //Set the properties of the student
    // ini kita set untuk call masa dekat mount tu
    private function setStudentProperties(): void
    {
        $this->studentId = $this->student->id;
        $this->form_sequence_number = $this->student->form_sequence_number;
        $this->name = $this->student->name;
        $this->adm_no = $this->student->adm_no;
        $this->form = $this->student->form;
        $this->stream_id = $this->student->stream_id;
    }

    private function setStudentDetails(): void
    {
        $this->studentDetailsId = $this->student->details;
        $this->studentDetails = $this->student->details;
        //If the student details are found, set the properties
        if ($this->studentDetails) {
            $this->primary_school = $this->studentDetails->primary_school;
            $this->kcpe_year = $this->studentDetails->kcpe_year;
            $this->kcpe_marks = $this->studentDetails->kcpe_marks;
            $this->kcpe_position = $this->studentDetails->kcpe_position;
        }
    }

    // To fetch and set the exam details for a student when the component is mount
    private function setExamDetails(): void
    {
        $exam = Exam::where('student_id', $this->student->id)->first();
        if ($exam) {
            $this->exam1 = $exam->exam1;
            $this->exam2 = $exam->exam2;
            $this->exam3 = $exam->exam3;
            $this->teacher = $exam->teacher;
            $this->subject_id = $exam->subject_id;
            $this->examId = $exam->id;
        }
    }

    //Method to update the student details
    public function updateStudent(): void
    {
        //Validate the input data
        $validatedData = $this->validate();
        //If student ID is set, update the student details
        if ($this->studentId) {
            $student = Student::find($this->studentId);
            $student->update($validatedData);
            $this->updateStudentDetails();
            $this->updateExam();
            session()->flash('success', 'Student updated successfully');
        } else {
            session()->flash('error', 'Failed to update student');
        }
    }

    //update student details
    private function updateStudentDetails(): void
    {
        // Prepare student details
        $kcpeDetails = [
            'student_id' => $this->studentId,
            'primary_school' => $this->primary_school,
            'kcpe_year' => $this->kcpe_year,
            'kcpe_marks' => $this->kcpe_marks,
            'kcpe_position' => $this->kcpe_position,
        ];

        // Update or create the student details
        StudentDetail::updateOrCreate(['student_id' => $this->studentId], $kcpeDetails);
    }

    //update or create exam details
    private function updateExam(): void
    {
        Exam::updateOrCreate(
            //Find the exam details for the student
            ['id' => $this->examId, 'student_id' => $this->studentId, 'subject_id' => $this->subject_id],
            [
                'subject_id' => $this->subject_id,
                'exam1' => $this->exam1,
                'exam2' => $this->exam2,
                'exam3' => $this->exam3,
                'teacher' => $this->teacher,
            ],
        );
    }

    //Method to fetch the exam details for a student
    public function updateSubjectId(): void
    {
        //If the subject id is set, fetch the exam details for the student
        if ($this->subject_id) {
            //Find the exam details for the student
            $exam = Exam::where('student_id', $this->studentId)
                ->where('subject_id', $this->subject_id)
                ->first();
            //if the exam details are found, set the properties
            if ($exam) {
                $this->setExamProperties($exam);
            } else {
                $this->resetExamFields();
            }
        } else {
            $this->resetExamFields();
        }
    }

    //set exam properties
    private function setExamProperties($exam): void
    {
        $this->exam1 = $exam->exam1;
        $this->exam2 = $exam->exam2;
        $this->exam3 = $exam->exam3;
        $this->teacher = $exam->teacher;
        $this->examId = $exam->exam_id;
    }

    //reset exam fields
    private function resetExamFields(): void
    {
        $this->exam1 = '';
        $this->exam2 = '';
        $this->exam3 = '';
        $this->teacher = '';
        $this->subject_id = '';
        $this->examId = '';
    }
}; ?>

<div>
    <input type="text" id="name"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
        wire:model="name" placeholder="Enter student name">

    @error('name')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror

    <input type="text" id="adm_no"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('adm_no') border-red-500 @enderror"
        wire:model="adm_no" placeholder="Enter admission number">

    @error('adm_no')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror

    <input type="text" id="primary_school"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('primary_school') border-red-500 @enderror"
        wire:model="primary_school" placeholder="Enter primary school">

    @error('primary_school')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror


    <input type="text" id="kcpe_year"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('kcpe_year') border-red-500 @enderror"
        wire:model="kcpe_year" placeholder="Enter kcpe year">

    @error('kcpe_year')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror


    <input type="text" id="kcpe_marks"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('kcpe_marks') border-red-500 @enderror"
        wire:model="kcpe_marks" placeholder="Enter kcpe marks">

    @error('kcpe_marks')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror


    <input type="text" id="kcpe_position"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('kcpe_position') border-red-500 @enderror"
        wire:model="kcpe_position" placeholder="Enter kcpe position">

    @error('kcpe_position')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror


    <div class="mb-3">
        <select
            class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('subject_id') border-red-500 @enderror "
            wire:model="subject_id">
            <option value="">Select Subject</option>
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

    <input type="number" id="exam1"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('exam1') border-red-500 @enderror"
        wire:model="exam1" placeholder="Enter exam 1">

    @error('exam1')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror

    <input type="number" id="exam2"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('exam2') border-red-500 @enderror"
        wire:model="exam2" placeholder="Enter exam 2">

    @error('exam2')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror

    <input type="number" id="exam3"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('exam3') border-red-500 @enderror"
        wire:model="exam3" placeholder="Enter exam 3">

    @error('exam3')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror

    <input type="text" id="teacher"
        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('teacher') border-red-500 @enderror"
        wire:model="teacher" placeholder="Enter teacher">

    @error('teacher')
        <div class="mt-1 text-red-500">
            {{ $message }}
        </div>
    @enderror

    <div class="mb-3">
        <select
            class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('form') border-red-500 @enderror "
            wire:model="form">
            <option value="">Select Form</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>

        @error('form')
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

    <div class="mb-3">
        {{-- <button wire:click="updateStudent" wire::loading.attr="disabled" class="px-4 py-2 mt-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
            Submit
        </button> --}}
        <button
            wire:click="updateStudent"class="px-4 py-2 mt-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
            Submit
        </button>
    </div>


    {{-- Flash success message --}}
    @if (session('success'))
        <div x-data="{ open: false, message: '' }" x-cloak
            @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 5000)"
            x-show="open" class="px-4 py-2 mt-4 font-bold text-white bg-green-500 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Flash error message --}}
    @if (session('error'))
        <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 5000)" x-show="open"
            class="px-4 py-2 mt-4 font-bold text-white bg-red-500 rounded">
            {{ session('error') }}
        </div>
    @endif
</div>
