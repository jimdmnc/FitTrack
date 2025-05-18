<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;
use Illuminate\Support\Facades\Validator;

class PriceController extends Controller
{
    /**
     * Display the pricing management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $prices = Price::orderBy('id')->get();
        return view('pricelist.index', compact('prices'));
    }

    /**
     * Update the specified prices in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($request->prices as $id => $amount) {
            $price = Price::find($id);
            if ($price) {
                $price->amount = $amount;
                $price->save();
            }
        }

        return back()->with('success', 'Prices updated successfully!');
    }

    /**
     * Display the pricing list for user profile.
     *
     * @return \Illuminate\View\View
     */
    public function pricelist()
    {
        $prices = Price::orderBy('id')->get();
        return view('profile.pricelist', compact('prices'));
    }
    
    /**
     * Update prices from the profile section.
     * This is an alias to the update method to maintain route consistency.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePrice(Request $request)
    {
        // Simply call the existing update method
        return $this->update($request);
    }

    public function showRegistration()
    {
        // Get the session price (or fail if not found)
        $sessionPrice = Price::where('type', 'session')->firstOrFail();
        
        return view('self.registration', [
            'sessionPrice' => $sessionPrice
        ]);
    }

}