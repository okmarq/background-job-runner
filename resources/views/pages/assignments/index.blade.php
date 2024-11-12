<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Assignments') }}
            </h2>

            <a class="bg-blue-500 hover:bg-blue-700 px-2 py-1 rounded-lg text-amber-50" href="{{ route('assignments.create') }}">Submit Assignment</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-black text-center mb-4">Assignments</h3>
                    <table class="table table-bordered w-full">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Course</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-4 py-2">Last Name</th>
                            <th class="px-4 py-2">Score</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td class="border px-4 py-2">{{ $assignment->id }}</td>
                                <td class="border px-4 py-2">{{ $assignment->course }}</td>
                                <td class="border px-4 py-2">{{ $assignment->user->firstname }}</td>
                                <td class="border px-4 py-2">{{ $assignment->user->lastname }}</td>
                                <td class="border px-4 py-2">{{ $assignment->score }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="border px-4 py-2 text-center">You have no assignment</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
