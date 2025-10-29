@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">User Management</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @foreach($users as $user)
                <tr class="border-t">
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->roles->pluck('name')->join(', ') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-sm text-blue-600">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
