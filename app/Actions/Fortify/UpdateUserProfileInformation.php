<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'state_id' => ['required', 'numeric'],
            'province_id' => ['required', 'numeric'],
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
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'gender' => $input['gender'],
                'state_id' => $input['state_id'],
                'province_id' => $input['province_id'],
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
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'gender' => $input['gender'],
            'state_id' => $input['state_id'],
            'province_id' => $input['province_id'],
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
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
