<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\ClassForm;
use Livewire\Attributes\Validate;

new class extends Component {
    public $students;
    public $classforms;
    public $forms;
    public $selectedStudent = null;
    public $selectedClass = null;

    #[Validate('required|string|max:255')]
    public $responsibilities = '';
    #[Validate('required|string|max:255')]
    public $clubs = '';
    #[Validate('required|string|max:255')]
    public $sports = '';
    #[Validate('required|string|max:255')]
    public $house_comment = '';
    public $teacher_comment = '';
    public $principal_comment = '';
    public $activityId;

    // Initialize the component
    public function mount(): void
    {
        // Get all the class forms
        $this->classforms = ClassForm::all();

        // Get all the forms available
        $this->forms = Student::select('form')->distinct()->get();

        // Start with an empty collection of students dia macam bila kita select form tu baru keluar data yg dalam form tu dalam dependent dropdown
        $this->students = collect();
    }

    public function updatedSelectedClass(): void
    {
        // Get all the students in the selected form
        $this->students = Student::where('form', $this->selectedClass)->get();

        // Reset the selected student when changing the form
        $this->selectedStudent = null;
    }

    public function saveComments()
    {
        // Validate the input data
        $this->validate();

        // Find the selected student
        $student = Student::find($this->selectedStudent);

        // Get existing student activity or create a new one
        $activity = $student->activity()->firstOrNew(['student_id' => $student->id]);

        // Only update the fields if they are provided
        $activity->responsibilities = $this->responsibilities ?? $activity->responsibilities;
        $activity->clubs = $this->clubs ?? $activity->clubs;
        $activity->sports = $this->sports ?? $activity->sports;
        $activity->house_comment = $this->house_comment ?? $activity->house_comment;

        // Check if teacher_comment is provided before saving
        if ($this->teacher_comment) {
            $activity->teacher_comment = $this->teacher_comment;
        }

        // Check if principal_comment is provided before saving
        if ($this->principal_comment) {
            $activity->principal_comment = $this->principal_comment;
        }

        // Save the activity
        $activity->save();

        // Reset the form fields
        $this->responsibilities = '';
        $this->clubs = '';
        $this->sports = '';
        $this->house_comment = '';
        $this->teacher_comment = '';
        $this->principal_comment = '';
    }

    public function updatedSelectedStudent(): void
    {
        // Get the current activities of the selected student
        $activity = Student::find($this->selectedStudent)->activity;

        // If there's an activity, load it into local variables
        if ($activity) {
            $this->responsibilities = $activity->responsibilities;
            $this->clubs = $activity->clubs;
            $this->sports = $activity->sports;
            $this->house_comment = $activity->house_comment;
            $this->teacher_comment = $activity->teacher_comment;
            $this->principal_comment = $activity->principal_comment;
        } else {
            // If there's no activity, reset the local variables
            $this->responsibilities = '';
            $this->clubs = '';
            $this->sports = '';
            $this->house_comment = '';
            $this->teacher_comment = '';
            $this->principal_comment = '';
        }
    }

    public function with(): array
    {
        // Get all the forms
        $this->forms = Student::select('form')->distinct()->get();

        // Get all the students in the selected form
        if ($this->selectedClass) {
            $this->students = Student::where('form', $this->selectedClass)->get();
        }

        // Return the data as an array
        return [
            'forms' => $this->forms,
            'students' => $this->students,
        ];
    }
};
?>

<div wire:poll.500ms>
    <div class="container p-6 mt-5 bg-white rounded-lg shadow-md">

        <div class="mb-3">
            <select
                class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('selectedClass') border-red-500 @enderror "
                wire:model="selectedClass">
                <option value="">Select a Form</option>
                @foreach ($forms as $form)
                    <!-- Add a space between 'Form' and $form->form -->
                    <option value="{{ $form->form }}">{{ 'Form ' . $form->form }}</option>
                @endforeach
            </select>
        </div>

        @if (!$students->isEmpty())
            <div class="mb-3">
                <select
                    class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('selectedStudent') border-red-500 @enderror "
                    wire:model="selectedStudent">
                    <option value="">Select a Student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} (Adm No: {{ $student->adm_no }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($selectedStudent)
            <form wire:submit="saveComments" class="mt-4">

                <div class="mb-3">
                    <textarea id="responsibilities"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('responsibilities') border-red-500 @enderror"
                        wire:model="responsibilities" placeholder="Responsibility"></textarea>

                    @error('responsibilities')
                        <div class="mt-1 text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <textarea id="clubs"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('clubs') border-red-500 @enderror"
                        wire:model="clubs" placeholder="Clubs"></textarea>

                    @error('clubs')
                        <div class="mt-1 text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <textarea id="sports"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('sports') border-red-500 @enderror"
                        wire:model="sports" placeholder="Sports"></textarea>

                    @error('sports')
                        <div class="mt-1 text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <textarea id="house_comment"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('house_comment') border-red-500 @enderror"
                        wire:model="house_comment" placeholder="House Comments"></textarea>

                    @error('house_comment')
                        <div class="mt-1 text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    @if ($teacher_comment)
                        <!-- Read-only textarea displaying existing comments -->
                        <textarea id="teacher_comment"
                            class="block w-full mt-1 bg-gray-100 border-gray-300 rounded-md shadow-sm cursor-not-allowed focus:ring-0 focus:border-gray-300"
                            readonly placeholder="Teacher Comments">{{ $teacher_comment }}</textarea>
                    @else
                        <!-- Editable textarea for entering comments -->
                        <textarea id="teacher_comment"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('teacher_comment') border-red-500 @enderror"
                            wire:model.debounce.4000ms="teacher_comment" placeholder="Teacher Comments"></textarea>
                        @error('teacher_comment')
                            <div class="mt-1 text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif
                </div>


                <div class="mb-3">
                    @if ($principal_comment)
                        <!-- Read-only textarea displaying existing comments -->
                        <textarea id="principal_comment"
                            class="block w-full mt-1 bg-gray-100 border-gray-300 rounded-md shadow-sm cursor-not-allowed focus:ring-0 focus:border-gray-300"
                            readonly placeholder="Principal Comments">{{ $principal_comment }}</textarea>
                    @else
                        <!-- Editable textarea for entering comments -->
                        <textarea id="principal_comment"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('principal_comment') border-red-500 @enderror"
                            wire:model.debounce.4000ms="principal_comment" placeholder="Principal Comments"></textarea>
                        @error('principal_comment')
                            <div class="mt-1 text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif
                </div>

                <button type="submit"
                    class="px-4 py-2 mt-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Submit
                </button>

            </form>
        @endif


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
