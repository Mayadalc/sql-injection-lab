<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabController extends Controller
{
    /**
     * نسخة ضعيفة (للعرض التعليمي فقط، لا تستعملوها في مشروع حقيقي).
     * المدخل $q يُدمج مباشرة داخل نص SQL، فيستطيع المهاجم تغيير بنية الاستعلام.
     */
    public function vulnerable(Request $request)
    {
        $q = (string) $request->query('q', '');

        // ثغرة: دمج مدخل المستخدم في النص بدل استعمال الربط (binding)
        $sql = "SELECT id, name, description, price FROM products "
             . "WHERE is_public = 1 AND name LIKE '%{$q}%'";

        $error = null;
        $results = [];

        try {
            $results = DB::select($sql);
        } catch (\Throwable $e) {
            // عرض الخطأ للمستخدم يسرّب بنية القاعدة (ثغرة إضافية: Information Disclosure)
            $error = $e->getMessage();
        }

        return view('lab.search', [
            'mode' => 'vulnerable',
            'q' => $q,
            'sql' => $sql,
            'bindings' => [],
            'results' => $results,
            'error' => $error,
        ]);
    }

    /**
     * نسخة مصلحة: تحقق من المدخل + Eloquent (ربط تلقائي للمعاملات عبر PDO).
     */
    public function secure(Request $request)
    {
        // 1) التحقق من صحة المدخل (validation)
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $q = $data['q'] ?? '';

        // 2) استعلام مُعَدّ مسبقاً: المدخل يُمرَّر كقيمة وليس كجزء من الكود
        $query = Product::query()
            ->where('is_public', true)
            ->where('name', 'like', '%' . $q . '%')
            ->select(['id', 'name', 'description', 'price']);

        return view('lab.search', [
            'mode' => 'secure',
            'q' => $q,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'results' => $query->get(),
            'error' => null,
        ]);
    }
}
