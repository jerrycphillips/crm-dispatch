<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of unposted purchase orders.
     * This implements the SQL query to show POs from the last 45 days that are not posted to QB.
     */
    public function unpostedIndex()
    {
        try {
            // Check if we should use test data
            if (request()->get('test') === '1') {
                return $this->unpostedIndexWithTestData();
            }

            // Execute the SQL query for unposted POs
            $unpostedPOs = DB::select("
                WITH po_cte AS (
        SELECT P.PurchaseOrder, SUM(P.Quantity * P.UnitCost) AS total
        FROM PurchaseOrder po
        INNER JOIN PODetails P ON po.PurchaseOrder = P.PurchaseOrder
        WHERE po.Date BETWEEN DATEADD(day, -45, CONVERT(date, GETDATE()))
                        AND DATEADD(day, 1, CONVERT(date, GETDATE()))
            AND po.IsToBePrinted = 0
        GROUP BY P.PurchaseOrder
    ),
    ps_cte AS (
        SELECT a.PurchaseOrderId, COUNT(*) AS PackingSlips
        FROM APVouchers a
        WHERE a.PackingSlip IS NOT NULL
        GROUP BY PurchaseOrderId
    ),
    inv_cte AS (
        SELECT PurchaseOrderId, COUNT(*) AS Bills
        FROM APVouchers a
        WHERE a.Invoice IS NOT NULL
        GROUP BY PurchaseOrderId
    )
    SELECT po.PurchaseOrder, v.Name, RefNumber, po.Date, 
           FORMAT(total, 'C') AS total,
           ISNULL(PackingSlips, 0) AS PackingSlips,
           ISNULL(Bills, 0) AS Bills
    FROM PurchaseOrder po
    INNER JOIN Vendor v ON po.VendorQBID = v.ListID
    INNER JOIN po_cte ON po.PurchaseOrder = po_cte.PurchaseOrder
    LEFT JOIN ps_cte ON po.PurchaseOrder = ps_cte.PurchaseOrderId
    LEFT JOIN inv_cte ON po.PurchaseOrder = inv_cte.PurchaseOrderId
    WHERE po.PostedToQB = 0
      AND po.Date BETWEEN DATEADD(day, -45, CONVERT(date, GETDATE()))
                      AND DATEADD(day, 1, CONVERT(date, GETDATE()))
      AND po.IsToBePrinted = 0
    ORDER BY po.Date
            ");

            // Convert to collection and clean the data
            $cleanUnpostedPOs = collect($unpostedPOs)->map(function ($po) {
                return [
                    'PurchaseOrder' => $this->cleanString((string) $po->PurchaseOrder),
                    'Name' => $this->cleanString($po->Name ?? ''),
                    'RefNumber' => $this->cleanString($po->RefNumber ?? ''),
                    'Date' => $po->Date ? date('Y-m-d', strtotime($po->Date)) : '',
                    'total' => $this->cleanString($po->total ?? '$0.00'),
                    'PackingSlips' => (int) ($po->PackingSlips ?? 0),
                    'Bills' => (int) ($po->Bills ?? 0),
                ];
            });

            // Additional validation to ensure all strings are JSON safe
            $safeUnpostedPOs = $cleanUnpostedPOs->map(function ($po) {
                return [
                    'PurchaseOrder' => json_encode($po['PurchaseOrder']) !== false ? $po['PurchaseOrder'] : 'Invalid',
                    'Name' => json_encode($po['Name']) !== false ? $po['Name'] : 'Invalid Name',
                    'RefNumber' => json_encode($po['RefNumber']) !== false ? $po['RefNumber'] : 'Invalid Ref',
                    'Date' => $po['Date'],
                    'total' => json_encode($po['total']) !== false ? $po['total'] : '$0.00',
                    'PackingSlips' => $po['PackingSlips'],
                    'Bills' => $po['Bills'],
                ];
            });

            return Inertia::render('PurchaseOrders/Unposted', [
                'unpostedPOs' => $safeUnpostedPOs,
                'auth' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'Jerry Phillips',
                        'email' => 'jerry@example.com',
                        'first_name' => 'Jerry',
                        'last_name' => 'Phillips',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Unposted POs index error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'error' => 'Unable to fetch unposted purchase orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test data version for debugging UTF-8 issues
     */
    private function unpostedIndexWithTestData()
    {
        $testData = [
            [
                'PurchaseOrder' => '12345',
                'Name' => 'Test Vendor 1',
                'RefNumber' => 'PO-2025-001',
                'Date' => '2025-07-15',
                'total' => '$1,234.56',
                'PackingSlips' => 2,
                'Bills' => 1,
            ],
            [
                'PurchaseOrder' => '12346',
                'Name' => 'Test Vendor 2',
                'RefNumber' => 'PO-2025-002',
                'Date' => '2025-07-20',
                'total' => '$2,567.89',
                'PackingSlips' => 0,
                'Bills' => 0,
            ],
            [
                'PurchaseOrder' => '12347',
                'Name' => 'Test Vendor 3',
                'RefNumber' => 'PO-2025-003',
                'Date' => '2025-07-25',
                'total' => '$987.65',
                'PackingSlips' => 1,
                'Bills' => 0,
            ],
        ];

        return Inertia::render('PurchaseOrders/Unposted', [
            'unpostedPOs' => $testData,
            'auth' => [
                'user' => [
                    'id' => 1,
                    'name' => 'Jerry Phillips',
                    'email' => 'jerry@example.com',
                    'first_name' => 'Jerry',
                    'last_name' => 'Phillips',
                ]
            ]
        ]);
    }

    /**
     * API endpoint for unposted purchase orders (for AJAX requests)
     */
    public function apiUnpostedIndex()
    {
        try {
            // Execute the SQL query for unposted POs
            $unpostedPOs = DB::select("
                DECLARE @StartDate AS DATETIME, @EndDate AS DATETIME
                
                SET @StartDate = dateadd(day, -45, convert(date, getdate()))
                SET @EndDate = dateadd(day, 1, convert(date, getdate()))
                
                WITH po_cte AS 
                (
                    SELECT P.PurchaseOrder, SUM(P.Quantity*P.UnitCost) total
                    FROM PurchaseOrder po INNER JOIN PODetails P ON po.PurchaseOrder = P.PurchaseOrder 
                    WHERE po.Date BETWEEN @StartDate AND @EndDate AND po.IsToBePrinted = 0  
                    GROUP BY P.PurchaseOrder
                ),
                ps_cte AS
                (
                    SELECT a.PurchaseOrderId, COUNT(*) PackingSlips 
                    FROM APVouchers a 
                    WHERE a.PackingSlip IS NOT null 
                    GROUP BY PurchaseOrderId
                ),
                inv_cte AS
                (
                    SELECT PurchaseOrderid, COUNT(*) Bills 
                    FROM APVouchers a 
                    WHERE a.Invoice IS NOT null 
                    GROUP BY PurchaseOrderId
                )
                SELECT po.PurchaseOrder, v.Name, RefNumber, po.Date, FORMAT(total, 'C') total, ISNULL(PackingSlips, 0) PackingSlips, ISNULL(Bills, 0) Bills 
                FROM PurchaseOrder po 
                INNER JOIN Vendor v ON po.VendorQBID = v.ListID 
                INNER JOIN po_cte ON po.PurchaseOrder = po_cte.PurchaseOrder
                LEFT JOIN ps_cte ON po.PurchaseOrder = ps_cte.PurchaseOrderId
                LEFT JOIN inv_cte ON po.PurchaseOrder = inv_cte.PurchaseOrderId
                WHERE po.PostedToQB = 0 AND po.Date BETWEEN @StartDate AND @EndDate AND po.IsToBePrinted = 0
                ORDER BY po.Date
            ");

            // Convert to collection and clean the data
            $cleanUnpostedPOs = collect($unpostedPOs)->map(function ($po) {
                return [
                    'PurchaseOrder' => $this->cleanString((string) $po->PurchaseOrder),
                    'Name' => $this->cleanString($po->Name ?? ''),
                    'RefNumber' => $this->cleanString($po->RefNumber ?? ''),
                    'Date' => $po->Date ? date('Y-m-d', strtotime($po->Date)) : '',
                    'total' => $this->cleanString($po->total ?? '$0.00'),
                    'PackingSlips' => (int) ($po->PackingSlips ?? 0),
                    'Bills' => (int) ($po->Bills ?? 0),
                ];
            });

            // Additional validation to ensure all strings are JSON safe
            $safeUnpostedPOs = $cleanUnpostedPOs->map(function ($po) {
                return [
                    'PurchaseOrder' => json_encode($po['PurchaseOrder']) !== false ? $po['PurchaseOrder'] : 'Invalid',
                    'Name' => json_encode($po['Name']) !== false ? $po['Name'] : 'Invalid Name',
                    'RefNumber' => json_encode($po['RefNumber']) !== false ? $po['RefNumber'] : 'Invalid Ref',
                    'Date' => $po['Date'],
                    'total' => json_encode($po['total']) !== false ? $po['total'] : '$0.00',
                    'PackingSlips' => $po['PackingSlips'],
                    'Bills' => $po['Bills'],
                ];
            });

            return response()->json([
                'unpostedPOs' => $safeUnpostedPOs
            ]);
        } catch (\Exception $e) {
            \Log::error('API Unposted POs error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Unable to fetch unposted purchase orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean string data to ensure proper encoding and remove problematic characters
     */
    private function cleanString($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        // Convert to string if not already
        $value = (string) $value;
        
        // Remove null bytes and other problematic characters
        $value = str_replace(["\x00", "\x1A", "\x0B", "\x0C", "\x1C", "\x1D", "\x1E", "\x1F"], '', $value);
        
        // Remove or replace special characters that can cause JSON encoding issues
        $value = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '', $value);
        
        // Ensure proper UTF-8 encoding
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');
        }
        
        // If still not valid UTF-8, force convert
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = utf8_encode($value);
        }
        
        return trim($value);
    }
}
