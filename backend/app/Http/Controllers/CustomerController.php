<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\ResponseTrait;


class CustomerController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $customers = Customer::all();

            // Transform the data to match frontend expectations
            $transformedCustomers = $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => ucfirst($customer->type) . ' Customer #' . $customer->id,
                    'email' => $customer->email,
                    'type' => $customer->type,
                    'loyaltyTier' => $customer->loyalty_tier,
                    'ordersCount' => $customer->orders_count,
                    'city' => $customer->city,
                ];
            });

            return $this->successResponse($transformedCustomers, 'Customers fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch customers', 500);
        }
    }

    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return $this->successResponse($customer, 'Customer fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Customer not found', 404);
        }
    }
}
