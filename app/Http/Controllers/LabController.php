<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LabController extends Controller
{
    public function vulnerable(Request $request)
    {
        $q = (string) $request->query('q', '');
        $sql = "SELECT id, name, description, price FROM products "
             . "WHERE is_public = 1 AND name LIKE '%{$q}%'";
        $error = null;
        $results = [];

        try {
            $results = \Illuminate\Support\Facades\DB::select($sql);
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('lab.search', [
            'mode' => 'vulnerable', 'q' => $q, 'sql' => $sql,
            'bindings' => [], 'results' => $results, 'error' => $error,
        ]);
    }

    public function secure(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $q = $data['q'] ?? '';
        $query = Product::query()
            ->where('is_public', true)
            ->where('name', 'like', '%' . $q . '%')
            ->select(['id', 'name', 'description', 'price']);

        return view('lab.search', [
            'mode' => 'secure', 'q' => $q, 'sql' => $query->toSql(),
            'bindings' => $query->getBindings(), 'results' => $query->get(),
            'error' => null,
        ]);
    }

    public function bruteForcePage()
    {
        return view('lab.brute-force', ['mode' => 'vulnerable']);
    }

    public function secureBruteForcePage()
    {
        return view('lab.brute-force', ['mode' => 'secure']);
    }

    // Intentionally vulnerable demonstration: no attempt throttling.
    public function vulnerableLogin(Request $request)
    {
        return $this->checkDemoCredentials($request, 'vulnerable');
    }

    // Protected demonstration: this route is also limited by throttle:5,1.
    public function secureLogin(Request $request)
    {
        return $this->checkDemoCredentials($request, 'secure');
    }

    private function checkDemoCredentials(Request $request, string $mode)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();
        $success = $user && Hash::check($data['password'], $user->password);

        return redirect()
            ->route($mode === 'secure' ? 'bruteforce.secure' : 'bruteforce.vulnerable')
            ->with($success ? 'success' : 'failure',
                $success ? 'Login successful. Demo account authenticated.' :
                    'Invalid email or password.');
    }
}
