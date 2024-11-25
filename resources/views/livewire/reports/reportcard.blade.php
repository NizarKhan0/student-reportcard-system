<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\SchoolSettings;
use App\Models\Exam;

new class extends Component {
    //Public properties
    public $students;
    public $studentId;
    public $totalExam1;
    public $totalExam2;
    public $totalExam3;
    public $totalAverage;
    public $totalPoints;
    public $useStreams;
    public $exams;
    public $averageGrade;
    public $averageExam1;
    public $averageExam2;
    public $averageExam3;
    public $averageTotalAverage;
    public $schoolSettings;
    public $responsibilities;
    public $clubs;
    public $sports;
    public $houseComment;
    public $teacherComment;
    public $principalComment;
    public $schoolActivity;
    public $schoolMotto;
    public $schoolVision;

    //Initialize the component
    public function mount($studentId)
    {
        //Fetch the student ID
        $this->studentId = $studentId;
        //Fetch the school settings
        $this->schoolSettings = SchoolSettings::first();
        //Get the student record from the DB
        $this->student = Student::find($this->studentId);
        //If the studnet is not found
        if (!$this->student) {
            return;
        }

        //Fetch the student activity
        $this->studentActivity = $this->student->studentActivity;
        //Populate the component with the fetched data
        $this->populateData();
        //Update the exam data
        $this->updateExamData();
        //if the student has exams, calculate the average mark
        if (count($this->exams) > 0) {
            $averageMark = $this->totalAverage / count($this->exams);
        } else {
            $averageMark = null;
        }

        //if the average mark is not null, calculate the average grade
        if ($averageMark !== null) {
            $this->averageGrade = $this->calculateGrade($averageMark);
        } else {
            $this->averageGrade = 'N/A';
        }

        //if school settings are available, fetch the school motto and vision
        if ($this->schoolSettings) {
            $this->schoolMotto = $this->schoolSettings->school_motto;
            $this->schoolVision = $this->schoolSettings->school_vision;
        } else {
            $this->schoolMotto = null;
            $this->schoolVision = null;
        }
    }

    //Populate the component with the fetched data
    public function populateData()
    {
        //Check if student and student activity are available
        if ($this->student && $this->studentActivity) {
            //Populate the component with the student and student activity data
            $this->responsibilities = $this->studentActivity->responsibilities;
            $this->clubs = $this->studentActivity->clubs;
            $this->sports = $this->studentActivity->sports;
            $this->houseComment = $this->studentActivity->house_comment;
            $this->teacherComment = $this->studentActivity->teacher_comment;
            $this->principalComment = $this->studentActivity->principal_comment;
        } else {
            //If either student or student activity is not available, set the data to null
            $this->responsibilities = 'N/A';
            $this->clubs = 'N/A';
            $this->sports = 'N/A';
            $this->houseComment = 'N/A';
            $this->teacherComment = 'N/A';
            $this->principalComment = 'N/A';
        }
    }

    //Update the exam data
    public function updateExamData()
    {
        // Find the student by ID
        $student = Student::find($this->studentId);
        if (!$student) {
            return;
        }

        // Retrieve the student's exams associated with the subjects
        $exams = $this->student->exams()->with('subject')->get();
        $this->exams = $this->student->exams;

        // Calculate the total marks for each exam
        $this->totalExam1 = $exams->sum('exam1');
        $this->totalExam2 = $exams->sum('exam2');
        $this->totalExam3 = $exams->sum('exam3');
        $this->totalAverage = $exams->sum('average');
        $this->totalPoints = $exams->sum('points');

        $examCount = count($this->exams);

        // Calculate the average scores if exams exist
        if ($examCount > 0) {
            $this->averageExam1 = round($this->totalExam1 / $examCount, 2);
            $this->averageExam2 = round($this->totalExam2 / $examCount, 2);
            $this->averageExam3 = round($this->totalExam3 / $examCount, 2);
            $this->averageTotalAverage = round($this->totalAverage / $examCount, 2);
            $this->averageGrade = $this->calculateGrade($this->averageTotalAverage);
        } else {
            $this->averageExam1 = 'N/A';
            $this->averageExam2 = 'N/A';
            $this->averageExam3 = 'N/A';
            $this->averageTotalAverage = 'N/A';
            $this->averageGrade = 'N/A';
        }

        // Check if student is assigned to a stream
        $this->useStreams = isset($this->student->stream_id);

        if (!$this->useStreams) {
            session()->flash('error', 'This student is not assigned to a stream.');
        }

        // Calculate details for each exam
        foreach ($exams as $exam) {
            $averageCATS = ($exam->exam1 + $exam->exam2) / 2;
            $catScore = ($averageCATS / 30) * 30;
            $finalExamScore = ($exam->exam3 / 70) * 70;
            $average = $catScore + $finalExamScore;

            $exam->average = round($average);
            $exam->grade = $this->calculateGrade($exam->average);
            $exam->points = $this->calculatePoints($exam->grade);
            $exam->position = $this->calculateSubjectPosition($exam->subject_id, $exam->average, $this->student->form, $this->student->stream_id);
            $exam->remarks = $this->generateRemarks($exam->grade);

            $exam->save();
        }
    }

    //Calculate the average grade
    public function calculateGrade($average)
    {
        //Determine grade based on average score
        if ($average >= 80) {
            return 'A';
        } elseif ($average >= 75) {
            return 'A-';
        } elseif ($average >= 70) {
            return 'B+';
        } elseif ($average >= 65) {
            return 'B';
        } elseif ($average >= 60) {
            return 'B-';
        } elseif ($average >= 55) {
            return 'C+';
        } elseif ($average >= 50) {
            return 'C';
        } elseif ($average >= 45) {
            return 'C-';
        } elseif ($average >= 40) {
            return 'D+';
        } elseif ($average >= 35) {
            return 'D';
        } elseif ($average >= 30) {
            return 'D-';
        } else {
            return 'E';
        }
    }

    //Calculate the points
    public function calculatePoints($grade)
    {
        //Map grades to points
        $gradePointsMapping = [
            'A' => 12,
            'A-' => 11,
            'B+' => 10,
            'B' => 9,
            'B-' => 8,
            'C+' => 7,
            'C' => 6,
            'C-' => 5,
            'D+' => 4,
            'D' => 3,
            'D-' => 2,
            'E' => 1,
        ];

        //Return the points for the given grade
        return $gradePointsMapping[$grade] ?? 0;
    }

    public function generateRemarks($grade)
    {
        //Map grades to remarks
        $gradeRemarksMapping = [
            'A' => 'Excellent',
            'A-' => 'Very Good',
            'B+' => 'Good',
            'B' => 'Good',
            'B-' => 'Satisfactory',
            'C+' => 'Satisfactory',
            'C' => 'Average',
            'C-' => 'Average',
            'D+' => 'Below Average',
            'D' => 'Below Average',
            'D-' => 'Poor',
            'E' => 'Poor',
        ];

        //Return the corresponding remark for the grade or 'N/A' if not found
        return $gradeRemarksMapping[$grade] ?? '';
    }

    public function calculateSubjectPosition($subjectId, $average, $form, $streamId)
    {
        //Retrieve exams for the given subject and form
        $studentsExams = Exam::where('subject_id', $subjectId)
            //retrieve students with the given form (all streams within the form)
            ->whereHas('student', function ($query) use ($form) {
                $query->where('form', $form);
            })
            ->get();

        //Count students with the higher average scores
        $higherScores = $studentsExams->filter(function ($exam) use ($average) {
            return $exam->average > $average;
        }); //80,70,60,50,40,30

        //Position is the count of higher scores plus 1
        return $higherScores->count() + 1;
        //for example we have 3 students with 80, 75, 70
    }

    public function calculateStreamPosition($studentId, $totalPoints, $streamId)
    {
        //Count students with higher points in the same stream
        $student_with_higher_points = Exam::where('student_id', $studentId)
            ->where(
                function ($query) use ($totalPoints) {
                    $query
                        ->selectRaw('SUM(exams.points)')
                        ->from('exams')
                        ->whereColumn('student_id', 'exams.student.id')
                        ->groupBy('exams.student_id')
                        ->havingRaw('SUM(exams.points) > ?', [$totalPoints]);
                },
                '>',
                0,
            )
            ->count();

        //calculate the stream position
        $stream_position = $student_with_higher_points + 1;

        return $stream_position;
    }

    public function calculateOverallPosition($studentId, $totalPoints)
    {
        //Find the student by ID
        $student = Student::find($this->studentId);

        //if the student is not found
        if (!$student) {
            return;
        }

        //Query to count students with the higher total points in the same form
        $student_with_higher_points = Exam::where('form', $student->form)
            ->whereHas('exams', function ($query) use ($totalPoints) {
                $query
                    ->selectRaw('SUM(points) as total_points')
                    ->groupBy('student_id')
                    ->havingRaw('SUM(total_points) > ?', [$totalPoints]);
            })
            ->count(); //400, 300, 200

        //Query to count students with the same points but lower student ID
        $student_with_same_points = Exam::where('form', $student->form)
            ->whereHas('exams', function ($query) use ($totalPoints) {
                $query
                    ->selectRaw('SUM(points) as total_points')
                    ->groupBy('student_id')
                    ->havingRaw('SUM(total_points) = ?', [$totalPoints]);
            })
            ->where('student_id', '>', $studentId) //Filter to include only students with higher student IDs
            ->count();

        //calculate the overall position
        $overall_position = $student_with_higher_points + $student_with_same_points + 1;

        return $overall_position;
    }

    public function refreshData()
    {
        //Fetch the student record from DB
        $this->updateExamData();

        //Refresh the view
        $this->render();
    }

    public function with(): array
    {
        //update exam data for the current student
        $this->updateExamData();

        //Find the school settings
        $student = Student::with('exams', 'studentdetails')->find($this->studentId);

        //Fetch the school settings
        $this->schoolSettings = SchoolSettings::first();

        //If no student is found, retun an error
        // if (!$student) {
        //     return [
        //         'error_message' => 'Student not found with ID',
        //         'schoolSettings' => $this->schoolSettings, //pass the school settings
        //     ];
        // }

        //calculate the student overall position baded on total points
        $overallPositions = $this->calculateOverallPosition($student->id, $this->totalPoints);
        //count the total number of students in the same form
        $totalStudents = Exam::where('form', $student->form)->count();

        //Prepaare the view data array
        return [
            'student' => $student,
            'overallPositions' => $overallPositions,
            'totalStudents' => $totalStudents,
            'schoolSettings' => $this->schoolSettings,
            'schoolMotto' => $this->schoolSettings ? $this->schoolSettings->school_motto : null,
            'schoolVision' => $this->schoolSettings ? $this->schoolSettings->school_vision : null,
            'totalExam1' => $this->totalExam1,
            'totalExam2' => $this->totalExam2,
            'totalExam3' => $this->totalExam3,
            'totalAverage' => $this->totalAverage,
            'totalPoints' => $this->totalPoints,
            'averageExam1' => $this->averageExam1,
            'averageExam2' => $this->averageExam2,
            'averageExam3' => $this->averageExam3,
            'averageTotalAverage' => $this->averageTotalAverage,
            'averageGrade' => $this->averageGrade,
        ];

        //calculate the student position within their stream
        $streamPosition = $this->calculateStreamPosition($student->id, $this->totalPoints, $student->stream_id);
        //count the total number of students in the same stream
        $totalStreamStudents = Exam::where('stream_id', $student->stream_id)->count();

        //add stream position and total stream students to the view data
        $viewData['streamPosition'] = $streamPosition;
        $viewData['studentsInStream'] = $totalStreamStudents;

        //Merge exams data with view adata and return
        return array_merge($viewData, ['exams' => $this->exams]);
    }
}; ?>

<div class="container p-6 mx-auto">
    <!-- Header Section -->
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold">Student Report Card</h1>
        <p class="text-gray-600">{{ $schoolMotto ?? 'School Motto: Knowledge is Power' }}</p>
        <p class="text-gray-600">{{ $schoolVision ?? 'School Vision: Excellence in Education' }}</p>
    </div>

    <!-- Student Details -->
    <div class="p-4 mb-6 bg-gray-100 rounded-lg shadow">
        <h2 class="mb-4 text-xl font-semibold">Student Information</h2>
        <p><strong>Name:</strong> {{ $student->name }}</p>
        <p><strong>Form:</strong> {{ $student->form }}</p>
        <p><strong>Stream:</strong> {{ $useStreams ? $student->stream->name : 'Not Assigned' }}</p>
    </div>

    <!-- Exam Summary -->
    <div class="p-4 mb-6 bg-white rounded-lg shadow">
        <h2 class="mb-4 text-xl font-semibold">Exam Summary</h2>
        <div class="grid grid-cols-2 gap-4">
            <p><strong>Total Exam 1:</strong> {{ $totalExam1 }}</p>
            <p><strong>Total Exam 2:</strong> {{ $totalExam2 }}</p>
            <p><strong>Total Exam 3:</strong> {{ $totalExam3 }}</p>
            <p><strong>Total Average:</strong> {{ $totalAverage }}</p>
            <p><strong>Total Points:</strong> {{ $totalPoints }}</p>
            <p><strong>Average Grade:</strong> {{ $averageGrade }}</p>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="p-4 mb-6 bg-gray-100 rounded-lg shadow">
        <h2 class="mb-4 text-xl font-semibold">Exam Details</h2>
        <table class="w-full border border-collapse border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2 border border-gray-300">Subject</th>
                    <th class="px-4 py-2 border border-gray-300">Exam 1</th>
                    <th class="px-4 py-2 border border-gray-300">Exam 2</th>
                    <th class="px-4 py-2 border border-gray-300">Exam 3</th>
                    <th class="px-4 py-2 border border-gray-300">Average</th>
                    <th class="px-4 py-2 border border-gray-300">Grade</th>
                    <th class="px-4 py-2 border border-gray-300">Points</th>
                    <th class="px-4 py-2 border border-gray-300">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($exams as $exam)
                    <tr>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->subject->name }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->exam1 }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->exam2 }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->exam3 }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->average }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->grade }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->points }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $exam->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Activities -->
    <div class="p-4 mb-6 bg-white rounded-lg shadow">
        <h2 class="mb-4 text-xl font-semibold">Student Activities</h2>
        <p><strong>Responsibilities:</strong> {{ $responsibilities }}</p>
        <p><strong>Clubs:</strong> {{ $clubs }}</p>
        <p><strong>Sports:</strong> {{ $sports }}</p>
    </div>

    <!-- Comments -->
    <div class="p-4 mb-6 bg-gray-100 rounded-lg shadow">
        <h2 class="mb-4 text-xl font-semibold">Comments</h2>
        <p><strong>House Comment:</strong> {{ $houseComment }}</p>
        <p><strong>Teacher Comment:</strong> {{ $teacherComment }}</p>
        <p><strong>Principal Comment:</strong> {{ $principalComment }}</p>
    </div>

    <!-- Position -->
    <div class="p-4 mb-6 bg-white rounded-lg shadow">
        <h2 class="mb-4 text-xl font-semibold">Student Position</h2>
        <p><strong>Overall Position:</strong> {{ $overallPositions }}</p>
        <p><strong>Position in Stream:</strong> {{ $streamPosition }}</p>
        <p><strong>Total Students in Stream:</strong> {{ $studentsInStream }}</p>
    </div>
</div>
