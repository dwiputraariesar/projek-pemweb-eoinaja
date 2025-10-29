@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Roles for {{ $user->name }}</h1>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PATCH')

        <div class="bg-white p-4 shadow rounded">
            <div class="mb-4">
                <label class="block font-medium">Roles</label>
                @foreach($roles as $role)
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role }}" class="form-checkbox" {{ $user->hasRole($role) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $role }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('admin.users.index') }}" class="mr-3 text-gray-600">Back</a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </div>
    </form>
</div>
@endsection
