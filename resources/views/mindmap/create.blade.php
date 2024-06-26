<x-app-layout>

    @can('mindmaps.create')
        <div class="w-full max-w-sm mx-auto mt-14 p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
            <form class="space-y-6" method="post" action="{{ route('mindmap.store') }}">
                <h5 class="text-xl font-medium text-gray-900 dark:text-white">{{ __('Create new mindmap') }}</h5>
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required />
                </div>
        
                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{ __('Save') }}</button>
            
                @csrf
                @method('POST')
            </form>
        </div>
    @endcan
        
</x-app-layout>