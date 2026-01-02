import './bootstrap';
import './echo';

const userId = document.querySelector('meta[name="user-id"]')?.content;

if (userId) {

    const notifQueue = [];
    let notifActive = false;

    // Fungsi untuk menampilkan notif berikutnya
    function showNextNotification() {
        if (notifQueue.length === 0) {
            notifActive = false;
            return;
        }

        notifActive = true;
        const n = notifQueue.shift();
        new FilamentNotification()
            .title(n.title)
            .body(n.body)
            .icon(n.icon)
            .iconColor(n.color)
            .duration(n.duration)
            .send();

        setTimeout(showNextNotification, n.duration + 200);
    }

    // Fungsi push notif ke queue
    function pushNotification(event) {
        let notif = null;

        if (event.status === 'success') {
            notif = {
                title: 'Berhasil',
                body: event.message || 'Operasi berhasil',
                icon: 'heroicon-o-check-circle',
                color: 'success',
                duration: 5000
            };
        } else if (event.status === 'failed') {
            notif = {
                title: 'Gagal',
                body: event.message || 'Operasi gagal',
                icon: 'heroicon-o-x-circle',
                color: 'danger',
                duration: 5000
            };
        } else if (event.status === 'pending') {
            notif = {
                title: 'Pending',
                body: event.message || 'Sedang memproses',
                icon: 'heroicon-o-clock',
                color: 'warning',
                duration: 4000
            };
        }

        if (notif) {
            notifQueue.push(notif);
            if (!notifActive) showNextNotification();
        }
    }

    // Listen event dari Pusher
    window.Echo.private(`user.${userId}`)
        .listen('.syncron-telegram', (e) => {
            console.log('Telegram Sync Event:', e);

            // Pastikan ada field status
            if (!e.status) e.status = e.success ? 'success' : 'failed';

            pushNotification(e);
        });

    // Koneksi Pusher
    // window.Echo.connector.pusher.connection.bind('connected', function() {
    //     console.log('Pusher Connected');
    //     pushNotification({
    //         status: 'success',
    //         message: 'Status saat ini online'
    //     });
    // });

    // window.Echo.connector.pusher.connection.bind('disconnected', function() {
    //     console.log('Pusher Disconnected');
    //     pushNotification({
    //         status: 'failed',
    //         message: 'Koneksi internet buruk'
    //     });
    // });

    console.log(`Listening on channel: user.${userId}`);
} else {
    console.warn('User ID tidak ditemukan');
}
