<?php

namespace Webkul\FieldSales\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\Product\Models\Product;
use Webkul\FieldSales\Models\Order;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ImportExportController extends Controller
{
    /**
     * Show Import/Export Dashboard.
     */
    public function index()
    {
        return view('field_sales::import_export.index');
    }

    /**
     * Export Orders to CSV.
     */
    public function exportOrders()
    {
        $orders = Order::with(['user', 'person', 'dispatcher'])->get();

        $filename = "orders_export_" . date('Y-m-d_H-i') . ".csv";
        $handle = fopen('php://output', 'w');

        // Headers
        ob_start();
        fputcsv($handle, ['Order ID', 'Date', 'Customer', 'Agent', 'Type', 'Amount', 'Status', 'Dispatcher']);

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->id,
                $order->created_at->format('Y-m-d'),
                $order->person->name ?? 'N/A',
                $order->user->name ?? 'N/A',
                $order->type,
                $order->grand_total,
                $order->status,
                $order->dispatcher->name ?? 'Unassigned'
            ]);
        }

        fclose($handle);
        $content = ob_get_clean();

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Import Products from CSV.
     * Simple implementation: matches by SKU, updates or creates.
     */
    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        // Skip header
        fgetcsv($handle);

        $imported = 0;
        $companyId = auth()->user()->company_id;

        while (($data = fgetcsv($handle)) !== FALSE) {
            // Expected format: Name, SKU, Price, Description, Quantity
            if (count($data) < 3)
                continue;

            $name = $data[0];
            $sku = $data[1];
            $price = $data[2];
            $description = $data[3] ?? '';
            $quantity = $data[4] ?? 0;

            if (!$sku)
                continue;

            Product::updateOrCreate(
                ['sku' => $sku, 'company_id' => $companyId],
                [
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'quantity' => $quantity,
                    'status' => 1
                ]
            );
            $imported++;
        }

        fclose($handle);

        session()->flash('success', "Successfully imported/updated $imported products.");
        return redirect()->back();
    }
}
