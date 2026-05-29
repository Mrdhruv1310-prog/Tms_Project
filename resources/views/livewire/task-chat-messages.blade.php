<div
    class="mb-4 max-h-80 space-y-3 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
    @forelse ($messages as $message)
        <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="mb-1 flex items-center justify-between gap-3">
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $message->user?->first_name }} {{ $message->user?->last_name }}
                </span>

                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $message->created_at?->format('d-m-Y H:i') }}
                </span>
            </div>

            <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-200">
                {{ $message->message }}
            </p>
        </div>
    @empty
        <div
            class="rounded-xl bg-white p-4 text-center text-sm text-gray-500 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700">
            No task conversation yet.
        </div>
    @endforelse
</div>
