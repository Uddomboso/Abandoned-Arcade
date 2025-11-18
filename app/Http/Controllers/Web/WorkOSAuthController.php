<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use WorkOS\WorkOS;
use WorkOS\UserManagement;

class WorkOSAuthController extends Controller
{
    protected $userManagement;

    public function __construct()
    {
        // Set WorkOS API key and Client ID from environment
        WorkOS::setApiKey(env('WORKOS_API_KEY'));
        WorkOS::setClientId(env('WORKOS_CLIENT_ID'));
        
        $this->userManagement = new UserManagement();
    }

    /**
     * Redirect to WorkOS login.
     */
    public function redirectToWorkOS(Request $request)
    {
        $redirectUri = config('app.url', 'http://localhost') . '/auth/workos/callback';
        
        try {
            // Get authorization URL using AuthKit provider
            $authorizationUrl = $this->userManagement->getAuthorizationUrl(
                $redirectUri,
                csrf_token(),
                UserManagement::AUTHORIZATION_PROVIDER_AUTHKIT
            );

            return redirect($authorizationUrl);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to initiate login: ' . $e->getMessage());
        }
    }

    /**
     * Handle WorkOS callback.
     */
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect('/')->with('error', 'Authorization failed. No code received.');
        }

        try {
            // Exchange code for user info
            $clientId = env('WORKOS_CLIENT_ID');
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            
            $authResponse = $this->userManagement->authenticateWithCode(
                $clientId,
                $code,
                $ipAddress,
                $userAgent
            );

            $workosUser = $authResponse->user;

            // Find or create user
            $user = User::where('workos_id', $workosUser->id)
                ->orWhere('email', $workosUser->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => trim(($workosUser->firstName ?? '') . ' ' . ($workosUser->lastName ?? '')),
                    'email' => $workosUser->email,
                    'workos_id' => $workosUser->id,
                    'workos_organization_id' => $authResponse->organizationId ?? null,
                    'is_workos_user' => true,
                    'email_verified_at' => $workosUser->emailVerified ? now() : null,
                ]);
            } else {
                // Update existing user with WorkOS info
                $user->update([
                    'workos_id' => $workosUser->id,
                    'workos_organization_id' => $authResponse->organizationId ?? null,
                    'is_workos_user' => true,
                    'email_verified_at' => $workosUser->emailVerified ? ($user->email_verified_at ?? now()) : $user->email_verified_at,
                ]);
            }

            // Log the user in
            Auth::login($user, true);

            return redirect('/')->with('success', 'Successfully logged in!');
        } catch (\Exception $e) {
            \Log::error('WorkOS authentication error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Successfully logged out!');
    }
}
