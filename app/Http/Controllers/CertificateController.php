<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Admin;
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
    
        // Check for an existing certificate request for the user
        $existingCertificate = Certificate::where('user_id', Auth::id())->where('status', '<', 3)->first();
    
        if ($existingCertificate) {
            return redirect()->route('certificates.request')->withErrors(['existing_request' => __('messages.certificate.request_already_exists')]);
        }
    
        $certificateCode = Str::random(8); // Generate a random certificate code (8 characters long)
    
        // Get admin ID based on the admin_mail associated with the authenticated user
        $adminId = $this->getAdminIdForUser(Auth::user()->admin_mail);
        if (is_null($adminId)) {
            return redirect()->route('certificates.request')->withErrors(['admin' => __('messages.certificate.no_admin')]);
        }
    
        $arabicName = Auth::user()->name;
        // Determine the user's name based on language preference
        $userName = $request->input('language') === 'en' ? $request->input('full_name_en', $arabicName) : $arabicName;
    
        try {
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
        } catch (\Exception $e) {
            return redirect()->route('certificates.request')->withErrors(['error' => 'An error occurred while submitting your request.']);
        }
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

     /**
     * Get certificate details by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCertificate($id)
    {
        $certificate = Certificate::findOrFail($id);

        return response()->json($certificate);
    }

    /**
     * Display the certificate approval page.
     *
     * @param  int  $certificateId
     * @return \Inertia\Response
     */
    public function approval($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);

        return Inertia::render('CertificateApproval', [
            'certificate' => [
                'id' => $certificate->id,
                'name' => $certificate->name,
                'language' => $certificate->language,
                'user_id' => $certificate->user_id,
                'created_at' => $certificate->created_at,
            ],
        ]);
    }

    /**
     * Approve a certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveCertificate($id)
    {
        $certificate = Certificate::find($id);
    
        if (!$certificate) {
            return response()->json(['error' => 'certificate.approve.error_not_found'], 404);
        }

        // Step 1: Check if current user already approved
        $user = Auth::user();
        if ($certificate->approve1 == $user->id || $certificate->approve2 == $user->id || $certificate->approve3 == $user->id) {
            return response()->json(['message' => 'certificate.approve.error_already_approved'], 400);
        }

        // Step 2 Check if the user is trying to approve their own certificate
        if ($certificate->user_id == $user->id) {
            return response()->json(['error' => 'certificate.approve.error_cannot_approve_own'], 403);
        }
        // Step 3: Check conditions for approving status 1
        if ($certificate->status == 0) {
            $certificate->status = 1;
            $certificate->approve1 = $user->id;
            $certificate->save();
            // Fire the event to notify the admin about the new certificate request
            event(new CertificateRequested(Auth::user(), $certificate));
            return response()->json(['message' => 'Certificate status updated (Approved by first approver)']);
        }
    
        // Step 4: Check conditions for approving status 2
        if ($certificate->status == 1) {
            // Fetch admin record associated with the current user
            $admin = Admin::where('user_id', $user->id)->first();
            
            // Check if the admin field is equal to 1
            if ($admin->admin == 1) {
                $certificate->status = 2;
                $certificate->approve2 = $user->id;
                $certificate->save();
               // Fire the event to notify the admin about the new certificate request
                event(new CertificateRequested(Auth::user(), $certificate));
                return response()->json(['message' => 'Certificate status updated (Approved by second approver)']);
            } else {
                return response()->json(['error' => 'certificate.approve.error_not_authorized'], 403);
            }
        }
    
        // Step 5: Check conditions for approving status 3
        if ($certificate->status == 2) {
            // Check if user_status_id is 8
            if ($user->user_status_id == 8) {
                $certificate->status = 3;
                $certificate->approve3 = $user->id;
                $certificate->save();
               // Fire the event to notify the member about the certificate approval
                event(new CertificateRequested(Auth::user(), $certificate));
                return response()->json(['message' => 'Certificate status updated (Approved by third approver)']);
            } else {
                return response()->json(['error' => 'certificate.approve.error_not_eligible'], 403);
            }
        }
    
        // If none of the conditions matched, return error
        return response()->json(['error' => 'certificate.approve.error_invalid_status'], 400);
    }

    public function viewCertificate($id)
    {
        // To be implemented later
    }
}