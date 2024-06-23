<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Events\CertificateRequested;

class CertificateController extends Controller
{
    /**
     * Show the certificate request form.
     *
     * @return \Inertia\Response
     */
    public function showRequestForm()
    {
        return Inertia::render('Certificate'); // Render the certificate request form using Inertia
    }

    /**
     * Check if the user has an existing certificate request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExistingRequest()
    {
        $user = Auth::user(); // Retrieve the authenticated user
        $existingRequest = Certificate::where('user_id', $user->id)->whereIn('status', [0, 1, 2])->exists(); // Check if there's an existing certificate request with status 0, 1, or 2
        
        return response()->json(['exists' => $existingRequest]); // Return JSON response indicating whether an existing request exists
    }

    /**
     * Submit a new certificate request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitRequest(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,ar',
            'full_name_en' => 'required_if:language,en',
        ]); // Validate the incoming request data

        $certificateCode = Str::random(8); // Generate a random certificate code (8 characters long)

        // Get admin ID based on the admin_mail associated with the authenticated user
        $adminId = $this->getAdminIdForUser(Auth::user()->admin_mail);
        if (is_null($adminId)) {
            return redirect()->route('certificates.request')->withErrors(['admin' => __('messages.certificate.no_admin')]);
        }

        $arabicName = Auth()->user()->name;
        // Determine the user's name based on language preference
        $userName = $request->input('language') === 'en' ? $request->input('full_name_en', $arabicName) : $arabicName;

        // Create a new certificate request
        $certificate = new Certificate();
        $certificate->user_id = Auth::id(); // Assign the authenticated user's ID
        $certificate->admin_id = $adminId; // Assign the admin's ID
        $certificate->name = $userName; // Assign the user's name for the certificate
        $certificate->language = $request->input('language'); // Assign the language preference
        $certificate->code = $certificateCode; // Assign the generated certificate code
        $certificate->status = 0; // Set initial status to pending
        $certificate->save(); // Save the certificate request to the database

        // Fire the event to notify the admin about the new certificate request
        event(new CertificateRequested(Auth::user(), $certificate));

        return redirect()->route('certificates.request')->with('success', 'Certificate request submitted successfully.'); // Redirect with success message
    }
    
    /**
     * Helper method to retrieve admin ID based on admin_mail.
     *
     * @param  string  $adminMail
     * @return int|null
     */
    private function getAdminIdForUser($adminMail)
    {
        $admin = User::where('email', $adminMail)->first(); // Find the admin user based on admin_mail
        if ($admin) {
            return $admin->id; // Return the admin's ID
        }
        return null; // Return null if admin is not found
    }    
}