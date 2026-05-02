@persist('navigate-spinner')
<div id="loader" x-data="{
        loading: true,
        navigating: false,
        init() {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOMContentLoaded event received');
                setTimeout(() => {
                    this.loading = false;
                }, 1000); // Adjust delay as needed (e.g., 2000ms)
            });
        }
}" x-show="navigating" x-transition:enter="transition-opacity duration-1000" x-transition:leave="transition-opacity duration-1000" style="z-index:9999999;" class="fixed inset-0 bg-black bg-opacity-100 flex items-center justify-center">
<div id="loader-center"></div>
</div>
@endpersist