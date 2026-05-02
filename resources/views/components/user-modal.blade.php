{{-- @props(['name', 'title']) --}}

<div x-data="{ show : true }" 
    x-show="visible"
    x-on:open-modal.window="show" class="fixed z-50 inset-0" x-transition.duration>
    
    {{-- Gray Background --}}
    <div x-on:click="show = false" class="fixed inset-0 bg-gray-300 opacity-40"></div>

    {{-- Modal Body --}}
    <div class="bg-white rounded m-auto fixed inset-0 max-w-2xl overflow-y-auto" style="max-height:500px">

        <div class="p-4">
           dsf ddssd
        </div>
    </div>
</div>