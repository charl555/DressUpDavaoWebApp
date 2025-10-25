<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Favorites;
use App\Models\ShopReviews;
use App\Models\UserMeasurements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function accountPage()
    {
        $user = Auth::user();

        $bookings = Bookings::with(['product.user.shop'])
            ->where('user_id', $user->id)
            ->latest('booking_date')
            ->paginate(10);

        // Load favorites using the belongsToMany relationship
        $favorites = $user
            ->favorites()
            ->with([
                'user.shop',
                'product_images',
                'product_measurements'
            ])
            ->latest()
            ->get();

        return view('AccountPage', compact('bookings', 'favorites'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/|unique:users,phone_number,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePreferences(Request $request)
    {
        try {
            $validated = $request->validate([
                'color_preference' => ['nullable', 'string'],
                'occasion_preference' => ['nullable', 'string'],
                'fabric_preference' => ['nullable', 'string'],
            ]);

            $user = Auth::user();

            $user->preferences = [
                'color' => $validated['color_preference'] ?? null,
                'occasion' => $validated['occasion_preference'] ?? null,
                'fabric' => $validated['fabric_preference'] ?? null,
            ];

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preferences. Please try again.'
            ], 500);
        }
    }

    public function updateMeasurements(Request $request)
    {
        $request->validate([
            'chest' => 'required|numeric|min:20|max:60',
            'waist' => 'required|numeric|min:20|max:50',
            'hips' => 'required|numeric|min:20|max:60',
            'shoulder' => 'required|numeric|min:10|max:30',
        ]);

        $user = Auth::user();

        $measurement = $user->user_measurements()->first();

        if ($measurement) {
            $measurement->update([
                'chest' => $request->chest,
                'waist' => $request->waist,
                'hips' => $request->hips,
                'shoulder' => $request->shoulder,
            ]);
        } else {
            $user->user_measurements()->create([
                'chest' => $request->chest,
                'waist' => $request->waist,
                'hips' => $request->hips,
                'shoulder' => $request->shoulder,
            ]);
        }

        return response()->json(['message' => 'Measurements updated successfully.']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'different:current_password',
                'confirmed',
                // Optional regex for stronger passwords:
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'new_password.different' => 'Your new password must be different from your current password.',
            'new_password.confirmed' => 'Password confirmation does not match.',
            'new_password.min' => 'Your password must be at least 8 characters long.',
        ]);

        $user = Auth::user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function requestAccountDeletion(Request $request)
    {
        $request->validate([
            'delete_confirmation' => 'required|in:DELETE',
            'delete_password' => 'required',
        ]);

        $user = Auth::user();

        // Check password
        if (!Hash::check($request->delete_password, $user->password)) {
            return back()->withErrors(['delete_password' => 'Incorrect password.']);
        }

        // Schedule deletion for 30 days from now
        $user->update([
            'deletion_requested_at' => now(),
            'scheduled_deletion_at' => now()->addDays(30),
            'deletion_reason' => $request->deletion_reason ?? 'User requested account deletion',
        ]);

        return back()->with('success', 'Your account has been scheduled for deletion in 30 days. You can cancel this request anytime before the deletion date.');
    }

    public function cancelAccountDeletion(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'deletion_requested_at' => null,
            'scheduled_deletion_at' => null,
            'deletion_reason' => null,
        ]);

        return back()->with('success', 'Your account deletion request has been cancelled.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'delete_confirmation' => 'required|in:DELETE',
            'delete_password' => 'required',
        ]);

        $user = Auth::user();

        // Check password
        if (!Hash::check($request->delete_password, $user->password)) {
            return back()->withErrors(['delete_password' => 'Incorrect password.']);
        }

        // Delete user's favorites before deleting account
        Favorites::where('user_id', $user->id)->delete();

        // Delete user
        $user->delete();

        Auth::logout();

        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,shop_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = auth()->id();

        // Count how many times this user booked from this shop
        $bookingCount = Bookings::where('user_id', $userId)
            ->whereHas('product.user.shop', function ($q) use ($request) {
                $q->where('shop_id', $request->shop_id);
            })
            ->count();

        $existing = ShopReviews::where('user_id', $userId)
            ->where('shop_id', $request->shop_id)
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your review has been updated successfully!'
                ]);
            }

            return back()->with('success', 'Your review has been updated successfully!');
        }

        ShopReviews::create([
            'user_id' => $userId,
            'shop_id' => $request->shop_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your review has been submitted successfully!'
            ]);
        }

        return back()->with('success', 'Your review has been submitted successfully!');
    }

    public function getReviewData($shopId)
    {
        $userId = auth()->id();

        $review = ShopReviews::where('user_id', $userId)
            ->where('shop_id', $shopId)
            ->first();

        // Count how many times this reviewer has booked from this shop
        $bookingCount = Bookings::where('user_id', $userId)
            ->whereHas('product', function ($q) use ($shopId) {
                $q->whereHas('user.shop', function ($subQ) use ($shopId) {
                    $subQ->where('shop_id', $shopId);
                });
            })
            ->where('status', 'Completed')
            ->count();

        return response()->json([
            'review' => $review,
            'bookingCount' => $bookingCount,
        ]);
    }

    /**
     * Remove a product from favorites
     */
    public function removeFavorite(Request $request, $productId)
    {
        $user = Auth::user();

        $favorite = Favorites::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from favorites!'
                ]);
            }

            return back()->with('success', 'Product removed from favorites!');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Favorite not found!'
            ], 404);
        }

        return back()->with('error', 'Favorite not found!');
    }
}
