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
                $name = $this->generateRealisticName($customer);

                return [
                    'id' => $customer->id,
                    'name' => $name,
                    'email' => $customer->email,
                    'type' => $customer->type,
                    'loyalty_tier' => $customer->loyalty_tier,
                    'orders_count' => $customer->orders_count,
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

    private function generateRealisticName($customer)
    {
        $restaurantNames = [
            'Green Garden Restaurant',
            'The Golden Spoon',
            'Sunset Bistro',
            'Royal Palace Dining',
            'Fresh Market Cafe'
        ];

        $retailNames = [
            'TechCorp Solutions',
            'Modern Retail Co.',
            'Prime Business Group',
            'Elite Commerce Ltd.',
            'Global Trade Partners'
        ];

        if ($customer->type === 'restaurants') {
            if (strpos($customer->email, 'apple') !== false) {
                return 'Apple Tree Restaurant';
            } elseif (strpos($customer->email, 'diner') !== false) {
                return 'Carol\'s Diner';
            } else {
                return $restaurantNames[($customer->id - 1) % count($restaurantNames)];
            }
        } else {
            if (strpos($customer->email, 'techcorp') !== false) {
                return 'TechCorp Solutions';
            } else {
                return $retailNames[($customer->id - 1) % count($retailNames)];
            }
        }
    }
}
