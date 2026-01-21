<?php

namespace Webkul\SAAS\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\SAAS\Models\SaasPackage;
use Webkul\SAAS\Models\SaasSubscription;
use Carbon\Carbon;

class BillingController extends Controller
{
    protected $api;

    public function __construct()
    {
        // $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function index()
    {
        $packages = SaasPackage::where('status', 1)->get();
        return view('saas::billing.index', compact('packages'));
    }

    public function checkout($packageId)
    {
        $package = SaasPackage::findOrFail($packageId);
        $user = auth()->user();

        // Simple fixed price calculation for now
        $amount = $package->price * 100; // in paise

        // This part would normally create an Order in Razorpay
        // $orderData = [
        //     'receipt'         => 'rcptid_' . time(),
        //     'amount'          => $amount,
        //     'currency'        => 'INR',
        //     'payment_capture' => 1
        // ];
        // $razorpayOrder = $this->api->order->create($orderData);

        return view('saas::billing.checkout', compact('package', 'user', 'amount'));
    }

    public function verify(Request $request)
    {
        // Verify signature here using Razorpay SDK

        // On success:
        $packageId = $request->input('package_id');
        $package = SaasPackage::findOrFail($packageId);

        SaasSubscription::updateOrCreate(
            ['company_id' => auth()->user()->company_id],
            [
                'package_id' => $package->id,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addYear(),
                'max_users' => $package->included_users,
                'status' => 'active',
                'amount_paid' => $package->price,
                'razorpay_subscription_id' => $request->input('razorpay_payment_id')
            ]
        );

        return redirect()->route('admin.dashboard.index')->with('success', 'Subscription activated!');
    }
}
