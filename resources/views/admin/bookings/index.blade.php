@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Bookings Management</h1>

        <div class="flex items-center gap-2">
            <form method="GET" action="" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users or events" class="rounded border-gray-300 px-3 py-2" />
                <select name="status" class="rounded border-gray-300 px-2 py-2">
                    <option value="">All statuses</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
                <select name="checked_in" class="rounded border-gray-300 px-2 py-2">
                    <option value="">Checked In?</option>
                    <option value="1" {{ request('checked_in')==='1'?'selected':'' }}>Yes</option>
                    <option value="0" {{ request('checked_in')==='0'?'selected':'' }}>No</option>
                </select>
                <select name="event_id" class="rounded border-gray-300 px-2 py-2">
                    <option value="">All events</option>
                    @foreach($eventsList ?? [] as $id => $title)
                        <option value="{{ $id }}" {{ request('event_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
                <x-button variant="primary" icon="filter" type="submit">Filter</x-button>
            </form>

            <a href="{{ route('admin.bookings.export', request()->query()) }}" class="ml-2">
                <x-button variant="secondary" icon="csv">Export CSV</x-button>
            </a>
        </div>
    </div>

    <form action="{{ route('admin.bookings.bulk_checkin') }}" method="POST" id="bulk-checkin-form">
        @csrf
        <div class="bg-white shadow rounded overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" /></th>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Event</th>
                        <th class="px-6 py-3 text-left">User</th>
                        <th class="px-6 py-3 text-left">Ticket</th>
                        <th class="px-6 py-3 text-left">Qty</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Checked In</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($bookings as $b)
                        <tr class="border-t">
                            <td class="px-4 py-4"><input type="checkbox" name="ids[]" value="{{ $b->id }}" class="select-row" {{ $b->checked_in ? 'disabled' : '' }} /></td>
                            <td class="px-6 py-4">#{{ $b->id }}</td>
                            <td class="px-6 py-4">{{ $b->event->title }}</td>
                            <td class="px-6 py-4">{{ $b->user->name }} ({{ $b->user->email }})</td>
                            <td class="px-6 py-4">{{ $b->ticket->name }}</td>
                            <td class="px-6 py-4">{{ $b->quantity }}</td>
                            <td class="px-6 py-4"><x-badge color="blue">{{ ucfirst($b->status) }}</x-badge></td>
                            <td class="px-6 py-4">@if($b->checked_in)<x-badge color="green">Yes</x-badge>@else<x-badge color="gray">No</x-badge>@endif</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('bookings.show', $b) }}" class="mr-2"><x-button variant="secondary" icon="eye">View</x-button></a>
                                @if(!$b->checked_in)
                                    <x-button variant="success" icon="checkin" type="button" data-booking-id="{{ $b->id }}" class="checkin-row-button">Check-in</x-button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div>
                <!-- Button triggers confirmation modal & AJAX -->
                <x-button variant="primary" icon="checkin" type="button" id="bulk-checkin-button">Bulk Check-in</x-button>
            </div>
            <div>
                {{ $bookings->links() }}
            </div>
        </div>
    </form>

    <!-- Confirmation modal -->
    <div id="bulk-confirm-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded shadow-lg w-11/12 max-w-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Confirm bulk check-in</h3>
            <p class="text-sm text-gray-600 mb-4">You're about to check-in <span id="bulk-count">0</span> attendee(s). This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <x-button variant="secondary" id="bulk-cancel">Cancel</x-button>
                <x-button variant="success" id="bulk-confirm">Confirm</x-button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed top-6 right-6 z-50 hidden">
        <div id="toast-inner" class="px-4 py-2 rounded shadow text-sm"></div>
    </div>

</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function(e){
        document.querySelectorAll('.select-row').forEach(function(cb){ if(!cb.disabled) cb.checked = e.target.checked; });
    });

    // Bulk check-in modal + AJAX
    const bulkBtn = document.getElementById('bulk-checkin-button');
    const modal = document.getElementById('bulk-confirm-modal');
    const bulkCount = document.getElementById('bulk-count');
    const bulkCancel = document.getElementById('bulk-cancel');
    const bulkConfirm = document.getElementById('bulk-confirm');

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.select-row'))
            .filter(cb => cb.checked && !cb.disabled)
            .map(cb => parseInt(cb.value, 10));
    }

    function showToast(message, success = true) {
        const toast = document.getElementById('toast');
        const inner = document.getElementById('toast-inner');
        inner.textContent = message;
        inner.className = 'px-4 py-2 rounded shadow text-sm ' + (success ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800');
        toast.classList.remove('hidden');
        setTimeout(() => { toast.classList.add('hidden'); }, 3000);
    }

    bulkBtn?.addEventListener('click', function(){
        const ids = getSelectedIds();
        if (!ids.length) {
            showToast('Please select at least one booking to check-in.', false);
            return;
        }
        bulkCount.textContent = ids.length;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    bulkCancel?.addEventListener('click', function(){
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    bulkConfirm?.addEventListener('click', function(){
        const ids = getSelectedIds();
        if (!ids.length) return;

        // send AJAX POST
        fetch("{{ route('admin.bookings.bulk_checkin') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids })
        })
        .then(r => r.json().then(j => ({ ok: r.ok, code: r.status, body: j })))
        .then(({ ok, code, body }) => {
            if (!ok) {
                showToast(body.message || 'Failed to check-in.', false);
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                return;
            }
            // update UI rows and disable checkboxes
            (body.ids || []).forEach(function(id){
                const cb = document.querySelector('.select-row[value="'+id+'"]');
                if (cb) {
                    cb.disabled = true;
                    cb.checked = false;
                    const row = cb.closest('tr');
                    if (row) {
                        // update checked-in badge
                        const badgeCell = row.querySelector('td:nth-child(8)');
                        if (badgeCell) badgeCell.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800">Yes</span>';
                        // remove check-in form/button
                        const actionCell = row.querySelector('td:last-child');
                        if (actionCell) {
                            const forms = actionCell.querySelectorAll('form');
                            forms.forEach(f => f.remove());
                        }
                    }
                }
            });

            showToast(body.message || 'Checked in.');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        })
        .catch(err => {
            console.error(err);
            showToast('Network error while checking in.', false);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    });

    // Per-row AJAX check-in
    document.querySelectorAll('.checkin-row-button').forEach(function(btn){
        btn.addEventListener('click', function(){
            const id = this.getAttribute('data-booking-id');
            if (!id) return;
            const url = '{{ url('/admin/bookings') }}' + '/' + id + '/checkin';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json().then(j => ({ ok: r.ok, code: r.status, body: j })))
            .then(({ ok, body }) => {
                if (!ok) {
                    showToast(body.message || 'Failed to check-in', false);
                    return;
                }
                // update row
                const row = btn.closest('tr');
                const cb = row.querySelector('.select-row');
                if (cb) { cb.disabled = true; cb.checked = false; }
                const badgeCell = row.querySelector('td:nth-child(8)');
                if (badgeCell) badgeCell.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800">Yes</span>';
                // remove the button
                btn.remove();
                showToast(body.message || 'Checked in');
            })
            .catch(err => { console.error(err); showToast('Network error', false); });
        });
    });
</script>

@endsection
