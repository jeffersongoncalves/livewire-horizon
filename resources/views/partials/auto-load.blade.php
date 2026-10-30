{{-- Re-renders the component every few seconds while "Auto load new entries" (layout) is on, like Horizon's toggle. --}}
<div class="hidden" x-data="{
    timer: null,
    init() {
        this.timer = setInterval(() => {
            if (this.$store.horizon.autoLoad && ! document.hidden) this.$wire.autoLoad()
        }, {{ (int) config('livewire-horizon.poll', 5) * 1000 }})
    },
    destroy() { clearInterval(this.timer) },
}"></div>
