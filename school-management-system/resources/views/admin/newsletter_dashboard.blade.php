
@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">📬 Newsletter Job Dashboard</h2>
    <ul class="list-disc ml-6 text-lg">
        <li>✅ Sent: <strong>{{ $sent }}</strong></li>
        <li>❌ Failed: <strong>{{ $failed }}</strong></li>
        <li>🕒 Pending: <strong>{{ $pending }}</strong></li>
    </ul>
</div>
@endsection
