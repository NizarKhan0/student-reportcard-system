<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\Exam;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public $form = 1;
    public $searchTerm = '';
    public $sortColumn = 'name';
    public $sortDirection = 'asc';

    public function mount(): void
    {
        //Get the form number from the session
        $this->form = session()->get('student_list_form', 1);
        //Get the student for the selected form
        $this->getStudents();
    }

    //Updateform is called when the form selection is changed
    public function updatedForm(): void
    {
        //Store the selected form number in the session
        session()->put('student_list_form', $this->form);
        //Get the student for the selected form
        $this->getStudents();
    }

    //Sort the students based on the selected column and direction
    public function sortBy($column): void
    {
        $this->sortColumn = $column;
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirm', ['id' => $id]);
    }

    #[On('deleteStudent')]
    public function deleteStudent($id)
    {
        $this->student_id = $id;
        //Find and delete the related exam records
        Exam::where('student_id', $this->student_id)->delete();

        //Find the student
        $student = Student::find($this->student_id);
        //Check if the student exists and delete it
        if ($student) {
            $student->delete();
        }

        //Dispatch an event to notify the student.list component that a student has been deleted
        $this->dispatch('studentDeleted', 'Student deleted successfully');
    }

    //Listen for the student-deleted event
    #[On('studentDeleted')]
    public function studentDeleted($message)
    {
        session()->flash('message', $message);
        $this->render();
    }

    #[On('student-created')]
    public function getStudents()
    {
        //Get the students for the selected form
        return Student::where('form', $this->form)
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            //eager load the stream relationship
            ->with('stream')
            //Order the students by the form sequence number
            // ->orderByRaw('CAST(form_sequence_number AS INT)' . $this->sortDirection)
            ->paginate(10);
    }

    //return the students and the form
    public function with(): array
    {
        return [
            //Get the students for the selected form
            'students' => $this->getStudents(),
        ];
    }
}; ?>

<div>

    <div class="p-6 bg-gray-100 min-h-screen">
        <div class="container mx-auto bg-white shadow-lg rounded-lg p-6">
            <!-- Filters Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <!-- Select Form -->
                <div class="w-full md:w-1/3 mb-4 md:mb-0">
                    <label for="form" class="block font-medium text-sm text-gray-700">Select Form</label>
                    <select name="form" id="form" wire:model.live="form"
                        class="form-select mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="1">Form 1</option>
                        <option value="2">Form 2</option>
                        <option value="3">Form 3</option>
                        <option value="4">Form 4</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="w-full md:w-1/3 mb-4 md:mb-0">
                    <input type="text" wire:model.live="searchTerm"
                        class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        placeholder="Search by name">
                </div>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
                <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg p-3">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Table Section -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                Admission Number
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                Stream
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                Form
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($students->items() as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $student->form_sequence_number }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $student->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $student->adm_no }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $student->stream->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $student->form }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 flex gap-3">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <button wire:click="confirmDelete({{ $student->id }})"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                                    <a href="#" class="text-blue-600 hover:text-blue-900">View Report Card</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            <div class="mt-6 text-center">
                @if ($students->count())
                    {{ $students->links() }}
                @else
                    <p class="text-yellow-600">
                        {{ $searchTerm ? 'No students found with that name.' : 'No students found.' }}
                    </p>
                @endif
            </div>
        </div>
    </div>

</div>

@script
    <script>
        window.addEventListener('show-delete-confirm', function(event) {
            let id = event.detail[0]; //Access the student id
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.dispatchSelf('deleteStudent', id);
                }
            });
        });
    </script>
@endscript
