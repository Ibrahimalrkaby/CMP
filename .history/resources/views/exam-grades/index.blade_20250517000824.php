@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Exam Grades - {{ $course->name }}</h4>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('exam-grades.store', $course) }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th colspan="2" class="text-center">Midterm Exam</th>
                                            <th colspan="2" class="text-center">Final Exam</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Grade</th>
                                            <th>Notes</th>
                                            <th>Grade</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                            <tr>
                                                <td>{{ $student->full_name }}</td>
                                                <td>{{ $student->student_id }}</td>
                                                <td>
                                                    <input type="number" name="grades[{{ $student->id }}][midterm_grade]"
                                                        class="form-control"
                                                        value="{{ $midtermGrades[$student->id]->grade ?? '' }}"
                                                        min="0" max="100" step="0.01">
                                                </td>
                                                <td>
                                                    <input type="text" name="grades[{{ $student->id }}][midterm_notes]"
                                                        class="form-control"
                                                        value="{{ $midtermGrades[$student->id]->notes ?? '' }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="grades[{{ $student->id }}][final_grade]"
                                                        class="form-control"
                                                        value="{{ $finalGrades[$student->id]->grade ?? '' }}"
                                                        min="0" max="100" step="0.01">
                                                </td>
                                                <td>
                                                    <input type="text" name="grades[{{ $student->id }}][final_notes]"
                                                        class="form-control"
                                                        value="{{ $finalGrades[$student->id]->notes ?? '' }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Save Grades</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
