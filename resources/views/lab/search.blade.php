
<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SQL Injection Lab</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; color: #1f2937; }
        .badge { display: inline-block; padding: .25rem .75rem; border-radius: 999px; color: #fff; font-weight: 600; }
        .vulnerable { background: #b91c1c; }
        .secure { background: #15803d; }
        form { margin: 1rem 0; display: flex; gap: .5rem; }
        input[type=text] { flex: 1; padding: .5rem; direction: ltr; }
        button { padding: .5rem 1rem; }
        pre { background: #f3f4f6; padding: .75rem; border-radius: 6px; direction: ltr; text-align: left; white-space: pre-wrap; word-break: break-all; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; direction: ltr; }
        th, td { border: 1px solid #d1d5db; padding: .4rem .6rem; text-align: left; }
        .error { background: #fee2e2; color: #7f1d1d; padding: .75rem; border-radius: 6px; direction: ltr; text-align: left; }
        nav a { margin-inline-end: 1rem; }
    </style>
</head>
<body>
    <h1>SQL Injection Lab</h1>

    <nav>
        <a href="/vulnerable/search">Vulnerable Page</a>
        <a href="/secure/search">Patched Page</a>
        <a href="/brute-force">Brute Force Lab</a>
    </nav>

    <p>
        Current Status:
        @if ($mode === 'vulnerable')
            <span class="badge vulnerable">Vulnerable (Before Fix)</span>
        @else
            <span class="badge secure">Secure (After Fix)</span>
        @endif
    </p>

    <form method="GET" action="{{ $mode === 'vulnerable' ? '/vulnerable/search' : '/secure/search' }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search for a product, e.g., Laptop">
        <button type="submit">Search</button>
    </form>

    @error('q')
        <div class="error">{{ $message }}</div>
    @enderror

    <h3>Actual Query Executed</h3>
    <pre>{{ $sql }}</pre>

    @if (!empty($bindings))
        <h3>Bound Parameters (Bindings)</h3>
        <pre>{{ json_encode($bindings, JSON_UNESCAPED_UNICODE) }}</pre>
    @endif

    @if ($error)
        <div class="error">{{ $error }}</div>
    @endif

    <h3>Results ({{ count($results) }})</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->price }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No results found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
