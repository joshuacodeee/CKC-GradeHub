<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();

    foreach (['users', 'school_years', 'settings', 'subjects', 'sections', 'enrollments', 'grades'] as $table) {
        Schema::dropIfExists($table);
    }

    Schema::create('users', function ($table) {
        $table->id();
        $table->string('firstname')->nullable();
        $table->string('middlename')->nullable();
        $table->string('lastname')->nullable();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role');
        $table->boolean('is_active')->default(true);
        $table->string('student_id')->nullable()->unique();
        $table->string('sex')->nullable();
        $table->timestamps();
    });

    Schema::create('school_years', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('semester')->nullable();
        $table->boolean('is_current')->default(false);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('settings', function ($table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->timestamps();
    });

    Schema::create('subjects', function ($table) {
        $table->id();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('sections', function ($table) {
        $table->id();
        $table->string('name')->nullable();
        $table->timestamps();
    });

    Schema::create('enrollments', function ($table) {
        $table->id();
        $table->unsignedBigInteger('section_id')->nullable();
        $table->string('status')->nullable();
        $table->timestamps();
    });

    Schema::create('grades', function ($table) {
        $table->id();
        $table->unsignedBigInteger('teacher_id')->nullable();
        $table->unsignedBigInteger('subject_id')->nullable();
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
    });
});

function makeUserForRoleTest(string $role, string $email): User
{
    return User::create([
        'firstname' => 'Test',
        'lastname' => ucfirst($role),
        'email' => $email,
        'password' => 'password123',
        'role' => $role,
        'is_active' => true,
    ]);
}

it('gives registrars view-only access to students and grade reports, but no printing', function () {
    $registrar = makeUserForRoleTest('registrar', 'registrar@example.test');

    $this->actingAs($registrar)
        ->get(route('registrar.students'))
        ->assertOk()
        ->assertSee('Student Records')
        ->assertDontSee('Add Student');

    $this->actingAs($registrar)
        ->get(route('registrar.reports'))
        ->assertOk()
        ->assertDontSee('data-print');

    $this->actingAs($registrar)
        ->get('/admin/teachers')
        ->assertForbidden();

    $this->get('/teacher/reports')
        ->assertForbidden();
});

it('requires teachers to use the CKC email domain', function () {
    $admin = makeUserForRoleTest('admin', 'admin@example.test');

    $this->actingAs($admin)
        ->from(route('admin.teachers'))
        ->post(route('admin.teachers.store'), [
            'firstname' => 'Casey',
            'lastname' => 'Teacher',
            'sex' => 'Female',
            'email' => 'casey@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertSessionHasErrors('email');

    expect(User::where('email', 'casey@example.test')->exists())->toBeFalse();

    $this->post(route('admin.teachers.store'), [
        'firstname' => 'Casey',
        'lastname' => 'Teacher',
        'sex' => 'Female',
        'email' => 'casey@ckcgingoog.edu.ph',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('admin.teachers'));

    expect(User::where('email', 'casey@ckcgingoog.edu.ph')->value('role'))->toBe('teacher');

    $teacher = User::where('email', 'casey@ckcgingoog.edu.ph')->firstOrFail();
    $this->actingAs($teacher)
        ->put(route('teacher.profile.update'), [
            'firstname' => 'Casey',
            'lastname' => 'Teacher',
            'sex' => 'Female',
            'email' => 'casey@example.test',
        ])
        ->assertSessionHasErrors('email');
});

it('renders the student, teacher, registrar, and registrar-settings forms', function () {
    $admin = makeUserForRoleTest('admin', 'admin@example.test');

    $this->actingAs($admin)
        ->get(route('admin.students'))
        ->assertOk()
        ->assertSee('add-student-form')
        ->assertSee('data-password-toggle');

    $this->get(route('admin.teachers'))
        ->assertOk()
        ->assertSee('add-teacher-form')
        ->assertSee('name@ckcgingoog.edu.ph')
        ->assertSee('data-password-toggle');

    $this->get(route('admin.registrars'))
        ->assertOk()
        ->assertSee('add-registrar-form');
    $this->post(route('admin.registrars.store'), [
        'firstname' => 'Riley',
        'lastname' => 'Registrar',
        'email' => 'riley@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('admin.registrars'));
    expect(User::where('email', 'riley@example.test')->value('role'))->toBe('registrar');
    $this->get(route('admin.settings'))->assertOk()->assertSee('Registrar Signature');

    $registrar = makeUserForRoleTest('registrar', 'registrar@example.test');
    $this->actingAs($registrar)
        ->get(route('registrar.settings'))
        ->assertOk()
        ->assertSee('Registrar Signature')
        ->assertDontSee('School Years');
    $this->get(route('registrar.profile'))->assertOk()->assertSee('Registrar');
});

it('lets a registrar upload a processed png signature', function () {
    Storage::fake('local');
    $registrar = makeUserForRoleTest('registrar', 'registrar@example.test');

    $this->actingAs($registrar)
        ->post(route('registrar.signature.update'), [])
        ->assertRedirect(route('registrar.settings', ['tab' => 'tab-signature']))
        ->assertSessionHasErrors('signature');

    $this->actingAs($registrar)
        ->post(route('registrar.signature.update'), [
            'signature' => UploadedFile::fake()->createWithContent(
                'registrar-signature.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/c5sAAAAASUVORK5CYII=', true),
            ),
        ])
        ->assertRedirect(route('registrar.settings', ['tab' => 'tab-signature']));

    $path = Setting::get('registrar_signature_path');

    expect($path)->not->toBeNull();
    Storage::disk('local')->assertExists($path);

    $this->get(route('registrar-signature'))
        ->assertOk()
        ->assertHeader('content-type', 'image/png');
});
