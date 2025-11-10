<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ===================== INCOME =====================
            ['name' => 'Salary', 'icon' => 'bx bx-briefcase', 'color' => '#4caf50', 'type' => 'income'],
            ['name' => 'Freelance', 'icon' => 'bx bx-laptop', 'color' => '#00c853', 'type' => 'income'],
            ['name' => 'Business Profit', 'icon' => 'bx bx-line-chart', 'color' => '#2ecc71', 'type' => 'income'],
            ['name' => 'Rental Income', 'icon' => 'bx bx-home-smile', 'color' => '#26a69a', 'type' => 'income'],
            ['name' => 'Interest Income', 'icon' => 'bx bx-coin', 'color' => '#7cb342', 'type' => 'income'],
            ['name' => 'Dividends', 'icon' => 'bx bx-chart', 'color' => '#5e35b1', 'type' => 'income'],
            ['name' => 'Gifts Received', 'icon' => 'bx bx-gift', 'color' => '#ab47bc', 'type' => 'income'],
            ['name' => 'Bonus', 'icon' => 'bx bx-money', 'color' => '#6a1b9a', 'type' => 'income'],
            ['name' => 'Refunds', 'icon' => 'bx bx-dollar-circle', 'color' => '#00897b', 'type' => 'income'],
            ['name' => 'Other Income', 'icon' => 'bx bx-wallet', 'color' => '#43a047', 'type' => 'income'],

            // ===================== EXPENSE =====================
            ['name' => 'Groceries', 'icon' => 'bx bx-cart', 'color' => '#ff5722', 'type' => 'expense'],
            ['name' => 'Restaurants', 'icon' => 'bx bx-restaurant', 'color' => '#f44336', 'type' => 'expense'],
            ['name' => 'Rent / Mortgage', 'icon' => 'bx bx-building-house', 'color' => '#d81b60', 'type' => 'expense'],
            ['name' => 'Utilities', 'icon' => 'bx bx-bulb', 'color' => '#e91e63', 'type' => 'expense'],
            ['name' => 'Transportation', 'icon' => 'bx bx-car', 'color' => '#9c27b0', 'type' => 'expense'],
            ['name' => 'Fuel', 'icon' => 'bx bx-gas-pump', 'color' => '#8e24aa', 'type' => 'expense'],
            ['name' => 'Internet', 'icon' => 'bx bx-wifi', 'color' => '#7b1fa2', 'type' => 'expense'],
            ['name' => 'Mobile Recharge', 'icon' => 'bx bx-mobile', 'color' => '#6a1b9a', 'type' => 'expense'],
            ['name' => 'Insurance', 'icon' => 'bx bx-shield', 'color' => '#ad1457', 'type' => 'expense'],
            ['name' => 'Healthcare', 'icon' => 'bx bx-plus-medical', 'color' => '#c2185b', 'type' => 'expense'],
            ['name' => 'Medicines', 'icon' => 'bx bx-capsule', 'color' => '#d32f2f', 'type' => 'expense'],
            ['name' => 'Fitness', 'icon' => 'bx bx-dumbbell', 'color' => '#f4511e', 'type' => 'expense'],
            ['name' => 'Education', 'icon' => 'bx bx-book', 'color' => '#fb8c00', 'type' => 'expense'],
            ['name' => 'Child Care', 'icon' => 'bx bx-child', 'color' => '#ff6f00', 'type' => 'expense'],
            ['name' => 'Subscriptions', 'icon' => 'bx bx-purchase-tag', 'color' => '#ff9800', 'type' => 'expense'],
            ['name' => 'Entertainment', 'icon' => 'bx bx-movie-play', 'color' => '#f57c00', 'type' => 'expense'],
            ['name' => 'Shopping', 'icon' => 'bx bx-shopping-bag', 'color' => '#ef6c00', 'type' => 'expense'],
            ['name' => 'Clothing', 'icon' => 'bx bx-t-shirt', 'color' => '#e65100', 'type' => 'expense'],
            ['name' => 'Travel', 'icon' => 'bx bx-plane', 'color' => '#a1887f', 'type' => 'expense'],
            ['name' => 'Pet Care', 'icon' => 'bx bx-bone', 'color' => '#795548', 'type' => 'expense'],
            ['name' => 'Charity', 'icon' => 'bx bx-donate-heart', 'color' => '#6d4c41', 'type' => 'expense'],
            ['name' => 'Loan EMI', 'icon' => 'bx bx-credit-card', 'color' => '#5d4037', 'type' => 'expense'],
            ['name' => 'Tax', 'icon' => 'bx bx-file', 'color' => '#4e342e', 'type' => 'expense'],
            ['name' => 'Alcohol & Smoking', 'icon' => 'bx bx-wine', 'color' => '#3e2723', 'type' => 'expense'],
            ['name' => 'Beauty & Salon', 'icon' => 'bx bx-brush', 'color' => '#bf360c', 'type' => 'expense'],
            ['name' => 'Home Maintenance', 'icon' => 'bx bx-home', 'color' => '#ff7043', 'type' => 'expense'],
            ['name' => 'Office Supplies', 'icon' => 'bx bx-folder', 'color' => '#ffa726', 'type' => 'expense'],
            ['name' => 'Other Expense', 'icon' => 'bx bx-receipt', 'color' => '#ffb74d', 'type' => 'expense'],

            // ===================== INVESTMENT =====================
            ['name' => 'Mutual Funds', 'icon' => 'bx bx-chart', 'color' => '#1e88e5', 'type' => 'investment'],
            ['name' => 'Stocks', 'icon' => 'bx bx-trending-up', 'color' => '#1976d2', 'type' => 'investment'],
            ['name' => 'Fixed Deposit', 'icon' => 'bx bx-lock', 'color' => '#1565c0', 'type' => 'investment'],
            ['name' => 'Public Provident Fund', 'icon' => 'bx bx-bank', 'color' => '#0d47a1', 'type' => 'investment'],
            ['name' => 'Gold', 'icon' => 'bx bx-crown', 'color' => '#42a5f5', 'type' => 'investment'],
            ['name' => 'Real Estate', 'icon' => 'bx bx-building', 'color' => '#64b5f6', 'type' => 'investment'],
            ['name' => 'Crypto', 'icon' => 'bx bxl-bitcoin', 'color' => '#90caf9', 'type' => 'investment'],
            ['name' => 'Retirement Fund', 'icon' => 'bx bx-calendar-star', 'color' => '#bbdefb', 'type' => 'investment'],
            ['name' => 'SIP', 'icon' => 'bx bx-pie-chart-alt', 'color' => '#2196f3', 'type' => 'investment'],
            ['name' => 'Bonds', 'icon' => 'bx bx-file-find', 'color' => '#0d47a1', 'type' => 'investment'],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, [
                'user_id' => null,
                'is_ai_suggested' => false,
                'status' => 'active'
            ]));
        }
    }
}
