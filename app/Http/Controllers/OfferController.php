<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Create a new offer.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric',
            'specialization' => 'required|string|max:255',
        ]);

        $offer = Offer::create(array_merge($validated, ['user_id' => auth()->id()]));

        return response()->json([
            'message' => 'Offer created successfully',
            'offer' => $offer,
        ], 201);
    }

    /**
     * Update an existing offer.
     */
    public function update(Request $request, $id)
    {
        $offer = Offer::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'cost' => 'numeric',
            'specialization' => 'string|max:255',
        ]);

        $offer->update($validated);

        return response()->json([
            'message' => 'Offer updated successfully',
            'offer' => $offer,
        ]);
    }

    /**
     * Delete an offer.
     */
    public function delete($id)
    {
        $offer = Offer::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $offer->delete();

        return response()->json(['message' => 'Offer deleted successfully']);
    }

    /**
     * Get a specific offer.
     */
    public function get($id)
    {
        $offer = Offer::findOrFail($id);

        return response()->json($offer);
    }

    /**
     * List all offers.
     */
    public function list()
    {
        $offers = Offer::paginate(10);

        return response()->json($offers);
    }

    /**
     * List offers created by the authenticated user.
     */
    public function listMyOffers()
    {
        $offers = Offer::where('user_id', auth()->id())->paginate(10);

        return response()->json($offers);
    }
}

