<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\Rule;
use App\Models\Role;
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $role = Role::where('name', 'user')->first();
        $input['role_id'] = $role->id;
        //dd($input);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'state' => ['required', Rule::in(['state1', 'state2', 'state3'])],
            'local' => ['required', Rule::in(['local1', 'local2', 'local3'])],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'educationLevel' => ['required', Rule::in(['Non_formal_education', 'Elementary', 'Secondary', 'University'])],
            'specialization' => ['nullable', 'string'],
            'skills' => ['required', 'string'],
            'alreadyVolunteering' => ['required', Rule::in(['true', 'false'])],
            'organizationName' => ['nullable', 'string'],
            'volunteeringStartDay' => ['nullable', 'date'],
            'volunteeringEndDay' => ['nullable', 'date'],
            'monthlyShare' => ['required', 'numeric'],
            'meetingDay' => ['required', 'string'],
            'role_id' => ['required', 'numeric'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'gender' => $input['gender'],
            'state' => $input['state'],
            'local' => $input['local'],
            'address' => $input['address'],
            'phone' => $input['phone'],
            'dob' => $input['dob'],
            'educationLevel' => $input['educationLevel'],
            'specialization' => $input['specialization'],
            'skills' => $input['skills'],
            'alreadyVolunteering' => $input['alreadyVolunteering'],
            'organizationName' => $input['organizationName'],
            'volunteeringStartDate' => $input['volunteeringStartDate'],
            'volunteeringEndDate' => $input['volunteeringEndDate'],
            'monthlyShare' => $input['monthlyShare'],
            'meetingDay' => $input['meetingDay'],
            'role_id' => $input['role_id'], // Assign 'main_admin' role
            'terms' => $input['terms'],
        ]);
    }
}
