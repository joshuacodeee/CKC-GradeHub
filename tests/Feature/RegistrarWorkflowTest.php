<?php

use App\Models\ClassOfferingStudent;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();

    foreach (['class_offering_student', 'grades', 'enrollments', 'teacher_subjects', 'sections', 'subjects', 'school_years', 'settings', 'users'] as $table) {
        Schema::dropIfExists($table);
    }

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('firstname')->nullable();
        $table->string('middlename')->nullable();
        $table->string('lastname')->nullable();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role');
        $table->boolean('is_active')->default(true);
        $table->string('student_id')->nullable()->unique();
        $table->string('course')->nullable();
        $table->string('year_level')->nullable();
        $table->string('block')->nullable();
        $table->boolean('is_irregular')->default(false);
        $table->timestamps();
    });

    Schema::create('school_years', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('semester');
        $table->boolean('is_current')->default(false);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->timestamps();
    });

    Schema::create('subjects', function (Blueprint $table) {
        $table->id();
        $table->string('code');
        $table->string('name');
        $table->text('description')->nullable();
        $table->unsignedTinyInteger('units')->default(3);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('sections', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('course')->nullable();
        $table->string('year_level')->nullable();
        $table->string('block')->nullable();
        $table->unsignedBigInteger('adviser_id')->nullable();
        $table->unsignedBigInteger('school_year_id')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('teacher_subjects', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('teacher_id');
        $table->unsignedBigInteger('subject_id');
        $table->unsignedBigInteger('section_id');
        $table->unsignedBigInteger('school_year_id')->nullable();
        $table->string('semester');
        $table->string('class_code')->nullable();
        $table->string('status')->default('active');
        $table->timestamps();
    });

    Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('section_id');
        $table->unsignedBigInteger('school_year_id')->nullable();
        $table->string('status')->default('enrolled');
        $table->timestamps();
        $table->unique(['student_id', 'section_id', 'school_year_id']);
    });

    Schema::create('class_offering_student', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('teacher_subject_id');
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('school_year_id')->nullable();
        $table->unsignedBigInteger('assigned_by')->nullable();
        $table->timestamp('assigned_at')->nullable();
        $table->timestamps();
        $table->unique(['teacher_subject_id', 'student_id']);
    });

    Schema::create('grades', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('teacher_assignment_id')->nullable();
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('enrollment_id');
        $table->unsignedBigInteger('subject_id');
        $table->unsignedBigInteger('teacher_id');
        $table->decimal('prelim', 5, 2)->nullable();
        $table->decimal('midterm', 5, 2)->nullable();
        $table->decimal('final', 5, 2)->nullable();
        $table->decimal('final_grade', 5, 2)->nullable();
        $table->string('remarks')->nullable();
        $table->string('status')->default('draft');
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
        $table->unique(['teacher_assignment_id', 'student_id']);
    });
});

function makeRegistrarWorkflowUser(string $role, string $email, array $attributes = []): User
{
    return User::create([
        'firstname' => 'Alex',
        'lastname' => ucfirst($role),
        'email' => $email,
        'password' => 'password123',
        'role' => $role,
        ...$attributes,
    ]);
}

function makeRegistrarOffering(User $teacher, Subject $subject, Section $section, SchoolYear $schoolYear, string $semester): TeacherSubject
{
    return TeacherSubject::create([
        'teacher_id' => $teacher->id,
        'subject_id' => $subject->id,
        'section_id' => $section->id,
        'school_year_id' => $schoolYear->id,
        'semester' => $semester,
        'class_code' => 'TEST-'.$section->id,
        'status' => 'active',
    ]);
}

it('lets a registrar enroll an irregular student once and exposes that student only to the assigned teacher', function () {
    $registrar = makeRegistrarWorkflowUser('registrar', 'workflow-registrar@example.test');
    $teacher = makeRegistrarWorkflowUser('teacher', 'workflow-teacher@example.test');
    $student = makeRegistrarWorkflowUser('student', 'workflow-student@example.test', [
        'student_id' => 'STU-100',
        'course' => 'BS Information Technology',
        'year_level' => '2nd Year',
    ]);
    $unassignedStudent = makeRegistrarWorkflowUser('student', 'workflow-other@example.test', [
        'student_id' => 'STU-200',
        'course' => 'BS Information Technology',
        'year_level' => '2nd Year',
    ]);
    $schoolYear = SchoolYear::create([
        'name' => '2026-2027',
        'semester' => 'First Semester',
        'is_current' => true,
        'is_active' => true,
    ]);
    $subject = Subject::create(['code' => 'IT201', 'name' => 'Systems Analysis']);
    $section = Section::create([
        'name' => 'IT-2A',
        'course' => 'BS Information Technology',
        'year_level' => '2nd Year',
        'school_year_id' => $schoolYear->id,
    ]);
    $otherSection = Section::create([
        'name' => 'IT-2B',
        'course' => 'BS Information Technology',
        'year_level' => '2nd Year',
        'school_year_id' => $schoolYear->id,
    ]);
    $offering = makeRegistrarOffering($teacher, $subject, $section, $schoolYear, 'First Semester');
    $sameSubjectDifferentSection = makeRegistrarOffering($teacher, $subject, $otherSection, $schoolYear, 'First Semester');

    $this->actingAs($registrar)
        ->get(route('registrar.dashboard'))
        ->assertOk()
        ->assertSee('Registrar');

    $this->actingAs($registrar)
        ->get(route('registrar.students', ['search' => 'STU-100']))
        ->assertOk()
        ->assertSee('STU-100')
        ->assertDontSee('STU-200');

    $this->patch(route('registrar.students.irregular', $student))
        ->assertRedirect(route('registrar.students.show', $student));
    expect($student->fresh()->is_irregular)->toBeTrue();

    $payload = [
        'teacher_subject_id' => $offering->id,
        'subject_id' => $subject->id,
        'section_id' => $section->id,
        'school_year_id' => $schoolYear->id,
        'semester' => 'First Semester',
    ];
    $this->post(route('registrar.students.subjects.assign', $student), $payload)
        ->assertRedirect(route('registrar.students.show', $student));

    expect(ClassOfferingStudent::where('student_id', $student->id)->count())->toBe(1)
        ->and(Enrollment::where('student_id', $student->id)->where('section_id', $section->id)->where('school_year_id', $schoolYear->id)->exists())->toBeTrue()
        ->and(Grade::where('student_id', $student->id)->where('teacher_assignment_id', $offering->id)->value('status'))->toBe('draft');

    $this->post(route('registrar.students.subjects.assign', $student), [
        ...$payload,
        'teacher_subject_id' => $sameSubjectDifferentSection->id,
        'section_id' => $otherSection->id,
    ])->assertSessionHas('error');

    expect(ClassOfferingStudent::where('student_id', $student->id)->count())->toBe(1);

    $this->actingAs($teacher)
        ->get(route('teacher.grades.enter', $offering))
        ->assertOk()
        ->assertSee('STU-100')
        ->assertDontSee('STU-200');

    $studentEnrollment = Enrollment::where('student_id', $student->id)->firstOrFail();
    $this->post(route('teacher.grades.save', $offering), [
        'action' => 'submit',
        'grades' => [[
            'enrollment_id' => $studentEnrollment->id,
            'student_id' => $student->id,
            'prelim' => 90,
            'midterm' => 90,
            'final' => 90,
        ]],
    ])->assertRedirect(route('teacher.grades'));

    expect(Grade::where('teacher_assignment_id', $offering->id)->where('student_id', $student->id)->value('status'))->toBe('submitted');

    $wrongEnrollment = Enrollment::create([
        'student_id' => $student->id,
        'section_id' => $otherSection->id,
        'school_year_id' => $schoolYear->id,
        'status' => 'enrolled',
    ]);
    $this->post(route('teacher.grades.save', $offering), [
        'grades' => [[
            'enrollment_id' => $wrongEnrollment->id,
            'student_id' => $student->id,
            'prelim' => 70,
        ]],
    ])->assertForbidden();
});

it('shows complete grade history with academic-period filters and a printable registrar report', function () {
    $registrar = makeRegistrarWorkflowUser('registrar', 'history-registrar@example.test');
    $student = makeRegistrarWorkflowUser('student', 'history-student@example.test', [
        'student_id' => 'STU-HISTORY',
        'course' => 'BS Information Technology',
        'year_level' => '4th Year',
        'is_irregular' => true,
    ]);
    $teacher = makeRegistrarWorkflowUser('teacher', 'history-teacher@example.test');
    $oldYear = SchoolYear::create(['name' => '2024-2025', 'semester' => 'First Semester']);
    $newYear = SchoolYear::create(['name' => '2025-2026', 'semester' => 'Second Semester']);
    $oldSubject = Subject::create(['code' => 'IT401', 'name' => 'Legacy Systems']);
    $newSubject = Subject::create(['code' => 'IT402', 'name' => 'Enterprise Systems']);
    $oldSection = Section::create(['name' => 'IT-4A', 'school_year_id' => $oldYear->id]);
    $newSection = Section::create(['name' => 'IT-4B', 'school_year_id' => $newYear->id]);
    $oldOffering = makeRegistrarOffering($teacher, $oldSubject, $oldSection, $oldYear, 'First Semester');
    $newOffering = makeRegistrarOffering($teacher, $newSubject, $newSection, $newYear, 'Second Semester');
    $oldEnrollment = Enrollment::create([
        'student_id' => $student->id,
        'section_id' => $oldSection->id,
        'school_year_id' => $oldYear->id,
        'status' => 'completed',
    ]);
    $newEnrollment = Enrollment::create([
        'student_id' => $student->id,
        'section_id' => $newSection->id,
        'school_year_id' => $newYear->id,
        'status' => 'enrolled',
    ]);
    Grade::create([
        'teacher_assignment_id' => $oldOffering->id,
        'student_id' => $student->id,
        'enrollment_id' => $oldEnrollment->id,
        'subject_id' => $oldSubject->id,
        'teacher_id' => $teacher->id,
        'prelim' => 85,
        'midterm' => 86,
        'final' => 87,
        'final_grade' => 86,
        'remarks' => 'Passed',
        'status' => 'submitted',
    ]);
    Grade::create([
        'teacher_assignment_id' => $newOffering->id,
        'student_id' => $student->id,
        'enrollment_id' => $newEnrollment->id,
        'subject_id' => $newSubject->id,
        'teacher_id' => $teacher->id,
        'prelim' => 70,
        'midterm' => 68,
        'final' => 65,
        'final_grade' => 68,
        'remarks' => 'Failed',
        'status' => 'submitted',
    ]);

    $this->actingAs($registrar)
        ->get(route('registrar.students.show', $student))
        ->assertOk()
        ->assertSee('IT401')
        ->assertSee('IT402')
        ->assertSee('Passed')
        ->assertSee('Failed')
        ->assertSee('2024-2025')
        ->assertSee('2025-2026');

    $this->get(route('registrar.students.show', [
        'student' => $student,
        'school_year_id' => $oldYear->id,
        'semester' => 'First Semester',
    ]))
        ->assertOk()
        ->assertSee('IT401')
        ->assertSee('2024-2025 · First Semester')
        ->assertDontSee('2025-2026 · Second Semester');

    $this->get(route('registrar.students.report', $student))
        ->assertOk()
        ->assertSee('Official student grade report')
        ->assertSee('IT401')
        ->assertSee('IT402');

    $anotherStudent = makeRegistrarWorkflowUser('student', 'history-other@example.test', [
        'student_id' => 'STU-OTHER',
    ]);
    $this->actingAs($anotherStudent)
        ->get(route('student.grades'))
        ->assertOk()
        ->assertDontSee('IT401')
        ->assertDontSee('IT402');
});

it('saves and removes an authorized signature with signatory and audit metadata', function () {
    Storage::fake('local');
    $registrar = makeRegistrarWorkflowUser('registrar', 'signature-registrar@example.test');

    $this->actingAs($registrar)
        ->get(route('registrar.settings'))
        ->assertOk()
        ->assertSee('Option A')
        ->assertSee('Option B')
        ->assertSee('signature-pad')
        ->assertSee('Signatory name');

    $this->post(route('registrar.signature.update'), [
        'signature' => UploadedFile::fake()->createWithContent(
            'official-signature.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/c5sAAAAASUVORK5CYII=', true),
        ),
        'signatory_name' => 'Alex Registrar',
        'signatory_position' => 'College Registrar',
    ])->assertRedirect(route('registrar.settings', ['tab' => 'tab-signature']));

    $signaturePath = Setting::get('registrar_signature_path');
    expect($signaturePath)->not->toBeNull()
        ->and(Setting::get('registrar_signatory_name'))->toBe('Alex Registrar')
        ->and(Setting::get('registrar_signatory_position'))->toBe('College Registrar')
        ->and(Setting::get('registrar_signature_updated_by'))->toBe((string) $registrar->id)
        ->and(Setting::get('registrar_signature_updated_at'))->not->toBeNull();
    Storage::disk('local')->assertExists($signaturePath);

    $this->get(route('registrar-signature'))->assertOk()->assertHeader('content-type', 'image/png');

    $student = makeRegistrarWorkflowUser('student', 'signature-student@example.test');
    $this->actingAs($student)
        ->get(route('student.grades'))
        ->assertOk()
        ->assertSee('Alex Registrar')
        ->assertSee('College Registrar');
    $this->get(route('registrar-signature'))
        ->assertOk()
        ->assertHeader('content-type', 'image/png');

    $this->actingAs($registrar)
        ->delete(route('registrar.signature.remove'))
        ->assertRedirect(route('registrar.settings', ['tab' => 'tab-signature']));

    expect(Setting::get('registrar_signature_path'))->toBeNull();
    Storage::disk('local')->assertMissing($signaturePath);

    $this->actingAs($student)
        ->post(route('registrar.signature.update'), [])
        ->assertForbidden();
});
