<div class="border border-gray-400 rounded">
    <img class="object-contain w-full h-40 purple-to-orange-horizontal" src={{ $collectible->getImage() }} alt="">

    <div class="pt-2 p-2">
        <h3 class="font-bold text-base text-gray-700">{{ $collectible->getName() }}</h3>
        <p class="text-sm text-gray-600 mt-1">{{ $collectible->getDescription() }}</p>
    </div>
</div>
