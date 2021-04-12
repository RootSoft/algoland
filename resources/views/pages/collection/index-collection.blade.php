<x-app-layout title="Algoland - My collection">

    <script>
    </script>

    <div class="max-w-6xl mx-auto px-4 p-8">
        <h3 class="text-gray-600 font-bold text-2xl">My collection</h3>

        <!-- A grid of collectibles -->
        <div class="grid grid-cols-4 gap-8 mt-6">
            @foreach ($collectibles as $collectible)
                <x-collectible-card :collectible="$collectible"/>
            @endforeach
        </div>
    </div>

</x-app-layout>
