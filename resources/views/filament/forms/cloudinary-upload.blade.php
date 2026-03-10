<div id="cloudinary-uploader"
     data-cloud-name="{{ config('filesystems.disks.cloudinary.cloud') }}"
     data-upload-preset="emillia">

    <button type="button"
        onclick="uploadToCloudinary(this)"
        class="px-4 py-2 bg-blue-600 text-white rounded">
        Upload Foto 
    </button>
</div>

<script>
async function uploadToCloudinary(button) {
    const container = button.closest('#cloudinary-uploader');
    const cloudName = container.dataset.cloudName;
    const uploadPreset = container.dataset.uploadPreset;

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';

    input.onchange = async () => {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('upload_preset', uploadPreset);

        const res = await fetch(
            `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
            { method: 'POST', body: formData }
        );

        const data = await res.json();

        // Ambil komponen Livewire terdekat
        const lwEl = button.closest('[wire\\:id]');
        if (!lwEl) return;

        const component = Livewire.find(lwEl.getAttribute('wire:id'));
        if (!component) return;

        // Update state form langsung (ini kunci)
        component.set('data.photo', data.secure_url);
    };

    input.click();
}
</script>