<?php

namespace App\Services;

class DataService
{
    public static function getDepartmentList(): array
    {
        return [
            'itsm' => 'ITSM, Compliance & Risk Mgmt',
            'ecommerce' => 'E-Commerce & CRM',
            'inventory' => 'Inventory & Warehouse',
            'manufacturing' => 'Manufacturing & Productions',
            'bi' => 'Business Intelligence & Analytics',
            'procurement' => 'Procurement',
            'finance' => 'Finance & Accounting',
            'fulfillment' => 'Order Fulfillment',
            'hr' => 'Human Resources',
        ];
    }
    public static function getDepartment(string $dept): array
    {
        return [
            'title' => $dept,
            'desc' => '',
            'stats' => [],
            'leftTitle' => 'Chart 1',
            'rightTitle' => 'Chart 2',
            'bottomCards' => [],
        ];
    }
    public static function getSalesForecast(): array
    {
        return [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'sales' => [0, 0, 0, 0, 0, 0, 0],
            'forecast' => [0, 0, 0, 0, 0, 0, 0],
        ];
    }
    public static function getForecastBoxes(): array
    {
        return [
            ['icon' => 'clock', 'label' => 'Forecast Accuracy', 'value' => '0%', 'change' => '0%', 'change_class' => 'change-up'],
            ['icon' => 'star', 'label' => 'High Demand Products', 'value' => '0', 'change' => '0', 'change_class' => 'change-up'],
            ['icon' => 'trending-up', 'label' => 'Revenue Growth', 'value' => '0%', 'change' => '0%', 'change_class' => 'change-up'],
        ];
    }
    public static function getAiResponse(string $message): string
    {
        return 'AI module is ready. Ask me about your business data.';
    }
    private static function formatCurrency($amount): string
    {
        return '₱' . number_format($amount, 0);
    }
}