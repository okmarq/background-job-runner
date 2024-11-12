<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Background Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8 bg-gray-100 dark:bg-gray-900">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @elseif (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <form method="GET" action="{{ route('background_jobs.index') }}" class="flex items-center justify-between mb-4">
                <div class="w-2/5">
                    <x-input-label for="status" :value="__('Status:')"/>
                    <select class="mt-1 block w-full rounded" name="status">
                        <option value="">All</option>
                        <option value="running">Running</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="w-2/5">
                    <x-input-label for="priority" :value="__('Priority:')"/>
                    <select class="mt-1 block w-full rounded" name="priority">
                        <option value="">All</option>
                        <option value="1">High</option>
                        <option value="2">Medium</option>
                        <option value="3">Low</option>
                    </select>
                </div>
                <button class="bg-blue-500 hover:bg-blue-700 px-2 py-1 rounded-lg text-amber-50 mt-5" type="submit">Filter</button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-black text-center mb-4">All Jobs</h3>
                    <table class="table table-bordered w-full">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Class</th>
                            <th class="px-4 py-2">Method</th>
                            <th class="px-4 py-2">Parameter</th>
                            <th class="px-4 py-2">Priority</th>
                            <th class="px-4 py-2">Attempt</th>
                            <th class="px-4 py-2">Delay</th>
                            <th class="px-4 py-2">Retries</th>
                            <th class="px-4 py-2">Output</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td class="border px-4 py-2">{{ $job->id }}</td>
                                <td class="border px-4 py-2">{{ $job->class }}</td>
                                <td class="border px-4 py-2">{{ $job->method }}</td>
                                <td class="border px-4 py-2">{{ $job->parameters }}</td>
                                <td class="border px-4 py-2">{{ $job->priority }}</td>
                                <td class="border px-4 py-2">{{ $job->attempt }}</td>
                                <td class="border px-4 py-2">{{ $job->delay }}</td>
                                <td class="border px-4 py-2">{{ $job->retries }}</td>
                                <td class="border px-4 py-2">{{ $job->output }}</td>
                                <td class="border px-4 py-2">{{ $job->status }}</td>
                                <td class="border px-4 py-2">
                                    @if($job->status === config('constants.status.running'))
                                        <form method="POST" action="{{ route('background_jobs.cancel', ['backgroundJob' => $job->id]) }}">
                                            @csrf
                                            <button type="submit" class="text-red-500">Cancel Job</button>
                                        </form>
                                    @elseif($job->status === config('constants.status.cancelled') || $job->status === config('constants.status.failed'))
                                        <form method="POST" action="{{ route('background_jobs.rerun', ['backgroundJob' => $job->id]) }}">
                                            @csrf
                                            <button type="submit" class="text-green-500">Rerun Job</button>
                                        </form>
                                    @elseif($job->status === config('constants.status.pending'))
                                        <form method="POST" action="{{ route('background_jobs.start', ['backgroundJob' => $job->id]) }}">
                                            @csrf
                                            <button type="submit" class="text-green-500">Run Job</button>
                                        </form>
                                    @else
                                        <span>No actions available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="border px-4 py-2 text-center">No Jobs</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-black text-center mb-4">Active Jobs</h3>
                    <table class="table table-bordered w-full">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Class</th>
                            <th class="px-4 py-2">Method</th>
                            <th class="px-4 py-2">Parameter</th>
                            <th class="px-4 py-2">Output</th>
                            <th class="px-4 py-2">Attempt</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activeJobs as $job)
                            <tr>
                                <td class="border px-4 py-2">{{ $job->id }}</td>
                                <td class="border px-4 py-2">{{ $job->class }}</td>
                                <td class="border px-4 py-2">{{ $job->method }}</td>
                                <td class="border px-4 py-2">{{ $job->parameters }}</td>
                                <td class="border px-4 py-2">{{ $job->output }}</td>
                                <td class="border px-4 py-2">{{ $job->attempts }}</td>
                                <td class="border px-4 py-2">{{ $job->status }}</td>
                                <td class="border px-4 py-2">
                                    <form method="POST" action="{{ route('background_jobs.cancel', ['backgroundJob' => $job->id]) }}">
                                        @csrf
                                        <button type="submit" class="text-red-500">Cancel Job</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="border px-4 py-2 text-center">No active Jobs</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900">
                    <h3>Error Logs</h3>
                    @foreach ($errorLogs as $error)
                        <p class="text-red-500">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
