<div x-data="{ show: false, message: '', type: 'success' }"
     @notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 3000)"
     x-show="show"
     x-transition
     class="fixed top-5 right-5 px-4 py-2 rounded-lg shadow-lg"
     :class="{
        'bg-green-500 text-white': type === 'success',
        'bg-red-500 text-white': type === 'error',
        'bg-blue-500 text-white': type === 'info',
        'bg-yellow-500 text-white' :type === 'warning'
     }">
    <p x-text="message"></p>
</div>
