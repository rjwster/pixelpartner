<x-app-layout>

    @can('mindmaps.show')
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            <div class="grid grid-cols-2 gap-4 mb-4">

                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Mindmaps</h1>
                </div>
                <div class="text-right">
                    <a href="{{ route('mindmap.create') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600">
                        {{ __('Create Mindmap') }}
                    </a>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                {{ __('Name') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                {{ __('Ideas') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                {{ __('Date') }}
                            </th>
                            @can('mindmaps.show')
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">{{ __('View') }}</span>
                                </th>
                            @endcan
                            {{-- @can('mindmaps.edit')
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">{{ __('Edit') }}</span>
                                </th>
                            @endcan --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mindmaps as $mindmap)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $mindmap->name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $mindmap->ideas_count }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $mindmap->created_at->diffForHumans() }}
                                </td>
                                @can('mindmaps.show')
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('mindmap.show', $mindmap->id) }}"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{ __('View') }}</a>
                                    </td>
                                @endcan
                                {{-- @can('mindmaps.edit')
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('mindmap.edit', $mindmap->id) }}"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{ __('Edit') }}</a>
                                    </td>
                                @endcan --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endcan

</x-app-layout>
