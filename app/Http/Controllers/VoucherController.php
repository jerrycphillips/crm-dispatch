<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VoucherController extends Controller
{
    /**
     * Display a listing of vouchers for accounts payable.
     * Shows vouchers where IsNull(InvoiceDate, PSFiledTime) >= DateAdd(D, -180, GetDate())
     */
    public function index()
    {
        try {
            // Check if we should use test data
            if (request()->get('test') === '1') {
                return $this->indexWithTestData();
            }

            // Use the original SQL predicate: IsNull(InvoiceDate, PSFiledTime) >= DateAdd(D, -180, GetDate())
            $vouchers = Voucher::with(['purchaseOrder', 'vendor'])
                ->whereRaw('COALESCE(InvoiceDate, PSFiledTime) >= DATEADD(day, -180, GETDATE())')
                ->orderByDesc(DB::raw('COALESCE(InvoiceDate, PSFiledTime)'))
                ->get(); // Removed limit to show all matching records

            // Clean data to ensure UTF-8 encoding and proper field mapping
            $cleanVouchers = $vouchers->map(function ($voucher) {
                return $this->cleanVoucherData($voucher);
            });

            return Inertia::render('Vouchers/Index', [
                'vouchers' => $cleanVouchers
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Voucher index error: ' . $e->getMessage());
            
            // Return empty result with error message
            return Inertia::render('Vouchers/Index', [
                'vouchers' => [],
                'error' => 'Unable to load vouchers: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Return test data for vouchers to verify the page works
     */
    private function indexWithTestData()
    {
        $testVouchers = collect([
            [
                'id' => 1,
                'packing_slip' => 'PS-2025-001.pdf',
                'invoice_date' => '2025-07-15',
                'invoice_no' => 'INV-001',
                'invoice' => 'invoice-001.pdf',
                'invoice_posted' => true,
                'purchase_order' => [
                    'ref_number' => 'PO-2025-001'
                ],
                'vendor' => [
                    'name' => 'Test Vendor Inc.'
                ]
            ],
            [
                'id' => 2,
                'packing_slip' => 'PS-2025-002.pdf',
                'invoice_date' => '2025-07-20',
                'invoice_no' => 'INV-002',
                'invoice' => 'invoice-002.pdf',
                'invoice_posted' => false,
                'purchase_order' => [
                    'ref_number' => 'PO-2025-002'
                ],
                'vendor' => [
                    'name' => 'Sample Vendor LLC'
                ]
            ]
        ]);

        return Inertia::render('Vouchers/Index', [
            'vouchers' => $testVouchers
        ]);
    }

    /**
     * API endpoint to get vouchers for AJAX requests
     */
    public function apiIndex()
    {
        try {
            $vouchers = Voucher::with(['purchaseOrder', 'vendor'])
                ->whereRaw('COALESCE(InvoiceDate, PSFiledTime) >= DATEADD(day, -180, GETDATE())')
                ->orderBy(DB::raw('COALESCE(InvoiceDate, PSFiledTime)'), 'desc')
                ->get();

            // Clean data to ensure UTF-8 encoding
            $cleanVouchers = $vouchers->map(function ($voucher) {
                return $this->cleanVoucherData($voucher);
            });

            return response()->json($cleanVouchers);
        } catch (\Exception $e) {
            // Fallback query for development/testing
            $vouchers = Voucher::with(['purchaseOrder', 'vendor'])
                ->where(function ($query) {
                    $query->where('InvoiceDate', '>=', now()->subDays(180))
                          ->orWhere('PSFiledTime', '>=', now()->subDays(180));
                })
                ->orderBy('InvoiceDate', 'desc')
                ->get();

            // Clean data to ensure UTF-8 encoding
            $cleanVouchers = $vouchers->map(function ($voucher) {
                return $this->cleanVoucherData($voucher);
            });

            return response()->json($cleanVouchers);
        }
    }

    /**
     * Show the form for creating a new voucher.
     */
    public function create()
    {
        return view('vouchers.form', ['voucher' => new Voucher()]);
    }

    /**
     * Store a newly created voucher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'PurchaseOrderId' => 'nullable|integer',
            'PackingSlip' => 'nullable|string',
            'InvoiceDate' => 'nullable|date',
            'InvoiceNo' => 'nullable|string|max:50',
            'Invoice' => 'nullable|string',
            'Vendor' => 'nullable|string|max:50',
            // Add other validation rules as needed
        ]);

        $voucher = Voucher::create($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher created successfully!');
    }

    /**
     * Display the specified voucher.
     */
    public function show(Voucher $voucher)
    {
        $voucher->load(['purchaseOrder', 'vendor']);
        return view('vouchers.show', compact('voucher'));
    }

    /**
     * Show the form for editing the specified voucher.
     */
    public function edit(Voucher $voucher)
    {
        return view('vouchers.form', compact('voucher'));
    }

    /**
     * Update the specified voucher in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'PurchaseOrderId' => 'nullable|integer',
            'PackingSlip' => 'nullable|string',
            'InvoiceDate' => 'nullable|date',
            'InvoiceNo' => 'nullable|string|max:50',
            'Invoice' => 'nullable|string',
            'Vendor' => 'nullable|string|max:50',
            // Add other validation rules as needed
        ]);

        $voucher->update($validated);

        return redirect()->back()->with('success', 'Voucher updated successfully!');
    }

    /**
     * Clean voucher data to ensure UTF-8 encoding for JSON serialization
     * and map field names to frontend expectations
     */
    private function cleanVoucherData($voucher)
    {
        $voucherArray = $voucher->toArray();
        
        // Recursively clean all string values
        $cleanedArray = $this->cleanArrayData($voucherArray);
        
        // Map database field names to frontend expectations
        $mappedVoucher = [
            'id' => $cleanedArray['VoucherId'] ?? null,
            'purchase_order_id' => $cleanedArray['PurchaseOrderId'] ?? null,
            'packing_slip' => $cleanedArray['PackingSlip'] ?? null,
            'ps_filed_by' => $cleanedArray['PSFiledBy'] ?? null,
            'ps_filed_time' => $cleanedArray['PSFiledTime'] ?? null,
            'ps_signature_name' => $cleanedArray['PSSignatureName'] ?? null,
            'ps_posted' => $cleanedArray['PSPosted'] ?? null,
            'vendor_id' => $cleanedArray['Vendor'] ?? null,
            'invoice' => $cleanedArray['Invoice'] ?? null,
            'invoice_filed_by' => $cleanedArray['InvoiceFiledBy'] ?? null,
            'invoice_filed_time' => $cleanedArray['InvoiceFiledTime'] ?? null,
            'invoice_posted' => $cleanedArray['InvoicePosted'] ?? null,
            'invoice_posted_by' => $cleanedArray['InvoicePostedBy'] ?? null,
            'invoice_posted_time' => $cleanedArray['InvoicePostedTime'] ?? null,
            'invoice_date' => $cleanedArray['InvoiceDate'] ?? null,
            'invoice_no' => $cleanedArray['InvoiceNo'] ?? null,
            'invoice_total' => $cleanedArray['InvoiceTotal'] ?? null,
            'payment_posted' => $cleanedArray['PaymentPosted'] ?? null,
            'paid_date' => $cleanedArray['PaidDate'] ?? null,
            'check_no' => $cleanedArray['CheckNo'] ?? null,
        ];
        
        // Map relationships
        if (isset($cleanedArray['purchase_order'])) {
            $mappedVoucher['purchase_order'] = [
                'id' => $cleanedArray['purchase_order']['PurchaseOrder'] ?? null,
                'ref_number' => $cleanedArray['purchase_order']['RefNumber'] ?? null,
                'purchase_order_no' => $cleanedArray['purchase_order']['PurchaseOrderNo'] ?? null,
                'date' => $cleanedArray['purchase_order']['Date'] ?? null,
                'work_order_num' => $cleanedArray['purchase_order']['WorkOrderNum'] ?? null,
                'vendor_name' => $cleanedArray['purchase_order']['VendorName'] ?? null,
            ];
        }
        
        if (isset($cleanedArray['vendor'])) {
            $mappedVoucher['vendor'] = [
                'list_id' => $cleanedArray['vendor']['ListID'] ?? null,
                'name' => $cleanedArray['vendor']['Name'] ?? null,
                'company_name' => $cleanedArray['vendor']['CompanyName'] ?? null,
                'phone' => $cleanedArray['vendor']['Phone'] ?? null,
                'email' => $cleanedArray['vendor']['Email'] ?? null,
                'account_number' => $cleanedArray['vendor']['AccountNumber'] ?? null,
            ];
        }
        
        return $mappedVoucher;
    }

    /**
     * Recursively clean array data to ensure UTF-8 encoding
     */
    private function cleanArrayData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanArrayData($value);
            }
        } elseif (is_string($data)) {
            // More aggressive cleaning for malformed UTF-8
            if (!mb_check_encoding($data, 'UTF-8')) {
                // Try to convert from common encodings
                $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
                foreach ($encodings as $encoding) {
                    $converted = @mb_convert_encoding($data, 'UTF-8', $encoding);
                    if (mb_check_encoding($converted, 'UTF-8')) {
                        return $converted;
                    }
                }
                // If all else fails, remove non-UTF-8 characters
                return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
            return $data;
        }
        
        return $data;
    }
}
