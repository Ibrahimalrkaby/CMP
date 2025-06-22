public function store(Request $request)
{
$validator = Validator::make($request->all(), [
'teacher_id' => 'required|exists:teacher_data,id',
'course_id' => 'required|exists:courses,id',
'table_name' => 'required|unique:lectures',
'start_time' => 'required|date',
'end_time' => 'required|date'
]);

if ($validator->fails()) {
return response()->json($validator->errors(), 422);
}

// Create lecture
$lecture = Lecture::create([
'teacher_id' => $request->teacher_id,
'course_id' => $request->course_id,
'table_name' => $request->table_name,
'start_time' => $request->start_time,
'end_time' => $request->end_time
]);

// Create dynamic attendance table
$tableName = 'attendance_lecture_' . $lecture->id;

try {
if (!Schema::hasTable($tableName)) {
Schema::create($tableName, function (Blueprint $table) {
$table->id();
$table->unsignedBigInteger('student_id');
$table->boolean('present')->default(false);
$table->timestamps();

$table->foreign('student_id')
->references('id')
->on('students_data')
->onDelete('cascade');
});
}

// Get course with enrolled students
$course = Course::with('students')->find($request->course_id);

if (!$course) {
return response()->json(['message' => 'Course not found'], 404);
}

// Insert attendance records
$attendanceData = [];
$now = now();

foreach ($course->students as $student) {
$attendanceData[] = [
'student_id' => $student->id,
'present' => false,
'created_at' => $now,
'updated_at' => $now
];
}

// Bulk insert into dynamic table
if (!empty($attendanceData)) {
DB::table($tableName)->insert($attendanceData);
}

return response()->json([
'message' => 'Lecture and attendance table created successfully',
'table_name' => $tableName,
'data' => $lecture
], 201);
} catch (\Exception $e) {
// Rollback lecture creation if table creation fails
$lecture->delete();

return response()->json([
'error' => 'Failed to create attendance table: ' . $e->getMessage()
], 500);
}
}