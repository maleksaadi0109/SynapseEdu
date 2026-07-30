<?php

namespace App\Actions\AuthAction;

use App\Http\Requests\Auth\RegisterStudentRequest;
use App\Http\Requests\Auth\RegisterTeacherRequest;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;

class RegisterUserAction
{
    /**
     * Inject existing CreateStudent and CreateTeacher actions.
     */
    public function __construct(
        private CreateStudent $createStudent,
        private CreateTeacher $createTeacher
    ) {}

    /**
     * Dispatch registration request to CreateStudent or CreateTeacher action based on role.
     *
     * @return array{user: UserResource, access_token: string, token_type: string}
     */
    public function handle(Request $request): array
    {
        $role = strtolower((string) $request->input('role'));

        return match ($role) {
            'student' => $this->registerStudent($request),
            'teacher' => $this->registerTeacher($request),
            default => throw new \InvalidArgumentException('Invalid role specified. Role must be "student" or "teacher".'),
        };
    }

    private function registerStudent(Request $request): array
    {
        $studentRequest = RegisterStudentRequest::createFrom($request);
        $studentRequest->setContainer(app())->setRedirector(app('redirect'))->validateResolved();

        return $this->createStudent->handle($studentRequest);
    }

    private function registerTeacher(Request $request): array
    {
        $teacherRequest = RegisterTeacherRequest::createFrom($request);
        $teacherRequest->setContainer(app())->setRedirector(app('redirect'))->validateResolved();

        return $this->createTeacher->handle($teacherRequest);
    }
}
