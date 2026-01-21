<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\SAAS\Models\SaasPackage;

class SeedSaasPlans extends Seeder
{
    public function run()
    {
        // 1. Basic Plan (15000/year, 20 Users, Basic Features)
        SaasPackage::withoutGlobalScopes()->updateOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Basic Plan',
                'price' => 15000.00,
                'included_users' => 20,
                'extra_user_price' => 0,
                'features' => [
                    'location_tracking',
                    'visit_management',
                    'attendance',
                    'leave_management'
                ],
                'status' => 1
            ]
        );

        // 2. FFT (Field Force Tracking)
        SaasPackage::withoutGlobalScopes()->updateOrCreate(
            ['slug' => 'fft'],
            [
                'name' => 'Field Force Tracking (FFT)',
                'price' => 10000.00,
                'included_users' => 6,
                'extra_user_price' => 2500.00,
                'features' => [
                    'location_tracking',
                    'visit_management',
                    'attendance',
                    'leave_management',
                    'geo_fencing',
                    'user_hierarchy',
                    'expense_management',
                    'custom_fields'
                ],
                'status' => 1
            ]
        );

        // 3. SFA (Sales Force Automation)
        SaasPackage::withoutGlobalScopes()->updateOrCreate(
            ['slug' => 'sfa'],
            [
                'name' => 'Sales Force Automation (SFA)',
                'price' => 25000.00,
                'included_users' => 3,
                'extra_user_price' => 4500.00,
                'features' => [
                    'location_tracking',
                    'visit_management',
                    'attendance',
                    'leave_management',
                    'geo_fencing',
                    'user_hierarchy',
                    'expense_management',
                    'custom_fields',
                    'unlimited_products',
                    'customer_hierarchy',
                    'order_processing',
                    'payment_collection',
                    'stock_management',
                    'route_planning',
                    'goal_setting',
                    'news_notifications',
                    'reimbursement',
                    'auto_sms_mail'
                ],
                'status' => 1
            ]
        );
    }
}
