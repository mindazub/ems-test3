<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }}
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                @if(!$plant)
                    <div class="text-red-600">Could not load plant data.</div>
                @else
                    <h1 class="text-2xl font-bold mb-4">Plant View Data</h1>
                    <table class="table-auto w-full mb-4">
                        <tbody>
                        @foreach($plant as $key => $value)
                            <tr>
                                <td class="font-semibold px-2 py-1">{{ $key }}</td>
                                <td class="px-2 py-1">
                                    @if(is_array($value) || is_object($value))
                                        <pre class="bg-gray-100 rounded p-2 text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('plants.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded transition">
                        Back to All Plants List
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
