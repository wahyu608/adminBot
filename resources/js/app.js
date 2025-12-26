import './bootstrap';
import './echo';

const userId = document.querySelector('meta[name="user-id"]')?.content;

if (userId) {
    window.Echo.private(`user.${userId}`)
        .listen('.syncron-telegram', (e) => {
            console.log('Telegram Sync Event:', e);
            new FilamentNotification()
                .title(e.success ? 'Berhasil' : 'Gagal')
                .body(e.message)
                .icon(e.success ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                .iconColor(e.success ? 'success' : 'danger')
                .duration(5000) 
                .send();
        });
    
    window.Echo.connector.pusher.connection.bind('connected', function() {
        console.log('Reverb Connected');
        new FilamentNotification()
            .title('Online')
            .body('Status saat ini online')
            .success()
            .duration(3000)
            .send();
    });
    
    window.Echo.connector.pusher.connection.bind('disconnected', function() {
        console.log('Reverb Disconnected');
        new FilamentNotification()
            .title('Koneksi buruk')
            .body('koneksi internet buruk')
            .success()
            .duration(3000)
            .send();
    });
    
    console.log(`Listening on channel: user.${userId}`);
} else {
    console.warn('User ID tidak ditemukan');
}